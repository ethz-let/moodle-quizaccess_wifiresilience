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
 * Implementaton of the quizaccess_wifiresilience plugin.
 *
 * @package   quizaccess_wifiresilience
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');


/**
 * The access rule class implementation for the quizaccess_wifiresilience plugin.
 *
 * A rule that hijacks the standard attempt.php page, and replaces it with
 * different script which loads all the questions at once and then allows the
 * student to keep working, even if the network connection is lost. However,
 * if the network is working, responses are saved back to the server.
 *
 * @copyright  2014 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_wifiresilience extends quiz_access_rule_base {

    /** @var string the URL path to our replacement attempt script. */
    const ATTEMPT_URL = '/mod/quiz/accessrule/wifiresilience/attempt.php';

    public static function make(quiz $quizobj, $timenow, $canignoretimelimits) {

        if (empty($quizobj->get_quiz()->wifiresilience_enabled) ||
                !self::is_compatible_behaviour($quizobj->get_quiz()->preferredbehaviour)) {
            return null;
        }

        return new self($quizobj, $timenow);
    }

    public static function add_settings_form_fields(
            mod_quiz_mod_form $quizform, MoodleQuickForm $mform) {

        $quizid = $quizform->get_current()->id;

        if($quizid) {
          global $DB;
          $config = $DB->get_record('quizaccess_wifiresilience', array('quizid' => $quizid));
        }
        if(!$quizid || !$config) { // Load Default Config for first time.
           $config = get_config('quizaccess_wifiresilience');
        }

        $mform->addElement('header', 'wifiresilienceenabled', get_string('wifiresilienceenabled', 'quizaccess_wifiresilience'));

        $mform->addElement('selectyesno', 'wifiresilience_enabled',
                get_string('wifiresilienceenabled', 'quizaccess_wifiresilience'));
        $mform->addHelpButton('wifiresilience_enabled',
                'wifiresilienceenabled', 'quizaccess_wifiresilience');
        $mform->setDefault('wifiresilience_enabled', !empty($config->defaultenabled));
    //    $mform->setAdvanced('wifiresilience_enabled', !empty($config->defaultenabled_adv));

        $mform->addElement('checkbox', 'wifiresilience_prechecks',
                get_string('prechecks', 'quizaccess_wifiresilience'));
        $mform->addHelpButton('wifiresilience_prechecks',
                'prechecks', 'quizaccess_wifiresilience');
        $mform->setDefault('wifiresilience_prechecks', !empty($config->prechecks));
    //    $mform->setAdvanced('wifiresilience_prechecks', 1);

        $mform->addElement('checkbox', 'wifiresilience_techerrors',
                get_string('techerrors', 'quizaccess_wifiresilience'));
        $mform->addHelpButton('wifiresilience_techerrors',
                'techerrors', 'quizaccess_wifiresilience');
        $mform->setDefault('wifiresilience_techerrors', !empty($config->techerrors));
    //    $mform->setAdvanced('wifiresilience_techerrors', 1);

        $mform->addElement('checkbox', 'wifiresilience_navdetails',
                get_string('navdetails', 'quizaccess_wifiresilience'));
        $mform->addHelpButton('wifiresilience_navdetails',
                'navdetails', 'quizaccess_wifiresilience');
        $mform->setDefault('wifiresilience_navdetails', !empty($config->navdetails));
    //    $mform->setAdvanced('wifiresilience_navdetails', 1);

        $mform->addElement('text', 'wifiresilience_wifitoken',
                get_string('wifitoken', 'quizaccess_wifiresilience'), 'size="36"');
        $mform->addHelpButton('wifiresilience_wifitoken',
                'wifitoken', 'quizaccess_wifiresilience');
        $mform->setType('wifiresilience_wifitoken', PARAM_RAW);
        $mform->setDefault('wifiresilience_wifitoken', $config->wifitoken);

  //      $mform->setAdvanced('wifiresilience_wifitoken', 1);
        $mform->addElement('html', '<div class="wifiresilience_hidden_form_elements">');
        
        $mform->addElement('textarea', 'wifiresilience_watchxhr',
                get_string('watchxhr', 'quizaccess_wifiresilience'), 'cols="60" rows="25"');
        $mform->addHelpButton('wifiresilience_watchxhr',
                'watchxhr', 'quizaccess_wifiresilience');
        $mform->setDefault('wifiresilience_watchxhr', $config->watchxhr);
    //    $mform->setAdvanced('wifiresilience_watchxhr', 1);

        $mform->addElement('textarea', 'wifiresilience_fetchandlog',
                get_string('fetchandlog', 'quizaccess_wifiresilience'), 'cols="60" rows="5"');
        $mform->addHelpButton('wifiresilience_fetchandlog',
                'fetchandlog', 'quizaccess_wifiresilience');
        $mform->setDefault('wifiresilience_fetchandlog', $config->fetchandlog);
    //    $mform->setAdvanced('wifiresilience_fetchandlog', 1);

        $mform->addElement('textarea', 'wifiresilience_precachefiles',
                get_string('precachefiles', 'quizaccess_wifiresilience'), 'cols="60" rows="5"');
        $mform->addHelpButton('wifiresilience_precachefiles',
                'precachefiles', 'quizaccess_wifiresilience');
        $mform->setDefault('wifiresilience_precachefiles', $config->precachefiles);

        $mform->addElement('textarea', 'wifiresilience_excludelist',
                get_string('excludelist', 'quizaccess_wifiresilience'), 'cols="60" rows="5"');
        $mform->addHelpButton('wifiresilience_excludelist',
                'excludelist', 'quizaccess_wifiresilience');
        $mform->setDefault('wifiresilience_excludelist', $config->excludelist);

    //    $mform->setAdvanced('wifiresilience_precachefiles', 1);

        $mform->addElement('textarea', 'wifiresilience_extraroutes',
                get_string('extraroutes', 'quizaccess_wifiresilience'), 'cols="60" rows="25"');
        $mform->addHelpButton('wifiresilience_extraroutes',
                'extraroutes', 'quizaccess_wifiresilience');
        $mform->setDefault('wifiresilience_extraroutes', $config->extraroutes);
  //      $mform->setAdvanced('wifiresilience_extraroutes', 1);

        $mform->disabledIf('wifiresilience_prechecks', 'wifiresilience_enabled',
                'eq', 0);
        $mform->disabledIf('wifiresilience_techerrors', 'wifiresilience_enabled',
                'eq', 0);
        $mform->disabledIf('wifiresilience_navdetails', 'wifiresilience_enabled',
                'eq', 0);
        $mform->disabledIf('wifiresilience_wifitoken', 'wifiresilience_enabled',
                'eq', 0);
        $mform->disabledIf('wifiresilience_watchxhr', 'wifiresilience_enabled',
                'eq', 0);
        $mform->disabledIf('wifiresilience_fetchandlog', 'wifiresilience_enabled',
                'eq', 0);
        $mform->disabledIf('wifiresilience_extraroutes', 'wifiresilience_enabled',
                'eq', 0);
        $mform->disabledIf('wifiresilience_precachefiles', 'wifiresilience_enabled',
                'eq', 0);
        $mform->disabledIf('wifiresilience_excludelist', 'wifiresilience_enabled',
                'eq', 0);

        foreach (question_engine::get_behaviour_options(null) as $behaviour => $notused) {
            if (!self::is_compatible_behaviour($behaviour)) {
                $mform->disabledIf('wifiresilience_enabled', 'preferredbehaviour',
                        'eq', $behaviour);
                $mform->disabledIf('wifiresilience_prechecks', 'preferredbehaviour',
                        'eq', $behaviour);
                $mform->disabledIf('wifiresilience_techerrors', 'preferredbehaviour',
                        'eq', $behaviour);
                $mform->disabledIf('wifiresilience_navdetails', 'preferredbehaviour',
                        'eq', $behaviour);
                $mform->disabledIf('wifiresilience_wifitoken', 'preferredbehaviour',
                                'eq', $behaviour);
                $mform->disabledIf('wifiresilience_watchxhr', 'preferredbehaviour',
                                'eq', $behaviour);
                $mform->disabledIf('wifiresilience_fetchandlog', 'preferredbehaviour',
                                                'eq', $behaviour);
                $mform->disabledIf('wifiresilience_extraroutes', 'preferredbehaviour',
                                'eq', $behaviour);
                $mform->disabledIf('wifiresilience_precachefiles', 'preferredbehaviour',
                                'eq', $behaviour);
                $mform->disabledIf('wifiresilience_excludelist', 'preferredbehaviour',
                                'eq', $behaviour);

            }
        }
        $mform->addElement('html', '</div>');
    }

    /**
     * Given the quiz "How questions behave" setting, can the fault-tolerant mode work
     * with that behaviour?
     * @param string $behaviour the internal name (e.g. 'interactive') of an archetypal behaviour.
     * @return boolean whether fault-tolerant mode can be used.
     */
    public static function is_compatible_behaviour($behaviour) {
        $unusedoptions = question_engine::get_behaviour_unused_display_options($behaviour);
        // Sorry, double negative here. The heuristic is that:
        // The behaviour is compatible if we don't need to show specific feedback during the attempt.
        return in_array('specificfeedback', $unusedoptions);
    }

    public static function save_settings($quiz) {
        global $DB;
        if (empty($quiz->wifiresilience_enabled)) {
            $DB->delete_records('quizaccess_wifiresilience', array('quizid' => $quiz->id));
        } else {

            if(empty($quiz->wifiresilience_prechecks) || !$quiz->wifiresilience_prechecks){
              $quiz->wifiresilience_prechecks = 0;
            }
            if(empty($quiz->wifiresilience_techerrors) || !$quiz->wifiresilience_techerrors){
              $quiz->wifiresilience_techerrors = 0;
            }
            if(empty($quiz->wifiresilience_navdetails) || !$quiz->wifiresilience_navdetails){
              $quiz->wifiresilience_navdetails = 0;
            }
            if(empty($quiz->wifiresilience_wifitoken) || !$quiz->wifiresilience_wifitoken){
              $quiz->wifiresilience_wifitoken = '';
            }
            if(empty($quiz->wifiresilience_watchxhr) || !$quiz->wifiresilience_watchxhr){
              $quiz->wifiresilience_watchxhr = '';
            }
            if(empty($quiz->wifiresilience_fetchandlog) || !$quiz->wifiresilience_fetchandlog){
              $quiz->wifiresilience_fetchandlog = '';
            }
            if(empty($quiz->wifiresilience_extraroutes) || !$quiz->wifiresilience_extraroutes){
              $quiz->wifiresilience_extraroutes = '';
            }
            if(empty($quiz->wifiresilience_precachefiles) || !$quiz->wifiresilience_precachefiles){
              $quiz->wifiresilience_precachefiles = '';
            }
            if(empty($quiz->wifiresilience_excludelist) || !$quiz->wifiresilience_excludelist){
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

    public static function delete_settings($quiz) {
        global $DB;
        $DB->delete_records('quizaccess_wifiresilience', array('quizid' => $quiz->id));
    }

    public static function get_settings_sql($quizid) {
        return array(
            'COALESCE(wifiresilience.enabled, 0) AS wifiresilience_enabled',
            'LEFT JOIN {quizaccess_wifiresilience} wifiresilience ON wifiresilience.quizid = quiz.id',
            array());
    }

    public function description() {
      global $CFG, $DB,$USER;
      $displayadminmsgs = 0;

      if ($this->quizobj->has_capability('quizaccess/wifiresilience:adminmessages')) {
        $displayadminmsgs = 1;
      }

      $uploadresponses_role = 0;
      if ($this->quizobj->has_capability('quizaccess/wifiresilience:uploadresponses')) {
        $uploadresponses_role = 1;
      }

      $inspectresponses_role = 0;
      if ($this->quizobj->has_capability('quizaccess/wifiresilience:inspectresponses')) {
        $inspectresponses_role = 1;
      }
      $localresponses_role = 0;
      if ($this->quizobj->has_capability('quizaccess/wifiresilience:localresponses')) {
        $localresponses_role = 1;
      }
      $browserchecks_role = 0;
      if ($this->quizobj->has_capability('quizaccess/wifiresilience:browserchecks')) {
        $browserchecks_role = 1;
      }
      $viewtechchecks_role = 0;
      if ($this->quizobj->has_capability('quizaccess/wifiresilience:viewtechchecks')) {
        $viewtechchecks_role = 1;
      }

      $quizid = $this->quizobj->get_quizid();
      if(!$quizid){
        print_error('invalidcourse');
      }
      $quiz_cmid = $this->quizobj->get_cmid();
      if(!$quiz_cmid){
        print_error('invalidcoursemodule');
      }
      $wifi_settings = $DB->get_record('quizaccess_wifiresilience', array('quizid' => $quizid));

      $return  = '';
/*
      if(!empty($wifi_settings->wifitoken) && trim($wifi_settings->wifitoken) !='' ){
        $token = $wifi_settings->wifitoken;
      } else {
        $token = '';
      }
*/
      if($displayadminmsgs == 1){

          // If mobile services are off, the user won't be able to use any external app.
          if (empty($CFG->enablemobilewebservice) || empty($wifi_settings->wifitoken) || trim($wifi_settings->wifitoken) =='') {
              $return .= '<div class="alert alert-error" style="text-align:left">Web Services are not enabled. Background Sync (automatically sending student emergency response copies to server) will not work without Mobile Web Services.';
              $return .= 'You can fix the issue by:<br><ol style="margin-left:20px;">';
              if (empty($CFG->enablemobilewebservice)) {
                $return .= '<li>Enable Web Services from <a href="'.$CFG->wwwroot.'/admin/search.php?query=enablewebservices">here</a>.</li>';
              }
              if (empty($wifi_settings->wifitoken) || trim($wifi_settings->wifitoken) =='') {
                $return .= '<li>Add Token at plugin <a href="'.$CFG->wwwroot.'/admin/settings.php?section=modsettingsquizcatwifiresilience">plugin level</a>, or <a href="'.$CFG->wwwroot.'/course/modedit.php?update='.$quiz_cmid.'&return=1">Quiz level</a> (In quiz settings. It has higher priority than site-level).</li>';
              }
              $return .= '</ol></div>';
          }

      }
        $return .= '<script src="accessrule/wifiresilience/js/jquery.js"></script>'; // For our old moodle theme.
        $return .= '<script src="accessrule/wifiresilience/js/localforage.js"></script>'; // To retrieve localdata.
        $return .= '<script src="accessrule/wifiresilience/js/startswith.js"></script>'; // To loop in localdata.

      if($displayadminmsgs == 1 || $uploadresponses_role == 1 || $inspectresponses_role == 1 || $browserchecks_role == 1 || $localresponses_role == 1) {
        $return .= '<div class="alert alert-info">'.get_string('description', 'quizaccess_wifiresilience').'</div>';

        $return .= '<div class="alert alert-warning" style="text-align:left">'.get_string('uploadresponsesadmin', 'quizaccess_wifiresilience');
        $return .= '<ul>';
      }
        if($uploadresponses_role == 1){
          $return .= '<li>' .html_writer::link(new moodle_url('/mod/quiz/accessrule/wifiresilience/upload.php',
                          array('id' => $quiz_cmid)),
                          get_string('descriptionlink', 'quizaccess_wifiresilience')) . '</li>';
        }

        if($localresponses_role == 1){
          $return .= '<li>' . html_writer::link(new moodle_url('/mod/quiz/accessrule/wifiresilience/local.php',
                        array('id' => $quiz_cmid)),
                        get_string('loadlocalresponses', 'quizaccess_wifiresilience')).'</li>';

          $return .= '<li>' . html_writer::link(new moodle_url('/mod/quiz/accessrule/wifiresilience/uploaded_syncedfiles.php',
                        array('id' => $quiz_cmid)),
                        get_string('syncedfiles', 'quizaccess_wifiresilience')).'</li>';
        }


        if($inspectresponses_role == 1){
          $return .=  '<li>' . html_writer::link(new moodle_url('/mod/quiz/accessrule/wifiresilience/inspect.php',
                         array('id' => $quiz_cmid)),
                         get_string('inspect', 'quizaccess_wifiresilience')).'</li>';
        }

        if($browserchecks_role == 1){
        $return .= '<li>' . html_writer::link(new moodle_url('/mod/quiz/accessrule/wifiresilience/cryptotest.php',
                         array('id' => $quiz_cmid)),
                         get_string('testencryption', 'quizaccess_wifiresilience')).'</li>';

        $return .= '<li>' . html_writer::link(new moodle_url('/mod/quiz/accessrule/wifiresilience/check/',
                          array('id' => $quiz_cmid)),
                          get_string('technicalchecks', 'quizaccess_wifiresilience'),array('target' => '_blank')).'</li>';

        }



          if($displayadminmsgs == 1 || $uploadresponses_role == 1 || $inspectresponses_role == 1 || $browserchecks_role == 1 || $localresponses_role == 1) {
            $return .= '</ul>';

        }

        if($displayadminmsgs == 1) {
          $return .= '<hr><h3>'.get_string('serviceworkermgmt', 'quizaccess_wifiresilience').'</h3><a class="btn btn-warning"  style="display:none;margin-bottom: 5px;" id="quizaccess_wifiresilience_reset_sw"> '.
                    get_string('resetserviceworker', 'quizaccess_wifiresilience').'</a>
                    <a class="btn btn-warning"  style="display:none;margin-bottom: 5px;"  id="quizaccess_wifiresilience_update_sw">'.
                    get_string('refreshserviceworker', 'quizaccess_wifiresilience').'</a> <a class="btn btn-warning"  style="display:none;margin-bottom: 5px;" id="quizaccess_wifiresilience_stop_sw"> '.
                                get_string('stopserviceworker', 'quizaccess_wifiresilience').'</a> <a class="btn btn-warning"  style="display:none;margin-bottom: 5px;" id="quizaccess_wifiresilience_sync_sw"> '.
                                            get_string('syncserviceworker', 'quizaccess_wifiresilience').'</a> <hr>';

          }
if($displayadminmsgs == 1 || $uploadresponses_role == 1 || $inspectresponses_role == 1 || $browserchecks_role == 1 || $localresponses_role == 1) {
          $return .= '</div>';
        }

        $return .= '<div id="wifiresilience_tech_pre_checks_div" class="alert alert-warning" style="display:none; text-align:left">Technical Inspection:<br /></div>';

        $serviceworker_params = '?cmid='.$quiz_cmid.'&quizid='.$quizid.'&rev='.rand();
        $return .= '<script>
        function wifiresilience_formatbytes(a,b){if(0==a)return"0 Bytes";var c=1e3,d=b||2,e=["Bytes","KB","MB","GB","TB","PB","EB","ZB","YB"],f=Math.floor(Math.log(a)/Math.log(c));return parseFloat((a/Math.pow(c,f)).toFixed(d))+" "+e[f]}


        wifiresilience_tech_info = "1. <font color=grey>[ETHz-SW] ethz-exams-sw.js is Registering..</font>";
        // Check for browser support of service worker
        if (\'serviceWorker\' in navigator) {
          //var rndm = Math.random();
          var exam_sw_name = "accessrule/wifiresilience/serviceworker.php'.$serviceworker_params.'";
          wifiresilience_tech_info =  "1. <font color=green>[ETHz-SW] Service-Worker registration successful. <span id=\"sw_kind\"></span>";
          navigator.serviceWorker.register(exam_sw_name)
          .then(function(registration) {
            registration.update();
            // Successful registration
            var swelement = document.querySelector(\'#sw_kind\');
            if (registration.installing) {
              ExamServiceWorker = registration.installing;
              if (typeof(swelement) != \'undefined\' && swelement != null){
                swelement.textContent = \'(Status: Installing)\';
              }
            } else if (registration.waiting) {
              ExamServiceWorker = registration.waiting;
              if (typeof(swelement) != \'undefined\' && swelement != null){
                swelement.textContent = \'(Status: Waiting)\';
              }

            } else if (registration.active) {
              ExamServiceWorker = registration.active;
              if (typeof(swelement) != \'undefined\' && swelement != null){
                swelement.textContent = \'(Status: Active)\';
              }

            }';
if($displayadminmsgs == 1) {
          $return .=' document.querySelector("#quizaccess_wifiresilience_reset_sw").addEventListener("click",
              function() {
                navigator.serviceWorker.getRegistrations().then(function(registrations) {
                if(registrations){
                  for(let registration of registrations) {
                  registration.unregister().then(function(boolean) {
                      // if boolean = true, unregister is successful
                      if(boolean){
                          console.log("[ETHz-SW] Service-Worker Reset successful: ",registration.scope);
                      }

                  });
                }
                 window.location.reload(true); // Hard Reload
              } else {
                alert("Service Worker is not registered (Might have errors, or not included in this page scope, or already deregistered), you can not reset it now.");
              }

                });
              });


              document.querySelector("#quizaccess_wifiresilience_update_sw").addEventListener("click",
                function() {
                  navigator.serviceWorker.getRegistrations().then(function(registrations) {
                  if(registrations){

                    for(let registration of registrations) {

                    registration.update().then(function(boolean) {
                        // if boolean = true, update is successful
                        if(boolean){
                            console.log("[ETHz-SW] Service-Worker Update successful: ",registration.scope);
                        }

                    });
                  }
                  // window.location.reload();
                } else {
                  alert("Service Worker is not registered (Might have errors, or not included in this page scope, or already deregistered), you can not update it now.");
                }

                  });
                });

                document.querySelector("#quizaccess_wifiresilience_stop_sw").addEventListener("click",
                  function() {
                    navigator.serviceWorker.getRegistrations().then(function(registrations) {
                    if(registrations){

                      for(let registration of registrations) {

                      registration.unregister().then(function(boolean) {
                          // if boolean = true, update is successful
                          if(boolean){
                              console.log("[ETHz-SW] Service-Worker STOP successful: ",registration.scope);
                          }

                      });
                    }
                  } else {
                    alert("Service Worker is not registered (Might have errors, or not included in this page scope, or already deregistered), you can not stop it now.");
                  }

                    });
                  });


                  document.querySelector("#quizaccess_wifiresilience_sync_sw").addEventListener("click",
                    function() {

                      registration.sync.register("upload-responses").then(() => {
                        console.log("[ETHz-SW] Sync Test upload-responses registered. Firing upload-responses.");
                          alert("Background Sync fired successfully");
                      }).catch(function(error){
                          alert("Background Sync failed.");
                        console.log("[ETHz-SW] Sync: Unable to register upload-responses.");
                      });


                    });
                ';
}
          $return .='

            ExamServiceWorker.addEventListener("statechange", function(e) {
                  document.querySelector(\'#sw_kind\').textContent = \'(Status: \'+e.target.state+\')\';
                  if (e.target.state == "activated") {
                      // use Background Sync for subscribing here.
                      console.log("[ETHz-SW] Background Sync: Service Worker is just now in Active Mode. Now we can subscribe for Background Sync");
                      // Then later, request a one-off sync:
                      if (\'SyncManager\' in window) {
                          console.log(\'[ETHz-SW] Background Sync: Ready to Register upload-responses event.\');
                            registration.sync.register(\'upload-responses\').then(() => {
                              console.log(\'[ETHz-SW] Background Sync: upload-responses Registered\');
                            }).catch(function(err) {
                              // system was unable to register for a sync,
                              // this could be an OS-level restriction
                              console.error(\'[ETHz-SW] Background Sync: System was unable to register for a sync, this could be an OS-level restriction (or not ready yet). Maybe try to reload the page again..\',err);
                            });
                      }

                      /*
                      else {
                        // sync not supported

                        console.error(\'[ETHz-SW] Background Sync is not supported in this browser. Resorting to Traditional Upload\');

                      }
                      */

                  }
            });
            console.log(\'[ETHz-SW] ethz-exams-sw.js Registration successful, scope is:\', registration.scope);
          }).catch(function(err) {
            // Failed registration, service worker won’t be installed
            wifiresilience_tech_info = "1. <font color=red>[ETHz-SW] Service-Worker registration failed. Error:" + err + " <span id=\"sw_kind\"></span></font>";
            console.error(\'[ETHz-SW] ethz-exams-sw.js Service worker registration failed, error:\', err);
          });
        } else {
          wifiresilience_tech_info = "1. <font color=red>[ETHz-SW] Service-Worker is not supported in this Browser. <span id=\"sw_kind\"></span></font>";
        }

        window.indexedDB = window.indexedDB ||
                   window.mozIndexedDB ||
                   window.webkitIndexedDB ||
                   window.msIndexedDB;
        wifiresilience_tech_info_db = "<br />2. <font color=grey>[ETHz-SW] IndexedDB support is Uknown.</font>";
        if (window.indexedDB) {
          wifiresilience_tech_info_db = "<br />2. <font color=green>[ETHz-SW] IndexedDB is supported.</font>";
          console.log("[ETHz-SW] IndexedDB is supported.");
        } else {
          wifiresilience_tech_info_db = "<br /><font color=red>[ETHz-SW] IndexedDB is not supprorted.</font>";
          console.log("[ETHz-SW] IndexedDB is NOT supported.");
        }

        wifiresilience_tech_info_presist_storage = "<br />3. <font color=grey>[ETHz-SW] Storage Persistance is Uknown.</font>";
        if (navigator.storage && navigator.storage.persist)
          navigator.storage.persisted().then(persistent=>{
            if (persistent){
              wifiresilience_tech_info_presist_storage = "<br />3. <font color=green>[ETHz-SW] Storage will not be cleared except by explicit user action.</font>";
              console.log("[ETHz-SW] Storage will not be cleared except by explicit user action");
            }else{
              wifiresilience_tech_info_presist_storage = "<br />3. <font color=red>[ETHz-SW] Storage may be cleared by the UA under storage pressure (Old records only).</font>";
              console.log("[ETHz-SW] Storage may be cleared by the UA under storage pressure.");
            }
          });


          wifiresilience_tech_info_avail_quota = "<br />4. <font color=grey>[ETHz-SW] Current available Storage Quota is Uknown.</font>";

          // Request storage usage and capacity left
          // Choose either Temporary or Persistent
        if(\'webkitTemporaryStorage\' in navigator){

          navigator.webkitTemporaryStorage.queryUsageAndQuota (
              function(usedBytes, grantedBytes) {
                  var usedbytes = wifiresilience_formatbytes(usedBytes);
                  var grantedbytes = wifiresilience_formatbytes(grantedBytes);
                  wifiresilience_tech_info_avail_quota = "<br />4. <font color=green>[ETHz-SW] Browser Storage already uses " + usedbytes + " of " + grantedbytes + "</font>";
                  console.log("[ETHz-SW] Browser Storage already uses ", usedbytes, " of ", grantedbytes);
              },
              function(e) {
                wifiresilience_tech_info_avail_quota = "<br />4. <font color=red>[ETHz-SW] Browser Storage can not be calculated.</font>";
                console.log("ETHz-SW] Browser Storage Calculation Error", e);
              }
          );
        } else {
          wifiresilience_tech_info_avail_quota = "<br />4. <font color=red>[ETHz-SW] Browser Storage (webkitTemporaryStorage) can not be calculated.</font>";
          console.log(\'webkitTemporaryStorage not supported in this browser..\');
        }
          wifiresilience_tech_info_req_quota = "<br />5. <font color=grey>[ETHz-SW] Requesting Extra Storage Quota is Uknown (less than available available storage).</font>";

          // Request Quota (only for File System API)
          var requestedBytes = 1024*1024*1024; // 1024MB
          function Wifi_Quote_errorHandler(e){
              console.log("[ETHz-SW] Request Quota of 1024MB Error: ", e);
          }
          function onInitFs(fs){
            console.log("[ETHz-SW] onInitFs called by requestQuota: ", fs);
          }

          if(\'webkitPersistentStorage\' in navigator){
          navigator.webkitPersistentStorage.requestQuota (
              requestedBytes, function(grantedBytes) {
                  window.webkitRequestFileSystem(PERSISTENT, grantedBytes, onInitFs, Wifi_Quote_errorHandler);
                  wifiresilience_tech_info_req_quota = "<br />5. <font color=green>[ETHz-SW] Requesting Extra Storage Quota (1GB) is Successful.</font>";
                  console.log("[ETHz-SW] Requesting Extra Storage Quota (1GB) is Successful.");

              }, function(e) {
                wifiresilience_tech_info_req_quota = "<br />5. <font color=red>[ETHz-SW] Requesting Extra Storage Quota (1GB) has Failed.</font>";
                console.log("[ETHz-SW] Request Quota of 1024MB Error: ", e);
              }
          );
        } else {
          wifiresilience_tech_info_avail_quota = "<br />5. <font color=red>[ETHz-SW] Requesting Extra Storage Quota (1GB) has Failed. webkitPersistentStorage not supported.</font>";
          console.log(\'[ETHz-SW] webkitTemporaryStorage not supported in this browser..\');
        }

        wifiresilience_tech_info_cacheAPI = "<br />6. <font color=grey>[ETHz-SW] Requesting information about cacheAPI, status: Uknown.</font>";

      if(\'caches\' in window) {
        wifiresilience_tech_info_cacheAPI = "<br />6. <font color=green>[ETHz-SW] CacheAPI is supported.</font>";
        console.log(\'[ETHz-SW] CacheAPI is supported in this browser..\');
      } else {
        wifiresilience_tech_info_cacheAPI = "<br />6. <font color=red>[ETHz-SW] CacheAPI is not supported.</font>";
        console.log(\'[ETHz-SW] CacheAPI is not supported in this browser..\');
      }

    wifiresilience_tech_info_sync = "<br />7. <font color=grey>[ETHz-SW] Requesting information about BackgroundSync, status: Uknown.</font>";

    if (\'SyncManager\' in window) {
      wifiresilience_tech_info_sync = "<br />7. <font color=green>[ETHz-SW] BackgroundSync is supported.</font>";
      console.log(\'[ETHz-SW] BackgroundSync is supported in this browser..\');
    } else {
      wifiresilience_tech_info_sync = "<br />7. <font color=red>[ETHz-SW] BackgroundSync is not supported.</font>";
      console.log(\'[ETHz-SW] BackgroundSync is not supported in this browser..\');
    }

        $(document).ready(function(){';
          if($displayadminmsgs == 1) {
            $return .= '
                    $("#quizaccess_wifiresilience_reset_sw").show();
                    $("#quizaccess_wifiresilience_update_sw").show();
                    $("#quizaccess_wifiresilience_stop_sw").show();
                    $("#quizaccess_wifiresilience_sync_sw").show();
                    ';
         }

         $return .= '
          $("#wifiresilience_tech_pre_checks_div").append(wifiresilience_tech_info);
          $("#wifiresilience_tech_pre_checks_div").append(wifiresilience_tech_info_db);
          $("#wifiresilience_tech_pre_checks_div").append(wifiresilience_tech_info_presist_storage);
          $("#wifiresilience_tech_pre_checks_div").append(wifiresilience_tech_info_avail_quota);
          $("#wifiresilience_tech_pre_checks_div").append(wifiresilience_tech_info_req_quota);
          $("#wifiresilience_tech_pre_checks_div").append(wifiresilience_tech_info_cacheAPI);
          $("#wifiresilience_tech_pre_checks_div").append(wifiresilience_tech_info_sync);
        ';

        if((!empty($wifi_settings->prechecks) && $wifi_settings->prechecks != 0) || $viewtechchecks_role == 1 || $displayadminmsgs == 1){
          $return .= '$("#wifiresilience_tech_pre_checks_div").show();';
        }

        $return .= '
        });
        </script>
        ';

        return $return;
    }

    public function setup_attempt_page($page) {
        if ($page->pagetype == 'mod-quiz-attempt' || $page->pagetype == 'mod-quiz-summary') {
            redirect(new moodle_url(self::ATTEMPT_URL, $page->url->params()));
        }
    }
}
