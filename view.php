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
 * Displays different views of the logs.
 *
 * @package    report_mystudent
 * @copyright  2022  Veronica Bermegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once('lib.php');

$id         = required_param('id', PARAM_INT); // Userid.
$report     = required_param('report', PARAM_TEXT); // Type of report.
$course = optional_param('course', 0 ,PARAM_INT);
$assignment = optional_param('assignment', 0 ,PARAM_INT);

require_login();

$context = context_system::instance();
$PAGE->set_context($context);

if (empty(get_mentor($id)) && !is_siteadmin($USER) && $id != $USER->id) {

    // Course managers can be browsed at site level. If not forceloginforprofiles, allow access (bug #4366).
    $struser = get_string('user');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_title("$SITE->shortname: $struser");  // Do not leak the name.
    $PAGE->set_heading($struser);
    $PAGE->set_pagelayout('mypublic');
    $PAGE->set_url('/user/profile.php', array('id' => $id));
    $PAGE->navbar->add($struser);
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('usernotavailable', 'error'));
    echo $OUTPUT->footer();
    exit;
}
$PAGE->set_url('/report/mystudent/view.php', array('id' => $id));

$sectionames =  [
    'attendance' => 'Attendance',
    'naplan' => 'NAPLAN',
    'gradeandeffort' => 'Grades and effort',
    'academic' => 'Academic info',
    'rubric' => 'Assessment Rubric'
];


$navigationinfo = array(
    'name' => $sectionames[$report],
    'url' => new moodle_url('/report/mystudent/view.php', array('id' => $id))
);

$PAGE->add_report_nodes($USER->id, $navigationinfo);

$userrec = $DB->get_record('user', array('id' => $id));
$heading = $USER->id == $id ? get_string('mydashboard', 'report_mystudent') : get_string('studentdashboard', 'report_mystudent', $userrec);
$PAGE->set_heading($heading);

$PAGE->set_title(get_string('mydashboard', 'report_mystudent'));

$PAGE->set_pagelayout('standard');

$PAGE->blocks->add_region('content');

$PAGE->add_body_class('report_mystudent');

$PAGE->set_cacheable(false);

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('report_mystudent');

switch ($report) {
    case 'attendance':
        $renderer->report_attendance($id);
        break;
    
    case 'academic':
        $renderer->report_academic($id, $USER->id);
        break;
    case 'rubric' :
        $renderer->report_rubric($id, $assignment, $course);
        break;
    default:
        # code...
        break;
}


echo $OUTPUT->footer();
