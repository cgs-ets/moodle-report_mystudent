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
 *  Attendance report block
 *
 * @package    report_mystudent
 * @copyright 2022 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns the context for the template
 * @return string
 */

namespace report_mystudent\attendance;

function get_template_context($data, $instanceid) {
    global  $COURSE;

    $urlparams = array('blockid' => $instanceid, 'courseid' => $COURSE->id);

    $data = ['attendancebasedonrm' => new \moodle_url('/blocks/block_attendance_report/view.php', $urlparams)];

    return $data;
}

/**
 * Call to the SP Class_Attendance_By_Term
 */
function get_attendance_by_term($profileuser) {

    try {

        $config = get_config('report_mystudent');

        // Last parameter (external = true) means we are not connecting to a Moodle database.
        $externalDB = \moodle_database::get_driver_instance($config->dbtype, 'native', true);

        // Connect to external DB
        $externalDB->connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, '');

        $sql = 'EXEC ' . $config->dbattbyterm . ' :id';

        $params = array(
            'id' => $profileuser->username,
        );
        $attendancedata = $externalDB->get_records_sql($sql, $params);

        return $attendancedata;
    } catch (\Exception $ex) {
        throw $ex;
    }
}

function get_attendance_by_class($profileuser) {

    try {
        $config = get_config('report_mystudent');

        $externalDB = \moodle_database::get_driver_instance($config->dbtype, 'native', true);

        // Connect to external DB.
        $externalDB->connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, '');

        $sql = 'EXEC ' . $config->dbattbyclass . ' :id';

        $params = array(
            'id' => $profileuser->username,
        );

        $attendancedata = $externalDB->get_records_sql($sql, $params);

        return $attendancedata;
    } catch (\Exception $ex) {

        throw $ex;
    }
}

// Full Class attendance current Term based on roll marking.
function get_student_attendance_based_on_rollmarking($username, $campus) {
    try {

        $config = get_config('report_mystudent');
        $externalDB = \moodle_database::get_driver_instance($config->dbtype, 'native', true);

        // Connect to external DB.
        $externalDB->connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, '');
        $sql = $campus == 'Senior' ? 'EXEC ' . $config->dbattbytermbyid . ' :id' :  'EXEC ' . $config->dbattbytermbyidprimary . ' :id';

        $params = array(
            'id' => $username,
        );

        $attendancedata = $externalDB->get_recordset_sql($sql, $params);

        $monthsdata = [];

        foreach ($attendancedata as $data) {

            $createDate = new \DateTime($data->attendancedate);
            $day = $createDate->format("d/m/Y");
            $month = $createDate->format("F");
            $swipedt = (new \DateTime($data->swipedt))->format('h:i A');
            $attendanceperiods[] = $data->attendanceperiod;
            $monthsdata['months'][$month . '_' . $day][$data->attendanceperiod] = [
                'attendancedate' => $day,
                'attendanceperiod' => $data->attendanceperiod,
                'ttclasscode' => $data->ttclasscode,
                'housesignin' => $swipedt,
                'nosignin' => $data->swipedt == null,
                'attendedflag' => $data->attendedflag,
                'latearrivalflag' => $data->latearrivalflag,
                'latearrivaltime' => $data->latearrivaltime,
                'month' => $month,
                'classdescription' => $data->classdescription,
                'perioddescription' => $data->description, // Periods can have different names. 
                'academicperiodtimefrom' => $data->academicperiodtimefrom,
                'academicperiodtimeto' => $data->academicperiodtimeto, // For Primary 
                'eventdate' => $data->eventdate,
                'absencetypecode' => $data->absencetypecode,
                'firstexcursionoverlapin'  => $data->firstexcur_overlapin,
                'firstexcursionoverlapout'  => $data->firstexcur_overlapout,
                'backgroundcolour' => $data->backgroundcolour,

            ];
        }
        $attendanceperiods = array_unique($attendanceperiods);
        $attperiods = [];
        foreach ($attendanceperiods as $attp) {
            $attendancep = new \stdClass();
            $attendancep->attendanceperiod = $attp;
            $attperiods[] = $attendancep;
        }
        if ($campus == 'Senior') {
            $days = get_student_attendance_based_on_rollmarking_senior($monthsdata);
        } else {
            $days = get_student_attendance_based_on_rollmarking_primary($monthsdata, $attendanceperiods);
        }

        return $days;
    } catch (\Exception $ex) {

        throw $ex;
    }
}

function get_student_attendance_based_on_rollmarking_senior($monthsdata) {
    $days = [];

    get_student_attendance_based_on_rollmarking_senior_helper($monthsdata);
    array_walk($monthsdata, function ($months) use (&$days) {

        foreach ($months as $key => $month) {
            $daydetails = new \stdClass();
            $classdesc = [];
            $time = "06:00 AM";

            list($daydetails->month, $daydetails->attendancedate) = explode('_', $key);

            foreach ($month as $i => $m) {

                $summary = new \stdClass();
                $summary->description = $m['classdescription'];
                $summary->attendedflag =  $m['attendedflag'];
                $summary->norolltaken = (is_null($m['attendedflag']) && is_null($m['latearrivalflag']));
                $summary->latearrivalflag = !is_null($m['latearrivalflag']) && $m['latearrivalflag'] != 0;
                $summary->latearrivaltime = $m['latearrivaltime'];
                $summary->cssclass = ($m['backgroundcolour'] == "#add7e5" ? "att-mark-exc" : '');
                $classdesc['descriptions'][] = $summary;

                foreach ($m as $j => $q) {

                    switch ($j) {
                        case 'housesignin':
                            $daydetails->housesignin = $q;
                            $daydetails->late  = strtotime($q) < strtotime($time);
                            break;
                        case 'nosignin':
                            $daydetails->nosignin = $q;
                            $daydetails->late  = $q;
                            break;
                        case 'firstexcursionoverlapin':
                            $daydetails->firstexcursionoverlapin = $q;
                            $daydetails->title = "Excursion $q";
                            break;
                    }
                }
            }

            $daydetails->classdescription = $classdesc;
            $days['months']['details']['det'][] = $daydetails;
        }
    });
    return $days;
}

// If there are periods where there  is no data, add dummy values.
function get_student_attendance_based_on_rollmarking_senior_helper(&$daysdata) {
    $p = [];

    foreach ($daysdata['months'] as $date => $periods) {
        $p[$date] = array_keys($periods);
    }

    $pvalue = [1, 2, 3, 4, 5, 6];
    $pmissing = [];

    foreach ($p as $day => $period) {
        $pmissing[$day] = array_diff($pvalue, $period);
    }

    foreach ($pmissing as $date => $periodmissing) {

        foreach ($periodmissing as $j => $missing) {
            $d = explode('_', $date);
            $dateindex = end($d);
            $month = reset($d);
            $daysdata['months'][$date][$missing] = [
                'attendancedate' => $dateindex,
                'attendanceperiod' => $missing,
                'ttclasscode' => '',
                'housesignin' => '',
                'nosignin' =>  null,
                'attendedflag' => null,
                'latearrivalflag' => null,
                'month' => $month,
                'classdescription' => ''
            ];

            ksort($daysdata['months'][$date]);
        }
    }

    return $daysdata;
}

function get_student_attendance_based_on_rollmarking_primary($monthsdata, $attendanceperiods) {
    $days = [];

    get_student_attendance_based_on_rollmarking_primary_helper($monthsdata, $attendanceperiods);
    array_walk($monthsdata, function ($months) use (&$days) {

        foreach ($months as $key => $month) {
            $daydetails = new \stdClass();
            $classdesc = [];
            $classrollnottaken = []; // Collect the classes where the roll is not taken.           
            $allperiods = [2, 3, 4, 5, 6, 7, 8];
            $countexcursion = 0;
            list($daydetails->month, $daydetails->attendancedate) = explode('_', $key);

            foreach ($month as $i => $m) {
                $summary = new \stdClass();
                $summary->description = $m['classdescription'];
                $summary->period = $m['attendanceperiod'];
                $summary->attendedflag =  $m['attendedflag'];
                $summary->rolltaken = !is_null($m['attendedflag']);
                $summary->academicperiodtimefrom = date('H:i', strtotime($m['academicperiodtimefrom']));
                $summary->academicperiodtimeto = date('H:i', strtotime($m['academicperiodtimeto']));
                $summary->absencertypecode = ($m['absencetypecode'] == 'EXCUR') ? $m['absencetypecode'] : ''; //ATM only display excursion

                if (!is_null($m['attendedflag'])) { // Roll taken
                    $classdesc['descriptions'][$i] = $summary;
                } else { // Roll not taken

                    $classrollnottaken[$i] =  clone $summary; // Keep track of the state, as it comes from the DB.       

                    if ($i > 2) { // Get the attendance state from the previous period.
                        $lastperiod =   $classdesc['descriptions'][$i - 1];
                    } else if ($i == 2) { // This means that in the period 2 when the rollmarking took place, there was no rollmark. 
                        $lastperiod =   $classdesc['descriptions'][$i];
                    }

                    $summary->attendedflag = $lastperiod->attendedflag;
                    $summary->rolltaken = $lastperiod->rolltaken;
                    $summary->absencertypecode = $lastperiod->absencertypecode;
                    $classdesc['descriptions'][$i] =  $summary;
                }
            }

            if (count($classrollnottaken) > 7) { // No roll taken all day means excursion or something that keeps the student away from the periods where the roll mark takes place
                $classdesc['descriptions'] = $classrollnottaken;
            } else if (count($classdesc['descriptions']) < 3) {
                $p = array_keys($classdesc['descriptions']);

                foreach ($classdesc['descriptions'] as $j => $description) {
                    $missingp = array_diff($allperiods, $p);
                }

                foreach ($missingp as $pm) {
                    $classdesc['descriptions'][$pm] = $classrollnottaken[$pm];
                }
            }

            $classdesc['descriptions'] = array_values($classdesc['descriptions']);
            $daydetails->classdescription = $classdesc;
            $daydetails->classrollnottaken = $classrollnottaken;
            $days['months']['details']['det'][] = $daydetails;
        }
    });

    return $days;
}

function get_student_attendance_based_on_rollmarking_primary_helper(&$daysdata, $attendanceperiods) {
    $p = [];

    foreach ($daysdata['months'] as $date => $periods) {
        $p[$date] = array_keys($periods);
    }

    $pvalue = $attendanceperiods;
    $pmissing = [];

    foreach ($p as $day => $period) {
        $pmissing[$day] = array_diff($pvalue, $period);
    }

    foreach ($pmissing as $date => $periodmissing) {

        foreach ($periodmissing as $j => $missing) {
            $d = explode('_', $date);
            $dateindex = end($d);
            $month = reset($d);
            $daysdata['months'][$date][$missing] = [
                'attendancedate' => $dateindex,
                'attendanceperiod' => $missing,
                'ttclasscode' => '',
                'housesignin' => '',
                'nosignin' =>  null,
                'attendedflag' => null,
                'latearrivalflag' => null,
                'month' => $month,
                'classdescription' => ''
            ];

            ksort($daysdata['months'][$date]);
        }
    }

    return $daysdata;
}
// Collect all the data related to attendance.
function get_data(/*$instanceid,*/ $profileuser) {
    global $USER;

    $attendacebyclass = get_attendance_by_class($profileuser);

    $classes = [];

    foreach ($attendacebyclass as $class) {
        $c = new \stdClass();
        $c->classdescription = $class->classdescription;
        $c->attended = $class->attended;
        $c->notattended = $class->notattended;
        $c->totalclasses = $class->totalclasses;
        $c->percentageattended = round(floatval($class->percentageattended));
        $c->nooflateclasses = $class->nooflateclasses;
        $c->nooflateshow = $class->nooflateclasses > 0;
        $c->percentagelate = round(floatval($class->percentagelate));
        $c->lessthan = $class->percentageattended < 90;
        $c->islate = $class->percentagelate != .00;
        $classes[] = $c;
    }

    $attendacebyterm = get_attendance_by_term($profileuser);
    $terms = array();

    foreach ($attendacebyterm as $term) {
        $data = new \stdClass();
        $data->totalpercentageattended = round(floatval($term->totalpercentageattended));
        $data->filesemester = $term->filesemester;
        $data->currentterm = $term->currentterm;
        $terms[] = $data;
    }

    $urlparams = array(/*'blockid' => $instanceid, 'courseid' => $COURSE->id,*/ 'id' => $profileuser->id);

    $notermdata = empty($terms);
    $noclassesdata = empty($terms);

    profile_load_custom_fields($profileuser);
    $isSenior = strpos(strtolower($profileuser->profile['CampusRoles']), 'senior');
  
    profile_load_custom_fields($USER);
    $result = [
        'terms' => $terms,
        'classes' => $classes,
        'attendancebasedonrm' => new \moodle_url('/blocks/attendance_report/view.php', $urlparams),
        'notermdata' => $notermdata,
        'noclassesdata' => $noclassesdata,
        'hidelink' => ($notermdata && $noclassesdata),
        'campus' =>  is_bool($isSenior) ? 'Primary' : 'Senior',
        'username' => $profileuser->username,
        'isparent' => !is_siteadmin($USER) && !is_bool(strpos(strtolower($USER->profile['CampusRoles']), 'parents'))

    ];

    return $result;
}


// Parent view of own child's activity functionality. TODO: USE THE ONE IN MYSTUDENTMANAGER>PHP
function can_view_on_profile() {
    global $DB, $USER, $PAGE;

    $config = get_config('report_mystudent');
    if ($PAGE->url->get_path() ==  $config->profileurl) {
        $profileuser = $DB->get_record('user', ['id' => $PAGE->url->get_param('id')]);
        // Admin is allowed.

        if (is_siteadmin($USER) && $USER->username != $profileuser->username) {
            return true;
        }

        // Students are allowed to see timetables in their own profiles.
        if ($profileuser->username == $USER->username && !is_siteadmin($USER)) {
            return true;
        }

        // Parents are allowed to view timetables in their mentee profiles.
        $mentorrole = $DB->get_record('role', array('shortname' => 'parent'));

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
                $profileuser->id, // of the prfile user
            );

            $mentor = $DB->get_records_sql($sql, $params);
            if (!empty($mentor)) {
                return true;
            }
        }
    }

    return false;
}
