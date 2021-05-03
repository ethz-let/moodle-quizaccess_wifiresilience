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

$startwithkey = 'Wifiresilience-crs' . $course->id . '-' . 'cm' . $cm->id . '-id' . $quiz->id;

$PAGE->set_url('/mod/quiz/accessrule/wifiresilience/local.php', array('id' => $cmid));
require_login($course, false, $cm);
require_capability('quizaccess/wifiresilience:localresponses', $context);

// Show the localstorage files.
$title = get_string('localresponsesfor', 'quizaccess_wifiresilience',
         format_string($quiz->name, true, array('context' => $context)));
$PAGE->navbar->add($title);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);

$PAGE->requires->js('/mod/quiz/accessrule/wifiresilience/js/localforage.js', true);
$PAGE->requires->js('/mod/quiz/accessrule/wifiresilience/js/startswith.js', true);

$PAGE->requires->strings_for_js(
    array('download', 'delete', 'localnorecordsfound', 'localconfirmdeletelocal', 'localconfirmdeletestatus'),
    'quizaccess_wifiresilience');

$PAGE->requires->yui_module(
    'moodle-quizaccess_wifiresilience-initialiselocal',
    'M.quizaccess_wifiresilience.initialiselocal.init',
    array("startwithkey" => $startwithkey)
);

echo $OUTPUT->header();

echo html_writer::tag('div',
    get_string('localtableinfo', 'quizaccess_wifiresilience', array("startwithkey" => $startwithkey, "name" => $quiz->name)),
    array('class' => 'alert alert-info'));
echo html_writer::start_div('responsive-table');

echo get_string('localtableheaderencryptedattempts', 'quizaccess_wifiresilience');
$table = new html_table();
$table->head = array(
    get_string('localtablerecord', 'quizaccess_wifiresilience'),
    get_string('localtablelastsavedserver', 'quizaccess_wifiresilience'),
    get_string('localtablelastchangelocal', 'quizaccess_wifiresilience'),
    get_string('localtabledownload', 'quizaccess_wifiresilience'),
    get_string('localtabledelete', 'quizaccess_wifiresilience'));
$table->id = "quizaccess_wifiresilience-indexeddb-table";
$table->class = "table table-striped";
echo html_writer::table($table);

echo get_string('localtableheaderattempts', 'quizaccess_wifiresilience');
$table = new html_table();
$table->head = array(
    get_string('localtablerecord', 'quizaccess_wifiresilience'),
    get_string('localtablelastsavedserver', 'quizaccess_wifiresilience'),
    get_string('localtablelastchangelocal', 'quizaccess_wifiresilience'),
    get_string('localtabledownload', 'quizaccess_wifiresilience'),
    get_string('localtabledelete', 'quizaccess_wifiresilience'));
$table->id = "quizaccess_wifiresilience-localstorage-table";
$table->class = "table table-striped";

echo html_writer::table($table);
echo html_writer::end_div('');
echo $OUTPUT->footer();
