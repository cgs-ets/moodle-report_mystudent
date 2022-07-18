<?php
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
 *
 * @package    report_mystudent
 * @copyright 2021 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_mystudent\assignments_report;

use moodle_url;

/**
 * Returns the context for the template
 * @return array
 */

// Get the assignments info that is set in Synergetic. It has all the summary.
function get_assign_template_context($username) {

    $assignments = get_assignment_context($username);

    return $assignments;
}

function get_assignment_context($username) {
    $results = get_assignments_by_student_id($username);

    if (empty($results)) {
        return [];
    }

    $assessments = [];
    $terms = ['username' => $username];

    foreach ($results as $result) {

        $assignmentsummary = new \stdClass();
        $assignmentsummary->heading = $result->heading;
        $assignmentsummary->result = $result->result;
        $assignmentsummary->outof = $result->markoutof;
        $assignmentsummary->classdescription = $result->classdescription;
        $assignmentsummary->weighting = (floatval(round($result->weightingfactor, 2))) * 100;
        $assignmentsummary->testdate = (new \DateTime($result->testdate))->format('d/m/Y');
        $assessments[$result->term][$result->weeknumber][] = $assignmentsummary;
    }

    $dummycell = new \stdClass();
    $dummycell->t = '';

    for ($i = 0; $i < 6; $i++) {
        $dummycells[] = $dummycell;
    }

    foreach ($assessments as $t => $weeks) {

        $term = new \stdClass();
        $term->term = $t;
        $term->dummycell = '';
        $term->results = [];

        foreach ($weeks as $wn => $assesment) {
            foreach ($assesment as $assess) {
                $assess->week = $wn;
                $term->results[] = $assess;
            }
        }

        $terms['assessments']['details'][] = $term;
    }

    return $terms;
}

/**
 * Call to the SP.
 */
function get_assignments_by_student_id($username) {

    try {

        $config = get_config('report_mystudent');

        // Last parameter (external = true) means we are not connecting to a Moodle database.
        $externaldb = \moodle_database::get_driver_instance($config->dbtypeassign, 'native', true);

        // Connect to external DB.
        $externaldb->connect($config->dbhost, $config->dbuserassign, $config->dbpassassign, $config->dbnameassign, '');

        $sql = 'EXEC ' . $config->dbspassignments . ' :id';

        $params = array(
            'id' => $username,
        );

        $assignments = $externaldb->get_records_sql($sql, $params);

        return $assignments;
    } catch (\Exception $ex) {
        throw $ex;
    }
}

// Get the courses id this student is part of.
function get_student_enrollments($userid, $idonly = true) {
    global $DB;
    $now = new \DateTime("now", \core_date::get_server_timezone_object());
    $year = $now->format('Y');

    $sql = "SELECT c.id , c.shortname as 'course name', c.idnumber 
            FROM mdl_user_enrolments ue
            INNER JOIN mdl_enrol e ON ue.enrolid = e.id
            INNER JOIN mdl_course c ON c.id = e.courseid
            WHERE userid = $userid and e.status = 0 and c.idnumber like '%$year%'"; //TODO: make it dinamic.


    $params_array = ['userid' => $userid, 'idnumber' => $year];
    $r = $DB->get_records_sql($sql, $params_array);

    $results = $idonly ? array_keys($r) :  $r;
    return $results;
}

function get_assessments_by_course($userid) {
    global $DB;
    $assessmentids = implode(',', array_column(get_assessment_submission_records($userid), 'assignment'));
    $result = [];

    if ($assessmentids != '') {
        $sql = "SELECT grades.id as gradeid, u.id as userid, u.firstname, u.lastname, c.id as courseid, c.shortname as coursename, grades.assignment as assignmentid, assign.name as 'assignmentname', assign.duedate
                FROM {assign_grades} AS grades
                JOIN {assign} as assign ON grades.assignment = assign.id
                JOIN {user} as u ON grades.userid = u.id
                JOIN {course} as c ON c.id = assign.course
                WHERE grades.assignment  IN ($assessmentids)
                AND grades.grade != -1.00000
                AND u.id = $userid
                ORDER BY c.shortname";
        $result = $DB->get_records_sql($sql);
    }


    return $result;
}

// Get the assesments from the courses this student is part of.

function get_assessment_submission_records($userid, $cid = null, $asid = null) {
    global $DB;

    $coursesid = $cid == null ? implode(',', get_student_enrollments($userid)) : $cid;

    if ($asid == null) {
        $sql = "SELECT id FROM mdl_assign WHERE course IN ($coursesid) ";
        $assignids = implode(',', array_keys($DB->get_records_sql($sql)));
    } else {
        $assignids = $asid;
    }

    $sql = "SELECT * FROM {assign} AS assign
            JOIN {assign_submission} AS asub
            ON asub.assignment = assign.id
            JOIN {files} as f  ON f.itemid = asub.id
            WHERE f.userid = ? 
            AND f.filearea = ? 
            AND assign.id IN ($assignids) 
            AND assign.course IN ($coursesid)
            AND asub.status = 'submitted'
            AND f.contextid <> 1
            ORDER BY asub.attemptnumber DESC, f.filename ASC";

    $params_array = ['userid' => $userid, 'filearea' => 'submission_files'];

    $results = $DB->get_records_sql($sql, $params_array);

    return $results;
}

function get_course_module($courseid, $assessids) {
    global $DB;

    $sql = "SELECT cm.instance, cm.id as cmid FROM mdl_course_modules AS cm
    JOIN  mdl_modules AS m ON cm.module = m.id
    WHERE cm.course = ? AND cm.instance IN ($assessids) AND m.name = 'assign'; ";
    $params_array = ['course' => $courseid];

    $results = ($DB->get_records_sql($sql, $params_array));

    return $results;
}

function get_cgs_connect_activities_context($userid) {
    $assessments = get_assessments_by_course($userid);
    $data = [];

    foreach ($assessments as $assess) {
        $userassessment = new \stdClass();
        $userassessment->coursename =  $assess->coursename;
        $userassessment->assignmentid =  $assess->assignmentid;
        $cmids = get_course_module($assess->courseid, $assess->assignmentid);
        $cmid = $cmids[$assess->assignmentid]->cmid;

        $assignmentrealurl = new moodle_url("/mod/assign/view.php", ['id' => $cmid]);
        $assignmenturlparent = new moodle_url("/local/parentview/get.php", ['addr' => $assignmentrealurl, 'user' => $userid]);

        $userassessment->assignmentnameurl = $assignmenturlparent;
        $userassessment->assignmentname = $assess->assignmentname;

        $userassessment->duedate =  userdate($assess->duedate, get_string('strftimedatefullshort', 'core_langconfig'));

        $data['assign'][] = $userassessment;
    }

    return $data;
}
