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
 * This script processes ajax start timer adjustment during the quiz.
 *
 * @package   quizaccess_wifiresilience
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

// Get submitted parameters.
$timestarted = required_param('actualstarttimeinput',  PARAM_INT);
$attemptid = required_param('attempt',  PARAM_INT);
$cmid = optional_param('cmid', null, PARAM_INT);

$timeqstarted = round($timestarted / 1000);

$attemptobj = quiz_create_attempt_handling_errors($attemptid, $cmid);
//$attemptobj = quiz_attempt::create($attemptid);
//$attemptobj = quiz_create_attempt_handling_errors($attemptid, null);

// Check login.
if (!isloggedin() || !confirm_sesskey()) {
    echo json_encode(array('result' => 'lostsession'));
    die;
}
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

// If the attempt is already closed, send them to the review page.
if ($attemptobj->is_finished()) {
    throw new moodle_quiz_exception($attemptobj->get_quizobj(),
            'attemptalreadyclosed', null, $attemptobj->review_url());
}

$originalrec = $DB->get_record('quiz_attempts', ['id' => $attemptid]);
$timerreference = new stdClass();
$timerreference->id = $attemptid;
$timerreference->timestart = $originalrec->timestart + $timeqstarted;
$adjuststarttime = $DB->update_record('quiz_attempts', $timerreference);

$result = array();
if($adjuststarttime) {
  $result['result'] = 'OK';
  $result['originalstarttime'] = $originalrec->timestart;
  $result['updatedstarttime'] = $timerreference->timestart;
  $result['timediff'] = $timeqstarted;
} else {
  $result['result'] = 'FAIL';
}
echo json_encode($result);
