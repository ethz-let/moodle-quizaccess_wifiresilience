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
 * This script processes ajax sync (saving) requests during the quiz.
 *
 * @package   quizaccess_wifiresilience
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace wificustomsequencecheck;

class question_usage_by_activity extends \question_usage_by_activity {
  /**
   * Check that the sequence number, that detects weird things like the student clicking back, is OK.
   *
   * If the sequence check variable is not present, returns
   * false. If the check variable is present and correct, returns true. If the
   * variable is present and wrong, throws an exception.
   *
   * @param int $slot the number used to identify this question within this usage.
   * @param array|null $postdata (optional) data to use in place of $_POST.
   * @return bool true if the check variable is present and correct. False if it
   * is missing. (Throws an exception if the check fails.)
   */
  public function validate_sequence_number($slot, $postdata = null) {
      print_r('fffff');exit;
          return true;
  }
}
