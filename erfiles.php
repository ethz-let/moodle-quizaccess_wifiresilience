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
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/accessrule/wifiresilience/er_table.php');

$cmid = optional_param('id', 0, PARAM_INT);
$cm = get_coursemodule_from_id('quiz', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$quiz = $DB->get_record('quiz', array('id' => $cm->instance), '*', MUST_EXIST);
$quizurl = new moodle_url('/mod/quiz/view.php', array('id' => $cm->id));
$context = context_module::instance($cm->id);
$pagesize = 30;
$useinitialsbar = false;

$PAGE->set_url('/mod/quiz/accessrule/wifiresilience/erfiles.php', array('id' => $cmid));

require_login($course, false, $cm);
require_capability('quizaccess/wifiresilience:uploadresponses', $context);

$title = get_string('erfiles');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');
$PAGE->set_pagetype('exam-er-files');

echo $OUTPUT->header();

//$table = new table_sql('id');
$table = new quizaccess_wifiresilience_er_table('id');

// Work out the sql for the table.

$from = '{quizaccess_wifiresilience_er} a, {user} b';
$where = 'a.quizid =' . $quiz->id . ' and a.userid = b.id';
$fields = 'a.*, a.id as mainerfileid, a.timecreated as recordcreationtime,  b.*';

// Use this method only if you want to specify some sql with less joins for
// counting the total records.
$table->set_count_sql('SELECT COUNT(1) FROM {quizaccess_wifiresilience_er} where quizid = ' . $quiz->id);

$table->set_sql($fields, $from, $where);

// Define table columns.
$columns = array();
$headers = array();
$help = array();

$columns[]= 'idnumber';
$headers[]= get_string('idnumber');
$help[] = NULL;

$columns[]= 'fullname';
$headers[]= get_string('name');
$help[] = NULL;

$columns[]= 'email';
$headers[]= get_string('email');
$help[] = NULL;

$columns[]= 'attempt';
$headers[]= get_string('attemptnumber', 'quiz');
$help[] = NULL;

$columns[]= 'answer_encrypted';
$headers[]= get_string('encrypted');
$help[] = NULL;

$columns[]= 'answer_plain';
$headers[]= get_string('plain');
$help[] = NULL;

$columns[]= 'recordcreationtime';
$headers[]= get_string('timecreated');
$help[] = NULL;

$table->define_columns($columns);
$table->define_headers($headers);
$table->define_help_for_headers($help);
$table->sortable(true, 'id');

// Set up the table some of these settings will be ignored for downloads
$table->define_baseurl('/mod/quiz/accessrule/wifiresilience/erfiles.php?id='.$cmid);

$table->column_suppress('picture');
$table->column_suppress('fullname');
$table->column_suppress('idnumber');

$table->no_sorting('answer_encrypted');
$table->no_sorting('answer_plain');
$table->no_sorting('attempt');

$table->column_class('fullname', 'bold');

$table->set_attribute('id', 'erfiles');

$table->out($pagesize, $useinitialsbar);



echo $OUTPUT->footer();
