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
 * Wifi-Resilient quiz mode, replacement attempt.php page.
 *
 * @package   quizaccess_wifiresilience
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// In case compression is not enabled on serever, maybe try to compress it locally?
@ini_set("zlib.output_compression", "On");
@header('Service-Worker-Allowed: /');
require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

// Get submitted parameters.
$attemptid = required_param('attempt', PARAM_INT);
$page = optional_param('page', null, PARAM_INT);

// Create the attempt object.
$attemptobj = quiz_attempt::create($attemptid);

// Fix the page number if necessary.
if ($page === null) {
    $page = $attemptobj->get_attempt()->currentpage;
}
$is_sequentional_quiz = 0;
if ($attemptobj->get_navigation_method() == QUIZ_NAVMETHOD_SEQ && $page < $attemptobj->get_currentpage()) {
    $page = $attemptobj->get_currentpage();
    $is_sequentional_quiz = 1;
}

// Initialise $PAGE.
$pageurl = $attemptobj->attempt_url(null, $page);
$PAGE->set_url(quizaccess_wifiresilience::ATTEMPT_URL, $pageurl->params());

$course = $attemptobj->get_course();
if(!$course){
    print_error('invalidcourse');
}
$courseid = $course->id;
$cmid = $attemptobj->get_cmid();
if(!$cmid){
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
    print_error('attempterror', 'quiz', $attemptobj->view_url(),
                    $output->access_messages($messages));
}
if ($accessmanager->is_preflight_check_required($attemptobj->get_attemptid())) {
    redirect($attemptobj->start_attempt_url(null, $page));
}
$PAGE->requires->js('/mod/quiz/accessrule/wifiresilience/js/jquery.js', true); // For our old moodle theme.
$PAGE->requires->js('/mod/quiz/accessrule/wifiresilience/js/localforage.js', true);

// Initialise the JavaScript.
question_engine::initialise_js();

// Dirty hack to play with the quiz timer :-)
$js_module = quiz_get_js_module();
$js_module['fullpath'] = '/mod/quiz/accessrule/wifiresilience/js/module.js';

$PAGE->requires->js_module($js_module);

$autosaveperiod = get_config('quiz', 'autosaveperiod');
if (!$autosaveperiod) {
    // Offline mode only works with autosave, so if it is off for normal quizzes,
    // use a sensible default.
    $autosaveperiod = 30;
}

$user_id = '-u' . $USER->id;
/*
 $emergencysavefilename = clean_filename(format_string($attemptobj->get_quiz_name()) .
 $user_id . '-a' . $attemptid . '-d197001010000.eth');
 */

$wifi_settings = $DB->get_record('quizaccess_wifiresilience', array('quizid' => $attemptobj->get_quizid()));

$display_tech_errors = 0;
$display_nav_details = 0;
$watchlist_config = 0;
$fetchandlogconfig = 0;


if($wifi_settings){
    if(!empty($wifi_settings->techerrors) && $wifi_settings->techerrors != 0){
        $display_tech_errors = 1;
    }
    if(!empty($wifi_settings->navdetails) && $wifi_settings->navdetails != 0){
        $display_nav_details = 1;
    }
    if(!empty($wifi_settings->watchxhr) && trim($wifi_settings->navdetails) != ''){
        $watchlist_config = 1;
    }
    if(!empty($wifi_settings->fetchandlog) && trim($wifi_settings->fetchandlog) != ''){
        $fetchandlogconfig = 1;
    }
}

$timeleft = $attemptobj->get_time_left_display(time());
if ($timeleft !== false) {
    $ispreview = $attemptobj->is_preview();
    $timerstartvalue = $timeleft;
    if (!$ispreview) {
        // Make sure the timer starts just above zero. If $timeleft was <= 0, then
        // this will just have the effect of causing the quiz to be submitted immediately.
        $timerstartvalue = max($timerstartvalue, 1);
    }
} else {
    $timerstartvalue = 0;
}

$exam_storage_keyname =  'ETHz-crs' .$courseid . '-cm' . $cmid . '-id' . $attemptobj->get_quizid() . $user_id . '-a' . $attemptid;

$emergencysavefilename = $exam_storage_keyname . '-d197001010000.eth';

$PAGE->requires->yui_module('moodle-quizaccess_wifiresilience-localforage',
                'M.quizaccess_wifiresilience.localforage.init', array($exam_storage_keyname));

$PAGE->requires->yui_module('moodle-quizaccess_wifiresilience-autosave',
                'M.quizaccess_wifiresilience.autosave.init', array($autosaveperiod, $exam_storage_keyname, $courseid, $cmid, $display_tech_errors, $display_nav_details, $attemptobj->get_uniqueid()));

$PAGE->requires->yui_module('moodle-quizaccess_wifiresilience-navigation',
                'M.quizaccess_wifiresilience.navigation.init', array($page));

$PAGE->requires->yui_module('moodle-quizaccess_wifiresilience-download',
                'M.quizaccess_wifiresilience.download.init',
                array($emergencysavefilename, get_config('quizaccess_wifiresilience', 'publickey')));

$PAGE->requires->yui_module('moodle-quizaccess_wifiresilience-isoffline',
                'M.quizaccess_wifiresilience.isoffline.init', array());


if($watchlist_config == 1){

    $watchlist = preg_replace('#\s+#',',',trim($wifi_settings->watchxhr));
    $PAGE->requires->yui_module('moodle-quizaccess_wifiresilience-watching',
                    'M.quizaccess_wifiresilience.watching.init', array($watchlist));
}


$PAGE->requires->strings_for_js(array('answerchanged', 'savetheresponses', 'submitting',
    'submitfailed', 'submitfailedmessage', 'submitfaileddownloadmessage',
    'lastsaved', 'lastsavedtotheserver', 'lastsavedtothiscomputer',
    'savingdots','submitallandfinishtryagain', 'savingtryagaindots', 'savefailed', 'logindialogueheader',
    'changesmadereallygoaway','loadingstep4','loadingstep5','loadingstep6','loadingstep7','loadingstep8', 'currentissue'), 'quizaccess_wifiresilience');
$PAGE->requires->strings_for_js(array('submitallandfinish', 'confirmclose'), 'quiz');
$PAGE->requires->string_for_js('flagged', 'question');
$PAGE->requires->string_for_js('confirmation', 'admin');


// Log this page view.
$params = array(
    'objectid' => $attemptid,
    'relateduserid' => $attemptobj->get_userid(),
    'courseid' => $attemptobj->get_courseid(),
    'context' => context_module::instance($cmid),
    'other' => array(
        'quizid' => $attemptobj->get_quizid()
    )
);
$event = \mod_quiz\event\attempt_viewed::create($params);
$event->add_record_snapshot('quiz_attempts', $attemptobj->get_attempt());
$event->trigger();
/*
// Arrange for the navigation to be displayed in the first region on the page.
$navbc = $attemptobj->get_navigation_panel($output, 'quiz_attempt_nav_panel', -1);
$regions = $PAGE->blocks->get_regions();
$PAGE->blocks->add_fake_block($navbc, reset($regions));

// Initialise $PAGE some more.
$title = get_string('attempt', 'quiz', $attemptobj->get_attempt_number());
$PAGE->set_title($attemptobj->get_quiz_name());
$PAGE->set_heading($course->fullname);
*/

// Arrange for the navigation to be displayed in the first region on the page.
$navbc = $attemptobj->get_navigation_panel($output, 'quiz_attempt_nav_panel', $page);
$regions = $PAGE->blocks->get_regions();
$PAGE->blocks->add_fake_block($navbc, reset($regions));

$headtags = $attemptobj->get_html_head_contributions($page);
$PAGE->set_title($attemptobj->attempt_page_title($page));
$PAGE->set_heading($attemptobj->get_course()->fullname);

// A few final things.
if ($attemptobj->is_last_page($page)) {
    $nextpage = -1;
} else {
    $nextpage = $page + 1;
}

if($page == 0 || !$page){
    $previouspage =  -1;
} else{
    $previouspage =  $page - 1;
}


// Display the page.

// From mod_quiz_renderer::attempt_form.
$form = '';

// Start the form.
$form .= html_writer::start_tag('form',
                array('action' => $attemptobj->processattempt_url(), 'method' => 'post',
                    'enctype' => 'multipart/form-data', 'accept-charset' => 'utf-8',
                    'id' => 'responseform'
                ));
$form .= html_writer::start_tag('div');

// Print all the questions on every page.
$numpages = $attemptobj->get_num_pages();
for ($i = 0; $i < $numpages; $i++) {
    $form .= html_writer::start_div('xquiz-loading-hide',
                    array('id' => 'quizaccess_wifiresilience-attempt_page-' . $i, 'data-qslot' => $i+1));
    foreach ($attemptobj->get_slots($i) as $slot) {
        $form .= $attemptobj->render_question($slot, false, $output,
                        $attemptobj->attempt_url($slot, $page));
    }
    $form .= html_writer::end_div('');
}

$form .= html_writer::start_tag('div', array('class' => 'submitbtns wifi_previous_next_btn'));
$form .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'previous',
    'value' => get_string('navigateprevious','quiz'), 'class' => 'mod_quiz-prev-nav btn btn-secondary', 'id' => 'quizaccess_wifiresilience-prev_btn'));
$form .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'next',
    'value' => get_string('navigatenext','quiz'), 'class' => 'mod_quiz-next-nav btn btn-secondary', 'id' => 'quizaccess_wifiresilience-next_btn'));
$form .= html_writer::end_tag('div');

$accessmanager = $attemptobj->get_quizobj()->get_access_manager(time());
$endtime = $accessmanager->get_end_time($attemptobj);
if($endtime === false) $endtime = 0;

// Some hidden fields to track what is going on.
$form .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'attempt',
    'value' => $attemptobj->get_attemptid()));
$form .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'thispage',
    'value' => $page, 'id' => 'followingpage'));
$form .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'previouspage',
    'value' => $previouspage));
$form .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'nextpage',
    'value' => $nextpage));
$form .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'timeup',
    'value' => '0', 'id' => 'timeup'));
$form .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey',
    'value' => sesskey()));
$form .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'scrollpos',
    'value' => '', 'id' => 'scrollpos'));
$form .= html_writer::empty_tag('input', array('type' => 'hidden',
    'value' => $USER->id, 'id' => 'quiz-userid'));
$form .= html_writer::empty_tag('input', array('type' => 'hidden',
    'value' => $endtime, 'id' => 'original_end_time'));
$form .= html_writer::empty_tag('input', array('type' => 'hidden',
    'value' => '1', 'id' => 'quizaccess_wifiresilience_hidden_cxn_status', 'name' => 'quizaccess_wifiresilience_cxn_status'));

$form .= html_writer::empty_tag('input', array('type' => 'hidden',
    'value' => '1', 'id' => 'quizaccess_wifiresilience_hidden_livewatch_status', 'name' => 'quizaccess_wifiresilience_livewatch_status'));

/*
 $form .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'total_offline_time',
 'value' => 0, , 'id' => 'total_offline_time'));
 */
// Add a hidden field with questionids. Do this at the end of the form, so
// if you navigate before the form has finished loading, it does not wipe all
// the student's answers.
$form .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'slots',
    'value' => implode(',', $attemptobj->get_slots())));

// Summary page. Code from mod_quiz_renderer::summary_page.
$summary = '';
$summary .= html_writer::start_div('', array('id' => 'quizaccess_wifiresilience-attempt_page--1'));
$summary .= $output->heading(format_string($attemptobj->get_quiz_name()));
$summary .= $output->heading(get_string('summaryofattempt', 'quiz'), 3);
$summary .= $output->summary_table($attemptobj, $attemptobj->get_display_options(false));


/*
 $controls = $output->summary_page_controls($attemptobj);
 */
/******* NOW CONTROL OVERRIDE******/

$controls = '';
// Return to place button.
if ($attemptobj->get_state() == quiz_attempt::IN_PROGRESS) {

    $button = '
    <form action="'.new moodle_url($attemptobj->attempt_url(null, $attemptobj->get_currentpage())).'">
    <input type="submit" id="quizaccess_wifiresilience_returntoattempt" value="'.get_string('returnattempt', 'quiz').'" class="btn btn-secondary">
    <input type="hidden" name="attempt" value="'.$attemptobj->get_attemptid().'" />
    <input type="hidden" name="cmid" value="'.$attemptobj->get_cmid().'" />
    <input type="hidden" name="sesskey" value="'.sesskey().'" />
    </form>
    ';
    /*
     $button =  new single_button(
     new moodle_url($attemptobj->attempt_url(null, $attemptobj->get_currentpage())),
     get_string('returnattempt', 'quiz'));
     */
    //$output->render($button)
    $controls .= $output->container($output->container($button, 'controls','wifi_return_to_attempt_div'), 'submitbtns mdl-align wifi_return_to_attempt_btn');
}
// Finish attempt button.



$options = array(
    'attempt' => $attemptobj->get_attemptid(),
    'finishattempt' => 1,
    'timeup' => 0,
    'slots' => '',
    'sesskey' => sesskey(),
);
/*
 $button = '
 <form action="'.new moodle_url($attemptobj->processattempt_url()).'" id="quizaccess_wifiresilience_timer_autosubmit_form">
 <input type="submit" id="quizaccess_wifiresilience_finishattempt" value="'.get_string('submitallandfinish', 'quiz').'" class="btn btn-secondary">
 <input type="hidden" name="attempt" value="'.$attemptobj->get_attemptid().'" />
 <input type="hidden" name="finishattempt" value="1" />
 <input type="hidden" name="timeup" value="0" />
 <input type="hidden" name="slots" value="" />
 <input type="hidden" name="sesskey" value="'.sesskey().'" />
 </form>
 ';
 */

$button = new single_button(
                new moodle_url($attemptobj->processattempt_url(), $options),
                get_string('submitallandfinish', 'quiz'));
$button->id = 'quizaccess_wifiresilience_timer_autosubmit_form'; // responseform

if ($attemptobj->get_state() == quiz_attempt::IN_PROGRESS) {
    $button->add_action(new confirm_action(get_string('confirmclose', 'quiz'), null,
                    get_string('submitallandfinish', 'quiz')));
}
$duedate = $attemptobj->get_due_date();
$message = '';
if ($attemptobj->get_state() == quiz_attempt::OVERDUE) {
    $message = get_string('overduemustbesubmittedby', 'quiz', userdate($duedate));
} else if ($duedate) {
    $message = get_string('mustbesubmittedby', 'quiz', userdate($duedate));
}


$thebutton = preg_replace('/<input type="submit"\s(.+?)>/is', '<input type="submit" id="wifi_exam_submission_finish" class="btn btn-secondary" value="'.get_string('submitallandfinish', 'quiz').'">', $output->render($button));
$thebutton = preg_replace('/<button type="submit"\s(.+?)>(.+?)<\/button>/is', '<input type="submit" id="wifi_exam_submission_finish" class="btn btn-secondary" value="$2">', $thebutton);


//$controls .= $output->countdown_timer($attemptobj, time());
$controls .= $output->container($message . $output->container(
                $thebutton, 'controls'), 'submitbtns mdl-align wifi_must_be_submitted_btn');
//$controls .= $output;
/******* END  CONTROL OVERRIDE******/


$controls = preg_replace('~<div id="quiz-timer".*?</div>~', '', $controls);

$controls = str_replace('<form method="post" action="'.$CFG->wwwroot.'/mod/quiz/processattempt.php"',
                '<form method="post" action="'.$CFG->wwwroot.'/mod/quiz/processattempt.php" id="quizaccess_wifiresilience_timer_autosubmit_form"',
                $controls);

/*
 $needle = '<input type="submit"';
 $replace = '<input type="submit" id="quizaccess_wifiresilience_returntoattempt"';
 $pos = strpos($controls, $needle);
 if ($pos !== false) {
 $controls = substr_replace($controls, $replace, $pos, strlen($needle));
 }

 */



/*
 $controls = str_replace('<form method="post" action="'.$CFG->wwwroot.'/mod/quiz/attempt.php"',
 '<form method="post" action="'.$CFG->wwwroot.'/mod/quiz/attempt.php" id="ATTEMPT_quizaccess_wifiresilience_form"',
 $controls);
 */
//preg_match_all('/<form[^>]+>/i',$controls, $found_forms);
//print_r($found_forms);
//$controls = preg_replace('~<div id="quiz-timer".*?</div>~', '', $controls);
$summary .= $controls;

$summary .= html_writer::end_div('');

// Finish the form.
$form .= html_writer::end_tag('div');
$form .= html_writer::end_tag('form');

// From mod_quiz_renderer::attempt_page.
$html = '';
$overlay_tags = '
<script>
wifiresilience_doc_ready_time = new Date().getTime();
</script>
<div id="quizaccess_wifiresilience_overlay">
   <div id="quizaccess_wifiresilience_reload"><a href="'.$pageurl.'" title="'.get_string('reload').'"><i class="fa fa-refresh" aria-hidden="true" alt="&#8635;"></i>&nbsp;</a></div>
   <div class="quizaccess_wifiresilience_progress">
       <div class="quizaccess_wifiresilience_bar"></div>
   </div>
   <div id="quizaccess_wifiresilience_logo">
       <img src="'.$CFG->wwwroot.'/mod/quiz/accessrule/wifiresilience/images/logo.png" width="200" height="auto"/>
   </div>
    <div id="quizaccess_wifiresilience_text">'.get_string('loading').'...</div>
    <div id="quizaccess_wifiresilience_result"></div>
</div>
';

$overlay_tags  .= "<style>
.fix_wifi_blocks{
  display:inline-block;
}
</style>
";

$old = array('\/mod\/quiz\/module.js','<body');
$new = array('\/mod\/quiz\/accessrule\/wifiresilience\/js\/module.js', $overlay_tags.'<body');
// Dirty hack to play with the quiz timer :-)
$html .= str_replace($old, $new, $output->header());



// Now make the overlay on top of the page so to hide any potential Displays
//$html .= str_replace('<div id="page">','<div id="page">'.,$html);
//$html .= str_replace('<body',$overlay_tags.'<body', $html);

/*
 preg_replace("~<body(.*?)>~is", $html, $matches);
 //echo $matches[0]; exit;
 if(isset($matches[0])){
 $html .= str_replace($matches[0], $matches[0].$overlay_tags, $html);
 }else{
 $html .= $overlay_tags;
 }
 */
$html .= $output->quiz_notices($messages);
$html .= $form;
$html .= $summary;


// Some hidden images and calls.
/*
 // Base 64 needed images first.
 function quizaccess_wifiresilience_get_datauri($image, $mime = "image/png") {
 return 'data: '.$mime.';base64,'.base64_encode(file_get_contents($image));
 }
 */

/*
 $html .= html_writer::start_div('quizaccess_wifiresilience_hidden_statics',
 array('id' => 'id_quizaccess_wifiresilience_hidden_statics'));
 $html .= '<img id="wifiresilience_flagged_image" src="'.$OUTPUT->image_url('i/flagged', 'moodle').'">';
 $html .= '<img id="wifiresilience_unflagged_image"  src="'.$OUTPUT->image_url('i/unflagged', 'moodle').'">';
 $html .= '<img id="wifiresilience_small_image" src="'.$OUTPUT->image_url('i/loading_small', 'moodle').'">';
 $html .= '<img id="wifiresilience_navflagged" src="'.$OUTPUT->image_url('navflagged', 'quiz').'">';
 $html .= '<img id="wifiresilience_folder_24" src="'.$OUTPUT->image_url('f/folder-24', 'core').'">';
 $html .= '<span id="wifiresilience_current_step" style="display:none;"></span>';
 $html .= html_writer::end_div('');
 */


// footer was here...........
$html .= $output->footer();

// New button instead of input submit in new themes need to be reverted to input in order for YUI to work correctly.
//$html = preg_replace_callback('/<button type="submit"\s(.+?)>(.+?)<\/button>/is', '<input type="submit" id="wifi_exam_submission_buttons" class="btn btn-secondary" value="$2">', $html);



$clean_exam_name = addslashes(format_string($attemptobj->get_quiz_name()));
$loadingstep1 = get_string('loadingstep1','quizaccess_wifiresilience', $clean_exam_name);
$html .= '
<script>
quizaccess_wifiresilience_progress = $(".quizaccess_wifiresilience_progress .quizaccess_wifiresilience_bar");
var examviewportmaxwidth = $(window).width();
if(!examviewportmaxwidth || examviewportmaxwidth == 0 || examviewportmaxwidth == \'undefined\'){
    examviewportmaxwidth = 1200;
}

   quizaccess_wifiresilience_progress_step = 1;
   $("#quizaccess_wifiresilience_result").html("'.$loadingstep1.'");
   //var quizaccess_wifiresilience_progress = $(".quizaccess_wifiresilience_progress .quizaccess_wifiresilience_bar");
   quizaccess_wifiresilience_progress.animate({
     width: examviewportmaxwidth * 2 / 10 + "px"
   });

$(window).on(\'pageshow\',function(){

 });
    $(document).ready(function(){
      quizaccess_wifiresilience_progress_step = 2;
      $("#quizaccess_wifiresilience_result").html("'.get_string('loadingstep2','quizaccess_wifiresilience').'");
    //  var quizaccess_wifiresilience_progress = $(".quizaccess_wifiresilience_progress .quizaccess_wifiresilience_bar");
      quizaccess_wifiresilience_progress.animate({
        width: examviewportmaxwidth * 3 / 10 + "px"
      });

    $(window).on("load", function() {


      quizaccess_wifiresilience_progress_step = 10;
      $("#quizaccess_wifiresilience_result").html("'.get_string('loadingstep10','quizaccess_wifiresilience').'");
    //  var quizaccess_wifiresilience_progress = $(".quizaccess_wifiresilience_progress .quizaccess_wifiresilience_bar");
      quizaccess_wifiresilience_progress.animate({
        width: "100%"
      });

	  // What if forced to go to summary page to force sumbission after leaving the quiz and coming back?
      if(typeof(M.quizaccess_wifiresilience.navigation) != "undefined"){


        // Add sequentional note - No navigation numbers should be clickable
        // Y.one("#mod_quiz_navblock_title").append(" <sup> '.get_string('navmethod_seq','mod_quiz').'</sup><br />");
        // Catch also auto submit form #quizaccess_wifiresilience_timer_autosubmit_form made by Timer




    // log in SESSION_STORAGE the original start time, so if refresh happens while offline, we still know
    // How long we got before the end of the exam instead of loading caches original exam length.
    // We will match the original start time with the recorded one in storage. (Match will happen in module.js)
    // Check SessionStorage browser support
    if(typeof(Storage) !== "undefined") {

      var sessionstorage_exam_key = \''.$exam_storage_keyname.'\';
/*
      var sessionstorage_attempt_key = \'ethz-original-secondsleft-'.$attemptid.'\';
      var last_timerstartvalue = sessionStorage.getItem(sessionstorage_attempt_key);

      // For first time, insert exam timerstartvalue.
      if(last_timerstartvalue == null || last_timerstartvalue == "undefined"){
        sessionStorage.setItem(sessionstorage_attempt_key, '.$timerstartvalue.');
      }
*/

      localStorage.setItem(\'current_exam\', sessionstorage_exam_key);
    }




    }
    // To be sure, sure.. save per question! For future, not now.. Enable in ROUND-2
    //  M.quizaccess_wifiresilience.localforage.save_html_per_question();

    if(typeof(M.quizaccess_wifiresilience.navigation) != "undefined"){
      // Only if Timer auto submit enabled.
        if(M.mod_quiz.timer.endtime && M.mod_quiz.timer.endtime != 0 && M.mod_quiz.timer.endtime != "undefined"){
          wifiresilience_window_load_time = (new Date().getTime()) - M.pageloadstarttime.getTime() + 12000; //wifiresilience_doc_ready_time;
          //M.mod_quiz.timer.endtime = M.mod_quiz.timer.endtime + wifiresilience_window_load_time + 12000; // +7 seconds for fadeout?

         }
    }
if(typeof(M.quizaccess_wifiresilience.navigation) != "undefined"){
    setTimeout( function(){
  Y.all(M.quizaccess_wifiresilience.navigation.SELECTORS.ALL_PAGE_DIVS).addClass("quizaccess_wifiresilience_hidden");
  Y.one(M.quizaccess_wifiresilience.navigation.SELECTORS.QUIZ_FORM).removeClass("quizaccess_wifiresilience_hidden");
  Y.one(M.quizaccess_wifiresilience.navigation.SELECTORS.PAGE_DIV_ROOT + '.$page.').removeClass("quizaccess_wifiresilience_hidden");
    $("#quizaccess_wifiresilience_overlay").fadeOut();
}  , 10000 );

} else {
    $("#quizaccess_wifiresilience_overlay").fadeOut();
}


  });

});


</script>';

$flagged =  $OUTPUT->image_url('i/flagged', 'moodle');
$unflagged =  $OUTPUT->image_url('i/unflagged', 'moodle');
$navflagged =  $OUTPUT->image_url('navflagged', 'quiz');
$flagon = $OUTPUT->image_url('mod/quiz/flag-on', 'theme');
$loadingsmall = $OUTPUT->image_url('i/loading_small', 'moodle');
$folder24 = $OUTPUT->image_url('f/folder-24', 'core');
//$sprite = $OUTPUT->image_url('sprite', 'theme');
//theme=ethz&component=theme&image=sprite

$examstaticflags = "
quizaccess_wifiresilience_flagging('$flagged');
quizaccess_wifiresilience_flagging('$unflagged');
quizaccess_wifiresilience_flagging('$navflagged');
quizaccess_wifiresilience_flagging('$flagon');
quizaccess_wifiresilience_flagging('$loadingsmall');
quizaccess_wifiresilience_flagging('$folder24');
";

$html .= '<script>

  // Flags and pre-cache specific static exam images (Flag, unflag, laoding etc).
  const staticExamFlagging = \'ETHz-SW-flags\';
  const quizaccess_wifiresilience_flagging = async (url) => {
    try {
      const response = await fetch(url, {mode: "cors", credentials: "include"  });
      if (response.ok) {
          console.log("[ETHz-SW] ServiceWorker: Exam Static Flags SUCCESSFUL: " + url);
      }
    } catch(error) {
      console.log("[ETHz-SW] ServiceWorker: Exam Static Flags ERROR: " + url, error);
      return;
    }
  };

  '.$examstaticflags.'
</script>';



if($fetchandlogconfig){

    $fetchandlog = '';
    $link_pieces = preg_split("/\\r\\n|\\r|\\n/", $wifi_settings->fetchandlog);
    if(count($link_pieces) != 0){
        foreach($link_pieces as $tobefetched){
            $tobefetched_cleaned = str_replace('pluginfile.php', 'mod/quiz/accessrule/wifiresilience/examfile.php', $tobefetched);
            $html = str_replace($tobefetched, $tobefetched_cleaned, $html);
            $fetchandlog .= 'quizaccess_wifiresilience_fetch_and_log("'.$tobefetched_cleaned.'"); ';
        }

    }

    /*
     $html .= html_writer::start_div('quizaccess_wifiresilience_hidden_statics',
     array('id' => 'id_quizaccess_wifiresilience_hidden_statics'));
     $html .= '<img id="wifiresilience_flagged_image" src="'.$OUTPUT->image_url('i/flagged', 'moodle').'">';
     $html .= '<img id="wifiresilience_unflagged_image"  src="'.$OUTPUT->image_url('i/unflagged', 'moodle').'">';
     $html .= '<img id="wifiresilience_small_image" src="'.$OUTPUT->image_url('i/loading_small', 'moodle').'">';
     $html .= '<img id="wifiresilience_navflagged" src="'.$OUTPUT->image_url('navflagged', 'quiz').'">';
     $html .= '<img id="wifiresilience_folder_24" src="'.$OUTPUT->image_url('f/folder-24', 'core').'">';
     $html .= '<span id="wifiresilience_current_step" style="display:none;"></span>';
     $html .= html_writer::end_div('');
     */

    if($fetchandlog){

        $html .= '<script>
    // Prefetch Script
    const pluginCacheNamePre = \'ETHz-SW-runtime-routes\';
    const quizaccess_wifiresilience_fetch_and_log = async (url) => {
      try {

        const response = await fetch(url, {mode: "cors", credentials: "include"  });
        if (response.ok) {

          console.log("[ETHz-SW] ServiceWorker: Prefetch Attachments/Embedded Files (as per fetchandlog setting in quiz) is SUCCESSFUL: " + url);

        }else {
          console.log("[ETHz-SW] ServiceWorker: Prefetch Attachments/Embedded Files (as per fetchandlog setting in quiz) has FAILED: " + url);
        }
      } catch(error) {
        console.log("[ETHz-SW] ServiceWorker: Prefetch Attachments/Embedded Files (as per fetchandlog setting in quiz) ERROR: " + url, error);
        return;
      }
    };
    $(window).on("load", function() {
    '.$fetchandlog.'
    });
    </script>';
    }

}

echo $html;
