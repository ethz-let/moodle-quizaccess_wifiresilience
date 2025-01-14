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
 * This script processes ajax timer equests during the quiz.
 *
 * @package   quizaccess_wifiresilience
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

// Remember the current time as the time any responses were submitted.
// (so as to make sure students don't get penalized for slow processing on this page).
$timenow = time();

// Get submitted parameters.
$attemptid = required_param('attempt',  PARAM_INT);
$cmid = optional_param('cmid', null, PARAM_INT);



$transaction = $DB->start_delegated_transaction();
$attemptobj = quiz_create_attempt_handling_errors($attemptid, $cmid);

// Check login.
if (!isloggedin() || !confirm_sesskey()) {
    echo json_encode(array('result' => 'lostsession'));
    die;
}
require_login($attemptobj->get_course(), false, $attemptobj->get_cm());
require_sesskey();

// Check that this attempt belongs to this user.
if ($attemptobj->get_userid() != $USER->id) {
    throw new moodle_exception('notyourattempt');
}

// Check capabilities.
if (!$attemptobj->is_preview_user()) {
    $attemptobj->require_capability('mod/quiz:attempt');
}
$options = $attemptobj->get_display_options(false);

// If the attempt is already closed, send them to the review page.
if ($attemptobj->is_finished()) {
    throw new moodle_exception('attemptalreadyclosed', 'quiz', $attemptobj->review_url());
}
/*** tobias */
// Check if its latest active session.
$ativesessobj = ['sesskey' => sesskey(), 'userid' => $USER->id, 'attemptid' => $attemptid];
$latestactivesession = $DB->get_record('quizaccess_wifiresilience_sess', $ativesessobj);
if(!$latestactivesession){
  $result = array('result' => 'blockattempt');
  echo json_encode($result);
  require_logout();
  die;
}
/*** end tobias */
$endtime = $attemptobj->get_quizobj()->get_access_manager(time())->get_end_time($attemptobj->get_attempt());

if ($endtime === false) {
    $endtime = 0;
}

$timeleft = $attemptobj->get_time_left_display(time());

if ($timeleft !== false) {
    $ispreview = $attemptobj->is_preview();
    $timerstartvalue = $timeleft;
    if (!$ispreview) {
        /*
        Make sure the timer starts just above zero. If $timeleft was <= 0, then
        this will just have the effect of causing the quiz to be submitted immediately.
        */
        $timerstartvalue = max($timerstartvalue, 1);
    }
} else {
    $timerstartvalue = 0;
}
$result = array();
$result['result'] = 'OK';
$result['timerstartvalue'] = $timerstartvalue;
$result['timelimit'] = $endtime;
echo json_encode($result);
