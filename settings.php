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
 * Links and settings
 *
 * Contains settings used by mystudents report.
 *
 * @package    report_mystudent
 * @copyright  2022 Veronica Bermegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('report_mystudent', '', ''));

    $options = array('', "mysqli", "oci", "pdo", "pgsql", "sqlite3", "sqlsrv");
    $options = array_combine($options, $options);


    // Attendance Section.
    $settings->add(new admin_setting_heading('report_mystudent_attendance', get_string('attendance', 'report_mystudent'), ''));

    $settings->add(new admin_setting_configselect(
        'report_mystudent/dbtype',
        get_string('dbtype', 'report_mystudent'),
        get_string('dbtype_desc', 'report_mystudent'),
        '',
        $options
    ));


    $settings->add(new admin_setting_configtext('report_mystudent/dbhost', get_string('dbhost', 'report_mystudent'), get_string('dbhost_desc', 'report_mystudent'), 'localhost'));

    $settings->add(new admin_setting_configtext('report_mystudent/dbuser', get_string('dbuser', 'report_mystudent'), '', ''));

    $settings->add(new admin_setting_configpasswordunmask('report_mystudent/dbpass', get_string('dbpass', 'report_mystudent'), '', ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbname', get_string('dbname', 'report_mystudent'), '', ''));


    $settings->add(new admin_setting_configtext('report_mystudent/dbattbyterm', get_string('dbattbyterm', 'report_mystudent'), get_string('dbattbyterm_desc', 'report_mystudent'), ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbattbyclass', get_string('dbattbyclass', 'report_mystudent'), get_string('dbattbyclass_desc', 'report_mystudent'), ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbattbytermbyid', get_string('dbattbytermbyid', 'report_mystudent'), get_string('dbattbytermbyid_desc', 'report_mystudent'), ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbattbytermbyidprimary', get_string('dbattbytermbyidprimary', 'report_mystudent'), get_string('dbattbytermbyidprimary_desc', 'report_mystudent'), ''));

    $settings->add(new admin_setting_configtext('report_mystudent/profileurl', get_string('profileurl', 'report_mystudent'), get_string('profileurl_desc', 'report_mystudent'), ''));

    // Academic infogandf
    $settings->add(new admin_setting_heading('report_mystudent_academicinfo', get_string('gandf', 'report_mystudent'), ''));

    // Grades Effort info 

    $settings->add(new admin_setting_configselect(
        'report_mystudent/dbtypegandf',
        get_string('dbtypegandf', 'report_mystudent'),
        get_string('dbtypegandf_desc', 'report_mystudent'),
        '',
        $options
    ));


    $settings->add(new admin_setting_configtext('report_mystudent/dbhostgandf', get_string('dbhostgandf', 'report_mystudent'), get_string('dbhostgandf_desc', 'report_mystudent'), 'localhost'));

    $settings->add(new admin_setting_configtext('report_mystudent/dbusergandf', get_string('dbusergandf', 'report_mystudent'), '', ''));

    $settings->add(new admin_setting_configpasswordunmask('report_mystudent/dbpassgandf', get_string('dbpassgandf', 'report_mystudent'), '', ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbnamegandf', get_string('dbnamegandf', 'report_mystudent'), get_string('dbnamegandf_desc', 'report_mystudent'), 'localhost'), '');

    $settings->add(new admin_setting_configtext('report_mystudent/dbaccgrades', get_string('dbaccgradessenior', 'report_mystudent'), get_string('dbaccgrades_desc', 'report_mystudent'), ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbaccgradesprimary', get_string('dbaccgradesprimary', 'report_mystudent'), get_string('dbaccgrades_desc', 'report_mystudent'), ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbefforthistory', get_string('dbefforthistorysenior', 'report_mystudent'), get_string('dbefforthistory_desc', 'report_mystudent'), ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbefforthistoryprimary', get_string('dbefforthistoryprimary', 'report_mystudent'), get_string('dbefforthistory_desc', 'report_mystudent'), ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbperformancetrend', get_string('dbperformancetrend', 'report_mystudent'), get_string('dbperformancetrend_desc', 'report_mystudent'), ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbprimaryperformancetrend', get_string('dbprimaryperformancetrend', 'report_mystudent'), get_string('dbperformancetrend_desc', 'report_mystudent'), ''));

    //$settings->add(new admin_setting_configtext('report_mystudent/profileurl', get_string('profileurl', 'report_mystudent'), get_string('profileurl_desc', 'report_mystudent'), ''));

    // Naplan

    $settings->add(new admin_setting_heading('report_mystudent_naplan', get_string('naplan', 'report_mystudent'), ''));

    $settings->add(new admin_setting_configselect(
        'report_mystudent/dbtypenaplan',
        get_string('dbtypenaplan', 'report_mystudent'),
        get_string('dbtypenaplan_desc', 'report_mystudent'),
        '',
        $options
    ));


    $settings->add(new admin_setting_configtext('report_mystudent/dbhostnaplan', get_string('dbhost', 'report_mystudent'), get_string('dbhost_desc', 'report_mystudent'), 'localhost'));

    $settings->add(new admin_setting_configtext('report_mystudent/dbusernaplan', get_string('dbuser', 'report_mystudent'), '', ''));

    $settings->add(new admin_setting_configpasswordunmask('report_mystudent/dbpassnaplan', get_string('dbpass', 'report_mystudent'), '', ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbnamenaplan', get_string('dbname', 'report_mystudent'), '', ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbspnaplanresult', get_string('dbspnaplanresult', 'report_mystudent'), get_string('dbspnaplanresult_desc', 'report_mystudent'), ''));

    $settings->add(new admin_setting_configtext('report_mystudent/profileurl', get_string('profileurl', 'report_mystudent'), get_string('profileurl_desc', 'report_mystudent'), ''));

    $settings->add(new admin_setting_configtext('report_mystudent/bcyear3', get_string('bcyear3', 'report_mystudent'), get_string('bcyear3_desc', 'report_mystudent'), 'rgba(255, 99, 132, 0.2)'));

    $settings->add(new admin_setting_configtext('report_mystudent/bcyear5', get_string('bcyear5', 'report_mystudent'), get_string('bcyear5_desc', 'report_mystudent'), 'rgba(255, 206, 86, 0.2)'));

    $settings->add(new admin_setting_configtext('report_mystudent/bcyear7', get_string('bcyear7', 'report_mystudent'), get_string('bcyear7_desc', 'report_mystudent'), 'rgba(153, 102, 255, 0.2)'));

    $settings->add(new admin_setting_configtext('report_mystudent/bcyear9', get_string('bcyear9', 'report_mystudent'), get_string('bcyear9_desc', 'report_mystudent'), 'rgba(153, 102, 255, 0.2)'));

    // Academic Report.
    $settings->add(new admin_setting_heading('report_mystudent_academicreport', get_string('academicreport', 'report_mystudent'), ''));
    $settings->add(new admin_setting_configselect(
        'report_mystudent/dbtypeacademicreport',
        get_string('dbtypeacademicreport', 'report_mystudent'),
        get_string('dbtypeacademicreport_desc', 'report_mystudent'),
        '',
        $options
    ));

    $settings->add(new admin_setting_configtext('report_mystudent/dbhostacademicreport', get_string('dbhostacademicreport', 'report_mystudent'), get_string('dbhostacademicreport_desc', 'report_mystudent'), 'localhost'));

    $settings->add(new admin_setting_configtext('report_mystudent/dbuseracademicreport', get_string('dbuseracademicreport', 'report_mystudent'), '', ''));

    $settings->add(new admin_setting_configpasswordunmask('report_mystudent/dbpassacademicreport', get_string('dbpassacademicreport', 'report_mystudent'), '', ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbnameacademicreport', get_string('dbnameacademicreport', 'report_mystudent'), '', ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbspstudentreportdocs', get_string('dbspstudentreportdocs', 'report_mystudent'), get_string('dbspstudentreportdocs_desc', 'report_mystudent'), ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbspsretrievestdreport', get_string('dbspsretrievestdreport', 'report_mystudent'), get_string('dbspsretrievestdreport_desc', 'report_mystudent'), ''));

    // $settings->add(new admin_setting_configtext('report_mystudent/profileurl', get_string('profileurl', 'report_mystudent'), get_string('profileurl_desc', 'block_attendance_report'), ''));

    // Assignments

    $settings->add(new admin_setting_heading('report_mystudent_assignmentreport', get_string('assig_report', 'report_mystudent'), ''));

    $options = array('', "mysqli", "oci", "pdo", "pgsql", "sqlite3", "sqlsrv");
    $options = array_combine($options, $options);

    $settings->add(new admin_setting_configselect(
        'report_mystudent/dbtypeassign',
        get_string('dbtypeassign', 'report_mystudent'),
        get_string('dbtypeassign_desc', 'report_mystudent'),
        '',
        $options
    ));

    $settings->add(new admin_setting_configtext('report_mystudent/dbhostassign', get_string('dbhost', 'report_mystudent'), get_string('dbhost_desc', 'report_mystudent'), 'localhost'));

    $settings->add(new admin_setting_configtext('report_mystudent/dbuserassign', get_string('dbuser', 'report_mystudent'), '', ''));

    $settings->add(new admin_setting_configpasswordunmask('report_mystudent/dbpassassign', get_string('dbpass', 'report_mystudent'), '', ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbnameassign', get_string('dbname', 'report_mystudent'), '', ''));

    // $settings->add(new admin_setting_configtext('report_mystudent/dbspmoodleassign', get_string('dbspmoodleassign', 'report_mystudent'), get_string('dbspmoodleassign_desc', 'report_mystudent'), ''));
    
    // $settings->add(new admin_setting_configtext('report_mystudent/dbspmoodleassignfeedback', get_string('dbspmoodleassignfeedback', 'report_mystudent'), get_string('dbspmoodleassignfeedback_desc', 'report_mystudent'), ''));

    // $settings->add(new admin_setting_configtext('report_mystudent/dbspquizzbyid', get_string('dbspquizzbyid', 'report_mystudent'), get_string('dbspquizzbyid_desc', 'report_mystudent'), ''));

    $settings->add(new admin_setting_configtext('report_mystudent/dbspassignments', get_string('dbspassignments', 'report_mystudent'), get_string('dbspassignments_desc', 'report_mystudent'), ''));
   
    //$settings->add(new admin_setting_configtext('report_mystudent/profileurl', get_string('profileurl', 'report_mystudent'), get_string('profileurl_desc', 'report_mystudent'), ''));
  
}
