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
 * Defines the APIs used by assignfeedback_download reports
 *
 * @package    report
 * @subpackage report_mystudent
 * @copyright  2021 Veronica Bermegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use report_mystudent\mystudentmanager;

defined('MOODLE_INTERNAL') || die;

function report_report_mystudent_extend_navigation_user($navigation, $user, $course) {

    if (report_mystudent_can_access_user_report($user, $course)) {
        $url = new moodle_url('/report/mystudent/index.php', array('id' => $user->id, 'course' => $course->id));
        $navigation->add(get_string('heading', 'report_mystudent'), $url);
    }
}

/**
 * Only the student parent, admin and teacher can see this report
 */
function report_mystudent_can_access_user_report($user) {
    return true; // TODO
}

/**
 * Add nodes to myprofile page.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 *
 * @return bool
 */
function report_mystudent_myprofile_navigation(\core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $CFG, $PAGE, $USER, $SITE;

    profile_load_custom_fields($USER);
    profile_load_custom_fields($user);
    if (!isset($USER->profile['CampusRoles'])) {
        return;
    }

    if(strpos('primary', strtolower( $user->profile['CampusRoles']))) {
        return;
    }

    $userroles = strtolower($USER->profile['CampusRoles']);

    $isstaff = preg_match('/(staff)/i', $userroles);
    $isparent = preg_match('/(parents)/i', $userroles);
    $isstudent = preg_match('/(Students)/i', $userroles);

    //Check the student is senior


    // show the dashboard block
    $category = new core_user\output\myprofile\category('mystudent', get_string('reportname', 'report_mystudent'), 'contact');
    $tree->add_category($category);
    $dburl = new moodle_url('/report/mystudent/index.php', ['id' => $user->id]);

    $localnode =  new core_user\output\myprofile\node('mystudent', 'mystudentdashboard', get_string('studentdashboard', 'report_mystudent', $user), null, $dburl, '');
    $manager = new mystudentmanager();

    switch ($iscurrentuser) {
        case false: // Im in a profile that is not mine
            if (($isstaff && $isparent) || ($isstaff && !$isparent)) { // staff that is also a parent can see their child and other children profile
                $tree->add_node($localnode);
            } else if (!$isstaff && $isparent) {
                // Parents can only see their childs profile.
                if ($manager->report_mystudent_user_mentor_of_student($user->id)) {
                    $tree->add_node($localnode);
                }
            }
            break;
        case true:  // Only students can see their dashboard.
            if ($isstudent && $USER->id == $user->id) {
                $localnode =  new core_user\output\myprofile\node('mystudent', 'mystudentdashboard', get_string('mydashboard', 'report_mystudent', $user), null, $dburl, '');
                $tree->add_node($localnode);
            }
            break;
    }
}

function get_mentor($profileuserid) {
    global $DB, $USER;
    // Parents are allowed to view block in their mentee profiles.
    $mentorrole = $DB->get_record('role', array('shortname' => 'parent'));
    $mentor = null;

    if ($mentorrole) {

        $sql = "SELECT ra.*, r.name, r.shortname
            FROM {role_assignments} ra
            INNER JOIN {role} r ON ra.roleid = r.id
            INNER JOIN {user} u ON ra.userid = u.id
            WHERE ra.userid = ?
            AND ra.roleid = ?
            AND ra.contextid IN (SELECT c.id
                FROM {context} c
                WHERE c.contextlevel = ?
                AND c.instanceid = ?)";
        $params = array(
            $USER->id, //Where current user
            $mentorrole->id, // is a mentor
            CONTEXT_USER,
            $profileuserid, // of the prfile user
        );

        $mentor = $DB->get_records_sql($sql, $params);
    }

    return $mentor;
}

/**
 * Serve the files from the MYPLUGIN file areas
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function report_mystudent_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    
    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false; 
    }

    // Make sure the filearea is one of those used by the plugin.
    if ($filearea !== 'mystudent') {
        return false;
    }

    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    // require_login($course, true, $cm);

    // Check the relevant capabilities - these may vary depending on the filearea being accessed.
    // if (!has_capability('mod/MYSTUDENT:view', $context)) {
    //     return false;
    // }

    // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = array_shift($args); // The first item in the $args array.
    
    // Use the itemid to retrieve any relevant data records and perform any security checks to see if the
    // user really does have access to the file in question.

    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/'.implode('/', $args).'/'; // $args contains elements of the filepath
    }

    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'report_mystudent', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }

    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering. 
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}