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

$cmid = optional_param('id', 0, PARAM_INT);
$cm = get_coursemodule_from_id('quiz', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$quiz = $DB->get_record('quiz', array('id' => $cm->instance), '*', MUST_EXIST);
$context = context_module::instance($cm->id);


// Check login.
if (!isloggedin() || !confirm_sesskey()) {
    echo "Not logged in.";
    die;
}
require_capability('quizaccess/wifiresilience:uploadresponses', $context);

$sql = "

SELECT DISTINCT  '' || u.id || '#' || COALESCE(quiza.attempt, 0)  AS uniqueid,
(CASE WHEN (quiza.state = 'finished' AND NOT EXISTS (
                           SELECT 1 FROM {quiz_attempts} qa2
                            WHERE qa2.quiz = quiza.quiz AND
                                qa2.userid = quiza.userid AND
                                 qa2.state = 'finished' AND (
                COALESCE(qa2.sumgrades, 0) > COALESCE(quiza.sumgrades, 0) OR
               (COALESCE(qa2.sumgrades, 0) = COALESCE(quiza.sumgrades, 0) AND qa2.attempt < quiza.attempt)
                                ))) THEN 1 ELSE 0 END) AS gradedattempt,
                quiza.uniqueid AS usageid,
                quiza.id AS attempt,
                u.id AS userid,
                u.idnumber,
                u.picture,
                u.imagealt,
                u.institution,
                u.department,
                u.email, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.firstname, u.lastname,
                quiza.state,
                quiza.sumgrades,
                quiza.timemodified,
                quiza.timefinish,
                quiza.timestart,
                CASE WHEN quiza.timefinish = 0 THEN null
                     WHEN quiza.timefinish > quiza.timestart THEN quiza.timefinish - quiza.timestart
                     ELSE 0 END AS duration
    FROM  {user} u
LEFT JOIN {quiz_attempts} quiza ON
                                    quiza.userid = u.id AND quiza.quiz = :quizid
JOIN {user_enrolments} ej1_ue ON ej1_ue.userid = u.id
JOIN {enrol} ej1_e ON (ej1_e.id = ej1_ue.enrolid AND ej1_e.courseid = :ej1_courseid)
JOIN (SELECT DISTINCT userid
                                FROM {role_assignments}
                               WHERE contextid IN (1,3,25,82)
                                     AND roleid IN (5)
                             ) ra ON ra.userid = u.id
    WHERE (quiza.preview = 0 OR quiza.preview IS NULL) AND 1 = 1 AND u.deleted = 0 AND u.id <> :eu1_guestid AND u.deleted = 0 AND (quiza.state IN (:state16,:state17,:state18) OR quiza.state IS NULL)



";

$params = ['quizid' => $quiz->id,
'ej1_courseid' => $course->id,
'eu1_guestid' => 1,
'state16' => 'inprogress',
'state17' => 'overdue',
'state18' => 'abandoned'];
$res = $DB->get_records_sql($sql, $params);

if(!$res || count($res) == 0){
  echo "No Data";
  die();
}
echo "<table class='generaltable'><tr><th>".get_string('name')."</th><th>".get_string('email')."</th><th>".get_string('last').' '.get_string('answer')."</th><th>".get_string('attempts','quiz')."</th><th>".get_string('status')." (5 min)</th></tr>";

$autosaveperiod = get_config('quiz', 'autosaveperiod');

foreach($res as $key => $val){
  echo "<tr><td>[<a href='$CFG->wwwroot/mod/quiz/review.php?attempt=$val->attempt' target='_blank'>$val->userid</a>] <a href='$CFG->wwwroot/user/profile.php?id=$val->userid' target='_blank'>$val->firstname $val->lastname</a></td><td>$val->email</td>";
  if(!$val->usageid || empty($val->usageid)){
    echo "<td>-</td>";
  } else {
    $usages = $DB->get_record_sql("select id, timemodified from mdl_question_attempts where questionusageid=? order by timemodified desc limit 1", array($val->usageid));
    echo "<td>".get_string('ago', 'core_message', format_time(time() + 1 - $usages->timemodified))."</td>";
  }
echo "<td>$val->state</td>";
if($usages->timemodified < strtotime( "-300 second") ){
  echo '<td bgcolor=red><strong>!!Check!!</strong></td>';
} else {
  echo '<td bgcolor=green>OK</td>';
}

echo "</tr>";
}
echo "</table>";


//$usages = $DB->get_records_sql("select * from mdl_question_attempts where questionusageid=?", array(300));
