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

$PAGE->set_url('/mod/quiz/accessrule/wifiresilience/inspect.php', array('id' => $cmid));
require_login($course, false, $cm);
require_capability('quizaccess/wifiresilience:inspectresponses', $context);

$form = new \quizaccess_wifiresilience\form\inspect_responses($PAGE->url);

if ($form->is_cancelled()) {
    echo '<script>setTimeout(function() {window.location="'.$quizurl.'";}, 0);</script>';
} else if ($fromform = $form->get_data()) {

    // Process submission.
    $title = get_string('uploadinspection', 'quizaccess_wifiresilience',
        format_string($quiz->name, true, array('context' => $context)));

    $PAGE->navbar->add($title);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title($title);
    $PAGE->set_heading($course->fullname);
    $PAGE->requires->strings_for_js(array('downloadfile'), 'quizaccess_wifiresilience');
    $PAGE->requires->yui_module(
        'moodle-quizaccess_wifiresilience-initialiseinspect',
        'M.quizaccess_wifiresilience.initialiseinspect.init',
        array()
    );

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
            continue;
        }

        if ($file->is_external_file()) {
            continue;
        }

        if ($file->is_directory()) {
            continue;
        }

        echo $OUTPUT->heading(get_string('decryptingfile', 'quizaccess_wifiresilience', s($file->get_filename())), 3);

        $originalpost = null;
        $originalrequest = null;

        try {
            $data = json_decode($file->get_content());
            if (!$data) {
                if (function_exists('json_last_error_msg')) {
                    throw new coding_exception(json_last_error_msg());
                } else {
                    throw new coding_exception('JSON error: ' . json_last_error());
                }
            }

            if (!isset($data->responses)) {
                echo $OUTPUT->notification(get_string('filenoresponses', 'quizaccess_wifiresilience'), 'notifyfail');
            } else {
                if (isset($data->iv) || isset($data->key)) {

                    if (!$privatekey) {
                        echo $OUTPUT->notification(
                            get_string('filenodecryptionkey', 'quizaccess_wifiresilience'), 'notifyfail');
                    }

                    $encryptedaeskey = base64_decode($data->key);
                    if (!$encryptedaeskey) {
                        echo $OUTPUT->notification(
                            get_string('fileencryptedkeynobase64', 'quizaccess_wifiresilience'), 'notifyfail');
                    }

                    $encryptediv = base64_decode($data->iv);
                    if (!$encryptediv) {
                        echo $OUTPUT->notification(
                            get_string('fileencryptedinitvaluenobase64', 'quizaccess_wifiresilience'), 'notifyfail');
                    }

                    $aeskeystring = '';
                    if (!openssl_private_decrypt($encryptedaeskey, $aeskeystring, $privatekey)) {
                        echo $OUTPUT->notification(
                            get_string('fileunabledecryptkey', 'quizaccess_wifiresilience', openssl_error_string()), 'notifyfail');
                    }

                    $ivstring = '';
                    if (!openssl_private_decrypt($encryptediv, $ivstring, $privatekey)) {
                        echo $OUTPUT->notification(
                            get_string('fileunabledecryptkey', 'quizaccess_wifiresilience', openssl_error_string()), 'notifyfail');
                    }

                    $aeskey = base64_decode($aeskeystring);
                    if (!$aeskey) {
                        echo $OUTPUT->notification(
                            get_string('filekeynobase64', 'quizaccess_wifiresilience'), 'notifyfail');
                    }

                    $iv = base64_decode($ivstring);
                    if (!$iv) {
                        echo $OUTPUT->notification(
                            get_string('fileinitvaluenobase64', 'quizaccess_wifiresilience'), 'notifyfail');
                    }

                    $responses = openssl_decrypt($data->responses, 'AES-256-CBC', $aeskey, 0, $iv);

                    if (!$responses) {
                        echo $OUTPUT->notification(
                            get_string('fileunabledecrypt', 'quizaccess_wifiresilience', openssl_error_string()), 'notifyfail');
                    } else {
                      $postdata = array();
                      parse_str($responses, $postdata);
                      echo get_string('filewithoutkeyandiv', 'quizaccess_wifiresilience');
                      echo html_writer::tag('textarea', $responses,
                          array('id' => 'quizaccess_wifiresilience_dectypted_file_output_json', 'class' => 'inspectresponse'));


                      echo get_string('filearraystyle', 'quizaccess_wifiresilience');
                      echo html_writer::tag('textarea', s(var_export($postdata, true)),
                          array('id' => 'quizaccess_wifiresilience_dectypted_file_output_array', 'class' => 'inspectresponse'));
                    

                    }
                } else {
                    $responses = $data->responses;

                    // Get original before encryption work.
                    $encodeddata = $data;
                    $encodeddata->responses = $responses;
                    $encodeddataback = json_encode($encodeddata);

                    $encodeddatawithoutkey = $encodeddata;

                    $encodeddatawithoutkey->iv = '';
                    unset($encodeddatawithoutkey->iv);

                    $encodeddatawithoutkey->key = '';
                    unset($encodeddatawithoutkey->key);

                    $encodeddatabackwithoutkey = json_encode($encodeddatawithoutkey);

                    $postdata = array();
                    parse_str($responses, $postdata);

                    if (!isset($postdata['attempt'])) {
                        echo html_writer::tag('div', get_string('filenoattemptid', 'quizaccess_wifiresilience'),
                            array('class' => 'alert alert-error'));
                    }

                    echo get_string('filewithkeyandiv', 'quizaccess_wifiresilience');
                    echo html_writer::tag('textarea', $encodeddataback,
                        array('id' => 'quizaccess_wifiresilience_dectypted_file_output_json', 'class' => 'inspectresponse'));
                    echo html_writer::tag('div', '',
                        array('id' => 'quizaccess_wifiresilience_dectypted_file_output_json_link'));

                    echo get_string('filewithoutkeyandiv', 'quizaccess_wifiresilience');
                    echo html_writer::tag('textarea', $encodeddatabackwithoutkey,
                        array('id' => 'quizaccess_wifiresilience_dectypted_file_output_json_nokey', 'class' => 'inspectresponse'));
                    echo html_writer::tag('div', '',
                        array('id' => 'quizaccess_wifiresilience_dectypted_file_output_json_nokey_link'));

                    echo get_string('filearraystyle', 'quizaccess_wifiresilience');
                    echo html_writer::tag('textarea', s(var_export($postdata, true)),
                        array('id' => 'quizaccess_wifiresilience_dectypted_file_output_array', 'class' => 'inspectresponse'));
                    echo html_writer::tag('div', '',
                        array('id' => 'quizaccess_wifiresilience_dectypted_file_output_array_link'));

                    // Process the uploaded data. (We have to do weird fakery with $_POST && $_REQUEST).

                    $timenow = time();
                    $postdata['sesskey'] = sesskey();
                    $originalpost = $_POST;
                    $_POST = $postdata;
                    $originalrequest = $_REQUEST;
                    $_REQUEST = $postdata;
                    $_POST = $originalpost;
                    $originalpost = null;
                    $_REQUEST = $originalrequest;
                    $originalrequest = null;

                    // Display a success message.
                    echo $OUTPUT->notification(get_string('inspectionprocessedsuccessfully', 'quizaccess_wifiresilience'),
                        'notifysuccess');
                }
            }
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

    echo \html_writer::div($OUTPUT->single_button($quizurl, get_string('continue'), 'get'));
    echo $OUTPUT->footer();

} else {

    // Show the form.
    $title = get_string('uploadinspectionfor', 'quizaccess_wifiresilience',
            format_string($quiz->name, true, array('context' => $context)));
    $PAGE->navbar->add($title);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title($title);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();
    echo $OUTPUT->heading($title);
    echo $OUTPUT->box(get_string('inspectingfiledesc', 'quizaccess_wifiresilience'),
        array('class' => 'alert alert-warning'));
    $form->display();
    echo $OUTPUT->footer();
}
