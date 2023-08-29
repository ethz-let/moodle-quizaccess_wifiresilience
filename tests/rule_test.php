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
 * Unit tests for the quizaccess_wifiresilience plugin.
 *
 * @package   quizaccess_wifiresilience
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/accessrule/wifiresilience/rule.php');


/**
 * Unit tests for the quizaccess_wifiresilience plugin.
 *
 * @copyright  2014 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_wifiresilience_rule_testcase extends basic_testcase {

    /**
     * Test is_compatible_behaviour
     */
    public function test_is_compatible_behaviour() {
        $this->assertTrue(quizaccess_wifiresilience::is_compatible_behaviour('deferredfeedback'));
        $this->assertFalse(quizaccess_wifiresilience::is_compatible_behaviour('interactive'));
    }

    /**
     * Test wifiresilience_rule_creation
     */
    public function test_wifiresilience_rule_creation() {
        $quiz = new stdClass();
        $quiz->preferredbehaviour = 'deferredfeedback';
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $rule = quizaccess_wifiresilience::make($quizobj, 0, false);
        $this->assertNull($rule);

        $quiz->wifiresilience_enabled = true;
        $rule = quizaccess_wifiresilience::make($quizobj, 0, false);
        $this->assertInstanceOf('quizaccess_wifiresilience', $rule);

        $quiz->preferredbehaviour = 'interactive';
        $rule = quizaccess_wifiresilience::make($quizobj, 0, false);
        $this->assertNull($rule);
    }
}
