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
 * @package    report_mystudent
 * @copyright  2021 Veronica Bermegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use function report_mystudent\academic_info\get_template_context;
use function report_mystudent\attendance\get_data;
use function report_mystudent\grades_effort\get_templates_contexts;
use function report_mystudent\naplan\get_template_contexts;


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/report/mystudent/classes/attendancemanager.php');
require_once($CFG->dirroot . '/report/mystudent/classes/naplanmanager.php');
require_once($CFG->dirroot . '/report/mystudent/classes/gradeseffortmanager.php');
require_once($CFG->dirroot . '/report/mystudent/classes/academicinfonamager.php');

class report_mystudent_renderer extends plugin_renderer_base {
    
    public function __construct(moodle_page $page, $target) {
       
        parent::__construct($page, $target);
    }


    public function report_mystudent_cgs_dashboard($id) {

        global $CFG, $DB;;

        $profileuser = $DB->get_record('user', ['id' => $id]);
       
        profile_load_custom_fields($profileuser);
        
        $isSenior = strpos(strtolower($profileuser->profile['CampusRoles']), 'senior');
        $data = new stdClass();

        $data->username = $profileuser->username;
        $data->userid = $id;
        $data->campus = is_bool($isSenior) ? 'Primary' : 'Senior';

        $data->attsrc =  new moodle_url($CFG->wwwroot . '/report/mystudent/pix/attendance.png');
        $data->att =  get_string('attendance','report_mystudent');
        $data->atturl = new moodle_url('/report/mystudent/view.php',['report' => 'attendance', 'id' => $id]);
       
        $data->gestr =  new moodle_url($CFG->wwwroot . '/report/mystudent/pix/marking.png');
        $data->geurl = new moodle_url('/report/mystudent/view.php',['report' => 'gradeandeffort', 'id' => $id]);

        $data->academic = new moodle_url($CFG->wwwroot . '/report/mystudent/pix/education.png');
        $data->acc =  get_string('academicinfo','report_mystudent');
        $data->accurl =  new moodle_url('/report/mystudent/view.php',['report' => 'academic', 'id' => $id]);
       
        $data->naplan = new moodle_url($CFG->wwwroot . '/report/mystudent/pix/naplogo.jpg');
        $data->nap = get_string('naplan','report_mystudent');
        $data->napurl = new moodle_url('/report/mystudent/view.php',['report' => 'naplan', 'id' => $id]);

        $data->currentyear = date("Y");
        echo $this->render_from_template('report_mystudent/cgsdashboard', $data);

    }

    public function report_attendance($id) {
        global $DB;
        $profileuser = $DB->get_record('user', ['id' =>$id]);
        $data =  get_data($profileuser);

        echo  $this->render_from_template('report_mystudent/attendance/main', $data);
    }

    public function report_naplan($id) {
        global $DB;
        $profileuser = $DB->get_record('user', ['id' => $id]);
        $data = get_template_contexts($profileuser->username);
        
        return $data;
        //echo  $this->render_from_template('report_mystudent/naplan/main', $data);
    }

    public function report_grades_effort($id){
        $data = get_templates_contexts($id);
    
        echo $this->render_from_template('report_mystudent/gradesandeffort/main', $data);
    }

    // Get the grade reports to conver to PDF
    public function report_grades($studentid, $currentuserid) {
        global $DB, $USER;

        
        $ids = [$studentid, $currentuserid];
        list($insql, $inparams) = $DB->get_in_or_equal($ids);
        $sql = "SELECT id, username FROM {user} WHERE id $insql";
        $users = $DB->get_records_sql($sql, $inparams);
       
        if (is_siteadmin($USER)) {
            $data = get_template_context($users[$studentid]->username, $users[$studentid]->username);
        } else {
            $data = get_template_context($users[$studentid]->username, $users[$currentuserid]->username);
        }
     //   print_object($data); 
       // $data = get_templates_contexts($studentid);
        $data = array_merge($data, get_templates_contexts($studentid));
      // print_object($data);exit;
        echo $this->render_from_template('report_mystudent/gradesandeffort/academic_reports', $data);
    }
} 