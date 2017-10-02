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

echo $OUTPUT->header();
//echo $OUTPUT->heading($title);

$start_with_key = 'ETHz-crs'.$course->id.'-'.'cm'.$cm->id.'-id'.$quiz->id;

echo "<script>";

echo "
responses_store = localforage.createInstance({
    name: 'ETHz-exams-responses'
});
status_store = localforage.createInstance({
    name: 'ETHz-exams-question-status'
});

";

echo "</script>";
?>
<div class="alert alert-info">Results are ONLY for KEY: <?php echo $start_with_key?> (Exam: <strong><?php echo $quiz->name;?></strong>)</div>
<div class="responsive-table">
<h3>indexedDB / WebSQL (Encrypted Attempts)</h3>
<table id="quizaccess_wifiresilience-indexeddb-table" class="table table-striped">
  <thead>
  <tr>
    <th>Record</th>
    <th>Last Saved on Server</th>
    <th>Last Changed Locally</th>
    <th>Download</th>
    <th>Delete</th>
  </tr>
</thead>
</table>
<br />
<h3>indexedDB / WebSQL (Attempts Status)</h3>
<table id="quizaccess_wifiresilience-localstorage-table" class="table table-striped">
  <thead>
  <tr>
    <th>Record</th>
    <th>Last Saved on Server</th>
    <th>Last Changed Locally</th>
    <th>Download</th>
    <th>Delete</th>
  </tr>
</thead>
</table>
</div>
<script>
function delete_indb_record(key){
  var confirmdelete = confirm("Are you sure you want to delete Response local record: " + key + "?");
  if (confirmdelete) {
    responses_store.removeItem(key);
    document.getElementById('indb_row_' + key).style.display = 'none';
  }
}
function delete_localstorage_record(key){
  var confirmdelete = confirm("Are you sure you want to delete Status record: " + key + "?");
  if (confirmdelete) {
    status_store.removeItem(key);
    document.getElementById('localstorage_row_' + key).style.display = 'none';
  }
}
function quizaccess_wifiresilience_create_rows(tableid,rownumber,data){
  // Find a <table> element with id="myTable":
  var table = document.getElementById(tableid);
  // Create an empty <tr> element and add it to the 1st position of the table:
  var row = table.insertRow(rownumber);
  row.id = 'indb_row_' + data['key'];
  // Insert new cells (<td> elements) at the 1st and 2nd position of the "new" <tr> element:

  var cell1 = row.insertCell(0);
  cell1.innerHTML = data['key'];

  var cell2 = row.insertCell(1);
  cell2.innerHTML = data['key'];

  var cell3 = row.insertCell(2);
  cell3.innerHTML = data['key'];

  var cell4 = row.insertCell(3);
  cell4.innerHTML = '<a href="#" id="download_indb_ls_'+rownumber+'">Download</a>';
  var dlink_element = document.getElementById("download_indb_ls_"+rownumber);

  var blob = new Blob([data['responses']], {type: "octet/stream"});
  var url = window.URL.createObjectURL(blob);

  dlink_element.setAttribute('href', url);
  dlink_element.setAttribute('download', data['key'] + '.eth');

  var cell5 = row.insertCell(4);
  cell5.innerHTML = '<a href="#" id="delete_indb_ls_'+rownumber+'" onclick="delete_indb_record(' + "'" + data['key'] + "'" + ')">Delete</a>';


}

function quizaccess_wifiresilience_localstorage_create_rows(tableid,rownumber,data){
  // Find a <table> element with id="myTable":
  var table = document.getElementById(tableid);
  // Create an empty <tr> element and add it to the 1st position of the table:
  var row = table.insertRow(rownumber);
  row.id = 'localstorage_row_' + data['key'];
  // Insert new cells (<td> elements) at the 1st and 2nd position of the "new" <tr> element:

  var cell1 = row.insertCell(0);
  cell1.innerHTML = data['key'];

  var cell2 = row.insertCell(1);
  cell2.innerHTML = data['key'];

  var cell3 = row.insertCell(2);
  cell3.innerHTML = data['key'];

  var cell4 = row.insertCell(3);
  cell4.innerHTML = '<a href="#" id="download_localstorage_ls_'+rownumber+'">Download</a>';
  var dlink_element = document.getElementById("download_localstorage_ls_"+rownumber);
  var blob = new Blob([data['responses']], {type: "octet/stream"});
  var url = window.URL.createObjectURL(blob);
  dlink_element.setAttribute('href', url);
  dlink_element.setAttribute('download', data['key'] + '.eth');

  var cell5 = row.insertCell(4);
  cell5.innerHTML = '<a href="#" id="delete_localstorage_ls_'+rownumber+'" onclick="delete_localstorage_record(' + "'" + data['key'] + "'" + ')">Delete</a>';


}

responses_store.startsWith('<?php echo $start_with_key;?>').then(function(results) {
  //console.error(results);
  var localforagedata = {};
  var row = 0;
  var foundx = 0;
   for (var ldbindex in results) {
     foundx = 1;
 //  console.error(a, ' = ', results[a]);
   localforagedata = {key: ldbindex, responses: results[ldbindex]};
   row ++;
   quizaccess_wifiresilience_create_rows("quizaccess_wifiresilience-indexeddb-table", row, localforagedata);
  }
  if(foundx == 0){
    var table = document.getElementById('quizaccess_wifiresilience-indexeddb-table');
    var row = table.insertRow(1);
    var cell = row.insertCell(0);
    cell.innerHTML= "No Local Records Found";
    cell.colSpan = 5
  }
});

status_store.startsWith('<?php echo $start_with_key;?>').then(function(results) {
  //console.error(results);
  var localforagedata = {};
  var row = 0;
  var found = 0;
   for (var ldbindex in results) {
      found = 1;
     //  console.error(a, ' = ', results[a]);
    localforagedata = {key: ldbindex, responses: results[ldbindex]};
    row ++;
    quizaccess_wifiresilience_localstorage_create_rows("quizaccess_wifiresilience-localstorage-table", row, localforagedata);
  }
  if(found == 0){
    var table = document.getElementById('quizaccess_wifiresilience-localstorage-table');
    var row = table.insertRow(1);
    var cell = row.insertCell(0);
    cell.innerHTML= "No Local Records Found";
    cell.colSpan = 5
  }
});


</script>

<?php

echo $OUTPUT->footer();
