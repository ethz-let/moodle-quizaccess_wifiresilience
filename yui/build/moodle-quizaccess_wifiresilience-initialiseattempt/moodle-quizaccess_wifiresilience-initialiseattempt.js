YUI.add('moodle-quizaccess_wifiresilience-initialiseattempt', function (Y, NAME) {

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
 * Sets up attempt page and precaches exam images.
 *
 * @module moodle-quizaccess_wifiresilience-initialiseattempt
 */

/**
 * Initialises attempt.php
 *
 * @class M.quizaccess_wifiresilience.initialiseattempt.
 */

M.quizaccess_wifiresilience = M.quizaccess_wifiresilience || {};
M.quizaccess_wifiresilience.initialiseattempt = {

    /**
     * Initialise the code.
     *
     * @method String
     * @param {String} keyname the key, which will be saved in indexedDb
     */
    init: function(cleanexamname, examstoragekeyname, page, fetchandlogconfig, fetchandlog) {

        quizaccess_wifiresilience_progress = $(".quizaccess_wifiresilience_progress .quizaccess_wifiresilience_bar");
        var examviewportmaxwidth = $(window).width();
        if (!examviewportmaxwidth || examviewportmaxwidth == 0 || examviewportmaxwidth == 'undefined') {
            examviewportmaxwidth = 1200;
        }

        quizaccess_wifiresilience_progress_step = 1;

        $("#quizaccess_wifiresilience_result").html(M.util.get_string('loadingstep1', 'quizaccess_wifiresilience', cleanexamname));
        quizaccess_wifiresilience_progress.animate({
            width: examviewportmaxwidth * 2 / 10 + "px"
        });

        $(window).on('pageshow',function() {
        });

        $(document).ready(function() {
            quizaccess_wifiresilience_progress_step = 2;

            $("#quizaccess_wifiresilience_result").html(M.util.get_string('loadingstep2', 'quizaccess_wifiresilience'));
            quizaccess_wifiresilience_progress.animate({
                width: examviewportmaxwidth * 3 / 10 + "px"
            });

            quizaccess_wifiresilience_progress_step = 10;
            $("#quizaccess_wifiresilience_result").html(M.util.get_string('loadingstep10', 'quizaccess_wifiresilience'));
            quizaccess_wifiresilience_progress.animate({
                width: "100%"
            });

            // What if forced to go to summary page to force sumbission after leaving the quiz and coming back?
            if (typeof(M.quizaccess_wifiresilience.navigation) != "undefined") {

                // log in SESSION_STORAGE the original start time, so if refresh happens while offline, we still know
                // How long we got before the end of the exam instead of loading caches original exam length.
                // We will match the original start time with the recorded one in storage. (Match will happen in module.js)
                // Check SessionStorage browser support

                if (typeof(Storage) !== "undefined") {
                    var sessionstorage_exam_key = examstoragekeyname;
                    localStorage.setItem('current_exam', sessionstorage_exam_key);
                }
            }

            // To be sure, sure.. save per question! For future, not now.. Enable in ROUND-2
            // M.quizaccess_wifiresilience.localforage.save_html_per_question();

            if (typeof(M.quizaccess_wifiresilience.navigation) != "undefined") {
                // Only if Timer auto submit enabled.
                if (M.mod_quiz.timer.endtime && M.mod_quiz.timer.endtime != 0 && M.mod_quiz.timer.endtime != "undefined") {
                    wifiresilience_window_load_time = (new Date().getTime()) - M.pageloadstarttime.getTime() + 12000;
                }
            }

            if (typeof(M.quizaccess_wifiresilience.navigation) != "undefined") {
                setTimeout( function() {
                    Y.all(M.quizaccess_wifiresilience.navigation.SELECTORS.ALL_PAGE_DIVS).addClass(
                        "quizaccess_wifiresilience_hidden"
                    );
                    Y.one(M.quizaccess_wifiresilience.navigation.SELECTORS.QUIZ_FORM).removeClass(
                        "quizaccess_wifiresilience_hidden"
                    );
                    Y.one(M.quizaccess_wifiresilience.navigation.SELECTORS.PAGE_DIV_ROOT + page).removeClass(
                        "quizaccess_wifiresilience_hidden"
                    );

                    $("#quizaccess_wifiresilience_overlay").fadeOut();
                }  , 10000 );
            } else {

                $("#quizaccess_wifiresilience_overlay").fadeOut();
            }
        });

        // Flags and pre-cache specific static exam images (Flag, unflag, laoding etc).
        const staticExamFlagging = 'Wifiresilience-SW-flags';
        const quizaccess_wifiresilience_flagging = async function(url) {
            try {
                const response = await fetch(url, {mode: "cors", credentials: "include"});
                if (response.ok) {
                    console.log("[Wifiresilience-SW] ServiceWorker: Exam Static Flags SUCCESSFUL:\n > " + url);
                }
            } catch(error) {
                console.log("[Wifiresilience-SW] ServiceWorker: Exam Static Flags ERROR:\n > " + url, error);
                return;
            }
        };

        quizaccess_wifiresilience_flagging(M.util.image_url('i/flagged'));
        quizaccess_wifiresilience_flagging(M.util.image_url('i/unflagged'));
        quizaccess_wifiresilience_flagging(M.util.image_url('navflagged', 'quiz'));
        quizaccess_wifiresilience_flagging(M.util.image_url('mod/quiz/flag-on', 'theme'));
        quizaccess_wifiresilience_flagging(M.util.image_url('i/loading_small'));
        quizaccess_wifiresilience_flagging(M.util.image_url('f/folder-24', 'core'));

        // Prefetch Script.
        const quizaccess_wifiresilience_fetch_and_log = async function(url) {
            try {
                const response = await fetch(url, {mode: "cors", credentials: "include" });
                if (response.ok) {
                    console.log("[Wifiresilience-SW] ServiceWorker: Prefetch Attachments/Embedded Files " +
                        "(as per fetchandlog setting in quiz) is SUCCESSFUL:\n > " + url);
                } else {
                    console.log("[Wifiresilience-SW] ServiceWorker: Prefetch Attachments/Embedded Files " +
                        "(as per fetchandlog setting in quiz) has FAILED:\n > " + url);
                }
            } catch(error) {
                console.log("[Wifiresilience-SW] ServiceWorker: Prefetch Attachments/Embedded Files " +
                    "(as per fetchandlog setting in quiz) ERROR:\n > " + url, error);
                return;
            }
        };

        if (fetchandlogconfig) {
            var links = fetchandlog.split(/\r\n|\r|\n/)
            if (links.length != 0) {
                for (var key in links) {
                    var oldLink = links[key]
                    var cleanedlink = links[key].split('pluginfile.php').join('mod/quiz/accessrule/wifiresilience/examfile.php');

                    var element = $("[src*='" + oldLink + "']").attr("src");
                    if (element) {
                        $("[src*='" + oldLink + "']").attr("src", cleanedlink);
                    }
                    quizaccess_wifiresilience_fetch_and_log(cleanedlink);
                }
            }
        }
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
        "core_question_engine",
        "mod_quiz"
    ]
});