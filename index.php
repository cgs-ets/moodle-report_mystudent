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

require_login();

$id = optional_param('id', 0, PARAM_INT); // User id.

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/report/mystudent/index.php', array('id' => $id));

$userrec = $DB->get_record('user', array('id' => $id));
$navigationinfo = array(
    'name' => get_string('reportname', 'report_mystudent'),
    'url' => new moodle_url('/report/mystudent/index.php', array('id' => $id))
);

if (!empty($id)) {
    $user = core_user::get_user($id);
    $PAGE->navigation->extend_for_user($user);
}

$PAGE->add_report_nodes($user->id, $navigationinfo);
// Now set the heading.
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

$heading = $USER->id == $id ? get_string('mydashboard', 'report_mystudent') : get_string('studentdashboard', 'report_mystudent', $userrec);

$PAGE->set_heading($heading);

$PAGE->set_title(get_string('mydashboard', 'report_mystudent'));

$PAGE->set_pagelayout('standard');

$PAGE->blocks->add_region('content');

$PAGE->add_body_class('report_mystudent');

$PAGE->set_cacheable(false);

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('report_mystudent');

echo $renderer->report_mystudent_cgs_dashboard($id);

$PAGE->requires->js_call_amd('report_mystudent/dashboard', 'init', []);

echo $OUTPUT->footer();
