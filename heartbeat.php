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

$cmid = optional_param('id', 0, PARAM_INT);
$cm = get_coursemodule_from_id('quiz', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$quiz = $DB->get_record('quiz', array('id' => $cm->instance), '*', MUST_EXIST);
$quizurl = new moodle_url('/mod/quiz/view.php', array('id' => $cm->id));
$context = context_module::instance($cm->id);

$PAGE->set_url('/mod/quiz/accessrule/wifiresilience/heartbeat.php', array('id' => $cmid));
require_login($course, false, $cm);
require_capability('quizaccess/wifiresilience:uploadresponses', $context);

// Show the form.
$title = "Heart Beat: ".format_string($quiz->name, true, array('context' => $context));
$PAGE->navbar->add($title);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
?>
<script>
function check_heatbeat(){
  // Set up our HTTP request
  var xhr = new XMLHttpRequest();
  // Setup our listener to process request state changes
  xhr.onreadystatechange = function () {

  	// Only run if the request is complete
  	if (xhr.readyState !== 4) return;

  	// Process our return data
  	if (xhr.status >= 200 && xhr.status < 300) {
  		// This will run when the request is successful
  		// It checks to make sure the status code is in the 200 range.
      document.getElementById('heartbeattable').innerHTML = xhr.responseText;
  	} else {
  		// This will run when it's not
  		console.log('Heart Beat request failed!', xhr.status, xhr.statusText);
  	}

  };

  xhr.open('GET', 'checkhbstatus.php?id='+<?php echo $cmid;?>+'&sesskey=<?php echo sesskey()?>');
  xhr.send();
}
setInterval(check_heatbeat, 10000);

check_heatbeat();
</script>
<div id="heartbeattable"><?php echo get_string('loading');?></div>
<?php
echo $OUTPUT->footer();
