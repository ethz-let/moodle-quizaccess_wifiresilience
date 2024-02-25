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
 * This script processes ER file when submission fails but there is internet connection.
 *
 * @package   quizaccess_wifiresilience
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

// Get submitted parameters.
$attemptid = required_param('attempt',  PARAM_INT);
$cmid = required_param('cmid',PARAM_INT);
$answerplain = required_param('answerplain', PARAM_RAW);
$answerencrypted = required_param('answerencrypted', PARAM_RAW);
$sesskey = required_param('sesskey', PARAM_RAW);

// Check login.
if (!isloggedin() || !confirm_sesskey()) {
    echo json_encode(array('result' => 'lostsession'));
    die;
}
$attemptobj = quiz_create_attempt_handling_errors($attemptid, $cmid);

require_login($attemptobj->get_course(), false, $attemptobj->get_cm());
require_sesskey();



// Check that this attempt belongs to this user.
if ($attemptobj->get_userid() != $USER->id) {
    throw new moodle_quiz_exception($attemptobj->get_quizobj(), 'notyourattempt');
}

// Check capabilities.
if (!$attemptobj->is_preview_user()) {
    $attemptobj->require_capability('mod/quiz:attempt');
}
$quizid = $attemptobj->get_quizid();
$attemptcount = $attemptobj->get_attempt()->attempt;

$originalrec = $DB->get_record('quizaccess_wifiresilience_er',
               ['quizid' => $quizid, 'userid' => $USER->id, 'attempt' => $attemptcount]);

$errecord = new stdClass;
$errecord->quizid = $quizid;
$errecord->userid = $USER->id;
$errecord->attempt = $attemptcount;
$errecord->answer_plain = $answerplain;
$errecord->answer_encrypted = $answerencrypted;
$errecord->timecreated = time();

if($originalrec) {
  $errecord->id = $originalrec->id;
  $senderfile = $DB->update_record('quizaccess_wifiresilience_er', $errecord);
} else {
  $senderfile = $DB->insert_record('quizaccess_wifiresilience_er', $errecord);
}

$result = array();
if($senderfile) {
  $result['result'] = 'OK';
} else {
  $result['result'] = 'FAIL';
}
echo json_encode($result);
