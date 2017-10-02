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
    redirect($quizurl);

} else if ($fromform = $form->get_data()) {

    // Process submission.
    $title = get_string('uploadinspection', 'quizaccess_wifiresilience',
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
                echo $OUTPUT->notification('This file does not appear to contain responses.', 'notifyfail');
            }

            if (isset($data->iv) || isset($data->key)) {
                if (!$privatekey) {
                    echo $OUTPUT->notification('Got apparently encrypted responses, but there is no decryption key.', 'notifyfail');
                }

                $encryptedaeskey = base64_decode($data->key);
                if (!$encryptedaeskey) {
                    echo $OUTPUT->notification('Encrypted AES key not properly base-64 encoded.', 'notifyfail');
                }
                $encryptediv = base64_decode($data->iv);
                if (!$encryptediv) {
                    echo $OUTPUT->notification('Encrypted initial value not properly base-64 encoded.', 'notifyfail');
                }

                $aeskeystring = '';
                if (!openssl_private_decrypt($encryptedaeskey, $aeskeystring, $privatekey)) {
                    echo $OUTPUT->notification('Could not decrypt the AES key. ' . openssl_error_string(), 'notifyfail');
                }

                $ivstring = '';
                if (!openssl_private_decrypt($encryptediv, $ivstring, $privatekey)) {
                    echo $OUTPUT->notification('Could not decrypt the AES key. ' . openssl_error_string(), 'notifyfail');
                }

                $aeskey = base64_decode($aeskeystring);
                if (!$aeskey) {
                    echo $OUTPUT->notification('AES key not properly base-64 encoded.', 'notifyfail');
                }
                $iv = base64_decode($ivstring);
                if (!$iv) {
                    echo $OUTPUT->notification('Initial value not properly base-64 encoded.', 'notifyfail');
                }

                $responses = openssl_decrypt($data->responses, 'AES-256-CBC', $aeskey, 0, $iv);
                if (!$responses) {
                    echo $OUTPUT->notification('Could not decrypt the responses. ' . openssl_error_string(), 'notifyfail');
                }

            } else {
                $responses = $data->responses;
            }
            // get original before encryption work..
            $encoded_data = $data;
            $encoded_data->responses = $responses;
            $encoded_data_back = json_encode($encoded_data);

            $encoded_data_withoutkey = $encoded_data;

            $encoded_data_withoutkey->iv = '';
            unset($encoded_data_withoutkey->iv);

            $encoded_data_withoutkey->key = '';
            unset($encoded_data_withoutkey->key);

            $encoded_data_back_without_key = json_encode($encoded_data_withoutkey);


            $postdata = array();
            parse_str($responses, $postdata);


            if (!isset($postdata['attempt'])) {
                echo '<div class="alert alert-error">The uploaded data did not include an attempt id.</div>';
            }

            echo "<br /><h3>Original Style (With KEY and IV)</h3>";
            echo '<textarea id="quizaccess_wifiresilience_dectypted_file_output_json" width="100%" height="auto" style="width:100%;max-width:100%;min-height:300px;height:auto;">' . $encoded_data_back . '</textarea>';
            echo '<div id="quizaccess_wifiresilience_dectypted_file_output_json_link"></div><br />';

            echo "<br /><h3>Original Style (Without KEY or IV) - Good to Use on other moodle instances or when Public and Private keys are Damaged.</h3>";
            echo '<textarea id="quizaccess_wifiresilience_dectypted_file_output_json_nokey" width="100%" height="auto" style="width:100%;max-width:100%;min-height:300px;height:auto;">' . $encoded_data_back_without_key . '</textarea>';
            echo '<div id="quizaccess_wifiresilience_dectypted_file_output_json_nokey_link"></div><br />';

            echo "<h3>Array Style</h3>";
            echo '<textarea id="quizaccess_wifiresilience_dectypted_file_output_array" width="100%" height="auto" style="width:100%;max-width:100%;min-height:300px;height:auto;">' . s(print_r($postdata, true)) . '</textarea>';
            echo '<div id="quizaccess_wifiresilience_dectypted_file_output_array_link"></div><br />';


            // Process the uploaded data. (We have to do weird fakery with $_POST && $_REQUEST.)
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
            echo $OUTPUT->notification(get_string('inspectionprocessedsuccessfully', 'quizaccess_wifiresilience'),'notifysuccess');
            echo '<script>
            function download_emergency_file_wifi_exam(whicharea){
              // create links
              var mydiv = document.getElementById(whicharea);
              var aTag = document.createElement("a");
              aTag.innerHTML = "Download as a file";
              which_textarea = whicharea.replace("_link","");

              var blob = new Blob([document.getElementById(which_textarea).value], {type: "octet/stream"});
              var url = window.URL.createObjectURL(blob);

              aTag.href = url;
              aTag.download = which_textarea + ".inspect";
              mydiv.appendChild(aTag);
            }
            download_emergency_file_wifi_exam("quizaccess_wifiresilience_dectypted_file_output_json_link");
            download_emergency_file_wifi_exam("quizaccess_wifiresilience_dectypted_file_output_json_nokey_link");
            download_emergency_file_wifi_exam("quizaccess_wifiresilience_dectypted_file_output_array_link");
            </script>';

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

    echo $OUTPUT->confirm(get_string('decryptingcomplete', 'quizaccess_wifiresilience', 3),
            new single_button($PAGE->url, get_string('uploadmoreresponses', 'quizaccess_wifiresilience'), 'get'),
            new single_button($quizurl, get_string('backtothequiz', 'quizaccess_wifiresilience'), 'get'));
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
    echo $OUTPUT->box(get_string('inspectingfiledesc', 'quizaccess_wifiresilience'),array('class' => 'alert alert-warning'));
    $form->display();
    echo $OUTPUT->footer();
}
