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
require_once("$CFG->dirroot/user/files_form.php");
require_once("$CFG->dirroot/repository/lib.php");
require_once($CFG->dirroot . '/mod/quiz/locallib.php');



$cmid = optional_param('id', 0, PARAM_INT);
$cm = get_coursemodule_from_id('quiz', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$quiz = $DB->get_record('quiz', array('id' => $cm->instance), '*', MUST_EXIST);
$quizurl = new moodle_url('/mod/quiz/view.php', array('id' => $cm->id));
$context = context_module::instance($cm->id);

$PAGE->set_url('/mod/quiz/accessrule/wifiresilience/upload.php', array('id' => $cmid));

$PAGE->requires->js('/mod/quiz/accessrule/wifiresilience/js/jquery.js', true); // For our old moodle theme.

require_login($course, false, $cm);
require_capability('quizaccess/wifiresilience:localresponses', $context);

$title = get_string('privatefiles');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');
$PAGE->set_pagetype('exam-synced-files');
echo $OUTPUT->header();

echo $OUTPUT->box_start();
$out = array();
$fs = get_file_storage();

$files = $fs->get_area_files($context->id, 'quizaccess_wifiresilience', 'synced_exam_files'); //, $cm->id

if(count($files) == 0){
   $OUTPUT->notification("No synced files for " . $quiz->name . " yet");
}
?>
<script>
$(document).ready(function() {
  $('#wifi_synced_responses').DataTable();
});
</script>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
<style>
  table.dataTable{clear:both;margin-top:6px !important;margin-bottom:6px !important;max-width:none !important;border-collapse:separate !important}table.dataTable td,table.dataTable th{-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box}table.dataTable td.dataTables_empty,table.dataTable th.dataTables_empty{text-align:center}table.dataTable.nowrap th,table.dataTable.nowrap td{white-space:nowrap}div.dataTables_wrapper div.dataTables_length label{font-weight:normal;text-align:left;white-space:nowrap}div.dataTables_wrapper div.dataTables_length select{width:75px;display:inline-block}div.dataTables_wrapper div.dataTables_filter{text-align:right}div.dataTables_wrapper div.dataTables_filter label{font-weight:normal;white-space:nowrap;text-align:left}div.dataTables_wrapper div.dataTables_filter input{margin-left:0.5em;display:inline-block;width:auto}div.dataTables_wrapper div.dataTables_info{padding-top:8px;white-space:nowrap}div.dataTables_wrapper div.dataTables_paginate{margin:0;white-space:nowrap;text-align:right}div.dataTables_wrapper div.dataTables_paginate ul.pagination{margin:2px 0;white-space:nowrap}div.dataTables_wrapper div.dataTables_processing{position:absolute;top:50%;left:50%;width:200px;margin-left:-100px;margin-top:-26px;text-align:center;padding:1em 0}table.dataTable thead>tr>th.sorting_asc,table.dataTable thead>tr>th.sorting_desc,table.dataTable thead>tr>th.sorting,table.dataTable thead>tr>td.sorting_asc,table.dataTable thead>tr>td.sorting_desc,table.dataTable thead>tr>td.sorting{padding-right:30px}table.dataTable thead>tr>th:active,table.dataTable thead>tr>td:active{outline:none}table.dataTable thead .sorting,table.dataTable thead .sorting_asc,table.dataTable thead .sorting_desc,table.dataTable thead .sorting_asc_disabled,table.dataTable thead .sorting_desc_disabled{cursor:pointer;position:relative}table.dataTable thead .sorting:after,table.dataTable thead .sorting_asc:after,table.dataTable thead .sorting_desc:after,table.dataTable thead .sorting_asc_disabled:after,table.dataTable thead .sorting_desc_disabled:after{position:absolute;bottom:8px;right:8px;display:block;font-family:'Glyphicons Halflings';opacity:0.5}table.dataTable thead .sorting:after{opacity:0.2;content:"\e150"}table.dataTable thead .sorting_asc:after{content:"\e155"}table.dataTable thead .sorting_desc:after{content:"\e156"}table.dataTable thead .sorting_asc_disabled:after,table.dataTable thead .sorting_desc_disabled:after{color:#eee}div.dataTables_scrollHead table.dataTable{margin-bottom:0 !important}div.dataTables_scrollBody table{border-top:none;margin-top:0 !important;margin-bottom:0 !important}div.dataTables_scrollBody table thead .sorting:after,div.dataTables_scrollBody table thead .sorting_asc:after,div.dataTables_scrollBody table thead .sorting_desc:after{display:none}div.dataTables_scrollBody table tbody tr:first-child th,div.dataTables_scrollBody table tbody tr:first-child td{border-top:none}div.dataTables_scrollFoot table{margin-top:0 !important;border-top:none}@media screen and (max-width: 767px){div.dataTables_wrapper div.dataTables_length,div.dataTables_wrapper div.dataTables_filter,div.dataTables_wrapper div.dataTables_info,div.dataTables_wrapper div.dataTables_paginate{text-align:center}}table.dataTable.table-condensed>thead>tr>th{padding-right:20px}table.dataTable.table-condensed .sorting:after,table.dataTable.table-condensed .sorting_asc:after,table.dataTable.table-condensed .sorting_desc:after{top:6px;right:6px}table.table-bordered.dataTable th,table.table-bordered.dataTable td{border-left-width:0}table.table-bordered.dataTable th:last-child,table.table-bordered.dataTable th:last-child,table.table-bordered.dataTable td:last-child,table.table-bordered.dataTable td:last-child{border-right-width:0}table.table-bordered.dataTable tbody th,table.table-bordered.dataTable tbody td{border-bottom-width:0}div.dataTables_scrollHead table.table-bordered{border-bottom-width:0}div.table-responsive>div.dataTables_wrapper>div.row{margin:0}div.table-responsive>div.dataTables_wrapper>div.row>div[class^="col-"]:first-child{padding-left:0}div.table-responsive>div.dataTables_wrapper>div.row>div[class^="col-"]:last-child{padding-right:0}
</style>
<div class="table-responsive">
<table id="wifi_synced_responses" class="generaltable quizattemptsummary"  cellspacing="0" width="100%">
<thead>
<tr>
<th ><?php echo get_string('user') ?></th>
<th ><?php echo get_string('attempts','quiz') ?></th>
<th ><?php echo get_string('date') ?></th>
<th ><?php echo get_string('filetype','quizaccess_wifiresilience') ?></th>
<th ><?php echo get_string('file') ?></th>
<th ><?php echo get_string('reference','quizaccess_wifiresilience') ?></th>
</tr>
</thead>
<tbody>
<?php

foreach ($files as $file) {

    $filename = $file->get_filename();
    if ($filename === '.') {
      continue;
    }
    $exploded_filename = explode('_',$filename);



    $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $filename);
    $out[] = html_writer::link($url, $filename);

    $userid = 0;
    if(!empty($exploded_filename[4])){
      $userid = trim(str_replace('u','',$exploded_filename[4]));
    }
    $userid = (int)$userid;

  $attemptid = '';
  if(!empty($exploded_filename[5])){
    $attemptid = str_replace('a','',$exploded_filename[5]);
  }


    $attemptid = trim(str_replace('.sync','',$attemptid));
    $attemptid = (int)$attemptid;

    $ftype = '';
    if(!empty($exploded_filename[6])){
      $ftype = trim($exploded_filename[6]);
    }


    $user = $DB->get_record('user', array('id' => $userid));
    $attempt = $DB->get_record('quiz_attempts', array('id' => $attemptid));

    $attemptinfo = "";
    if(!$attempt){
      $attemptinfo = "(Removed/Abandoned)";
      $attempt = new stdClass;
      $attempt->id = $attemptid;
    }
    $userinfo = "";
    if(!$user){
      $user = new stdClass;
      $user->id = 0;
      $userinfo = "(Unknown User)";
    }

    //get_timecreated
?>
    <tr>
      <td><a href="../../../../user/profile.php?id=<?php echo $user->id?>" target="_blank"><?php echo fullname($user)?> (ID: <?php echo $user->id;?>)  <?php echo $userinfo?></a></td>
      <td><a href="../../../../mod/quiz/review.php?attempt=<?php echo $attempt->id?>" target="_blank"><?php echo $attemptid?> <?php echo $attemptinfo?></a></td>
      <td><?php echo userdate($file->get_timecreated());?></td>
      <th ><?php echo $ftype; ?></th>
      <td><?php echo html_writer::link($url, get_string('download'));?></td>
      <td><?php echo $filename;?></td>
    </tr>
<?php

}
//$br = html_writer::empty_tag('br');

//echo implode($br, $out);
echo '</tbody></table></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.12/js/dataTables.bootstrap.min.js"></script>';
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
