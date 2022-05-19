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
 * @package   report_mystudent
 * @copyright 2022 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/log'], function ($, Ajax, Log) {
    'use strict';

    function init() {
        Log.debug("mystudent: attendance report : initialising controls");
        const element = document.getElementById('rollmarking-container');
        const username = element.getAttribute('data-username');
        const campus = element.getAttribute('data-campus');
        var control = new AttendanceControl(username, campus);
        control.main();
    }

    /**
     * AttendanceControl a single block_assignmentsquizzes_report block instance contents.
     *
     * @constructor
     */
    function AttendanceControl(username, campus) {
        let self = this;
        self.username = username;
        self.campus = campus;
    }

    /**
     * Run the controller.
     *
     */
    AttendanceControl.prototype.main = function () {
        let self = this;
        self.setupEvents();

    };

    AttendanceControl.prototype.setupEvents = function () {
        let self = this;

        $('.attendance-rollmarking').on('custom.getAttendanceRollmarking', function () {
            self.getAttendanceRollmarking();
        });

        $('.attendance-rollmarking').click(function () {
            $(this).trigger("custom.getAttendanceRollmarking");
        });
    };

    AttendanceControl.prototype.getAttendanceRollmarking = function () {
        let self = this;
        const username = self.username;
        const campus = self.campus;

        // Add spinner.
        $('#attendance-based-on-rollmarking-table').removeAttr('hidden');
        $('#attendance-based-on-rollmarking-show').toggle(); // Carret down.
        $('#attendance-based-on-rollmarking-hide').toggle(); // Carret right

        Ajax.call([{
            methodname: 'report_mystudent_get_attendance_rollmarking_context',
            args: {
                username: username,
                campus: campus
            },

            done: function (response) {
                const htmlResult = response.html;
               
                $('#attendance-based-on-rollmarking-table').attr('hidden', true);
                $('[data-region="attendance-rollmarking"]').replaceWith(htmlResult);
            },

            fail: function (reason) {
                Log.error('block_attendance_report_get_attendance_rollmarking_context: Unable to get context.');
                Log.debug(reason);
                $('[data-region="rm-table-container"]').replaceWith('<p class="alert alert-danger">Data not available. Please try later</p>');
            }
        }]);

    };


    return {
        init: init
    }
});