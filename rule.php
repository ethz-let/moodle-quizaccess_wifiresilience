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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Implementaton of the quizaccess_wifiresilience plugin.
 *
 * @package quizaccess_wifiresilience
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once ($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');

/**
 * The access rule class implementation for the quizaccess_wifiresilience plugin.
 * A rule that hijacks the standard attempt.php page, and replaces it with
 * different script which loads all the questions at once and then allows the
 * student to keep working, even if the network connection is lost. However,
 * if the network is working, responses are saved back to the server.
 *
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_wifiresilience extends quiz_access_rule_base {

    /** @var string the URL path to our replacement attempt script. */
    const ATTEMPT_URL = '/mod/quiz/accessrule/wifiresilience/attempt.php';

    /**
     * Declare make function
     *
     * @param quiz $quizobj
     *        An instance of the class quiz from attemptlib.php.
     *        The quiz we will be controlling access to.
     * @param int $timenow
     *        $timenow The time to use as 'now'.
     * @param bool $canignoretimelimits
     *        Whether this user is exempt from time
     *        limits (has_capability('mod/quiz:ignoretimelimits', ...)).
     * @return object quiz_access_rule_base
     */
    public static function make(quiz $quizobj, $timenow, $canignoretimelimits) {
        if (empty($quizobj->get_quiz()->wifiresilience_enabled) ||
             !self::is_compatible_behaviour($quizobj->get_quiz()->preferredbehaviour)) {
            return null;
        }
        
        return new self($quizobj, $timenow);
    }

    /**
     * Add settings form
     *
     * @param mod_quiz_mod_form $quizform
     * @param MoodleQuickForm $mform
     */
    public static function add_settings_form_fields(mod_quiz_mod_form $quizform, MoodleQuickForm $mform) {
        $quizid = $quizform->get_current()->id;

        $config = get_config('quizaccess_wifiresilience');

        $mform->addElement('header', 'wifiresilienceenabled', get_string('wifiresilienceenabled', 'quizaccess_wifiresilience'));

        $mform->addElement('selectyesno', 'wifiresilience_enabled',
                        get_string('wifiresilienceenabled', 'quizaccess_wifiresilience'));

        $mform->addHelpButton('wifiresilience_enabled', 'wifiresilienceenabled', 'quizaccess_wifiresilience');

        $mform->setDefault('wifiresilience_enabled', !empty($config->defaultenabled));

        $mform->addElement('html', '<div class="wifiresilience_hidden_form_elements">');

        $mform->addElement('checkbox', 'wifiresilience_prechecks', get_string('prechecks', 'quizaccess_wifiresilience'));

        $mform->addHelpButton('wifiresilience_prechecks', 'prechecks', 'quizaccess_wifiresilience');

        $mform->setDefault('wifiresilience_prechecks', !empty($config->prechecks));

        $mform->addElement('checkbox', 'wifiresilience_techerrors', get_string('techerrors', 'quizaccess_wifiresilience'));

        $mform->addHelpButton('wifiresilience_techerrors', 'techerrors', 'quizaccess_wifiresilience');

        $mform->setDefault('wifiresilience_techerrors', !empty($config->techerrors));

        $mform->addElement('checkbox', 'wifiresilience_navdetails', get_string('navdetails', 'quizaccess_wifiresilience'));

        $mform->addHelpButton('wifiresilience_navdetails', 'navdetails', 'quizaccess_wifiresilience');

        $mform->setDefault('wifiresilience_navdetails', !empty($config->navdetails));

        $mform->addElement('hidden', 'wifiresilience_wifitoken');

        $mform->setType('wifiresilience_wifitoken', PARAM_RAW);
        $mform->setDefault('wifiresilience_wifitoken', $config->wifitoken);

        $mform->addElement('textarea', 'wifiresilience_watchxhr', get_string('watchxhr', 'quizaccess_wifiresilience'),
                        'cols="60" rows="25"');

        $mform->addHelpButton('wifiresilience_watchxhr', 'watchxhr', 'quizaccess_wifiresilience');

        $mform->setDefault('wifiresilience_watchxhr', $config->watchxhr);

        $mform->addElement('textarea', 'wifiresilience_fetchandlog', get_string('fetchandlog', 'quizaccess_wifiresilience'),
                        'cols="60" rows="5"');

        $mform->addHelpButton('wifiresilience_fetchandlog', 'fetchandlog', 'quizaccess_wifiresilience');

        $mform->setDefault('wifiresilience_fetchandlog', $config->fetchandlog);

        $mform->addElement('textarea', 'wifiresilience_precachefiles', get_string('precachefiles', 'quizaccess_wifiresilience'),
                        'cols="60" rows="5"');

        $mform->addHelpButton('wifiresilience_precachefiles', 'precachefiles', 'quizaccess_wifiresilience');

        $mform->setDefault('wifiresilience_precachefiles', $config->precachefiles);

        $mform->addElement('textarea', 'wifiresilience_excludelist', get_string('excludelist', 'quizaccess_wifiresilience'),
                        'cols="60" rows="5"');

        $mform->addHelpButton('wifiresilience_excludelist', 'excludelist', 'quizaccess_wifiresilience');

        $mform->setDefault('wifiresilience_excludelist', $config->excludelist);

        $mform->addElement('textarea', 'wifiresilience_extraroutes', get_string('extraroutes', 'quizaccess_wifiresilience'),
                        'cols="60" rows="25"');

        $mform->addHelpButton('wifiresilience_extraroutes', 'extraroutes', 'quizaccess_wifiresilience');

        $mform->setDefault('wifiresilience_extraroutes', $config->extraroutes);

        $mform->disabledIf('wifiresilience_prechecks', 'wifiresilience_enabled', 'eq', 0);
        $mform->disabledIf('wifiresilience_techerrors', 'wifiresilience_enabled', 'eq', 0);
        $mform->disabledIf('wifiresilience_navdetails', 'wifiresilience_enabled', 'eq', 0);
        $mform->disabledIf('wifiresilience_watchxhr', 'wifiresilience_enabled', 'eq', 0);
        $mform->disabledIf('wifiresilience_fetchandlog', 'wifiresilience_enabled', 'eq', 0);
        $mform->disabledIf('wifiresilience_extraroutes', 'wifiresilience_enabled', 'eq', 0);
        $mform->disabledIf('wifiresilience_precachefiles', 'wifiresilience_enabled', 'eq', 0);
        $mform->disabledIf('wifiresilience_excludelist', 'wifiresilience_enabled', 'eq', 0);

        foreach (question_engine::get_behaviour_options(null) as $behaviour => $notused) {
            if (!self::is_compatible_behaviour($behaviour)) {
                $mform->disabledIf('wifiresilience_enabled', 'preferredbehaviour', 'eq', $behaviour);
                $mform->disabledIf('wifiresilience_prechecks', 'preferredbehaviour', 'eq', $behaviour);
                $mform->disabledIf('wifiresilience_techerrors', 'preferredbehaviour', 'eq', $behaviour);
                $mform->disabledIf('wifiresilience_navdetails', 'preferredbehaviour', 'eq', $behaviour);
                $mform->disabledIf('wifiresilience_watchxhr', 'preferredbehaviour', 'eq', $behaviour);
                $mform->disabledIf('wifiresilience_fetchandlog', 'preferredbehaviour', 'eq', $behaviour);
                $mform->disabledIf('wifiresilience_extraroutes', 'preferredbehaviour', 'eq', $behaviour);
                $mform->disabledIf('wifiresilience_precachefiles', 'preferredbehaviour', 'eq', $behaviour);
                $mform->disabledIf('wifiresilience_excludelist', 'preferredbehaviour', 'eq', $behaviour);
            }
        }
        $mform->addElement('html', '</div>');
    }

    /**
     * Given the quiz "How questions behave" setting, can the fault-tolerant mode work
     * with that behaviour?
     *
     * @param string $behaviour
     *        the internal name (e.g. 'interactive') of an archetypal behaviour.
     * @return boolean whether fault-tolerant mode can be used.
     */
    public static function is_compatible_behaviour($behaviour) {
        $unusedoptions = question_engine::get_behaviour_unused_display_options($behaviour);
        // Sorry, double negative here. The heuristic is that:
        // The behaviour is compatible if we don't need to show specific feedback during the attempt.
        return in_array('specificfeedback', $unusedoptions);
    }

    /**
     * Save settings
     *
     * @param object $quiz
     *        the data from the quiz form, including $quiz->id
     *        which is the id of the quiz being saved
     */
    public static function save_settings($quiz) {
        global $DB;

        if (empty($quiz->wifiresilience_enabled)) {
            $DB->delete_records('quizaccess_wifiresilience', array('quizid' => $quiz->id));
        } else {
            if (empty($quiz->wifiresilience_prechecks) || !$quiz->wifiresilience_prechecks) {
                $quiz->wifiresilience_prechecks = 0;
            }
            if (empty($quiz->wifiresilience_techerrors) || !$quiz->wifiresilience_techerrors) {
                $quiz->wifiresilience_techerrors = 0;
            }
            if (empty($quiz->wifiresilience_navdetails) || !$quiz->wifiresilience_navdetails) {
                $quiz->wifiresilience_navdetails = 0;
            }
            if (empty($quiz->wifiresilience_wifitoken) || !$quiz->wifiresilience_wifitoken) {
                $quiz->wifiresilience_wifitoken = '';
            }
            if (empty($quiz->wifiresilience_watchxhr) || !$quiz->wifiresilience_watchxhr) {
                $quiz->wifiresilience_watchxhr = '';
            }
            if (empty($quiz->wifiresilience_fetchandlog) || !$quiz->wifiresilience_fetchandlog) {
                $quiz->wifiresilience_fetchandlog = '';
            }
            if (empty($quiz->wifiresilience_extraroutes) || !$quiz->wifiresilience_extraroutes) {
                $quiz->wifiresilience_extraroutes = '';
            }
            if (empty($quiz->wifiresilience_precachefiles) || !$quiz->wifiresilience_precachefiles) {
                $quiz->wifiresilience_precachefiles = '';
            }
            if (empty($quiz->wifiresilience_excludelist) || !$quiz->wifiresilience_excludelist) {
                $quiz->wifiresilience_excludelist = '';
            }
            if ($DB->record_exists('quizaccess_wifiresilience', array('quizid' => $quiz->id))) {
                $DB->delete_records('quizaccess_wifiresilience', array('quizid' => $quiz->id));
            }

            $record = new stdClass();
            $record->quizid = $quiz->id;
            $record->enabled = 1;
            $record->prechecks = $quiz->wifiresilience_prechecks;
            $record->techerrors = $quiz->wifiresilience_techerrors;
            $record->navdetails = $quiz->wifiresilience_navdetails;
            $record->wifitoken = $quiz->wifiresilience_wifitoken;
            $record->watchxhr = $quiz->wifiresilience_watchxhr;
            $record->fetchandlog = $quiz->wifiresilience_fetchandlog;
            $record->extraroutes = $quiz->wifiresilience_extraroutes;
            $record->precachefiles = $quiz->wifiresilience_precachefiles;
            $record->excludelist = $quiz->wifiresilience_excludelist;

            $DB->insert_record('quizaccess_wifiresilience', $record);
        }
    }

    /**
     * Delete settings
     *
     * @param object $quiz
     *        the data from the database, including $quiz->id
     *        which is the id of the quiz being deleted.
     */
    public static function delete_settings($quiz) {
        global $DB;
        $DB->delete_records('quizaccess_wifiresilience', array('quizid' => $quiz->id));
    }

    /**
     * Get settings
     *
     * @param int $quizid
     *        the id of the quiz we are loading settings for.
     */
    public static function get_settings_sql($quizid) {
        return array('COALESCE(wifiresilience.enabled, 0) AS wifiresilience_enabled',
            'LEFT JOIN {quizaccess_wifiresilience} wifiresilience ON wifiresilience.quizid = quiz.id', array());
    }

    /**
     * Generate and display description
     */
    public function description() {
        global $CFG, $DB, $USER, $PAGE;

        $displayadminmsgs = 0;

        if ($this->quizobj->has_capability('quizaccess/wifiresilience:adminmessages')) {
            $displayadminmsgs = 1;
        }

        $uploadresponsesrole = 0;
        if ($this->quizobj->has_capability('quizaccess/wifiresilience:uploadresponses')) {
            $uploadresponsesrole = 1;
        }

        $inspectresponsesrole = 0;
        if ($this->quizobj->has_capability('quizaccess/wifiresilience:inspectresponses')) {
            $inspectresponsesrole = 1;
        }

        $localresponsesrole = 0;
        if ($this->quizobj->has_capability('quizaccess/wifiresilience:localresponses')) {
            $localresponsesrole = 1;
        }

        $browserchecksrole = 0;
        if ($this->quizobj->has_capability('quizaccess/wifiresilience:browserchecks')) {
            $browserchecksrole = 1;
        }

        $viewtechchecksrole = 0;
        if ($this->quizobj->has_capability('quizaccess/wifiresilience:viewtechchecks')) {
            $viewtechchecksrole = 1;
        }

        $quizid = $this->quizobj->get_quizid();
        if (!$quizid) {
            print_error('invalidcourse');
        }

        $quizcmid = $this->quizobj->get_cmid();
        if (!$quizcmid) {
            print_error('invalidcoursemodule');
        }

        $serviceworkerparams = '?cmid=' . $quizcmid . '&quizid=' . $quizid . '&rev=' . rand();

        $showtechprechecks = (!empty($wifisettings->prechecks) && $wifisettings->prechecks != 0) || $viewtechchecksrole == 1 ||
             $displayadminmsgs == 1;

        $PAGE->requires->jquery();
        $PAGE->requires->js('/mod/quiz/accessrule/wifiresilience/js/localforage.js', true);
        $PAGE->requires->js('/mod/quiz/accessrule/wifiresilience/js/startswith.js', true);

        $PAGE->requires->strings_for_js(
                                        array('rule1start', 'rule1success', 'rule1fail', 'rule1error', 'rule1statusactive',
                                            'rule1statusinstalling', 'rule1statuswaiting', 'rule2start', 'rule2success',
                                            'rule2error', 'rule3start', 'rule3success', 'rule3error', 'rule4start', 'rule4success',
                                            'rule4fail', 'rule4error', 'rule5start', 'rule5success', 'rule5fail', 'rule5error',
                                            'rule6start', 'rule6success', 'rule6error', 'rule7start', 'rule7success', 'rule7error',
                                            'rulebgsyncsuccess', 'rulebgsyncfail', 'rulebgsyncsupported', 'ruleswnotregisteredreset',
                                            'ruleswnotregisteredstop', 'ruleswnotregisteredupdate'), 'quizaccess_wifiresilience');

        $PAGE->requires->yui_module('moodle-quizaccess_wifiresilience-initialiserule',
                                    'M.quizaccess_wifiresilience.initialiserule.init',
                                    array($serviceworkerparams, $displayadminmsgs, $showtechprechecks));

        $wifisettings = get_config('quizaccess_wifiresilience');

        $return = '';


        if ($displayadminmsgs == 1 || $uploadresponsesrole == 1 || $inspectresponsesrole == 1 || $browserchecksrole == 1 ||
             $localresponsesrole == 1) {
            $return .= '<div class="alert alert-info">' . get_string('description', 'quizaccess_wifiresilience') . '</div>' .
             '<div class="alert alert-warning" style="text-align:left">' .
             get_string('uploadresponsesadmin', 'quizaccess_wifiresilience');
             $return .= '<ul>';
        }

        if ($uploadresponsesrole == 1) {
            $return .= '<li>' . html_writer::link(
                                                new moodle_url('/mod/quiz/accessrule/wifiresilience/upload.php',
                                                            array('id' => $quizcmid)),
                                                get_string('descriptionlink', 'quizaccess_wifiresilience'), array('style' => 'color:#0f6cbf')) . '</li>';
        }
        if ($inspectresponsesrole == 1) {
            $return .= '<li>' . html_writer::link(
                                                new moodle_url('/mod/quiz/accessrule/wifiresilience/inspect.php',
                                                            array('id' => $quizcmid)),
                                                get_string('inspect', 'quizaccess_wifiresilience'), array('style' => 'color:#0f6cbf')) . '</li>';
        }
        if ($displayadminmsgs == 1 || $uploadresponsesrole == 1 || $inspectresponsesrole == 1 || $browserchecksrole == 1 ||
             $localresponsesrole == 1) {
            $return .= '</ul>';
        }

        if ($displayadminmsgs == 1 || $uploadresponsesrole == 1 || $inspectresponsesrole == 1 || $browserchecksrole == 1 ||
             $localresponsesrole == 1) {
            $return .= '</div>';
        }

        $return .= html_writer::div(get_string('technicalinspection', 'quizaccess_wifiresilience'), 'alert alert-warning',
                                    array('id' => 'wifiresilience_tech_pre_checks_div', 'style' => 'display:none; text-align:left'));

        return $return;
    }

    /**
     * Setup attempt page
     *
     * @param moodle_page $page
     *        the page object to initialise.
     */
    public function setup_attempt_page($page) {
        if ($page->pagetype == 'mod-quiz-attempt' || $page->pagetype == 'mod-quiz-summary') {
            redirect(new moodle_url(self::ATTEMPT_URL, $page->url->params()));
        }
    }
}
