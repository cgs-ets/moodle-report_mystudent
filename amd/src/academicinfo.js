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

define(['jquery', 'core/log', 'report_mystudent/chart', 'core/ajax'], function ($, Log, Chart, Ajax) {
    'use strict';

    function init(origin) {
        if (origin == 'dashboard') {
            getGradesEffortTrend();
        } else { // Im in the view for this section.
            document.getElementById("searchReportInput").addEventListener('keyup', filterYear);
            document.getElementById("searchByClassGradeInput").addEventListener('keyup', filterByClass);
            document.getElementById("searchByClassEffortInput").addEventListener('keyup', filterByClass);
            document.querySelector('.pop-up-grade').addEventListener('click', popup);
            document.querySelector('.pop-up-effort').addEventListener('click', popup);
            document.querySelectorAll('.file-pdf').forEach(function (icon) {
                icon.addEventListener('click', displayReportService)
            });
           
            getEffortHistory();
            getAssign();
            getGradeHistory(); // Takes the longest
        }
    }

    function getGradesEffortTrend() {

        const username = document.querySelector('[data-username]').getAttribute('data-username');
        const campus = document.querySelector('[data-campus]').getAttribute('data-campus');

        document.getElementById("overlay").style.display = "flex";
        document.querySelector('.card-img-academic').firstElementChild.style.display = "flex";

        Ajax.call([{
            methodname: 'report_mystudent_get_grade_effort_trend',
            args: {
                username: username,
                campus: campus
            },

            done: function (response) {
                const htmlResult = response.result;
                Log.debug(htmlResult);
               
                if (campus == 'Senior') {
                    trendChartSenior(htmlResult);
                } else {
                    trendChartPrimary(htmlResult);
                }
            },

            fail: function (reason) {
                Log.debug(reason);
                // remove spinner
                document.querySelector('.card-img-academic').firstElementChild.style.display = "none";
                $('#card-body-academic-info-text').replaceWith('<p class="card-text alert alert-danger" id ="card-body-academic-info-text" >Data not available. Please try later</p>');
            }
        }]);

    };

    function trendChartSenior(result) {
        const isEmpty = result == null || Object.keys(result).length === 0;
        if (isEmpty) {
            document.querySelector('.card-img-academic').firstElementChild.style.display = "none";
            $('#card-body-academic-info-text').replaceWith('<p class="card-text alert alert-danger" id ="card-body-academic-info-text" >Data not available.</p>');
        } else {

            const ctx = document.getElementById("chart-academic");
            if (!ctx) {
                return;
            }
    
            const performance = JSON.parse(result);
            let labels = [];
            let sets = [];
            let attendance = [];
            let effort = [];
            let grades = [];
            let gradeperterm = [];
    
            const TAGS = {
                avgattendance: 'Average Attendance',
                avgeffort: 'Average Effort',
                avggrades: 'Average Grade',
            }
    
            for (let i = 0; i < performance.length; i++) {
                var p = performance[i];
    
                const year = p.details.year.toString();
                const term = p.details.term.toString();
    
                labels.push(['T' + term, year]);
                gradeperterm.push(p.details.avggrades);
    
                grades.push(p.details.avggrades);
                effort.push(p.details.avgeffort)
                attendance.push(p.details.avgattendance)
    
            }
    
            sets.push({
                label: TAGS.avggrades,
                data: grades,
                fill: false,
                borderColor: '#31326f',
                backgroundColor: '#31326f',
                tension: 0.1,
                spanGaps: true
            });
    
            sets.push({
                label: TAGS.avgeffort,
                data: effort,
                fill: false,
                borderColor: '#ffc93c',
                backgroundColor: '#ffc93c',
                tension: 0.1,
                spanGaps: true
            });
    
            sets.push({
                label: TAGS.avgattendance,
                data: attendance,
                fill: false,
                borderColor: '#1687a7',
                backgroundColor: '#1687a7',
                tension: 0.1,
                spanGaps: true
            });
    
    
            const data = {
                labels: labels,
                datasets: sets
            };
    
            const options = {
                responsive: true,
                maintainAspectRatio: false,
    
            }
    
            // remove spinner
            document.querySelector('.card-img-academic').firstElementChild.style.display = "none";
    
            new Chart(ctx, {
                type: 'line',
                data: data,
                options: options,
    
            });
        }

    }

    function trendChartPrimary(result) {
        const ctx = document.getElementById("chart-academic");
        if (!ctx) {
            return;
        }

        const performance = JSON.parse(result);

        let labels = [];
        let sets = [];
        let attendance = [];
        let effort = [];
        let grades = [];

        const TAGS = {
            avgattendance: 'Average Attendance',
            avgeffort: 'Average Effort',
            avggrades: 'Average Grade',
        }

        const TAGS_EFFORTS_DESC = {
            e: 'Excellent (E)',
            vg: 'Very Good (VG)',
            avg: 'Average (AVG)',
            ni: 'Needs Improvement (NI)',
        }

        const TAGS_GRADES_DESC = {
            be: 'Below Expectations',
            gs: 'Good start (GS)',
            ms: 'Making strides (MS)',
            grwi: 'Go run with it (GRWI)',
            ae: 'Above expectations'
        }

        for (let i = 0; i < performance.length; i++) {
            var p = performance[i];

            const year = p.details.year.toString();
            const term = p.details.term.toString();

            labels.push(['T' + term, year]);

            if (p.details.avggrades == null) {
                p.details.avggrades = undefined;
            }
            grades.push(p.details.avggrades);

            if (p.details.effortaverage == null) {
                p.details.effortaverage = undefined;
            }
            effort.push(p.details.effortaverage);

            if (p.details.percentageattended == null) {
                p.details.percentageattended = undefined;
            }
            attendance.push(p.details.percentageattended);

        }

        sets.push({
            label: TAGS.avggrades,
            data: grades,
            fill: false,
            borderColor: '#31326f',            
            backgroundColor: '#31326f',
            tension: 0.1,
            spanGaps: true
        });

        sets.push({
            label: TAGS.avgeffort,
            data: effort,
            fill: false,
            borderColor: '#ffc93c',
            backgroundColor: '#ffc93c',
            tension: 0.1,
            spanGaps: true
        });

        sets.push({
            label: TAGS.avgattendance,
            data: attendance,
            fill: false,
            borderColor: '#1687a7',
            backgroundColor: '#1687a7',
            tension: 0.1,
            spanGaps: true
        });

        const data = {
            labels: labels,
            datasets: sets
        };

        const options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {

                            if (context.dataset.label == TAGS.avggrades) {

                                if (context.parsed.y > 0.00 && context.parsed.y <= 20.00) {
                                    return TAGS_GRADES_DESC.be;
                                } else if (context.parsed.y > 20.00 && context.parsed.y <= 40.00) {
                                    return TAGS_GRADES_DESC.gs
                                } else if (context.parsed.y > 40.00 && context.parsed.y <= 60.00) {
                                    return TAGS_GRADES_DESC.ms;
                                } else if (context.parsed.y > 60.00 && context.parsed.y <= 80.00) {
                                    return TAGS_GRADES_DESC.grwi;
                                } else if (context.parsed.y > 80.00) {
                                    return TAGS_GRADES_DESC.ae
                                }
                            }
                            if (context.dataset.label == TAGS.avgeffort) {
                                if (parseFloat(context.parsed.y) > 0.00 && parseFloat(context.parsed.y) <= 25.00) {
                                    return TAGS_EFFORTS_DESC.ni;
                                } else if (parseFloat(context.parsed.y) > 25.00 && parseFloat(context.parsed.y) <= 50.00) {
                                    return TAGS_EFFORTS_DESC.avg
                                } else if (parseFloat(context.parsed.y) > 50.00 && parseFloat(context.parsed.y) < 95.00) {
                                    return TAGS_EFFORTS_DESC.vg;
                                } else if (parseFloat(context.parsed.y) >= 95.00) {
                                    return TAGS_EFFORTS_DESC.e;
                                }
                            } else {

                                return context.parsed.y + "%";
                            }
                        }
                    }
                }
            },
            scales: {
                y: {
                    suggestedMin: 25,
                    display: true,

                }
            }
        }

        /// remove spinner
        document.querySelector('.card-img-academic').firstElementChild.style.display = "none";

        new Chart(ctx, {
            type: 'line',
            data: data,
            options: options,
        });
    }


    function filterYear() {
        // Declare variables

        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchReportInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("reports-table");
        tr = table.getElementsByTagName("tr");

        // Loop through all table rows, and hide those who don't match the search query
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[2];
            if (td) {
                txtValue = td.textContent || td.innerText;
                const date = txtValue.split('/').pop()
                if (date.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    function filterByClass(e) {
        // Declare variables
        var input, filter, table, tr, td, i, txtValue;
        const inputid = e.target.id;
        input = document.getElementById(inputid); //"searchByClassInput searchByClassGradeInput"
        filter = input.value.toUpperCase();
        table = inputid == "searchByClassGradeInput" ? document.getElementById("grade-history-table") : document.getElementById("effort-history-table");
        tr = table.getElementsByTagName("tr");

        // Loop through all table rows, and hide those who don't match the search query
        for (i = 1; i < tr.length; i++) {
            var trclasslist = tr[i].classList;
            if (trclasslist.contains('reports-heading') || trclasslist.contains('my-student-learning-title')) continue;
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    function displayReportService(e) {
        
        const tdocumentsseq = e.target.getAttribute('data-tdss');
        Ajax.call([{
            methodname: 'report_mystudent_get_student_academic_report',
            args: {
                tdocumentsseq: tdocumentsseq
            },

            done: function (response) {
                const base64Data = JSON.parse(response.blob);
                displayReport(base64Data);
            },

            fail: function (reason) {
                Log.error('block_academic_report_get_student_report: Unable to get blob.');
                Log.debug(reason);
            }
        }]);


    };

    async function displayReport(base64Data) {
        const base64Response = await fetch(`data:application/pdf;base64,${base64Data}`);
        const blob = await base64Response.blob();
        var blobURL = URL.createObjectURL(blob);
        window.open(blobURL);
    }

    function getGradeHistory() {

        const element = document.querySelector('.cgs-dashboard-academic-info-container');
        const username = element.getAttribute('data-username');
        const campus = element.getAttribute('data-campus');
        // Add spinner.
        $('#grade-history-tb').removeAttr('hidden');

        Ajax.call([{
            methodname: 'report_mystudent_get_grade_history',
            args: {
                username: username,
                campus: campus
            },

            done: function (response) {
                Log.debug(response);
                const htmlResult = response.html;
                $('#grade-history-tb').attr('hidden', true);
                $('[data-region="grade-history-table"]').replaceWith(htmlResult);
                //enable expand button
                $('.pop-up-effort').removeAttr('disabled');

            },

            fail: function (reason) {
                Log.error('report_mystudent: Unable to get context.');
                Log.debug(reason);
                $('#grade-history-tb').attr('hidden', true);
                $('[data-region="grade-history-table"]').replaceWith('<p class="alert alert-danger">Data not available. Please try later</p>');
            }
        }]);


    }

    function getEffortHistory() {

        const element = document.querySelector('.cgs-dashboard-academic-info-container');
        const username = element.getAttribute('data-username');
        const campus = element.getAttribute('data-campus');
        // Add spinner.
        $('#effort-history-tb').removeAttr('hidden');

        Ajax.call([{
            methodname: 'report_mystudent_get_effort_history',
            args: {
                username: username,
                campus: campus
            },

            done: function (response) {
                Log.debug(response);
                const htmlResult = response.html;
                $('#effort-history-tb').attr('hidden', true);
                $('[data-region="effort-history-table"]').replaceWith(htmlResult);
                $('.pop-up-grade').removeAttr('disabled');

            },

            fail: function (reason) {
                Log.error('report_mystudent: Unable to get context.');
                Log.debug(reason);
                $('#effort-history-tb').attr('hidden', true);
                $('[data-region="effort-history-table"]').replaceWith('<p class="alert alert-danger">Data not available. Please try later</p>');
            }
        }]);

    }

    function getAssign() {
        const element = document.querySelector('.cgs-dashboard-academic-info-container');
        const username = element.getAttribute('data-username');

        // Add spinner.
        $('#assignment-tb').removeAttr('hidden');

        Ajax.call([{
            methodname: 'report_mystudent_get_assign_context',
            args: {
                username: username
            },

            done: function (response) {
                const htmlResult = response.html;
                $('#assignment-tb').attr('hidden', true);
                $('[data-region="assignment-table"]').replaceWith(htmlResult);

            },

            fail: function (reason) {
                Log.error('report_mystudent: Unable to get context.');
                Log.debug(reason);
                $('#assignment-tb').attr('hidden', true);
                $('[data-region="assignment-table"]').replaceWith('<p class="alert alert-danger">Data not available. Please try later</p>');
            }
        }]);


    }


    function popup(e) {
        var container;
        var modalcontainer;
        //replace the icon
        e.target.classList.remove('fa-external-link');
        e.target.classList.add('fa-window-close');
        e.target.setAttribute('title', 'Close');
        Y.log(e);

        if (e.target.classList.contains('pop-up-grade')) {
            container = document.querySelector('.pop-up-grade').closest('div.cgs-grade-history-container');
            modalcontainer = container.closest('div.cgs-grade-main-container');
            document.getElementById('grade-history-table').closest('div.grades-his').classList.add('modal-history-content');
            modalcontainer.classList.add('modal-history-background');
            document.querySelector('.pop-up-grade.fa-window-close').addEventListener('click', closePopup);
            document.querySelector('.pop-up-grade').removeEventListener('click', popup);


        } else {
            container = document.querySelector('.pop-up-effort').closest('div.cgs-effort-history-container');
            modalcontainer = container.closest('div.cgs-effort-main-container');
            document.getElementById('effort-history-table').closest('div.effort-his').classList.add('modal-history-content');
            modalcontainer.classList.add('modal-history-background');
            document.querySelector('.pop-up-effort.fa-window-close').addEventListener('click', closePopup);
            document.querySelector('.pop-up-effort').removeEventListener('click', popup);
        }
    }

    function closePopup(e) {
        const gradepopup = e.target.classList.contains('pop-up-grade');
        const modalcontentdiv = e.target.classList.contains('pop-up-grade') ? e.target.closest("div.grades-his") : e.target.closest("div.effort-his");
        modalcontentdiv.classList.remove('modal-history-content');

        if (gradepopup) {
            (modalcontentdiv.closest('.cgs-grade-main-container')).classList.remove('modal-history-background');
        } else {
            (modalcontentdiv.closest('.cgs-effort-main-container')).classList.remove('modal-history-background');
        }

        // Add the listener for the close
        e.target.classList.add('fa-external-link');
        e.target.classList.remove('fa-window-close');
        e.target.setAttribute('title', 'Expand');

        document.querySelector('.pop-up-grade').removeEventListener('click', closePopup);
        document.querySelector('.pop-up-effort').removeEventListener('click', closePopup);
        document.querySelector('.pop-up-grade').addEventListener('click', popup);
        document.querySelector('.pop-up-effort').addEventListener('click', popup);
    }


    // function popupfeedback(e) {
    //     Y.log("popupfeedback");
    //     Y.log(e);
    //     e.target.classList.remove('fa-external-link');
    //     e.target.classList.add('fa-window-close');
    //     e.target.setAttribute('title', 'Close');


    //     const container = e.target.closest('div.feedbackcomment-modal');
    //     const containerContact = e.target.closest('div.feedbackcomment-modal-content');

    //     container.classList.add('modal-history-background');
    //     containerContact.classList.add('modal-history-content');
    //     containerContact.classList.add('feedbackcomment-modal-content-expanded');

    //     e.target.removeEventListener('click', popupfeedback);
    //     e.target.addEventListener('click', popupfeedbackclose);

    // }

    // function popupfeedbackclose(e) {
    //     e.target.classList.add('fa-external-link');
    //     e.target.classList.remove('fa-window-close');
    //     e.target.setAttribute('title', 'Open');

    //     const container = e.target.closest('div.feedbackcomment-modal');
    //     const containerContact = e.target.closest('div.feedbackcomment-modal-content');


    //     container.classList.remove('modal-history-background');
    //     containerContact.classList.remove('modal-history-content');
    //     containerContact.classList.remove('feedbackcomment-modal-content-expanded');


    //     e.target.addEventListener('click', popupfeedback);
    //     e.target.removeEventListener('click', popupfeedbackclose);
    // }

    return {
        init: init
    }
});