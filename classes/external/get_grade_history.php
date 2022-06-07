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
 * @package   report_mystudent
 * @category
 * @copyright 2021 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_mystudent\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_value;
use external_single_structure;

use function report_mystudent\grades_effort\get_templates_context;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/report/mystudent/classes/gradeseffortmanager.php');

/**
 * Trait implementing the external function block_grades_effort_report
 */
trait get_grade_history {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */

    public static  function get_grade_history_parameters() {
        return new external_function_parameters(
            array(
                'username' => new external_value(PARAM_RAW, 'student username'),
                'campus' => new external_value(PARAM_RAW, 'campus the student belongs to'),
            )
        );
    }

    /**
     * Return context.
     */
    public static function get_grade_history($username, $campus) {
        global $USER, $PAGE, $DB;

        $context = \context_user::instance($USER->id);

        self::validate_context($context);
        //Parameters validation
        self::validate_parameters(self::get_grade_history_parameters(), array('username' => $username, 'campus' => $campus));

        // Get the context for the grade history table template.
       
        $ctx = get_templates_context('grades', $username, $campus);
      
        $renderer = $PAGE->get_renderer('report_mystudent');

        switch ($campus) {
            case 'Primary':
                $html =  $renderer->render_from_template('report_mystudent/academic/academic_grades_history_primary', $ctx);
                break;
            case 'Senior':
                $html =  $renderer->render_from_template('report_mystudent/academic/academic_grades_history_senior', $ctx);
                break;
            default:
                $html =  get_string('nodataavailable', 'report_mystudent');
                break;
        }

        return array(
            'html' => $html,
        );
    }

    /**
     * Describes the structure of the function return value.
     * @return external_single_structures
     */
    public static function get_grade_history_returns() {
        return new external_single_structure(array(
            'html' =>  new external_value(PARAM_RAW, 'HTML with the grade history table context'),
        ));
    }
}
