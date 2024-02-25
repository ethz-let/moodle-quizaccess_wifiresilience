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
 * Backup code for the quizaccess_wifiresilience plugin.
 *
 * @package   quizaccess_wifiresilience
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/backup/moodle2/backup_mod_quiz_access_subplugin.class.php');

/**
 * Provides the information to backup the fault-tolerant mode quiz access plugin.
 *
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_quizaccess_wifiresilience_subplugin extends backup_mod_quiz_access_subplugin {

    /**
     * Use this method to describe the XML structure required to store your
     * sub-plugin's settings for a particular quiz, and how that data is stored
     * in the database.
     */
    protected function define_quiz_subplugin_structure() {

        // Create XML elements.
        $subplugin = $this->get_subplugin_element();

        $subpluginwrapper = new backup_nested_element($this->get_recommended_name());
        $subplugintablesettings = new backup_nested_element('quizaccess_wifiresilience', null,
                array('enabled', 'prechecks', 'techerrors', 'navdetails'));

        $emergencyfiles = new backup_nested_element('emergencyfiles');

        $emergencyfile = new backup_nested_element('emergencyfile', array('id'),
                                                    array('quizid', 'userid', 'attempt', 'answer_plain', 'answer_encrypted',
                                                        'timecreated'));

        // Connect XML elements into the tree.
        $subplugin->add_child($subpluginwrapper);
        $subpluginwrapper->add_child($subplugintablesettings);
        $subpluginwrapper->add_child($emergencyfiles);
        $emergencyfiles->add_child($emergencyfile);

        // Set source to populate the data.
        $subplugintablesettings->set_source_table('quizaccess_wifiresilience',
                array('quizid' => backup::VAR_ACTIVITYID));
        // Emergency files, per quizid.
        $emergencyfile->set_source_table('quizaccess_wifiresilience_er',
                array('quizid' => backup::VAR_PARENTID));
        $emergencyfile->annotate_ids('user', 'userid');

        return $subplugin;
    }
}
