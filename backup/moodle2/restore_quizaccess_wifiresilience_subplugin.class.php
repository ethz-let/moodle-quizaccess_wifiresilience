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
 * Restore code for the quizaccess_wifiresilience plugin.
 *
 * @package   quizaccess_wifiresilience
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/backup/moodle2/restore_mod_quiz_access_subplugin.class.php');

/**
 * Provides the information to restore the fault-tolerant mode quiz access plugin.
 *
 * If this plugin is required, a single
 * <quizaccess_wifiresilience><enabled>1</enabled></quizaccess_wifiresilience> tag
 * will be in the XML, and this needs to be written to the DB. Otherwise, nothing
 * needs to be written to the DB.
 *
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_quizaccess_wifiresilience_subplugin extends restore_mod_quiz_access_subplugin {

    /**
     * Use this method to describe the XML paths that store your sub-plugin's
     * settings for a particular quiz.
     */
    protected function define_quiz_subplugin_structure() {

        $paths = array();

        $elename = $this->get_namefor('');
        $elepath = $this->get_pathfor('/quizaccess_wifiresilience');
        $paths[] = new restore_path_element($elename, $elepath);
        $paths[] = new restore_path_element('emergencyfiles',
                       $this->get_pathfor('/emergencyfiles/emergencyfile'));

        return $paths;
    }

    /**
     * Processes the quizaccess_wifiresilience element, if it is in the file.
     * @param array $data the data read from the XML file.
     */
    public function process_quizaccess_wifiresilience($data) {
        global $DB;

        $data = (object)$data;
        $data->quizid = $this->get_new_parentid('quiz');
        $DB->insert_record('quizaccess_wifiresilience', $data);
    }
    /**
     * Processes the quizaccess_wifiresilience_er element, if it is in the file.
     * @param array $data the data read from the XML file.
     */
    public function process_emergencyfiles($data) {
        global $DB;

        $data = (object)$data;
        $data->quizid = $this->get_new_parentid('quiz');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $DB->insert_record('quizaccess_wifiresilience_er', $data);
    }
    /**
     * Return the contents of this quizaccess_wifiresilience_er to be processed by the links decoder
     */
    public static function define_decode_contents() {
        $fields = array('answer_plain', 'answer_encrypted');
        $contents[] = new restore_decode_content('quizaccess_wifiresilience_er', $fields, 'quizaccess_wifiresilience_er');

        return $contents;
    }
}
