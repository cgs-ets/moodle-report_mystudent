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

define(['jquery', 'core/log', 'report_mystudent/chart'], function ($, Log, Chart) {
    'use strict';

    function init(naplanscale) {

        $("#scale-info").hover(function () {
            $(this).append($('<img id = "scaleimg" src=" ' + naplanscale + '"' + '>'));
        }, function () {
            $(this).find("img").last().remove();
        });
        renderBarChar();
    }


    function renderBarChar() {
        const ctx = document.getElementById("resultsChart");
        if (!ctx) {
            return;
        }

        const results = JSON.parse(ctx.getAttribute('data-results'));
        const datasets = [];

        for (let i = 0; i < results.datasets.length; i++) {
            let data = {
                label: results.datasets[i].label,
                data: results.datasets[i].results,
                backgroundColor: results.datasets[i].backgroundcolor,
                borderColor: results.datasets[i].backgroundcolor,
                borderWidth: 1
            }
            datasets.push(data);
        }

        Log.debug(datasets);

        Chart.register({
            id: 'custom_naplan_canvas_background_color',
            beforeDraw: (chart) => {
                const ctx = chart.canvas.getContext('2d');
                ctx.save();
                ctx.globalCompositeOperation = 'destination-over';
                ctx.fillStyle = '#f6f5f5';
                ctx.fillRect(0, 0, chart.width, chart.height);
                ctx.restore();
            }
        });

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
                    custom_naplan_canvas_background_color: true
                },
                legend: {
                    position: 'top',
                },

            }
        };

        const myChart = new Chart(ctx, config);

    }

    return {
        init: init
    }
});