YUI.add('moodle-quizaccess_wifiresilience-download', function (Y, NAME) {

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
 * Provide a download link to save all the current responses to a file.
 *
 * @module moodle-quizaccess_wifiresilience-download
 */

/**
 * Provide a download link to save all the current responses to a file.
 *
 * @class M.quizaccess_wifiresilience.download
 */

M.quizaccess_wifiresilience = M.quizaccess_wifiresilience || {};
M.quizaccess_wifiresilience.download = {
    /**
     * The selectors used throughout this class.
     *
     * @property SELECTORS
     * @private
     * @type Object
     * @static
     */
    SELECTORS: {
        DOWNLOAD_CONFIRM_MESSAGE: '#quiz-download-confirm-message',
        QUIZ_FORM:                '#responseform'
    },

    /**
     * The filename to use for the download.
     *
     * @property filename
     * @type String
     * @default null
     */
    filename: null,

    /**
     * The pulic key to be used to encrypt the responses before download.
     *
     * @property publicKey
     * @type String
     * @default null
     */
    publicKey: null,

    /**
     * A Node reference to the form we are monitoring.
     *
     * @property form
     * @type Node
     * @default null
     */
    form: null,

    /**
     * Initialise the autosave code.
     *
     * @method init
     */
    init: function(filename, publicKey) {

        quizaccess_wifiresilience_progress_step = 7;
        $("#quizaccess_wifiresilience_result").html(M.util.get_string('loadingstep7', 'quizaccess_wifiresilience'));

        this.filename = filename;
        this.publicKey = publicKey;

        Y.Crypto.sjcl.random.startCollectors();
        Y.Crypto.sjcl.beware["CBC mode is dangerous because it doesn't protect message integrity."]();

        this.form = Y.one(this.SELECTORS.QUIZ_FORM);
        if (!this.form) {
            Y.log('No response form found. Why did you try to set up download?', 'debug', '[ETHz-SW] Download Emergency File');
            return;
        }

        Y.delegate('click', this.downloadClicked, 'body', '.response-download-link', this);

      //  var quizaccess_wifiresilience_progress = $(".quizaccess_wifiresilience_progress .quizaccess_wifiresilience_bar");
        quizaccess_wifiresilience_progress.animate({
          width: examviewportmaxwidth * 7 / 10 + "px"
        });
    },

    /**
     * Handle the link click, and put the data in the URL so that it gets saved.
     *
     * @method downloadClicked
     */
    downloadClicked: function(e) {
        var link = e.currentTarget;

        if (typeof tinyMCE !== 'undefined') {
            tinyMCE.triggerSave();
        }

        link.set('download', this.filename.replace(
                /-d\d+\.eth/, '-d' + this.getCurrentDatestamp() + '.eth'));

        var data = {responses: Y.IO.stringify(this.form)};
        if (this.publicKey) {
            data = this.encryptResponses(data);
        }


        var blob = new Blob([Y.JSON.stringify(data)], {type: "octet/stream"});
        var url = window.URL.createObjectURL(blob);
        link.set('href', url);

        Y.later(500, this, this.showDownloadMessage, Y.one(this.SELECTORS.DOWNLOAD_CONFIRM_MESSAGE));

    },    /**
         * Handle the autosave in localStorage, and put the data in localstorage - called by yui autosave.
         *
         * @method downloadNeeded
         */
        downloadNeeded: function() {

            if (typeof tinyMCE !== 'undefined') {
                tinyMCE.triggerSave();
            }
            var data = {responses: Y.IO.stringify(this.form)};


            if (this.publicKey) {
                data = this.encryptResponses(data);
            }
            return Y.JSON.stringify(data);
        },

    /**
     * Get the current date/time in a format suitable for using in filenames.
     *
     * @method getCurrentDatestamp
     * @return String like '197001010000'.
     */
    getCurrentDatestamp: function() {
        var now = new Date();
        function pad(number) {
            return number < 10 ? '0' + number : number;
        }
        return '' + now.getUTCFullYear() + pad(now.getUTCMonth() + 1) +
                pad(now.getUTCDate()) + pad(now.getUTCHours()) + pad(now.getUTCMinutes());
    },

    /**
     * Display a message following the paragraph containing the link, to confirm
     * that the responses were saved locally.
     */
    showDownloadMessage: function() { //container
        if (Y.one(this.SELECTORS.DOWNLOAD_CONFIRM_MESSAGE)) {
            Y.one(this.SELECTORS.DOWNLOAD_CONFIRM_MESSAGE).remove(true);
        }
        function pad(number) {
            return number < 10 ? '0' + number : number;
        }
        now = new Date();
        //container
        Y.one('#mod_quiz_navblock .content').append('<p id="quiz-download-confirm-message">' +
                M.util.get_string('lastsavedtothiscomputer', 'quizaccess_wifiresilience',
                        pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds()) ) + '</p>');
    },

    /**
     * Encrypt the responses using our encryption protocol.
     *
     * @method getCurrentDatestamp
     * @return Object with three fields, the AES -ncrypted responses, and the
     *      RSA-encrypted AES key and initial values..
     */
    encryptResponses: function(data) {

        var aeskey = Y.Crypto.sjcl.random.randomWords(8);
        var rp = {};
        var encrypted = Y.Crypto.sjcl.encrypt(aeskey, data.responses, { ks: 256, mode: 'cbc' }, rp);

        var jsEncrypt = new Y.Crypto.JSEncrypt();
        jsEncrypt.setPublicKey(this.publicKey);
/*
       if(M.quizaccess_wifiresilience.autosave.offline_happened_on != 0){
          var temp_missing_seconds = Math.floor((new Date().getTime() - M.quizaccess_wifiresilience.autosave.offline_happened_on)/1000); // Getting ms
          this.temp_total_offline_time += temp_missing_seconds;
        }
*/
      // try to get offline time for reporting..
      M.quizaccess_wifiresilience.autosave.connection_changed();
      if(M.quizaccess_wifiresilience.autosave.disconnection_events && M.quizaccess_wifiresilience.autosave.disconnection_events.length > 0){
        var total_disconnection_times = M.quizaccess_wifiresilience.autosave.disconnection_events.reduce(function (a, b) {
            return a + b;
        }, 0);
      } else {
        total_disconnection_times = 0;
      }

        return {
            "last_change": M.quizaccess_wifiresilience.autosave.last_change,
            "last_save": M.quizaccess_wifiresilience.autosave.last_successful_server_save_timestamp,
            "final_submission_time": M.quizaccess_wifiresilience.autosave.final_submission_time,
            "userid": M.quizaccess_wifiresilience.autosave.userid,
            "real_offline_time": M.quizaccess_wifiresilience.autosave.real_offline_time,
            "total_offline_time": total_disconnection_times,
            "cid": M.quizaccess_wifiresilience.autosave.courseid,
            "cmid": M.quizaccess_wifiresilience.autosave.cmid,
            "attemptid": M.quizaccess_wifiresilience.autosave.attemptid,
            "responses": Y.JSON.parse(encrypted).ct,
            "key":       jsEncrypt.encrypt(Y.Crypto.sjcl.codec.base64.fromBits(aeskey)),
            "iv":        jsEncrypt.encrypt(Y.Crypto.sjcl.codec.base64.fromBits(rp.iv))
        };
    }
};


}, '@VERSION@', {
    "requires": [
        "base",
        "node",
        "event",
        "node-event-delegate",
        "json",
        "io-form",
        "moodle-quizaccess_wifiresilience-jsencrypt",
        "moodle-quizaccess_wifiresilience-sjcl"
    ]
});
