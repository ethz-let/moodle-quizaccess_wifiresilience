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
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

$cmid = required_param('cmid', PARAM_INT);
$id = required_param('id', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$type = required_param('t', PARAM_RAW);

$cm = get_coursemodule_from_id('quiz', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$quiz = $DB->get_record('quiz', array('id' => $cm->instance), '*', MUST_EXIST);
$user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, false, $cm);
require_capability('quizaccess/wifiresilience:uploadresponses', $context);
require_sesskey();

$conditions = ['id' => $id, 'quizid' => $quiz->id, 'userid' => $userid];
$filerec = $DB->get_record('quizaccess_wifiresilience_er', $conditions);

if(!$filerec) {
  throw new \moodle_exception('notfound');
}

if($type == 'plain') {
  $filecontent = $filerec->answer_plain;
  $prename = 'plain_';
} else {
  $filecontent = $filerec->answer_encrypted;
}
$namefile = clean_filename($prename.substr($user->idnumber.'_'.str_replace(' ','_', fullname($user)).'_'.str_replace(' ', '_', $quiz->name), 0, 50)).'.ethz';
//header download
header("Content-Disposition: attachment; filename=\"" . $namefile . "\"");
header("Content-Type: application/force-download");
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header ('Content-Type: application/octet-stream');
echo $filecontent;
