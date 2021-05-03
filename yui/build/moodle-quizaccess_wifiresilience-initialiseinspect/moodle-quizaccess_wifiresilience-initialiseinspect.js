YUI.add('moodle-quizaccess_wifiresilience-initialiseinspect', function (Y, NAME) {

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
 * Generates download links.
 *
 * @module moodle-quizaccess_wifiresilience-initialiseinspect
 */

/**
 * Initialises inspect.php
 *
 * @class M.quizaccess_wifiresilience.initialiseinspect
 */

M.quizaccess_wifiresilience = M.quizaccess_wifiresilience || {};
M.quizaccess_wifiresilience.initialiseinspect = {

    /**
     * Initialise the code.
     *
     * @method String
     * @param {String} keyname the key, which will be saved in indexedDb
     */
    init: function() {

        function download_emergency_file_wifi_exam(whicharea) {

            var mydiv = document.getElementById(whicharea);
            var aTag = document.createElement("a");
            aTag.innerHTML = M.util.get_string('downloadfile', 'quizaccess_wifiresilience');
            which_textarea = whicharea.replace("_link","");

            var blob = new Blob([document.getElementById(which_textarea).value], {type: "octet/stream"});
            var url = window.URL.createObjectURL(blob);

            aTag.href = url;
            aTag.download = which_textarea + ".inspect";
            mydiv.appendChild(aTag);
        }

        download_emergency_file_wifi_exam("quizaccess_wifiresilience_dectypted_file_output_json_link");
        download_emergency_file_wifi_exam("quizaccess_wifiresilience_dectypted_file_output_json_nokey_link");
        download_emergency_file_wifi_exam("quizaccess_wifiresilience_dectypted_file_output_array_link");
    }
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
    ]
});