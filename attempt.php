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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Wifi-Resilient quiz mode, replacement attempt.php page.
 *
 * @package quizaccess_wifiresilience
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 use mod_quiz\output\navigation_panel_attempt;
 use mod_quiz\output\renderer;
 use mod_quiz\quiz_attempt;
// In case compression is not enabled on server, maybe try to compress it locally?
@ini_set("zlib.output_compression", "On");
@header('Service-Worker-Allowed: /');

require_once (__DIR__ . '/../../../../config.php');
require_once ($CFG->dirroot . '/mod/quiz/locallib.php');

// Get submitted parameters.
$attemptid = required_param('attempt', PARAM_INT);
$page = optional_param('page', null, PARAM_INT);
$cmid = optional_param('cmid', null, PARAM_INT);

if ($page == -1) {
    $page = 0;
}

// Create the attempt object.
//$attemptobj = quiz_attempt::create($attemptid);
$attemptobj = quiz_create_attempt_handling_errors($attemptid, $cmid);

// Fix the page number if necessary.
if ($page === null) {
    $page = $attemptobj->get_attempt()->currentpage;
}

$issequentionalquiz = 0;
if ($attemptobj->get_navigation_method() == QUIZ_NAVMETHOD_SEQ && $page < $attemptobj->get_currentpage()) {
    $page = $attemptobj->get_currentpage();
    $issequentionalquiz = 1;
}

// Initialise $PAGE.
$pageurl = $attemptobj->attempt_url(null, $page);
$PAGE->set_url(quizaccess_wifiresilience::ATTEMPT_URL, $pageurl->params());


$course = $attemptobj->get_course();
if (!$course) {
    print_error('invalidcourse');
}

$courseid = $course->id;
$cmid = $attemptobj->get_cmid();
if (!$cmid) {
    print_error('invalidcoursemodule');
}

// Check login.
require_login($course, false, $attemptobj->get_cm());

// Check that this attempt belongs to this user.
if ($attemptobj->get_userid() != $USER->id) {
    if ($attemptobj->has_capability('mod/quiz:viewreports')) {
        redirect($attemptobj->review_url(null, $page));
    } else {
        throw new moodle_quiz_exception($attemptobj->get_quizobj(), 'notyourattempt');
    }
}
$PAGE->activityheader->disable();
// Check capabilities and block settings.
if (!$attemptobj->is_preview_user()) {
    $attemptobj->require_capability('mod/quiz:attempt');
    if (empty($attemptobj->get_quiz()->showblocks)) {
        $PAGE->blocks->show_only_fake_blocks();
    }
} else {
    navigation_node::override_active_url($attemptobj->start_attempt_url());
}

// If the attempt is already closed, send them to the review page.
if ($attemptobj->is_finished()) {
    redirect($attemptobj->review_url(null, $page));
} else if ($attemptobj->get_state() == quiz_attempt::OVERDUE) {
    redirect($attemptobj->summary_url());
}

// Check the access rules.
$accessmanager = $attemptobj->get_access_manager(time());
$accessmanager->setup_attempt_page($PAGE);

// Complete masquerading as the mod-quiz-attempt page. Must be done after setup_attempt_page.
$PAGE->set_pagetype('mod-quiz-attempt');

// Get the renderer.
$output = $PAGE->get_renderer('mod_quiz');
$messages = $accessmanager->prevent_access();
if (!$attemptobj->is_preview_user() && $messages) {
    print_error('attempterror', 'quiz', $attemptobj->view_url(), $output->access_messages($messages));
}
if ($accessmanager->is_preflight_check_required($attemptobj->get_attemptid())) {
    redirect($attemptobj->start_attempt_url(null, $page));
}

$PAGE->requires->jquery();
$PAGE->requires->js('/mod/quiz/accessrule/wifiresilience/js/localforage.js', true);

// Initialise the JavaScript.
question_engine::initialise_js();

// Dirty hack to play with the quiz timer :-).
$jsmodule = quiz_get_js_module();
if ($CFG->version <= 2021053099 ) {
    $jslinkmodule = 'module.311.js';
} else {
    $jslinkmodule = 'module.js';
}
$jsmodule['fullpath'] = '/mod/quiz/accessrule/wifiresilience/js/'.$jslinkmodule;

$PAGE->requires->js_module($jsmodule);

$autosaveperiod = get_config('quiz', 'autosaveperiod');
if (!$autosaveperiod) {
    // Offline mode only works with autosave, so if it is off for normal quizzes, use a sensible default.
    $autosaveperiod = 30;
}

$userid = '-u' . $USER->id;

$wifisettings = get_config('quizaccess_wifiresilience');

$displaytecherrors = 0;
$displaynavdetails = 0;
$watchlistconfig = 0;
$fetchandlogconfig = 0;

if ($wifisettings) {
    if (!empty($wifisettings->techerrors) && $wifisettings->techerrors != 0) {
        $displaytecherrors = 1;
    }
    if (!empty($wifisettings->navdetails) && $wifisettings->navdetails != 0) {
        $displaynavdetails = 1;
    }
    if (!empty($wifisettings->watchxhr) && trim($wifisettings->navdetails) != '') {
        $watchlistconfig = 1;
    }
    if (!empty($wifisettings->fetchandlog) && trim($wifisettings->fetchandlog) != '') {
        $fetchandlogconfig = 1;
    }
}

$timeleft = $attemptobj->get_time_left_display(time());
if ($timeleft !== false) {
    $ispreview = $attemptobj->is_preview();
    $timerstartvalue = $timeleft;
    if (!$ispreview) {
        // Make sure the timer starts just above zero.
        // If $timeleft was <= 0, then this will just have the effect of causing the quiz to be submitted immediately.
        $timerstartvalue = max($timerstartvalue, 1);
    }
} else {
    $timerstartvalue = 0;
}

$examstoragekeyname = 'Wifiresilience-crs' . $courseid . '-cm' . $cmid . '-id' . $attemptobj->get_quizid() . $userid . '-a' .
     $attemptid;

$emergencysavefilename = $examstoragekeyname . '-d197001010000.eth';

$cleanexamname = addslashes(format_string($attemptobj->get_quiz_name()));

$PAGE->requires->strings_for_js(
                                array('answerchanged', 'savetheresponses', 'submitting', 'submitfailed', 'submitfailedmessage',
                                    'submitfaileddownloadmessage', 'lastsaved', 'lastsavedtotheserver', 'lastsavedtothiscomputer',
                                    'savingdots', 'submitallandfinishtryagain', 'savingtryagaindots', 'savefailed',
                                    'logindialogueheader', 'changesmadereallygoaway', 'loadingstep1', 'loadingstep2', 'loadingstep4',
                                    'loadingstep5', 'loadingstep6', 'loadingstep7', 'loadingstep8', 'loadingstep10', 'currentissue',
                                    'localstorage'), 'quizaccess_wifiresilience');
$PAGE->requires->strings_for_js(array('submitallandfinish', 'confirmclose'), 'quiz');
$PAGE->requires->string_for_js('flagged', 'question');
$PAGE->requires->string_for_js('confirmation', 'admin');

$PAGE->requires->yui_module('moodle-quizaccess_wifiresilience-localforage', 'M.quizaccess_wifiresilience.localforage.init',
                            array($examstoragekeyname));

$PAGE->requires->yui_module('moodle-quizaccess_wifiresilience-autosave', 'M.quizaccess_wifiresilience.autosave.init',
                            array($autosaveperiod, $examstoragekeyname, $courseid, $cmid, $displaytecherrors, $displaynavdetails,
                                $attemptobj->get_uniqueid()));

$PAGE->requires->yui_module('moodle-quizaccess_wifiresilience-navigation', 'M.quizaccess_wifiresilience.navigation.init',
                            array($page));

$PAGE->requires->yui_module('moodle-quizaccess_wifiresilience-download', 'M.quizaccess_wifiresilience.download.init',
                            array($emergencysavefilename, get_config('quizaccess_wifiresilience', 'publickey')));

$PAGE->requires->yui_module('moodle-quizaccess_wifiresilience-isoffline', 'M.quizaccess_wifiresilience.isoffline.init', array());

$PAGE->requires->yui_module('moodle-quizaccess_wifiresilience-initialiseattempt',
                            'M.quizaccess_wifiresilience.initialiseattempt.init',
                            array($cleanexamname, $examstoragekeyname, $page, $fetchandlogconfig, trim($wifisettings->fetchandlog)));

if ($watchlistconfig == 1) {

    $watchlist = preg_replace('#\s+#', ',', trim($wifisettings->watchxhr));

    $PAGE->requires->yui_module('moodle-quizaccess_wifiresilience-watching', 'M.quizaccess_wifiresilience.watching.init',
                                array($watchlist));
}

// Log this page view.
$attemptobj->fire_attempt_viewed_event();

// Arrange for the navigation to be displayed in the first region on the page.
$navbc = $attemptobj->get_navigation_panel($output, navigation_panel_attempt::class, $page);
$regions = $PAGE->blocks->get_regions();

$PAGE->blocks->add_fake_block($navbc, reset($regions));

if ($page >= 0) {
    $headtags = $attemptobj->get_html_head_contributions($page);
}
$PAGE->set_title($attemptobj->attempt_page_title($page));
$PAGE->set_heading($attemptobj->get_course()->fullname);

// A few final things.
if ($attemptobj->is_last_page($page)) {
    $nextpage = -1;
} else {
    $nextpage = $page + 1;
}

if ($page == 0 || !$page) {
    $previouspage = -1;
} else {
    $previouspage = $page - 1;
}

// Display the page.

// From mod_quiz_renderer::attempt_form.
$form = '';

$form .= html_writer::start_tag('form',
                                array('action' => $attemptobj->processattempt_url(), 'method' => 'post',
                                    'enctype' => 'multipart/form-data', 'accept-charset' => 'utf-8', 'id' => 'responseform'));

$form .= html_writer::start_tag('div');

// Print all the questions on every page.
$numpages = $attemptobj->get_num_pages();
for ($i = 0; $i < $numpages; $i++) {
    $form .= html_writer::start_div('xquiz-loading-hide',
                                    array('id' => 'quizaccess_wifiresilience-attempt_page-' . $i, 'data-qslot' => $i + 1));

    foreach ($attemptobj->get_slots($i) as $slot) {
        $form .= $attemptobj->render_question($slot, false, $output, $attemptobj->attempt_url($slot, $page));
    }
    $form .= html_writer::end_div('');
}

$form .= html_writer::start_tag('div',
                                array('class' => 'submitbtns wifi_previous_next_btn'));

$form .= html_writer::empty_tag('input',
                                array('type' => 'submit', 'name' => 'previous', 'value' => get_string('navigateprevious', 'quiz'),
                                    'class' => 'mod_quiz-prev-nav btn btn-secondary', 'id' => 'quizaccess_wifiresilience-prev_btn'));

$form .= html_writer::empty_tag('input',
                                array('type' => 'submit', 'name' => 'next', 'value' => get_string('navigatenext', 'quiz'),
                                    'class' => 'mod_quiz-next-nav btn btn-secondary', 'id' => 'quizaccess_wifiresilience-next_btn'));

$form .= html_writer::end_tag('div');

$endtime = $attemptobj->get_quizobj()->get_access_manager(time())->get_end_time($attemptobj->get_attempt());


if ($endtime === false) {
    $endtime = 0;
}

// Some hidden fields to track what is going on.

$form .= html_writer::empty_tag('input',
                                array('type' => 'hidden', 'name' => 'attempt', 'value' => $attemptobj->get_attemptid()));

$form .= html_writer::empty_tag('input',
                                array('type' => 'hidden', 'name' => 'cmid', 'value' => $cmid));

$form .= html_writer::empty_tag('input',
                                array('type' => 'hidden', 'id' => 'actualstarttimeinput' , 'name' => 'actualstarttimeinput', 'value' => 0));

$form .= html_writer::empty_tag('input',
                                array('type' => 'hidden', 'name' => 'thispage', 'value' => $page, 'id' => 'followingpage'));

$form .= html_writer::empty_tag('input',
                                array('type' => 'hidden', 'name' => 'previouspage', 'value' => $previouspage));

$form .= html_writer::empty_tag('input',
                                array('type' => 'hidden', 'name' => 'nextpage', 'value' => $nextpage));

$form .= html_writer::empty_tag('input',
                                array('type' => 'hidden', 'name' => 'timeup', 'value' => '0', 'id' => 'timeup'));

$form .= html_writer::empty_tag('input',
                                array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));

$form .= html_writer::empty_tag('input',
                                array('type' => 'hidden', 'name' => 'scrollpos', 'value' => '', 'id' => 'scrollpos'));

$form .= html_writer::empty_tag('input',
                                array('type' => 'hidden', 'value' => $USER->id, 'id' => 'quiz-userid'));

$form .= html_writer::empty_tag('input',
                                array('type' => 'hidden', 'value' => $endtime, 'id' => 'original_end_time'));

$form .= html_writer::empty_tag('input',
                                array('type' => 'hidden', 'value' => '1', 'id' => 'quizaccess_wifiresilience_hidden_cxn_status',
                                    'name' => 'quizaccess_wifiresilience_cxn_status'));

$form .= html_writer::empty_tag('input',
                                array('type' => 'hidden', 'value' => '1',
                                    'id' => 'quizaccess_wifiresilience_hidden_livewatch_status',
                                    'name' => 'quizaccess_wifiresilience_livewatch_status'));

// Add a hidden field with questionids. Do this at the end of the form, so
// if you navigate before the form has finished loading, it does not wipe all
// the student's answers.

$form .= html_writer::empty_tag('input',
                                array('type' => 'hidden', 'name' => 'slots', 'value' => implode(',', $attemptobj->get_slots())));

// Summary page. Code from mod_quiz_renderer::summary_page.

$summary = '';
$summary .= html_writer::start_div('', array('id' => 'quizaccess_wifiresilience-attempt_page--1'));
$summary .= $output->heading(format_string($attemptobj->get_quiz_name()));
$summary .= $output->heading(get_string('summaryofattempt', 'quiz'), 3);
$summary .= $output->summary_table($attemptobj, $attemptobj->get_display_options(false));

/**
 * ***** NOW CONTROL OVERRIDE*****
 */

$controls = '';

// Return to place button.

if ($attemptobj->get_state() == quiz_attempt::IN_PROGRESS) {

    $button = '
    <form action="' .
         new moodle_url($attemptobj->attempt_url(null, $attemptobj->get_currentpage())) . '">
    <input type="submit" id="quizaccess_wifiresilience_returntoattempt" value="' . get_string('returnattempt', 'quiz') . '" class="btn btn-secondary">
    <input type="hidden" name="attempt" value="' . $attemptobj->get_attemptid() . '" />
    <input type="hidden" name="cmid" value="' . $attemptobj->get_cmid() . '" />
    <input type="hidden" id="actualstarttimeinput" name="actualstarttimeinput" value="9000"/>
    <input type="hidden" name="sesskey" value="' . sesskey() . '" />
    </form>
    ';

    $controls .= $output->container(
                                    $output->container($button, 'controls', 'wifi_return_to_attempt_div'),
                                    'submitbtns mdl-align wifi_return_to_attempt_btn');
}

// Finish attempt button.

$options = array('attempt' => $attemptobj->get_attemptid(), 'finishattempt' => 1, 'timeup' => 0, 'slots' => '',
    'sesskey' => sesskey());

$button = new single_button(new moodle_url($attemptobj->processattempt_url(), $options), get_string('submitallandfinish', 'quiz'));
$button->set_attribute('id', 'quizaccess_wifiresilience_timer_autosubmit_form');

if ($attemptobj->get_state() == quiz_attempt::IN_PROGRESS) {
    $button->add_action(
                        new confirm_action(get_string('confirmclose', 'quiz'), null, get_string('submitallandfinish', 'quiz')));
}

$duedate = $attemptobj->get_due_date();
$message = '';

if ($attemptobj->get_state() == quiz_attempt::OVERDUE) {
    $message = get_string('overduemustbesubmittedby', 'quiz', userdate($duedate));
} else if ($duedate) {
    $message = get_string('mustbesubmittedby', 'quiz', userdate($duedate));
}

$thebutton = preg_replace('/<input type="submit"\s(.+?)>/is',
                        '<input type="submit" id="wifi_exam_submission_finish" class="btn btn-secondary" value="' .
                             get_string('submitallandfinish', 'quiz') . '">', $output->render($button));
$thebutton = preg_replace('/<button type="submit"\s(.+?)>(.+?)<\/button>/is',
                        '<input type="submit" id="wifi_exam_submission_finish" class="btn btn-secondary" value="$2">', $thebutton);

$controls .= $output->container(
                                $message . $output->container($thebutton, 'controls'),
                                'submitbtns mdl-align wifi_must_be_submitted_btn');
$controls .= $output->container('
                                <div class="container controls wifi_submit_er_div">
                                <h4>'.get_string('emergencyfileoptions', 'quizaccess_wifiresilience').'</h4>
                                 <div class="row">

                                   <div class="col-md" style="display:none">
                                   <div class="card">

  <div class="card-body">
                                     ' . $output->container('<input type="submit" id="wifi_submit_erfile_btn" class="btn btn-secondary" value="SEND_ER_FILE_TXT"><div id="wifi_er_file_info_div"></div>', '').'
                                   </div></div>
                                   </div>
                                   <div class="col-md">
                                   <div class="card">
  <div class="card-body" style="margin-bottom:10px"><a href="#" class="response-download-link btn btn-secondary">'.
          get_string('savetheresponses', 'quizaccess_wifiresilience') . '</a></div>
                                 </div></div>
                                </div> ','submitbtns mdl-align wifi_submit_erfile_area');




/**
 * ***** END CONTROL OVERRIDE*****
 */

$controls = preg_replace('~<div id="quiz-timer".*?</div>~', '', $controls);
$controls = str_replace('<form method="post" action="' . $CFG->wwwroot . '/mod/quiz/processattempt.php"',
                        '<form method="post" action="' . $CFG->wwwroot .
                             '/mod/quiz/processattempt.php" id="quizaccess_wifiresilience_timer_autosubmit_form"', $controls);

$summary .= $controls;
$summary .= html_writer::end_div('');

// Finish the form.
$form .= html_writer::end_tag('div');
$form .= html_writer::end_tag('form');

// From mod_quiz_renderer::attempt_page.
$html = '';
$html .= $output->header();
$html .= $output->quiz_notices($messages);
$html .= $output->countdown_timer($attemptobj, time());
$html .= $form;
$html .= $summary;
$html .= $output->footer();

// Dirty hack to play with the quiz timer :-).

$overlaytags = '
<script>
    wifiresilience_doc_ready_time = new Date().getTime();
</script>
<div id="quizaccess_wifiresilience_overlay">
    <div id="quizaccess_wifiresilience_reload">
        <a href="' . $pageurl . '" title="' . get_string('reload') . '">
            <i class="fa fa-refresh" aria-hidden="true" alt="&#8635;"></i>&nbsp;
        </a>
    </div>
    <div class="quizaccess_wifiresilience_progress">
        <div class="quizaccess_wifiresilience_bar"></div>
    </div>
    <div id="quizaccess_wifiresilience_text">' . get_string('loading') . '...</div>
    <div id="quizaccess_wifiresilience_result"></div>
</div>
<style>
    .fix_wifi_blocks {
        display: inline-block;
    }
</style>';

$old = array('\/mod\/quiz\/module.js', '<body');
$new = array('\/mod\/quiz\/accessrule\/wifiresilience\/js\/'.$jslinkmodule, $overlaytags . '<body');

$html = str_replace($old, $new, $html);

echo $html;
