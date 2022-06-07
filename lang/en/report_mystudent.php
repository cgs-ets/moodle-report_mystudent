<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     report_mystudent
 * @category    string
 * @copyright   2022  Veronica Bermegui
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$string['pluginname'] = 'My student';
$string['reportname'] = 'Student\'s CGS dashboard';
$string['studentdashboard'] = '{$a->firstname}\'s CGS dashboard';
$string['mydashboard'] = 'My CGS dashboard';
$string['cgsdashboard'] = 'CGS dashboard';

$string['academicinfo'] = 'Academic Info';
$string['gradeseffort'] = 'Grades - Effort';
$string['naplan'] = 'NAPLAN';

// Common settings strings
$string['commonconfig'] = 'Common SP configuration';
$string['dbtype'] = 'Database driver';
$string['dbtype_desc'] = 'ADOdb database driver name, type of the external database engine.';
$string['dbhost'] = 'Database host';
$string['dbhost_desc'] = 'Type database server IP address or host name. Use a system DSN name if using ODBC. Use a PDO DSN if using PDO.';
$string['dbname'] = 'Database name';
$string['dbuser'] = 'Database user';
$string['dbpass'] = 'Database password';
$string['nodbsettings'] = 'Please configure the DB options for the plugin';
$string['profileurl'] = 'Profile URL';
$string['profileurl_desc'] =' Moodle\'s profile URL';

// Attendance String
$string['attendance'] = 'Attendance';
$string['dbattbyterm'] = 'Attendance by Term';
$string['dbattbyterm_desc'] = 'Stored procedure name to retrieve student attendance by term';
$string['dbattbyclass'] = 'Attendance by Class';
$string['dbattbyclass_desc'] = 'Stored procedure name to retrieve student attendance by class';
$string['dbattbytermbyid'] = ' Full class attendance based on roll marking. Senior students';
$string['attbasedonrollmarkinglink'] = ' Full class attendance based on roll marking';
$string['dbattbytermbyid_desc'] = 'Stored procedure name to retrieve senior student attendance this term by id';
$string['dbattbytermbyidprimary'] = ' Full class attendance based on roll marking. Primary students';
$string['dbattbytermbyidprimary_desc'] = 'Stored procedure name to retrieve primary student attendance this term by id';
$string['invalidcourse'] = 'Invalid course';
$string['attendancetitle'] = 'Attendance';
$string['btnattendanceid'] = 'btnattendance';
$string['termlabel'] = 'Term';
$string['attendancereportitle'] = 'Attendance';
$string['attendancebyterm'] = date('Y') .' Attendance';
$string['classcode'] = 'Class Code';
$string['classdescription'] = 'Class Ddescription';
$string['attendancebyclassforterm'] = 'Attendance by class for this term ';
$string['notattendancebyclassforterm'] = 'Classes not attended';
$string['totalclassforterm'] = 'Total classes';
$string['percentageattendedforterm'] = 'Percentage attended';
$string['arrivedlate'] = 'Arrived late';
$string['percentagearrivedlate'] = 'Percentage arrived late';
$string['profile'] = 'Profile';
$string['attbasedonrollmarking'] = 'Attendance based on roll marking';
$string['attbasedonrmtitle'] = 'Full class attendance this term based on roll marking';
$string['nosignin'] = 'No sign-in';
$string['reportunavailable'] = 'Attendance report unavailable';
$string['clarification'] = 'NB this data is dependent on accurate roll marking.';
$string['norolltaken'] = 'No roll taken';
$string['markedabset'] = 'Marked absent';
$string['markedpresent'] = 'Marked present';
$string['studenthub'] = 'Student Hub';
$string['studentexcursion'] = 'Excursion';
$string['healthClinic'] = 'Health Clinic';
$string['markedlate'] = 'Red text - marked late';
$string['pastoral'] = 'Pastoral';
$string['excursion'] = 'Excursion';
$string['userprofilenotsetup'] = 'Campus role is not set';
$string['attendancedate'] = 'Attendance date';
$string['housesignin'] = 'House sign in';
$string['periods'] = 'Periods';

// Grades and effort
$string['gandf'] = 'Grades and effort';
$string['grades_effort_report'] = 'Grades and Effort Report';
$string['dbtypegandf'] = 'Database driver';
$string['dbtypegandf_desc'] = 'ADOdb database driver name, type of the external database engine.';
$string['dbhostgandf'] = 'Database host';
$string['dbhostgandf_desc'] = 'Type database server IP address or host name. Use a system DSN name if using ODBC. Use a PDO DSN if using PDO.';
$string['dbnamegandf'] = 'Database name';
$string['dbnamegandf_desc'] = 'Name of the DB where the SPs are stored';
$string['dbusergandf'] = 'Database user';
$string['dbpassgandf'] = 'Database password';
$string['dbaccgradessenior'] = 'Academic Grades (Senior)';
$string['dbaccgradesprimary'] = 'Academic Grades (Primary)';
$string['dbaccgrades_desc'] = 'Stored procedure name to retrieve student academic grades by ID';
$string['dbefforthistorysenior'] = 'Effort Grades (Senior)';
$string['dbefforthistoryprimary'] = 'Effort Grades (Primary)';
$string['dbefforthistory_desc'] = 'Stored procedure name to retrieve student academic effort by ID';
$string['dbperformancetrend'] = 'Performance Trend (Senior)';
$string['dbprimaryperformancetrend'] = 'Performance Trend (Primary)';
$string['dbperformancetrend_desc'] = 'Stored procedure name to retrieve student performance trend by ID';

$string['gradesandeffortreportitle'] = 'Grades and Effort';
$string['gradehistory'] = 'Grade History ';
$string['efforthistory'] = ' Effort History';
$string['performancetrend'] = 'Performance Trend';
$string['invalidcourse'] = 'Invalid course';
$string['profile'] = 'Profile';

$string['gradeslabel'] = 'Grades';
$string['learningarealabel'] = 'Learning area';
$string['classlabel'] = 'Class';
$string['title'] = '{$a->name} report';
$string['reportunavailable'] = 'Grades and Effort Report unavailable';

//Naplan
$string['dbtypenaplan'] = 'Database driver';
$string['dbtypenaplan_desc'] = 'ADOdb database driver name, type of the external database engine.';
$string['dbhosnaplan'] = 'Database host';
$string['dbhostnaplan_desc'] = 'Type database server IP address or host name. Use a system DSN name if using ODBC. Use a PDO DSN if using PDO.';
$string['dbnamenaplan'] = 'Database name';
$string['dbusernaplan'] = 'Database user';
$string['dbpassnaplan'] = 'Database password';
$string['dbspnaplanresult'] = 'NAPLAN Results';
$string['dbspnaplanresult_desc'] = 'Stored procedure name to retrieve NAPLAN results';
$string['profileurl'] = 'Profile URL';
$string['profileurl_desc'] = 'Moodle\'s profile URL';
$string['nodbsettings'] = 'Please configure DB settings';
$string['reportlabel'] = 'Results';
$string['naplanscale'] = 'NAPLAN scales';
$string['testarea'] = 'Test Area';
$string['reportunavailable'] = 'NAPLAN results report unavailable';
$string['naplanscale'] = 'National Assessment Program -Literacy and Numeracy National Assessment Scale';
$string['naplanscales'] = 'National Assessment URL';
$string['naplanscales_desc'] = 'URL to NAPLAN scales';
$string['bcyear3'] = 'Background colour year 3';
$string['bcyear3_desc'] = 'Background colour for bar in graph representing year 3';
$string['bcyear5'] = 'Background colour year 5';
$string['bcyear5_desc'] = 'Background colour for bar in graph representing year 5';
$string['bcyear7'] = 'Background colour year 7';
$string['bcyear7_desc'] = 'Background colour for bar in graph representing year 7';
$string['bcyear9'] = 'Background colour year 9';
$string['bcyear9_desc'] = 'Background colour for bar in graph representing year 9';
$string['effortdesc'] = 'Hover over the grades to see the feedback given';


// Academic records.
$string['academicreport'] = 'Academic report';
$string['dbtypeacademicreport'] = 'Database driver';
$string['dbtypeacademicreport_desc'] = 'ADOdb database driver name, type of the external database engine.';
$string['dbhostacademicreport'] = 'Database host';
$string['dbhostacademicreport_desc'] = 'Type database server IP address or host name. Use a system DSN name if using ODBC. Use a PDO DSN if using PDO.';
$string['dbnameacademicreport'] = 'Database name';
$string['dbuseracademicreport'] = 'Database user';
$string['dbpassacademicreport'] = 'Database password';
$string['dbspstudentreportdocs'] = 'Student report docs';
$string['dbspstudentreportdocs_desc'] = 'Stored procedure name to retrieve students reports';
$string['dbspsretrievestdreport'] = 'Retrieve Student Report';
$string['dbspsretrievestdreport_desc'] = 'Stored procedure name to retrieve report document information.';
// $string['profileurl'] = 'Profile URL';
// $string['profileurl_desc'] =' Moodle\'s profile URL';

$string['nodbsettings'] = 'Please configure the DB options for the plugin';
$string['description'] = 'Description';
$string['createddate'] = 'Created date';
$string['viewreport'] = 'View';

// Assignment

$string['assig_report'] = 'Assignments summary';
$string['dbtypeassign'] = 'Database driver';
$string['dbtypeassign_desc'] = 'ADOdb database driver name, type of the external database engine.';
$string['dbhostassign'] = 'Database host';
$string['dbhostassign_desc'] = 'Type database server IP address or host name. Use a system DSN name if using ODBC. Use a PDO DSN if using PDO.';
$string['dbuserassign'] = 'Database user';
$string['dbpassassign'] = 'Database password';
$string['dbspassignments'] = 'Student Synergetic Assignments by id SP';
$string['dbspassignments_desc'] = 'Stored procedure name to retrieve Synergetic Assignments  by student by ID';
$string['assessmentdescription'] = 'Assessment description';
$string['markoutof'] = 'Mark (Out of)';
$string['weighting'] = 'Weighting (%)';
$string['cohortmeanscore'] = 'Cohort Mean';
$string['testdate'] = 'Date';
$string['result'] = "Result";
$string['term'] = 'Term';
$string['week'] = 'Week';
$string['subject'] = 'Subject';
$string['view'] = 'View';
$string['nodataavailable'] = 'Data not available.';
