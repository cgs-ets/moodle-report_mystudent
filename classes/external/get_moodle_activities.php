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
 *  Web service to get grade history/effort data.
 *
 * @package   assignmentsquizzes_report
 * @category
 * @copyright 2023 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_mystudent\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_value;
use external_single_structure;

use function report_mystudent\assignments_report\get_cgs_connect_activities_context;
require_once($CFG->dirroot . '/report/mystudent/classes/assignmentsmanager.php');
require_once($CFG->libdir . '/externallib.php');

/**
 * Trait implementing the external function get_moodle_activities
 */
trait get_moodle_activities {


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */

    public static function get_moodle_activities_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_RAW, 'student id')
            )
        );
    }

    /**
     * Return context.
     */
    public static function get_moodle_activities($userid) {
        global $USER, $PAGE;

        $context = \context_user::instance($USER->id);

        self::validate_context($context);
        // Parameters validation.
        self::validate_parameters(self::get_moodle_activities_parameters(), array('userid' => $userid));

        // Get the context for the template.
        $ctx = get_cgs_connect_activities_context($userid);

        // if (empty($ctx)) {
        //     $html = get_string('nodataavailable', 'report_mystudent');
        // } else {
        //     $output = $PAGE->get_renderer('core');
        //     $html = $output->render_from_template('report_mystudent/academic/academic_cgs_activities', $ctx);
        // }

        return array(
            'ctx' => json_encode($ctx),
        );
    }

    /**
     * Describes the structure of the function return value.
     * @return external_single_structures
     */
    public static function get_moodle_activities_returns() {
        return new external_single_structure(array(
            'ctx' => new external_value(PARAM_RAW, 'data to render in template'),
        ));
    }
}
