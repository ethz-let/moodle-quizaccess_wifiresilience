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
/* eslint camelcase: off */

/**
 * JavaScript library for the quiz module.
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.mod_quiz = M.mod_quiz || {};

M.mod_quiz.init_attempt_form = function(Y) {
    require(['core_question/question_engine'], function(qEngine) {
        qEngine.initForm('#responseform');
    });
    Y.on('submit', M.mod_quiz.timer.stop, '#responseform');
    require(['core_form/changechecker'], function(FormChangeChecker) {
        FormChangeChecker.watchFormById('responseform');
    });
};

M.mod_quiz.init_attempt_form = function(Y) {
    M.core_question_engine.init_form(Y, '#responseform');
    Y.on('submit', M.mod_quiz.timer.stop, '#responseform');
    require(['core_form/changechecker'], function(FormChangeChecker) {
        FormChangeChecker.watchFormById('responseform');
    });
};

M.mod_quiz.init_review_form = function(Y) {
    M.core_question_engine.init_form(Y, '.questionflagsaveform');
    Y.on('submit', function(e) { e.halt(); }, '.questionflagsaveform');
};

M.mod_quiz.init_comment_popup = function(Y) {
    // Add a close button to the window.
    var closebutton = Y.Node.create('<input type="button" class="btn btn-secondary" />');
    closebutton.set('value', M.util.get_string('cancel', 'moodle'));
    Y.one('#id_submitbutton').ancestor().append(closebutton);
    Y.on('click', function() { window.close() }, closebutton);
}

// Code for updating the countdown timer that is used on timed quizzes.
M.mod_quiz.timer = {
    // YUI object.
    Y: null,

    // Timestamp at which time runs out, according to the student's computer's clock.
    endtime: 0,

    // Is this a quiz preview?
    preview: 0,

    // Original start time.
    start_time: 0,
    // Threshold for updating time remaining, in milliseconds.
    threshold: 3000,
    timer_offline_counter: 0,

    // This records the id of the timeout that updates the clock periodically,
    // so we can cancel.
    timeoutid: null,

    // Threshold for updating time remaining, in milliseconds.
    threshold: 3000,

    /**
     * @param Y the YUI object
     * @param start, the timer starting time, in seconds.
     * @param preview, is this a quiz preview?
     */
    init: function(Y, start, preview) {
        var page_loaded_time = M.pageloadstarttime.getTime();
        var attemptid = Y.one('input[name=attempt]').get('value');

        // For reload on offline - with time compensation.
        var registered_end_time = sessionStorage.getItem('wifiresilience-secondsleft-' + attemptid);

        if (registered_end_time != null && registered_end_time != 'undefined') {
            // Time has changed.
            if (Number(registered_end_time) != start) {
                start = Number(registered_end_time);
            }
        }
        M.mod_quiz.timer.Y = Y;
        M.mod_quiz.timer.endtime = page_loaded_time + start * 1000;
        M.mod_quiz.timer.preview = preview;
        M.mod_quiz.timer.start_time = start;
        M.mod_quiz.timer.process_offline_refresh_attempt_time(attemptid);

        window.addEventListener('load', function() {
          //  exam_extra_page_load_time = 500 + (new Date().getTime()) - page_loaded_time;
            var actualstarttimeinput = Y.one('#actualstarttimeinput');
            exam_extra_page_load_time = actualstarttimeinput.get('value');
            M.mod_quiz.timer.endtime += Number(exam_extra_page_load_time);
            M.mod_quiz.timer.update();
            Y.one('#quiz-timer-wrapper').setStyle('display', 'flex');
            Y.log('Added extra time to compensate for page load & waiting, milliseconds: [' + exam_extra_page_load_time + ']', 'debug', '[Wifiresilience-SW] Timer');
        });
        require(['core_form/changechecker'], function(FormChangeChecker) {
            M.mod_quiz.timer.FormChangeChecker = FormChangeChecker;
        });
        Y.one('#toggle-timer').on('click', function() {
            M.mod_quiz.timer.toggleVisibility();
        });
    },

        /**
     * Toggle the timer's visibility.
     */
        toggleVisibility: function() {
            var Y = M.mod_quiz.timer.Y;
            var timer = Y.one('#quiz-time-left');
    
            // If the timer is currently hidden, the visibility should be set to true and vice versa.
            this.setVisibility(timer.getAttribute('hidden') === 'hidden');
        },
    
        /**
         * Set visibility of the timer.
         * @param visible whether the timer should be visible
         * @param updatePref whether the new status should be stored as a preference
         */
        setVisibility: function(visible, updatePref = true) {
            var Y = M.mod_quiz.timer.Y;
            var timer = Y.one('#quiz-time-left');
            var button = Y.one('#toggle-timer');
    
            if (visible) {
                button.setContent(M.util.get_string('hide', 'moodle'));
                timer.show();
            } else {
                button.setContent(M.util.get_string('show', 'moodle'));
                timer.hide();
            }
    
            // Only update the user preference if this has been requested.
            if (updatePref) {
                require(['core_user/repository'], function(UserRepository) {
                    UserRepository.setUserPreference('quiz_timerhidden', (visible ? '0' : '1'));
                });
            }
    
        },
    /**
     * Stop the timer, if it is running.
     */
    stop: function(e) {
        if (M.mod_quiz.timer.timeoutid) {
            clearTimeout(M.mod_quiz.timer.timeoutid);
        }
    },
    /**
     * check if user refreshed while offline.
     */
    process_offline_refresh_attempt_time: function(attemptid) {

        // Check SessionStorage browser support.
        if(typeof(Storage) !== "undefined") {

            var last_calculated_end_time = sessionStorage.getItem('wifiresilience-offline-' + attemptid);
            if(last_calculated_end_time && last_calculated_end_time != null && last_calculated_end_time != 'undefined'){
                M.mod_quiz.timer.endtime += Number(last_calculated_end_time);
                Y.log('Exam page got refreshed while in offline mode while actively attempting (same session). New Exam End Time: ' +
                    M.mod_quiz.timer.endtime, 'debug', '[Wifiresilience-SW] Timer');
            } else {
                Y.log('There is no record for any effective offline time. Exam Start Time - as per moodle timer - is: [' +
                    M.mod_quiz.timer.endtime + ']', 'debug', '[Wifiresilience-SW] Timer');
            }
        } else {
            Y.log('Session Storage not supported. Exam Start Time - as per moodle timer - is: [' +
                M.mod_quiz.timer.endtime + ']', 'debug', '[Wifiresilience-SW] Timer');
        }
    },
    /**
     * Function to convert a number between 0 and 99 to a two-digit string.
     */
    two_digit: function(num) {
        if (num < 10) {
            return '0' + num;
        } else {
            return num;
        }
    },

    // Function to update the clock with the current time left, and submit the quiz if necessary.
    update: function() {
        var Y = M.mod_quiz.timer.Y;
        M.mod_quiz.timer.endtime += 1000 * M.quizaccess_wifiresilience.autosave.last_real_disconnection_time;

        var secondsleft = Math.floor((M.mod_quiz.timer.endtime - new Date().getTime()) / 1000);

        // If time has expired, set the hidden form field that says time has expired and submit
        if (secondsleft < 0) {
            M.mod_quiz.timer.stop(null);
            Y.one('#quiz-time-left').setContent(M.util.get_string('timesup', 'quiz'));
            var input = Y.one('input[name=timeup]');
            input.set('value', 1);
            var form = input.ancestor('form');
            if (form.one('input[name=finishattempt]')) {
                form.one('input[name=finishattempt]').set('value', 0);
            }
            M.mod_quiz.timer.FormChangeChecker.markFormSubmitted(input.getDOMNode());
            M.quizaccess_wifiresilience.navigation.navigate_to_page(-1);

             $(".qn_buttons").hide();
             $(".qn_buttons").hide();
             $("#quizaccess_wifiresilience-attempt_page--1 h2").hide();
             $("#quizaccess_wifiresilience-attempt_page--1 h3").hide();
             $(".quizsummaryofattempt").hide();
             $('#quizaccess_wifiresilience_returntoattempt').hide();
             $("input[name=next]").hide();
             $("input[name=previous]").hide();
             $("[id^=quizaccess_wifiresilience-attempt_page-]").addClass('quizaccess_wifiresilience_disallow_edit');
             $("#quizaccess_wifiresilience-attempt_page--1").removeClass('quizaccess_wifiresilience_disallow_edit');

             $("#responseform").on('submit', function (evt) {
                 evt.preventDefault();
                 M.quizaccess_wifiresilience.autosave.submit_and_finish(evt);
             });

             $("#responseform").trigger('submit', M.quizaccess_wifiresilience.autosave);
             return;
            return;
        }

        // If time has nearly expired, change the colour.
        if (secondsleft < 100) {
            Y.one('#quiz-timer').removeClass('timeleft' + (secondsleft + 2))
                    .removeClass('timeleft' + (secondsleft + 1))
                    .addClass('timeleft' + secondsleft);
        }

        // Update the time display.
        var hours = Math.floor(secondsleft / 3600);
        secondsleft -= hours * 3600;
        var minutes = Math.floor(secondsleft / 60);
        secondsleft -= minutes * 60;
        var seconds = secondsleft;
        Y.one('#quiz-time-left').setContent(hours + ':' +
                M.mod_quiz.timer.two_digit(minutes) + ':' +
                M.mod_quiz.timer.two_digit(seconds));

        // Arrange for this method to be called again soon.
        M.mod_quiz.timer.timeoutid = setTimeout(M.mod_quiz.timer.update, 100);
    },
    // Allow the end time of the quiz to be updated.
    updateEndTime: function(timeleft) {
        var newtimeleft = new Date().getTime() + timeleft * 1000;

        // Only update if change is greater than the threshold, so the
        // time doesn't bounce around unnecessarily.
        if (Math.abs(newtimeleft - M.mod_quiz.timer.endtime) > M.mod_quiz.timer.threshold) {
            M.mod_quiz.timer.endtime = newtimeleft;
            M.mod_quiz.timer.update();
        }
    }
};

M.mod_quiz.filesUpload = {
    /**
     * YUI object.
     */
    Y: null,

    /**
     * Number of files uploading.
     */
    numberFilesUploading: 0,

    /**
     * Disable navigation block when uploading and enable navigation block when all files are uploaded.
     */
    disableNavPanel: function() {
        var quizNavigationBlock = document.getElementById('mod_quiz_navblock');
        if (quizNavigationBlock) {
            if (M.mod_quiz.filesUpload.numberFilesUploading) {
                quizNavigationBlock.classList.add('nav-disabled');
            } else {
                quizNavigationBlock.classList.remove('nav-disabled');
            }
        }
    }
};

M.mod_quiz.nav = M.mod_quiz.nav || {};

M.mod_quiz.nav.update_flag_state = function(attemptid, questionid, newstate) {
    var Y = M.mod_quiz.nav.Y;
    var navlink = Y.one('#quiznavbutton' + questionid);
    navlink.removeClass('flagged');
    if (newstate == 1) {
        navlink.addClass('flagged');
        navlink.one('.accesshide .flagstate').setContent(M.util.get_string('flagged', 'question'));
    } else {
        navlink.one('.accesshide .flagstate').setContent('');
    }
};

M.mod_quiz.nav.init = function(Y) {
    M.mod_quiz.nav.Y = Y;

    Y.all('#quiznojswarning').remove();

    var form = Y.one('#responseform');
    if (form) {
        function nav_to_page(pageno) {
            Y.one('#followingpage').set('value', pageno);

            // Automatically submit the form. We do it this strange way because just
            // calling form.submit() does not run the form's submit event handlers.
            var submit = form.one('input[name="next"]');
            submit.set('name', '');
            submit.getDOMNode().click();
        };

        Y.delegate('click', function(e) {
            if (this.hasClass('thispage')) {
                return;
            }

            e.preventDefault();

            var pageidmatch = this.get('href').match(/page=(\d+)/);
            var pageno;
            if (pageidmatch) {
                pageno = pageidmatch[1];
            } else {
                pageno = 0;
            }

            var questionidmatch = this.get('href').match(/#question-(\d+)-(\d+)/);
            if (questionidmatch) {
                form.set('action', form.get('action') + questionidmatch[0]);
            }

            nav_to_page(pageno);
        }, document.body, '.qnbutton');
    }

    if (Y.one('a.endtestlink')) {
        Y.on('click', function(e) {
            e.preventDefault();
            nav_to_page(-1);
        }, 'a.endtestlink');
    }

    // Navigation buttons should be disabled when the files are uploading.
    require(['core_form/events'], function(formEvent) {
        document.addEventListener(formEvent.eventTypes.uploadStarted, function() {
            M.mod_quiz.filesUpload.numberFilesUploading++;
            M.mod_quiz.filesUpload.disableNavPanel();
        });

        document.addEventListener(formEvent.eventTypes.uploadCompleted, function() {
            M.mod_quiz.filesUpload.numberFilesUploading--;
            M.mod_quiz.filesUpload.disableNavPanel();
        });
    });

    if (M.core_question_flags) {
        M.core_question_flags.add_listener(M.mod_quiz.nav.update_flag_state);
    }
};

M.mod_quiz.secure_window = {
    init: function(Y) {
        if (window.location.href.substring(0, 4) == 'file') {
            window.location = 'about:blank';
        }
        Y.delegate('contextmenu', M.mod_quiz.secure_window.prevent, document, '*');
        Y.delegate('mousedown',   M.mod_quiz.secure_window.prevent_mouse, 'body', '*');
        Y.delegate('mouseup',     M.mod_quiz.secure_window.prevent_mouse, 'body', '*');
        Y.delegate('dragstart',   M.mod_quiz.secure_window.prevent, document, '*');
        Y.delegate('selectstart', M.mod_quiz.secure_window.prevent_selection, document, '*');
        Y.delegate('cut',         M.mod_quiz.secure_window.prevent, document, '*');
        Y.delegate('copy',        M.mod_quiz.secure_window.prevent, document, '*');
        Y.delegate('paste',       M.mod_quiz.secure_window.prevent, document, '*');
        Y.on('beforeprint', function() {
            Y.one(document.body).setStyle('display', 'none');
        }, window);
        Y.on('afterprint', function() {
            Y.one(document.body).setStyle('display', 'block');
        }, window);
        Y.on('key', M.mod_quiz.secure_window.prevent, '*', 'press:67,86,88+ctrl');
        Y.on('key', M.mod_quiz.secure_window.prevent, '*', 'up:67,86,88+ctrl');
        Y.on('key', M.mod_quiz.secure_window.prevent, '*', 'down:67,86,88+ctrl');
        Y.on('key', M.mod_quiz.secure_window.prevent, '*', 'press:67,86,88+meta');
        Y.on('key', M.mod_quiz.secure_window.prevent, '*', 'up:67,86,88+meta');
        Y.on('key', M.mod_quiz.secure_window.prevent, '*', 'down:67,86,88+meta');
    },

    is_content_editable: function(n) {
        if (n.test('[contenteditable=true]')) {
            return true;
        }
        n = n.get('parentNode');
        if (n === null) {
            return false;
        }
        return M.mod_quiz.secure_window.is_content_editable(n);
    },

    prevent_selection: function(e) {
        return false;
    },

    prevent: function(e) {
        alert(M.util.get_string('functiondisabledbysecuremode', 'quiz'));
        e.halt();
    },

    prevent_mouse: function(e) {
        if (e.button == 1 && /^(INPUT|TEXTAREA|BUTTON|SELECT|LABEL|A)$/i.test(e.target.get('tagName'))) {
            // Left click on a button or similar. No worries.
            return;
        }
        if (e.button == 1 && M.mod_quiz.secure_window.is_content_editable(e.target)) {
            // Left click in Atto or similar.
            return;
        }
        e.halt();
    },

    init_close_button: function(Y, url) {
        Y.on('click', function(e) {
            M.mod_quiz.secure_window.close(url, 0)
        }, '#secureclosebutton');
    },

    close: function(url, delay) {
        setTimeout(function() {
            if (window.opener) {
                window.opener.document.location.reload();
                window.close();
            } else {
                window.location.href = url;
            }
        }, delay * 1000);
    }
};
