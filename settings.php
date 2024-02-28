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
 * Configuration settings for the quizaccess_wifiresilience plugin.
 *
 * @package   quizaccess_wifiresilience
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configcheckbox('quizaccess_wifiresilience/defaultenabled',
            get_string('wifiresilienceenabled', 'quizaccess_wifiresilience'),
            get_string('wifiresilienceenabled_desc', 'quizaccess_wifiresilience'),
            0));

    $settings->add(new admin_setting_configtextarea('quizaccess_wifiresilience/privatekey',
            get_string('privatekey', 'quizaccess_wifiresilience'),
            get_string('privatekey_desc', 'quizaccess_wifiresilience'), '', PARAM_RAW, 60, 14));

    $settings->add(new admin_setting_configtextarea('quizaccess_wifiresilience/publickey',
            get_string('publickey', 'quizaccess_wifiresilience'),
            get_string('publickey_desc', 'quizaccess_wifiresilience'), '', PARAM_RAW, 60, 6));

    $settings->add(new admin_setting_configcheckbox('quizaccess_wifiresilience/prechecks',
            get_string('prechecks', 'quizaccess_wifiresilience'),
            get_string('prechecks_help', 'quizaccess_wifiresilience'), 1));

    $settings->add(new admin_setting_configcheckbox('quizaccess_wifiresilience/techerrors',
            get_string('techerrors', 'quizaccess_wifiresilience'),
            get_string('techerrors_help', 'quizaccess_wifiresilience'), 1));

    $settings->add(new admin_setting_configcheckbox('quizaccess_wifiresilience/navdetails',
            get_string('navdetails', 'quizaccess_wifiresilience'),
            get_string('navdetails_help', 'quizaccess_wifiresilience'), 1));

    $settings->add(new admin_setting_configtextarea('quizaccess_wifiresilience/watchxhr',
            get_string('watchxhr', 'quizaccess_wifiresilience'),
            get_string('watchxhr_help', 'quizaccess_wifiresilience'), '', PARAM_RAW, 60, 25));

    $settings->add(new admin_setting_configtextarea('quizaccess_wifiresilience/fetchandlog',
            get_string('fetchandlog', 'quizaccess_wifiresilience'),
            get_string('fetchandlog_help', 'quizaccess_wifiresilience'), '', PARAM_RAW, 60, 5));

    $settings->add(new admin_setting_configtextarea('quizaccess_wifiresilience/precachefiles',
            get_string('precachefiles', 'quizaccess_wifiresilience'),
            get_string('precachefiles_help', 'quizaccess_wifiresilience'), '', PARAM_RAW, 60, 5));

    $settings->add(new admin_setting_configtextarea('quizaccess_wifiresilience/excludelist',
            get_string('excludelist', 'quizaccess_wifiresilience'),
            get_string('excludelist_help', 'quizaccess_wifiresilience'), '', PARAM_RAW, 60, 5));

    $settings->add(new admin_setting_configtextarea('quizaccess_wifiresilience/extraroutes',
            get_string('extraroutes', 'quizaccess_wifiresilience'),
            get_string('extraroutes_help', 'quizaccess_wifiresilience'), '', PARAM_RAW, 60, 30));
}
