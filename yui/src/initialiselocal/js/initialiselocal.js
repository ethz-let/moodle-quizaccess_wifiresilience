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
 * Generates download tables.
 *
 * @module moodle-quizaccess_wifiresilience-initialiselocal
 */

/**
 * Initialises local.php.
 *
 * @class M.quizaccess_wifiresilience.initialiselocal
 */

M.quizaccess_wifiresilience = M.quizaccess_wifiresilience || {};
M.quizaccess_wifiresilience.initialiselocal = {

    delete_indb_record: function (key) {

        var confirmdelete = confirm(M.util.get_string('localconfirmdeletelocal', 'quizaccess_wifiresilience', key));
        if (confirmdelete) {
            responses_store.removeItem(key);
            document.getElementById('indb_row_' + key).style.display = 'none';
        }
    },

    delete_localstorage_record: function (key) {

        var confirmdelete = confirm(M.util.get_string('localconfirmdeletestatus', 'quizaccess_wifiresilience', key));
        if (confirmdelete) {
            status_store.removeItem(key);
            document.getElementById('localstorage_row_' + key).style.display = 'none';
        }
    },

    /**
     * Initialise the code.
     *
     * @method String
     * @param {String} keyname the key, which will be saved in indexedDb
     */
    init: function(startwithkey) {

        function quizaccess_wifiresilience_create_rows(tableid,rownumber,data) {

            var table = document.getElementById(tableid);
            var row = table.insertRow(rownumber);
            row.id = 'indb_row_' + data['key'];

            var cell1 = row.insertCell(0);
            cell1.innerHTML = data['key'];

            var cell2 = row.insertCell(1);
            cell2.innerHTML = data['key'];

            var cell3 = row.insertCell(2);
            cell3.innerHTML = data['key'];

            var cell4 = row.insertCell(3);
            cell4.innerHTML = '<a href="#" id="download_indb_ls_' + rownumber + '">' +
                M.util.get_string('download', 'quizaccess_wifiresilience') + '</a>';

            var dlink_element = document.getElementById("download_indb_ls_" + rownumber);

            var blob = new Blob([data['responses']], {type: "octet/stream"});
            var url = window.URL.createObjectURL(blob);

            dlink_element.setAttribute('href', url);
            dlink_element.setAttribute('download', data['key'] + '.eth');

            var cell5 = row.insertCell(4);
            cell5.innerHTML = '<a href="#" id="delete_indb_ls_' + rownumber +
                '" onclick="M.quizaccess_wifiresilience.initialiselocal.delete_indb_record(' +
                "'" + data['key'] + "'" + ')">' +
                M.util.get_string('delete', 'quizaccess_wifiresilience') + '</a>';
        }

        function quizaccess_wifiresilience_localstorage_create_rows(tableid,rownumber,data) {

            var table = document.getElementById(tableid);
            var row = table.insertRow(rownumber);
            row.id = 'localstorage_row_' + data['key'];

            var cell1 = row.insertCell(0);
            cell1.innerHTML = data['key'];

            var cell2 = row.insertCell(1);
            cell2.innerHTML = data['key'];

            var cell3 = row.insertCell(2);
            cell3.innerHTML = data['key'];

            var cell4 = row.insertCell(3);
            cell4.innerHTML = '<a href="#" id="download_localstorage_ls_' + rownumber + '">' +
                M.util.get_string('download', 'quizaccess_wifiresilience') + '</a>';

            var dlink_element = document.getElementById("download_localstorage_ls_" + rownumber);

            var blob = new Blob([data['responses']], {type: "octet/stream"});
            var url = window.URL.createObjectURL(blob);

            dlink_element.setAttribute('href', url);
            dlink_element.setAttribute('download', data['key'] + '.eth');

            var cell5 = row.insertCell(4);
            cell5.innerHTML = '<a href="#" id="delete_localstorage_ls_' +
                rownumber + '" onclick="M.quizaccess_wifiresilience.initialiselocal.delete_localstorage_record(' +
                "'" + data['key'] + "'" + ')">' +
                M.util.get_string('delete', 'quizaccess_wifiresilience') + '</a>';
        }

        responses_store = localforage.createInstance({
            name: "Wifiresilience-exams-responses"
        });
        status_store = localforage.createInstance({
            name: "Wifiresilience-exams-question-status"
        });

        responses_store.startsWith(startwithkey).then(function(results) {
            var localforagedata = {};
            var row = 0;
            var foundx = 0;

            for (var ldbindex in results) {
                foundx = 1;
                localforagedata = {key: ldbindex, responses: results[ldbindex]};
                row ++;
                quizaccess_wifiresilience_create_rows("quizaccess_wifiresilience-indexeddb-table", row, localforagedata);
            }

            if (foundx == 0) {
                var table = document.getElementById('quizaccess_wifiresilience-indexeddb-table');
                var row = table.insertRow(1);
                var cell = row.insertCell(0);
                cell.innerHTML = M.util.get_string('localnorecordsfound', 'quizaccess_wifiresilience');
                cell.colSpan = 5;
            }
        });

        status_store.startsWith(startwithkey).then(function(results) {
            var localforagedata = {};
            var row = 0;
            var found = 0;

            for (var ldbindex in results) {
                found = 1;
                localforagedata = {key: ldbindex, responses: results[ldbindex]};
                row ++;
                quizaccess_wifiresilience_localstorage_create_rows("quizaccess_wifiresilience-localstorage-table", row, localforagedata);
            }

            if (found == 0) {
                var table = document.getElementById('quizaccess_wifiresilience-localstorage-table');
                var row = table.insertRow(1);
                var cell = row.insertCell(0);
                cell.innerHTML = M.util.get_string('localnorecordsfound', 'quizaccess_wifiresilience');
                cell.colSpan = 5;
            }
        });
    }
};