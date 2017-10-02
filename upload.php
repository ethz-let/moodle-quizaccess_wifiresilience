<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Script to upload responses saved from the emergency download link.
 *
 * @package   quizaccess_wifiresilience
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

$cmid = optional_param('id', 0, PARAM_INT);
$cm = get_coursemodule_from_id('quiz', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$quiz = $DB->get_record('quiz', array('id' => $cm->instance), '*', MUST_EXIST);
$quizurl = new moodle_url('/mod/quiz/view.php', array('id' => $cm->id));
$context = context_module::instance($cm->id);

$PAGE->set_url('/mod/quiz/accessrule/wifiresilience/upload.php', array('id' => $cmid));
require_login($course, false, $cm);
require_capability('quizaccess/wifiresilience:uploadresponses', $context);

$form = new \quizaccess_wifiresilience\form\upload_responses($PAGE->url);
if ($form->is_cancelled()) {
    redirect($quizurl);

} else if ($fromform = $form->get_data()) {

    // Process submission.
    $title = get_string('uploadingresponsesfor', 'quizaccess_wifiresilience',
            format_string($quiz->name, true, array('context' => $context)));
    $PAGE->navbar->add($title);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title($title);
    $PAGE->set_heading($course->fullname);

    $files = get_file_storage()->get_area_files(context_user::instance($USER->id)->id,
            'user', 'draft', $fromform->responsefiles, 'id');
    $filesprocessed = 0;

    $privatekey = null;
    $privatekeystring = get_config('quizaccess_wifiresilience', 'privatekey');
    if ($privatekeystring) {
        $privatekey = openssl_get_privatekey($privatekeystring);
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading($title);

    foreach ($files as $file) {
        if ($file->get_filepath() !== '/') {
            continue; // Should not happen due to form validation.
        }
        if ($file->is_external_file()) {
            continue; // Should not happen due to form validation.
        }

        if ($file->is_directory()) {
            continue; // Not interesting.
        }

        echo $OUTPUT->heading(get_string('processingfile', 'quizaccess_wifiresilience', s($file->get_filename())), 3);

        $originalpost = null;
        $originalrequest = null;
        try {
          // Some files are already encoded, so decode them just in case.
          $original_content = $file->get_content();
          $decoded_content = urldecode($original_content);
          // Decode, compare to original. If it does differ, original is encoded.
          // If it doesn't differ, original isn't encoded.
          if($decoded_content == $original_content){
            $data_res = $original_content; // or Decoded.. Doesnt matter here.
            $OUTPUT->notification("Data File isnt URL-ENCODED. Use data as is.");
          } else {
            $data_res = $original_content;
            $OUTPUT->notification("Data File is URL-ENCODED. Use URL-DECODED data.");
          }
        //  print_r($data);exit;
          //$file->get_content()
            $data = json_decode($data_res);

            if (!$data) {
                if (function_exists('json_last_error_msg')) {
                    throw new coding_exception("JSON Data Decode: ".json_last_error_msg());
                } else {
                    throw new coding_exception('JSON error: ' . json_last_error());
                }
            }
            if (!isset($data->responses)) {
                throw new coding_exception('This file does not appear to contain responses.');
            }

            if (isset($data->iv) || isset($data->key)) {
                if (!$privatekey) {
                    throw new coding_exception('Got apparently encrypted responses, but there is no decryption key.');
                }

                $encryptedaeskey = base64_decode($data->key);
                if (!$encryptedaeskey) {
                    throw new coding_exception('Encrypted AES key not properly base-64 encoded.');
                }
                $encryptediv = base64_decode($data->iv);
                if (!$encryptediv) {
                    throw new coding_exception('Encrypted initial value not properly base-64 encoded.');
                }

                $aeskeystring = '';
                if (!openssl_private_decrypt($encryptedaeskey, $aeskeystring, $privatekey)) {
                    throw new coding_exception('Could not decrypt the AES key. ' . openssl_error_string());
                }

                $ivstring = '';
                if (!openssl_private_decrypt($encryptediv, $ivstring, $privatekey)) {
                    throw new coding_exception('Could not decrypt the AES key. ' . openssl_error_string());
                }

                $aeskey = base64_decode($aeskeystring);
                if (!$aeskey) {
                    throw new coding_exception('AES key not properly base-64 encoded.');
                }
                $iv = base64_decode($ivstring);
                if (!$iv) {
                    throw new coding_exception('Initial value not properly base-64 encoded.');
                }

                $responses = openssl_decrypt($data->responses, 'AES-256-CBC', $aeskey, 0, $iv);
                if (!$responses) {
                    throw new coding_exception('Could not decrypt the responses. ' . openssl_error_string());
                }

            } else {
                $responses = $data->responses;
            }

            $postdata = array();
            parse_str($responses, $postdata);


            if(isset($fromform->takeattemptfromjson)){
              if (!isset($data->attemptid)) {
                  throw new coding_exception('This file does not appear to non encrypted attempt ID (attemptid). You have selected to take attempt ID from non encryped JSON parameter, Please unselect the option and try again.');
              }
              $postdata['attempt'] = $data->attemptid;
            }

            if (!isset($postdata['attempt'])) {
                throw new coding_exception('The uploaded data did not include an attempt id.');
            }

            echo html_writer::tag('textarea', s(print_r($postdata, true)), array('readonly' => 'readonly'));

            // Load the attempt.
            $attemptobj = quiz_attempt::create($postdata['attempt']);
            if ($attemptobj->get_cmid() != $cmid) {
                throw new coding_exception('The uploaded data does not belong to this quiz.');
            }

            // Process the uploaded data. (We have to do weird fakery with $_POST && $_REQUEST.)
            $timenow = time();
            $postdata['sesskey'] = sesskey();
            $originalpost = $_POST;
            $_POST = $postdata;
            $originalrequest = $_REQUEST;
            $_REQUEST = $postdata;

            // Process times correctly
            if ($fromform->submissiontime) {
                switch($fromform->submissiontime) {
                  case 1:
                      // From File last_change
                      if(!isset($postdata['last_change']) || $postdata['last_change'] == '0' || !$postdata['last_change'] || $postdata['last_change'] == 'undefined'){
                        $postdata['last_change'] = $timenow;
                        $timenow = $timenow;
                      } else {
                        $date = new DateTime($postdata['last_change']);
                        $timenow = $date->getTimestamp();
                      }


                      break;
                  case 2:
                      // Now
                      $timenow = time();
                      break;
                  case 3:
                      // Quiz Finishtime

                      $duedate = 0;
                      if($quiz->timelimit){
                        $duedate = $attemptobj->timestart + $quiz->timelimit;
                      }
                      // if exceeded, put the time to be the due date (if set)
                      if($duedate !=0 && $timenow > $duedate) {
                        $timenow = $duedate;
                      } else {
                        $timenow = time();
                      }

                      break;
                  default:
                      $timenow = time();
                }
                if(!isset($timenow)) $timenow = time();
            }
            if ($fromform->finishattempts) {
                // Only if final submission has happened - otherwise now time for uploaded responses.
                // Override $fromform->submissiontime
                if (isset($fromform->usefinalsubmissiontime) && isset($postdata['final_submission_time']) && $postdata['final_submission_time'] != 0){
                    $timenow = $postdata['final_submission_time'];
                }
                $attemptobj->process_finish($timenow, true); // Finish.
            } else {

                if(isset($fromform->countrealofflinetime) && isset($postdata['real_offline_time'])){
                  $timenow = $timenow - $postdata['real_offline_time'];
                }

                $attemptobj->process_submitted_actions($timenow); // In progress.
            }
            $_POST = $originalpost;
            $originalpost = null;
            $_REQUEST = $originalrequest;
            $originalrequest = null;

            // Display a success message.
            echo $OUTPUT->notification(get_string('dataprocessedsuccessfully', 'quizaccess_wifiresilience',
                    html_writer::link($attemptobj->review_url(), get_string('reviewthisattempt', 'quizaccess_wifiresilience'))),
                    'notifysuccess');

        } catch (Exception $e) {
            if ($originalpost !== null) {
                $_POST = $originalpost;
                $originalpost = null;
            }
            if ($originalrequest !== null) {
                $_REQUEST = $originalrequest;
                $originalrequest = null;
            }
            echo $OUTPUT->box_start();
            echo $OUTPUT->heading(get_string('uploadfailed', 'quizaccess_wifiresilience'), 4);
            echo $OUTPUT->notification($e->getMessage());
            echo format_backtrace($e->getTrace());
            echo $OUTPUT->box_end();
        }
    }
    if ($privatekey) {
        openssl_pkey_free($privatekey);
    }

    echo $OUTPUT->confirm(get_string('processingcomplete', 'quizaccess_wifiresilience', 3),
            new single_button($PAGE->url, get_string('uploadmoreresponses', 'quizaccess_wifiresilience'), 'get'),
            new single_button($quizurl, get_string('backtothequiz', 'quizaccess_wifiresilience'), 'get'));
    echo $OUTPUT->footer();

} else {

    // Show the form.
    $title = get_string('uploadresponsesfor', 'quizaccess_wifiresilience',
            format_string($quiz->name, true, array('context' => $context)));
    $PAGE->navbar->add($title);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title($title);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();
    echo $OUTPUT->heading($title);
    $form->display();
    echo $OUTPUT->footer();
}
