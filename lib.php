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

    if (!isset($USER->profile['CampusRoles'])) {
        return;
    }

    $userroles = strtolower($USER->profile['CampusRoles']);

    $isstaff = preg_match('/(staff)/i', $userroles);
    $isparent = preg_match('/(parents)/i', $userroles);
    $isstudent = preg_match('/(Students)/i', $userroles);

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
