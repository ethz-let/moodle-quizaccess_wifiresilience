YUI.add('moodle-quizaccess_wifiresilience-watching', function (Y, NAME) {

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
 * @module moodle-quizaccess_wifiresilience-watching
 */

/**
 * watchinging functionality for during quiz attempts.
 *
 * @class M.quizaccess_wifiresilience.watching
 */

M.quizaccess_wifiresilience = M.quizaccess_wifiresilience || {};
M.quizaccess_wifiresilience.watching = {
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
     * Initialise the watching code.
     *
     * @method String
     * @param {String} keyname the key, which will be saved in indexedDb
     */
    init: function(wathclist) {
      wathclist = wathclist.replace(/\\\//g, "/");

      this.form = Y.one(this.SELECTORS.QUIZ_FORM);
      if (!this.form) {
          Y.log('No response form found. Why did you try to set up download?', 'debug', '[ETHz-SW] Live Watching');
          return;
      }

      quizaccess_wifiresilience_progress_step = 9;
      $("#quizaccess_wifiresilience_result").html(M.util.get_string('loadingstep9', 'quizaccess_wifiresilience'));



      Y.log('Watching Live Scripts/XHR requests Initialised. (Only Initialised if Watch List is filled in WIFI-Config in admin pages)', 'debug', '[ETHz-SW] Live Watching');

      wifi_xhr_args = '';
      function wifi_get_xhr_args(){
        return wifi_xhr_args;
      }
      function wifi_set_xhr_args(val){
        wifi_xhr_args = val;
      }
      function wifi_get_watch_list(){
        return wathclist;

      }
      (function(open) {
          XMLHttpRequest.prototype.open = function(ev) {

              wifi_set_xhr_args(arguments);
              this.addEventListener("readystatechange", function(e) {
                  var wifi_args =  wifi_get_xhr_args();
                  var whatlist = wifi_get_watch_list();
                  if(whatlist.indexOf(wifi_args[1]) !== -1){
                    var livewatchel = document.querySelector('#quizaccess_wifiresilience_hidden_livewatch_status');

                    if (this.readyState == 4) {
                        if(this.status == 200) {
                          livewatchel.value = 1;
                          M.quizaccess_wifiresilience.autosave.livewatch = true;
                          Y.log('Intercepted Live Watch Script with status: '+this.status+'. Timer is running normal.', 'debug', '[ETHz-SW] Live Watching')
                        } else {
                           livewatchel.value = 0;
                            M.quizaccess_wifiresilience.autosave.livewatch = false;
                            Y.log('Intercepted Live Watch Script with status: '+this.status+'. Stop Timer now until Server/Internet is responding.', 'debug', '[ETHz-SW] Live Watching');
                          }
                    }

                  }

              }, false);

             open.apply(this, arguments);

          };
      })(XMLHttpRequest.prototype.open);


  //    var quizaccess_wifiresilience_progress = $(".quizaccess_wifiresilience_progress .quizaccess_wifiresilience_bar");
      quizaccess_wifiresilience_progress.animate({
        width: examviewportmaxwidth * 9 / 10 + "px"
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
