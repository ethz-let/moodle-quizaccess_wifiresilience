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

class quizaccess_wifiresilience_er_table extends table_sql {
  private $cmid;

   function __construct($uniqueid){
     parent::__construct($uniqueid);
     $this->cmid = optional_param('id', 0, PARAM_INT);
   }
   function col_recordcreationtime($row) {
         return userdate($row->recordcreationtime);
   }
   function col_answer_encrypted($row) {
         return '<a href="download_er.php?sesskey='.sesskey().'&userid='.$row->userid.'&t=encrypted&cmid='.$this->cmid.'&id='.$row->mainerfileid.'">'.get_string('download').'</a>';
   }
   function col_answer_plain($row) {
     return '<a href="download_er.php?sesskey='.sesskey().'&userid='.$row->userid.'&t=plain&cmid='.$this->cmid.'&id='.$row->mainerfileid.'">'.get_string('download').'</a>';
   }
 }
