YUI.add('moodle-quizaccess_wifiresilience-localforage', function (Y, NAME) {

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
 * @module moodle-quizaccess_wifiresilience-localforage
 */

/**
 * Auto-save functionality for during quiz attempts.
 *
 * @class M.quizaccess_wifiresilience.localforage
 */

M.quizaccess_wifiresilience = M.quizaccess_wifiresilience || {};
M.quizaccess_wifiresilience.localforage = {
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
        PER_QUESTION_BLOCK:    '.quiz-loading-hide'
    },


    /**
     * Prefix for the localStorage key.
     *
     * @property LOCAL_STORAGE_KEY_PREFIX
     * @type String
     * @default 'accessrule_offlinemode-responses-'
     * @private
     */
    LOCAL_STORAGE_KEY_PREFIX: 'ETHz-exams-',

    /**
     * The keyname.
     *
     * @property keyname
     * @type String
     * @default null
     * @private
     */
    keyname: null,

    /**
     * The responses_store.
     *
     * @property responses_store
     * @type Object
     * @default null
     * @private
     */
    responses_store: null,

    /**
     * The status_store.
     *
     * @property status_store
     * @type Object
     * @default null
     * @private
     */
    status_store: null,
    /**
     * The tablename.
     *
     * @property keyname
     * @type Object
     * @default null
     * @private
     */
    responses_details_store: null,
    questions_store:null,

    /**
     * Initialise the localforage code.
     *
     * @method String
     * @param {String} keyname the key, which will be saved in indexedDb
     */
    init: function(keyname) {

      quizaccess_wifiresilience_progress_step = 4;
      quizaccess_wifiresilience_progress_step_txt = "Preparing Exam Data..";
      $("#quizaccess_wifiresilience_result").html(quizaccess_wifiresilience_progress_step_txt);


      this.form = Y.one(this.SELECTORS.QUIZ_FORM);
      if (!this.form) {
          Y.log('No response form found. Why did you try to set up download?', 'debug', '[ETHz-SW] LocalStorage');
          return;
      }

      this.keyname = keyname;

      this.status_store = localforage.createInstance({
          name: "ETHz-exams-question-status"
      });

      this.responses_store = localforage.createInstance({
          name: "ETHz-exams-responses"
      });

      this.responses_details_store = localforage.createInstance({
          name: "ETHz-exams-individual-questions"
      });
      this.questions_store = localforage.createInstance({
          name: "ETHz-exams-all-questions"
      });

      // To be sure, sure.. save per question!
      this.save_html_per_question();

      var quizaccess_wifiresilience_progress = $(".quizaccess_wifiresilience_progress .quizaccess_wifiresilience_bar");
      quizaccess_wifiresilience_progress.animate({
        width: "40%"
      });

    },

    save_status_records: function(nonencrypteddata) {
        if(!nonencrypteddata) return;
        // Setting the key on one of these doesn't affect the other.
        this.status_store.setItem(this.keyname, nonencrypteddata).then(function () {
          return true;
        }).then(function (value) {
          // we got our value
          return true;
        }).catch(function (err) {
          // we got an error
          return true;
        });
      //  this.status_store.setItem(this.keyname, nonencrypteddata);
    },

    get_status_records: function() {

        if(!this.keyname) return;
        // Callback version:
        this.status_store.getItem(this.keyname, function(err, value) {
            // Run this code once the value has been
            // loaded from the offline store.
            var storeddata = value;
          Y.log('[FORM-REFILL]: we got store data now :-)', 'debug', '[ETHz-SW] LocalStorage');
            if(!storeddata || storeddata == 'undefined'){
                Y.log('[FORM-REFILL]: SLOW data retreival :(', 'debug', '[ETHz-SW] LocalStorage');
                //SLOW!! var storeddata = localStorage.getItem(M.quizaccess_wifiresilience.autosave.local_storage_key);
                Y.log('[LOCALSTORAGE]: Exam Data Found in IndexedDB LocalStorage', 'debug', '[ETHz-SW] LocalStorage');
            } else {
                Y.log('[IndexedDB]: Exam Data Found in IndexedDB', 'debug', '[ETHz-SW] LocalStorage');
            }

            if (storeddata && storeddata != 'undefined') { // Final storeddata result
                M.quizaccess_wifiresilience.autosave.locally_stored_data = Y.JSON.parse(storeddata);
            } else {
                var now = new Date();
                M.quizaccess_wifiresilience.autosave.locally_stored_data = {
                     last_change: 0,
                     last_save: M.quizaccess_wifiresilience.autosave.last_successful_server_save_timestamp,
                     final_submission_time: 0,
                     userid: M.quizaccess_wifiresilience.autosave.userid,
                     real_offline_time: M.quizaccess_wifiresilience.autosave.real_offline_time,
                     total_offline_time: M.quizaccess_wifiresilience.autosave.total_offline_time,
                     cid: M.quizaccess_wifiresilience.autosave.courseid,
                     cmid: M.quizaccess_wifiresilience.autosave.cmid,
                     attemptid: M.quizaccess_wifiresilience.autosave.attemptid,
                     responses: ''
                 };
            }

            var last_change_compare = new Date(M.quizaccess_wifiresilience.autosave.locally_stored_data.last_change).getTime();
            var last_save_compare = new Date(M.quizaccess_wifiresilience.autosave.locally_stored_data.last_save).getTime();
            Y.log('[FORM-REFILL]: Checking if Local Data is newer than Server Data..', 'debug', '[ETHz-SW] LocalStorage');
            if (last_change_compare > last_save_compare) {
                Y.log('[FORM-REFILL]: Local Data is indeed newer than Server Data. Repopulate the Exam with Latest Student Data', 'debug', '[ETHz-SW] LocalStorage');
                M.quizaccess_wifiresilience.autosave.try_to_use_locally_saved_responses();
            } else {
              Y.log('[FORM-REFILL]: Local Data is NOT newer (surprisingly!) than Server Data. Use Server Data.', 'debug', '[ETHz-SW] LocalStorage');
            }


        });
    },


    save_attempt_records: function(localdata) {
        if(!localdata) return;

        // Setting the key on one of these doesn't affect the other.
        this.responses_store.setItem(this.keyname, localdata).then(function () {
          return true;
        }).then(function (value) {
          // we got our value
          return true;
        }).catch(function (err) {
          // we got an error
          return true;
        });

      //  this.responses_store.setItem(this.keyname, localdata);
    },
    save_attempt_records_encrypted: function() {
        attempt_encrypted = M.quizaccess_wifiresilience.download.downloadNeeded();

        // Setting the key on one of these doesn't affect the other.
        this.responses_store.setItem(this.keyname, attempt_encrypted).then(function () {
          return true;
        }).then(function (value) {
          // we got our value
          return true;
        }).catch(function (err) {
          // we got an error
          return true;
        });

      //  this.responses_store.setItem(this.keyname, attempt_encrypted);
    },
    save_html_per_question: function() {

      if (!Y.all(this.SELECTORS.PER_QUESTION_BLOCK)) {
          Y.log('No Questions found with class "quiz-loading-hide" attribute', 'debug', '[ETHz-SW] LocalStorage');
          return;
      }

      Y.all(this.SELECTORS.PER_QUESTION_BLOCK).each(function(node) {
          question_html = Y.one('#'+node.getAttribute('id')).getHTML();
          page_number = node.getAttribute('id').replace('quizaccess_wifiresilience-attempt_page-','');
          question_number = parseInt(page_number) + 1;
        //  this.responses_details_store.setItem(this.keyname + '-p' + page_number + '-q' + question_number, question_html);
          this.responses_details_store.setItem(this.keyname + '-p' + page_number + '-q' + question_number, question_html).then(function () {
            return true;
          }).then(function (value) {
            // we got our value
            return true;
          }).catch(function (err) {
            // we got an error
            return true;
          });
  			}.bind(this));

        // Now Save All exam HTML
        var all_exam_html = document.documentElement.innerHTML;
      //  this.questions_store.setItem(this.keyname + '-full', all_exam_html);
      this.questions_store.setItem(this.keyname + '-full', all_exam_html).then(function () {
        return true;
      }).then(function (value) {
        // we got our value
        return true;
      }).catch(function (err) {
        // we got an error
        return true;
      });


    },

    delete_records_after_successful_submission: function() {
        // remove full exam
        this.questions_store.removeItem(this.keyname + '-full');

        // remove full response
        this.responses_store.removeItem(this.keyname);

        // remove response status
        this.status_store.removeItem(this.keyname);

        // remove Localstorage
        localStorage.removeItem(this.keyname);

        Y.all(this.SELECTORS.PER_QUESTION_BLOCK).each(function(node) {
            question_html = Y.one('#'+node.getAttribute('id')).getHTML();
            page_number = node.getAttribute('id').replace('quizaccess_wifiresilience-attempt_page-','');
            question_number = parseInt(page_number) + 1;
            this.responses_details_store.removeItem(this.keyname + '-p' + page_number + '-q' + question_number);
        }.bind(this));
        Y.log('OK.. Cleared out all local data..', 'debug', '[ETHz-SW] LocalStorage');
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
