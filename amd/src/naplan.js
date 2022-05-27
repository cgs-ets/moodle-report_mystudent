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

// /**
//  * @package   report_mystudent
//  * @copyright 2022 Veronica Bermegui
//  * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
//  */

define(['jquery', 'report_mystudent/chart', 'core/ajax', 'core/modal_factory'], function ($, Chart, Ajax, ModalFactory) {
    'use strict';

    function init() {
        getNaplanResult();
        open_naplan_scale();
        close_naplan_scale();

        window.onclick = function (event) {
            var modal = document.getElementById("myModal");
            if (event.target == document.getElementById("myModal")) {
                modal.style.display = "none";
            }
        }
    }

    function getNaplanResult() {

        const username = document.querySelector('[data-username]').getAttribute('data-username');

        document.querySelector('.card-top-naplan').firstElementChild.style.display = "flex"

        Ajax.call([{
            methodname: 'report_mystudent_get_naplan_result',
            args: {
                username: username,
            },

            done: function (response) {
                const htmlResult = response.result;
                renderBarChar(htmlResult);

            },

            fail: function (reason) {
                // remove spinner
                document.querySelector('.card-body-naplan').firstElementChild.style.display = "none"
                document.querySelector('.card-top-naplan').firstElementChild.style.display = "none"

                const pelement = document.createElement('p');
                pelement.textContent = "There was a problem when getting data. Please try again later";
                document.getElementById('chart-naplan').appendChild(pelement);
            }
        }]);

    };


    function renderBarChar(result) {
        const ctx = document.getElementById("chart-naplan");
        if (!ctx) {
            return;
        }

        const results = JSON.parse(result);
        const datasets = [];

        for (let i = 0; i < results.datasets.length; i++) {
            let data = {
                label: results.datasets[i].label,
                data: results.datasets[i].results,
                backgroundColor: results.datasets[i].backgroundcolor,
                borderColor: results.datasets[i].backgroundcolor,
                tension: 0.4,
                borderWidth: 0,
                borderRadius: 4,
                borderSkipped: false,
                borderWidth: 1,

            }
            datasets.push(data);
        }


        const config = {
            type: 'bar',
            labels: results.labels,
            data: {
                labels: results.labels,
                datasets: datasets,
            },
            options: {
                scales: {
                    y: {
                        suggestedMax: 10,
                        min: 1,
                        title: {
                            text: 'Band',
                            display: true,
                        }
                    },
                },
                interaction: {
                    mode: 'point',
                    intersect: false,
                },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const belowStd = 'Below the national minimun standard';
                                const minStd = 'National minimun standard';
                                const aboveStd = 'Above the national minimun standard';
                                if (context.dataset.label == 'Year 3') {
                                    if (context.parsed.y >= 3) {
                                        return context.dataset.label + ": Band " + context.parsed.y + '. ' + aboveStd;
                                    } else if (context.parse.y == 2) {
                                        return context.dataset.label + ": Band " + context.parsed.y + '. ' + minStd;
                                    } else {
                                        return context.dataset.label + ": Band " + context.parsed.y + '. ' + belowStd;
                                    }
                                } else if (context.dataset.label == 'Year 5') {
                                    if (context.parsed.y >= 5) {
                                        return context.dataset.label + ": Band " + context.parsed.y + '. ' + aboveStd;
                                    } else if (context.parse.y == 4) {
                                        return context.dataset.label + ": Band " + context.parsed.y + '. ' + minStd;
                                    } else {
                                        return context.dataset.label + ": Band " + context.parsed.y + '. ' + belowStd;
                                    }
                                } else if (context.dataset.label == 'Year 7') {
                                    if (context.parsed.y >= 6) {
                                        return context.dataset.label + ": Band " + context.parsed.y + '. ' + aboveStd;
                                    } else if (context.parse.y == 5) {
                                        return context.dataset.label + ": Band " + context.parsed.y + '. ' + minStd;
                                    } else {
                                        return context.dataset.label + ": Band " + context.parsed.y + '. ' + belowStd;
                                    }
                                } else if (context.dataset.label == 'Year 9') {
                                    if (context.parsed.y >= 7) {
                                        return context.dataset.label + ": Band " + context.parsed.y + '. ' + aboveStd;
                                    } else if (context.parse.y == 6) {
                                        return context.dataset.label + ": Band " + context.parsed.y + '. ' + minStd;
                                    } else {
                                        return context.dataset.label + ": Band " + context.parsed.y + '. ' + belowStd;
                                    }
                                }
                            },
                        }
                    },
                },
                legend: {
                    position: 'top',
                },

            }
        };
        // remove spinner
        document.querySelector('.card-top-naplan').firstElementChild.style.display = "none";
        const myChart = new Chart(ctx, config);

    }

    function open_naplan_scale() {
        $('a.naplan-scale').on('click', function (e) {
            var modal = document.getElementById("myModal");
            modal.style.display = "block";
        });
    }

    function close_naplan_scale() {
        $('i.cgs-naplan-close').on('click', function (e) {

            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        });
    }

    return {
        init: init
    }
});