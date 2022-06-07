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
 *  Academic report block
 *
 * @package    report_mystudent
 * @copyright 2021 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_mystudent\academic_info;

/**
 * Returns the context for the template
 * @return array
 */

function get_template_context($studentusername, $mentorusername) {
    global $CFG;
    $reports = get_student_academic_reports($studentusername, $mentorusername);    
    $data = [];
    
    foreach ($reports as $report) {
        $repo = new \stdClass();
        $repo->description = $report->description;
        $repo->documentcreateddate = (new  \DateTime($report->documentcreateddate))->format("d/m/Y");;
        $repo->tdocumentsseq = $report->tdocumentsseq;
        $repo->icon = new \moodle_url($CFG->wwwroot . '/report/mystudent/pix/acrobat.png');
      
        $data['reports'][] = $repo;
      
    }
    return $data;
}

/**
 * Call to the SP 
 */
function get_student_academic_reports($studentusername, $mentorusername) {
    $docreports = [];
    try {

        $config = get_config('report_mystudent');

        // Last parameter (external = true) means we are not connecting to a Moodle database.
        $externalDB = \moodle_database::get_driver_instance($config->dbtypeacademicreport, 'native', true);

        // Connect to external DB.
        $externalDB->connect($config->dbhostacademicreport, $config->dbuseracademicreport, $config->dbpassacademicreport, $config->dbnameacademicreport, '');

        $sql = 'EXEC ' . $config->dbspstudentreportdocs . ':studentid, :userid';
        $params = array(
            'studentid' => $studentusername,
            'userid' => $mentorusername
        );

        $docreports = $externalDB->get_records_sql($sql, $params);
        
    } catch (\Exception $ex) {
    }

    return $docreports;
}

function get_student_academic_report_file($tdocumentsseq) {
    $config = get_config('report_mystudent');
    // Last parameter (external = true) means we are not connecting to a Moodle database.
    $externalDB = \moodle_database::get_driver_instance($config->dbtype, 'native', true);
    // Connect to external DB.
    $externalDB->connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, '');

    $sql = 'EXEC ' . $config->dbspsretrievestdreport . ':tdocumentsseq';
    $params = array('tdocumentsseq' => intval($tdocumentsseq));

    $documents = $externalDB->get_records_sql($sql, $params);
    $document = reset($documents);

    return $document->document;
}
