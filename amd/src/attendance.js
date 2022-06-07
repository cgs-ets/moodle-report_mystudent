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

define(['jquery', 'core/ajax', 'core/log', 'report_mystudent/chart'], function ($, Ajax, Log, Chart) {
    'use strict';

    function init(origin) {
        Log.debug("mystudent: attendance report : initialising controls");
        var control = new AttendanceControl(origin);
        control.main(origin);
    }

    /**
     * AttendanceControl a single block_assignmentsquizzes_report block instance contents.
     *
     * @constructor
     */
    function AttendanceControl(origin) {
        let self = this;

        if (origin != 'dashboard') {
            const element = document.getElementById('rollmarking-container');
            const username = element.getAttribute('data-username');
            const campus = element.getAttribute('data-campus');
            self.username = username;
            self.campus = campus;
        }
    }

    /**
     * Run the controller.
     *
     */
    AttendanceControl.prototype.main = function (origin) {
        let self = this;
        if (origin != 'dashboard') {
            self.setupEvents();
        } else {
            self.getAttendanceChart();
        }

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
                $('[data-region="attendance-rollmarking"]').replaceWith('<p class="alert alert-danger">Data not available. Please try later</p>');
            }
        }]);

    };

    AttendanceControl.prototype.getAttendanceChart = function () {
        const self = this;
        const userid = document.querySelector('.card-deck').getAttribute('data-userid')
        const campus = document.querySelector('[data-campus]').getAttribute('data-campus');

        // document.getElementById('chart-attendance').nextElementSibling.removeAttribute('hidden');
        document.querySelector('.card-img-attendance').firstElementChild.style.display = "flex";


        Ajax.call([{
            methodname: 'report_mystudent_get_attendance_by_term',
            args: {
                userid: userid,
                campus: campus
            },

            done: function (response) {
                const htmlResult = response.result;
                Log.debug(htmlResult);
                Log.debug(self);
                self.renderAttendanceBarChar(htmlResult);

            },

            fail: function (reason) {
                Log.debug(reason);
                // remove spinner
                //document.querySelector('.card-body-attendance').firstElementChild.style.display = "none";
                document.querySelector('.card-img-attendance').firstElementChild.style.display = "none";
                $('#card-body-attendance-info-text').replaceWith('<p class="card-text alert alert-danger" id ="card-body-attendance-info-text" >Data not available. Please try later</p>');
            }
        }]);

    }

    AttendanceControl.prototype.renderAttendanceBarChar = function (results) {
        const ctx = document.getElementById("chart-attendance");

        if (!ctx) {
            return;
        }

        const result = JSON.parse(results);
        const data = [];
        const labels = [];

        for (let i = 0; i < result.length; i++) {

            labels.push(`Term ${result[i].filesemester}`);
            data.push(result[i].totalpercentageattended);
           
        }

        const config = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Term',
                    tension: 0.4,
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                    borderColor: '#ffc93c',
                    backgroundColor: '#ffc93c',
                    data: data,
                    maxBarThickness: 6
                }]

            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        suggestedMax: 100,
                        min: 1,
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                Y.log(context);
                                return `${context.parsed.y} %`
                            },
                        }
                    },
                },
            },
        };

        // remove spinner
    //    document.querySelector('.card-body-attendance').firstElementChild.style.display = "none";
       document.querySelector('.card-img-attendance').firstElementChild.style.display = "none";
        const myChart = new Chart(ctx, config);
    }




    return {
        init: init
    }
});