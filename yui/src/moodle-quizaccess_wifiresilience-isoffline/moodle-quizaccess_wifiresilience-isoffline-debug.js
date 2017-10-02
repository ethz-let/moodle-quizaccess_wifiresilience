YUI.add('moodle-quizaccess_wifiresilience-isoffline', function (Y, NAME) {

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
 * Auto-save functionality for during quiz attempts.
 *
 * @module moodle-quizaccess_wifiresilience-isoffline
 */

/**
 * Wifi status Checking functionality for during quiz attempts.
 *
 * @class M.quizaccess_wifiresilience.isoffline
 */

M.quizaccess_wifiresilience = M.quizaccess_wifiresilience || {};
M.quizaccess_wifiresilience.isoffline = {
    /**
     * The selectors used throughout this class.
     *
     * @property SELECTORS
     * @private
     * @type Object
     * @static
     */
    SELECTORS: {
        QUIZ_FORM:             '#responseform',
    },

    /**
     * Initialise the isoffline code.
     *
     * @method String
     * @param {String} keyname the key, which will be saved in indexedDb
     */
    init: function() {

      this.form = Y.one(this.SELECTORS.QUIZ_FORM);
      if (!this.form) {
          Y.log('No response form found. Why did you try to set up download?', 'debug', '[ETHz-SW] Connection Status');
          return;
      }

      quizaccess_wifiresilience_progress_step = 8;
      quizaccess_wifiresilience_progress_step_txt = "Verifying Network Status..";
      $("#quizaccess_wifiresilience_result").html(quizaccess_wifiresilience_progress_step_txt);

      Y.one('#mod_quiz_navblock .content').append('<div id="quizaccess_wifiresilience_connection"><a href="#" class="response-download-link" title="' + M.util.get_string('savetheresponses', 'quizaccess_wifiresilience') + '"><div></div></a></div>');

      function quizaccess_wifiresilience_onlinestatus(msg, connected) {

        var el = document.querySelector('#quizaccess_wifiresilience_connection');
        var cxn_hidden = document.querySelector('#quizaccess_wifiresilience_hidden_cxn_status');
        if (connected) {
          cxn_hidden.value = 1;
          if (el.classList) {
            el.classList.add('connected');
            el.classList.remove('disconnected');
          } else {
            el.addClass('connected');
            el.removeClass('disconnected');
          }
        } else {
          cxn_hidden.value = 0;
          if (el.classList) {
            el.classList.remove('connected');
            el.classList.add('disconnected');
          } else {
            el.removeClass('connected');
            el.addClass('disconnected');
          }
        }
        // for module.js
        M.quizaccess_wifiresilience.autosave.connected = connected;


        Y.log('Device is: ' + msg, 'debug', '[ETHz-SW] Connection Status');

        if(!connected){
          // Save encrypted file immediately!.
          // Save form elements - make sure to re-read the form!
          M.quizaccess_wifiresilience.autosave.locally_stored_data.responses = Y.IO.stringify(M.quizaccess_wifiresilience.autosave.form);
          var stringified_data = Y.JSON.stringify(M.quizaccess_wifiresilience.autosave.locally_stored_data);
          M.quizaccess_wifiresilience.localforage.save_status_records(stringified_data);
          Y.log('Device is Offline: Force Saving Exam Elements Status in indexedDB.', 'debug', '[ETHz-SW] Connection Status');
          // Save the encrypted file too?
          M.quizaccess_wifiresilience.localforage.save_attempt_records_encrypted();
          Y.log('Device is Offline: Force Saving Exam Encrypted Emergency File in indexedDB.', 'debug', '[ETHz-SW] Connection Status');
        }
      }
/*
      function process_offline_situation(connected) {
        if(connected){
          //calculate the time we got connected.
        //   M.quizaccess_wifiresilience.autosave.endtime = M.mod_quiz.timer.endtime + M.quizaccess_wifiresilience.autosave.offline_happened_on;
        var total_seconds_missing = 0;
        if(M.quizaccess_wifiresilience.autosave.offline_happened_on != 0){
          total_seconds_missing = Math.floor((new Date().getTime() - M.quizaccess_wifiresilience.autosave.offline_happened_on)/1000); // Getting ms
          M.mod_quiz.timer.endtime += total_seconds_missing;
        }

        //  M.quizaccess_wifiresilience.autosave.total_offline_time += fx;
           console.error('total missing seconds which got compensated: ['+ total_seconds_missing +'] Seconds');

          // Reset seconds of offline
            M.quizaccess_wifiresilience.autosave.offline_happened_on = 0;
            // Update end time
            M.mod_quiz.timer.update(evt);

        } else {
            //Log the time it happened.
            M.mod_quiz.timer.stop(evt);
            M.quizaccess_wifiresilience.autosave.offline_happened_on = new Date().getTime();
          //  console.error('Offline Counter happened: '+M.quizaccess_wifiresilience.autosave.offline_happened_on);

        }
      }
*/
      window.addEventListener('load', function(e) {
      if (navigator.onLine) {
        quizaccess_wifiresilience_onlinestatus('Online', true);
      } else {
        quizaccess_wifiresilience_onlinestatus('Offline', false);
      }
      }, false);

      window.addEventListener('online', function(e) {
        quizaccess_wifiresilience_onlinestatus('Online', true);
      // Get updates from server.
      }, false);

      window.addEventListener('offline', function(e) {
        quizaccess_wifiresilience_onlinestatus('Offline', false);
      // Use offine mode.
      }, false);

      Y.log('Device Connectivity Status Sniffer Initialised', 'debug', '[ETHz-SW] Connection Status');


      var quizaccess_wifiresilience_progress = $(".quizaccess_wifiresilience_progress .quizaccess_wifiresilience_bar");
      quizaccess_wifiresilience_progress.animate({
        width: "80%"
      });

    },

};


}, '@VERSION@', {
    "requires": [
        "base",
        "node",
        "event",
        "event-valuechange",
        "node-event-delegate",
        "io-form",
        "json",
        "core_question_engine",
        "mod_quiz"
    ]
});
