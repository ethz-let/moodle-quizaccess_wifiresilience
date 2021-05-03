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
 * Generates uploaded_syncedfiles.php datatables tables.
 *
 * @module moodle-quizaccess_wifiresilience-initialisesyncedfiles
 */

/**
 * Initialises uploaded_syncedfiles.php
 *
 * @class M.quizaccess_wifiresilience.initialisesyncedfiles
 */

M.quizaccess_wifiresilience = M.quizaccess_wifiresilience || {};
M.quizaccess_wifiresilience.initialisesyncedfiles = {
    /**
     * The selectors used throughout this class.
     *
     * @property SELECTORS
     * @private
     * @type Object
     * @static
     */
    SELECTORS: {
        SEARCH: '#syncedfilessearch'
    },
    _table: null,
    _tableData: null,

    /**
     * Initialise the code.
     *
     * @method String
     * @param {String} keyname the key, which will be saved in indexedDb
     */
    init: function(tableData) {

        this._tableData = tableData
        this._table = new Y.DataTable({
            columns: [
            {
                key: "user",
                label: M.util.get_string('user', 'moodle'),
                allowHTML: true,
                sortable: true,
                title: M.util.get_string('user', 'moodle')
            },
            {
                key: "attempt",
                label: M.util.get_string('attempt', 'quizaccess_wifiresilience'),
                allowHTML: true,
                sortable: true,
                title: M.util.get_string('attempt', 'quizaccess_wifiresilience'),
            },
            {
                key: "date",
                label: M.util.get_string('date', 'moodle'),
                allowHTML: true,
                sortable: true,
                title: M.util.get_string('date', 'moodle'),
            },
            {
                key: "type",
                label: M.util.get_string('filetype', 'quizaccess_wifiresilience'),
                allowHTML: true,
                sortable: true,
                title: M.util.get_string('filetype', 'quizaccess_wifiresilience')
            },
            {
                key: "file",
                label: M.util.get_string('file', 'moodle'),
                allowHTML: true,
                title: M.util.get_string('file', 'moodle')
            },
            {
                key: "reference",
                label: M.util.get_string('reference', 'quizaccess_wifiresilience'),
                allowHTML: true,
                sortable: true,
                title: M.util.get_string('reference', 'quizaccess_wifiresilience')
            },
            ],
            data: this._tableData,
            pagedData: {
                location: 'footer',
                pageSizes: [10, 20, 100, 'all'],
                rowsPerPage: 10,
                pageLinks: 5
            },
            editable: true
        });
        this._table.render('#datatable');

        Y.one(this.SELECTORS.SEARCH).on('keyup', this.search_table, this);

    },
    search_table: function (e) {
        var result = [];
        var searchText = Y.one(this.SELECTORS.SEARCH).get('value');

        for (var i = 0; i < this._tableData.length; i++) {
            for (var key in this._tableData[i]) {
                if (key == "file") {
                    continue;
                }
                if ((this._tableData[i][key]).toString().toLowerCase().includes(searchText)) {
                    result.push(this._tableData[i]);
                    break;
                }
            }
        }

        this._table.set('data', result);
    }
};