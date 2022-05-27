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
 * @package   academic_reports
 * @category
 * @copyright 2021 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_mystudent\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_value;
use external_single_structure;

use function report_mystudent\academic_info\get_student_academic_report_file;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/report/mystudent/classes/academicinfonamager.php');

/**
 * Trait implementing the external function report_mystudent
 */
trait get_student_academic_report {


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */

    public static  function get_student_academic_report_parameters() {
        return new external_function_parameters(
            array(
                'tdocumentsseq' => new external_value(PARAM_RAW, 'file id to get'),
            )
        );
    }

    /**
     * Return context.
     */
    public static function get_student_academic_report($tdocumentsseq) {
        global $USER, $PAGE;

        $context = \context_user::instance($USER->id);

        self::validate_context($context);
        //Parameters validation
        self::validate_parameters(self::get_student_academic_report_parameters(), array('tdocumentsseq' => $tdocumentsseq));

        // Get the context for the template.
        $blob = get_student_academic_report_file($tdocumentsseq);

        return array(
            'blob' => json_encode(base64_encode($blob), JSON_UNESCAPED_UNICODE),
        );
    }

    /**
     * Describes the structure of the function return value.
     * @return external_single_structures
     */
    public static function get_student_academic_report_returns() {
        return new external_single_structure(array(
            'blob' =>  new external_value(PARAM_RAW, 'Blob representation of the file'),
        ));
    }
}
