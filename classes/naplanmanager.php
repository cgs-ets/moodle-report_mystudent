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
 *  NAPLAN report
 *
 * @package    report_mystudent
 * @copyright 2022 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_mystudent\naplan;

/**
 * Returns the context for the template
 * @return string
 */

function get_template_contexts($username) {

    $results = get_naplan_results($username);
    $config = get_config('report_mystudent');

    $years = [];
    $areasdetails = [];
    $colours = [
        'LightGreen' => '#8fd9a8',
        'LightSalmon' => '#ffba93',
        '#F3FCD6' => '#F3FCD6',
        'HotPink' => '#e05297',
        'whitesmoke' => 'whitesmoke'
    ];
    $backgroundcolor = [
        'Year 3' => $config->bcyear3,
        'Year 5' =>  $config->bcyear5,
        'Year 7' => $config->bcyear7,
        'Year 9' => $config->bcyear9
    ];

    $datasets = [];

    foreach ($results as $result) {
        $datasets['labels'][] = trim(str_replace('Band', '', $result->testareadescription));

        $years['year'][] = $result->testleveldescription;
        $r = new \stdClass();
        $r->value = $result->testresultdescription;
        $r->colour = $colours[$result->thecolour];
        $areasdetails[$result->testareadescription]['result'][] = $r;

        $dataset = new \stdClass();
        $dataset->label = $result->testareadescription;
        $dataset->testresultdescription =  $result->testresultdescription;
        $dataset->year = $result->testleveldescription;
        $datasets[$result->testleveldescription][] = $dataset;
    }

    if (count($datasets) == 0) {
        return;
    }


    $datasets['labels'] = array_unique($datasets['labels']); // Remove duplicates.
    $resultsperyear = [];

    foreach ($datasets as $i => $dataset) {
        if ($i == 'labels') continue;
        $datatorender = new \stdClass();
        $datatorender->label = $i;
        $datatorender->results = [];
        $datatorender->backgroundcolor = [$backgroundcolor[$i]];

        foreach ($dataset as $y => $data) {
            array_push($datatorender->results, $data->testresultdescription);
        }

        array_push($resultsperyear, $datatorender);
    }

    $graphdata = ['labels' => $datasets['labels'], 'datasets' => $resultsperyear];
    $years = array_unique($years['year']);
    $yearlabels = [];

    foreach ($years as $year) {
        $y = new \stdClass();
        $y->year = $year;
        $yearlabels['label'][] = $y;
    }

    foreach ($areasdetails as $area => $results) {
        $summary = new \stdClass();
        $summary->area = $area;
        $summary->results = $results;
        $summaries['summaries'][] = $summary;
    }

    $data = [
        'years' => $yearlabels,
        'testarea' => $summaries,
        'hasdata' => !empty($summaries), 
        'results' => json_encode($graphdata), 
        'naplanscale' => 'https://www.nap.edu.au/_resources/common_scales_image_file.png'
    ];

    return $data;
}

/**
 * Call to the SP 
 */
function get_naplan_results($username) {

    try {

        $config = get_config('report_mystudent');

        // Last parameter (external = true) means we are not connecting to a Moodle database.
        $externalDB = \moodle_database::get_driver_instance($config->dbtypenaplan, 'native', true);

        // Connect to external DB
        $externalDB->connect($config->dbhostnaplan, $config->dbusernaplan, $config->dbpassnaplan, $config->dbnamenaplan, '');

        $sql = 'EXEC ' . $config->dbspnaplanresult . ' :id';

        $params = array(
            'id' => $username,
        );

        $naplanresults = $externalDB->get_records_sql($sql, $params);

        return $naplanresults;
    } catch (\Exception $ex) {
        throw $ex;
    }
}


// Parent view of own child's activity functionality
// function can_view_on_profile() { // TODO: USe the new one
//     global $DB, $USER, $PAGE;

//     $config = get_config('report_mystudent');
//     if ($PAGE->url->get_path() ==  $config->profileurl) {
//         // Admin is allowed.
//         $profileuser = $DB->get_record('user', ['id' => $PAGE->url->get_param('id')]);

//         if (is_siteadmin($USER) && $profileuser->username != $USER->username) {
//             return true;
//         }

//         // Students are allowed to see timetables in their own profiles.
//         if ($profileuser->username == $USER->username && !is_siteadmin($USER)) {
//             return true;
//         }

//         // Parents are allowed to view timetables in their mentee profiles.
//         $mentorrole = $DB->get_record('role', array('shortname' => 'parent'));

//         if ($mentorrole) {

//             $sql = "SELECT ra.*, r.name, r.shortname
//                 FROM {role_assignments} ra
//                 INNER JOIN {role} r ON ra.roleid = r.id
//                 INNER JOIN {user} u ON ra.userid = u.id
//                 WHERE ra.userid = ?
//                 AND ra.roleid = ?
//                 AND ra.contextid IN (SELECT c.id
//                     FROM {context} c
//                     WHERE c.contextlevel = ?
//                     AND c.instanceid = ?)";
//             $params = array(
//                 $USER->id, //Where current user
//                 $mentorrole->id, // is a mentor
//                 CONTEXT_USER,
//                 $profileuser->id, // of the prfile user
//             );
//             $mentor = $DB->get_records_sql($sql, $params);
//             if (!empty($mentor)) {
//                 return true;
//             }
//         }
//     }

//     return false;
// }
