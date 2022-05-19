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
 * @copyright  2022 Veronica Bermegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_mystudent;

class mystudentmanager {

    public function report_mystudent_user_mentor_of_student($userid) {

        global $USER, $DB;
        $mentorrole = $DB->get_record('role', array('shortname' => 'parent'));
        $sql = "SELECT  ra.userid, ra.*, r.name, r.shortname
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
            $userid
        );

        $mentor = $DB->get_records_sql($sql, $params);
      
        return !empty($mentor);
    }
}
