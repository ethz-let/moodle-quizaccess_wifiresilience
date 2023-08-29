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
require_once("$CFG->dirroot/repository/lib.php");
require_once("$CFG->dirroot/repository/lib.php");
require_once("$CFG->dirroot/mod/quiz/locallib.php");

$cmid = optional_param('id', 0, PARAM_INT);
$cm = get_coursemodule_from_id('quiz', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$quiz = $DB->get_record('quiz', array('id' => $cm->instance), '*', MUST_EXIST);
$quizurl = new moodle_url('/mod/quiz/view.php', array('id' => $cm->id));
$context = context_module::instance($cm->id);

$PAGE->set_url('/mod/quiz/accessrule/wifiresilience/uploaded_syncedfiles.php', array('id' => $cmid));

require_login($course, false, $cm);
require_capability('quizaccess/wifiresilience:localresponses', $context);

$title = get_string('privatefiles');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');
$PAGE->set_pagetype('exam-synced-files');

echo $OUTPUT->header();
echo $OUTPUT->box_start();

$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'quizaccess_wifiresilience', 'synced_exam_files');

if (count($files) == 0) {
    $OUTPUT->notification("No synced files for " . $quiz->name . " yet");
}

echo html_writer::empty_tag('input',
array(
    'type' => 'text',
    'id' => 'syncedfilessearch',
    'placeholder' => 'Search'
));

// Create output table.

$content = [];

foreach ($files as $file) {

    $filename = $file->get_filename();

    if ($filename === '.') {
        continue;
    }
    $explodedfilename = explode('_', $filename);

    $url = moodle_url::make_pluginfile_url(
        $file->get_contextid(),
        $file->get_component(),
        $file->get_filearea(),
        $file->get_itemid(),
        $file->get_filepath(),
        $filename);

    $userid = 0;
    if (!empty($explodedfilename[4])) {
        $userid = trim(str_replace('u', '', $explodedfilename[4]));
    }
    $userid = (int)$userid;

    $attemptid = '';
    if (!empty($explodedfilename[5])) {
        $attemptid = str_replace('a', '', $explodedfilename[5]);
    }
    $attemptid = trim(str_replace('.sync', '', $attemptid));
    $attemptid = (int)$attemptid;

    $ftype = '';
    if (!empty($explodedfilename[6])) {
        $ftype = trim($explodedfilename[6]);
    }

    $user = $DB->get_record('user', array('id' => $userid));
    $attempt = $DB->get_record('quiz_attempts', array('id' => $attemptid));

    $attemptinfo = "";
    if (!$attempt) {
        $attemptinfo = "(Removed/Abandoned)";
        $attempt = new stdClass;
        $attempt->id = $attemptid;
    }

    $userinfo = "";
    if (!$user) {
        $user = new stdClass;
        $user->id = 0;
        $userinfo = "(Unknown User)";
    }

    array_push($content, array(
        'user' => "<a href='../../../../user/profile.php?id=$user->id' target='_blank'>" .
            fullname($user) . "(ID: $user->id) $userinfo" .
            "</a>",
        "attempt" => "<a href='../../../../mod/quiz/review.php?attempt=$attempt->id' target='_blank'>" .
            $attemptid . "(ID: $user->id) $attemptinfo" .
            "</a>",
        "date" => userdate($file->get_timecreated()),
        "type" => $ftype,
        "file" => "<a href='" . $url . "'>" . get_string('download') . "</a>",
        "reference" => $filename
    ));
}

$PAGE->requires->strings_for_js(array('date', 'file', 'user'), 'moodle');
$PAGE->requires->strings_for_js(array('attempt', 'filetype', 'reference'), 'quizaccess_wifiresilience');

$PAGE->requires->yui_module(
    'moodle-quizaccess_wifiresilience-initialisesyncedfiles',
    'M.quizaccess_wifiresilience.initialisesyncedfiles.init',
    array("content" => $content)
);

echo html_writer::tag('div', '', array('id' => 'datatable'));

echo $OUTPUT->box_end();
echo $OUTPUT->footer();
