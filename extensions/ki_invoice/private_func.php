<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2009 Kimai-Development-Team
 *
 * Kimai is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; Version 3, 29 June 2007
 *
 * Kimai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimai; If not, see <http://www.gnu.org/licenses/>.
 */

// when creating the short form contains index of each activity in the array
$activityIndexMap = array();

/**
 * Get a combined array with time recordings and expenses to export.
 *
 * @param int $start Time from which to take entries into account.
 * @param int $end Time until which to take entries into account.
 * @param array $projects Array of project IDs to filter by.
 * @param int $filter_cleared (-1: show all, 0:only cleared 1: only not cleared) entries
 * @param bool $short_form should the short form be created
 * @return array with time recordings and expenses chronologically sorted
 */
function invoice_get_data($start, $end, $projects, $filter_cleared, $short_form)
{
    global $database;

    $limitCommentSize = true;
    $results = array();

    // --------------------------------------------------------------------------------
    // timesheet entries
    $timeSheetEntries = $database->get_timeSheet($start, $end, null, null, $projects, null, false, false, $filter_cleared);

    foreach($timeSheetEntries as $entry)
    {
        // active recordings will be omitted
        if ($entry['end'] == 0) {
            continue;
        }

        $arr = ext_invoice_empty_entry();

        $arr['type'] = 'timeSheet';
        $arr['desc'] = $entry['activityName'];
        $arr['start'] = $entry['start'];
        $arr['end'] = $entry['end'];
        $arr['hour'] = $entry['duration'] / 3600;
        $arr['fDuration'] = $entry['formattedDuration']; // @deprecated use duration instead
        $arr['duration'] = $entry['duration'];
        $arr['timestamp'] = $entry['start'];
        $arr['amount'] = $entry['wage'];
        $arr['description'] = $entry['description'];
        $arr['rate'] = $entry['rate'];
        $arr['comment'] = $entry['comment'];
        $arr['username'] = $entry['userName'];
        $arr['useralias'] = $entry['userAlias'];
        $arr['location'] = $entry['location'];
        $arr['trackingNr'] = $entry['trackingNumber'];
        $arr['projectID'] = $entry['projectID'];
        $arr['projectName'] = $entry['projectName'];
        $arr['projectComment'] = $entry['projectComment'];

        invoice_add_to_array($results, $arr, $short_form);
    }

    // --------------------------------------------------------------------------------
    // if expenses extension is used, load expenses as well
    if (file_exists('../ki_expenses/private_db_layer_mysql.php'))
    {
        include_once '../ki_expenses/private_db_layer_mysql.php';

        $expenses = get_expenses($start, $end, null, null, $projects, false, false, -1, $filter_cleared);

        foreach($expenses as $entry)
        {
            $arr = ext_invoice_empty_entry();

            $arr['type'] = 'expense';
            $arr['desc'] = $entry['designation'];
            $arr['start'] = $entry['timestamp'];
            $arr['end'] = $entry['timestamp'];
            $arr['hour'] = null;
            $arr['fDuration'] = $entry['multiplier']; // @deprecated use duration instead
            $arr['duration'] = $entry['multiplier'];
            $arr['timestamp'] = $entry['timestamp'];
            $arr['amount'] = sprintf("%01.2f", $entry['value'] * $entry['multiplier']);
            $arr['description'] = $entry['designation'];
            $arr['rate'] = $entry['value'];
            $arr['comment'] = $entry['comment'];
            $arr['username'] = $entry['userName'];
            $arr['useralias'] = $entry['userAlias'];
            $arr['location'] = null;
            $arr['trackingNr'] = null;
            $arr['projectID'] = $entry['projectID'];
            $arr['projectName'] = $entry['projectName'];
            $arr['projectComment'] = $entry['projectComment'];

            // @deprecated expenses only: will be removed in the future, can be fetched otherwise
            $arr['activityName'] = $entry['designation'];
            // @deprecated expenses only: will be removed in the future, can be fetched otherwise
            $arr['multiplier'] = $entry['multiplier'];
            // @deprecated expenses only: will be removed in the future, can be fetched otherwise
            $arr['value'] = $entry['value'];

            invoice_add_to_array($results, $arr, $short_form);
        }
    }

    $allEntries = array();
    foreach($results as $entry)
    {
        if ($limitCommentSize) {
            $entry['comment'] = Kimai_Format::addEllipsis($entry['comment'], 150);
        }
        // FIXME use date_format_3 instead
        $entry['date'] = date("m/d/Y", $entry['timestamp']);

        $allEntries[] = $entry;
    }

    return $allEntries;
}

function invoice_add_to_array(&$array, $row, $short_form)
{
    global $activityIndexMap;

    if ($short_form && $row['type'] == 'timeSheet') {
        if (isset($activityIndexMap[$row['desc']])) {
            $index = $activityIndexMap[$row['desc']];
            $totalTime = $array[$index]['hour'];
            $totalAmount = $array[$index]['amount'];
            $start = $array[$index]['start'];
            $end = $array[$index]['end'];
            $duration = $array[$index]['duration'];
            $array[$index] = array(
                'type' => 'timeSheet',
                'desc' => $row['desc'],
                'start' => ($start < $row['start']) ? $start : $row['start'],
                'end' => ($end > $row['end']) ? $end : $row['end'],
                'hour' => $totalTime + $row['hour'],
                'fDuration' => Kimai_Format::formatDuration($duration + $row['duration']), // @deprecated use duration instead
                'duration' => $duration + $row['duration'],
                'timestamp' => ($start < $row['start']) ? $start : $row['start'],
                'amount' => $totalAmount + $row['amount'],
                'description' => $row['description'],
                'rate' => ($totalAmount + $row['amount']) / ($totalTime + $row['hour']),
                'comment' => $row['comment'],
                'username' => $row['username'],
                'useralias' => $row['useralias'],
                'location' => $row['location'],
                'trackingNr' => $row['trackingNr'],
                'projectID' => $row['projectID'],
                'projectName' => $row['projectName'],
                'projectComment' => $row['projectComment'],
                // FIXME use date_format_3 instead
                'date' => date("m/d/Y", $row['timestamp']),
            );
            return;
        } else {
            $activityIndexMap[$row['desc']] = count($array);
        }
    }
    $array[] = $row;
}

function ext_invoice_empty_entry()
{
    return array(
        'type' => null,
        'desc' => null,
        'start' => null,
        'end' => null,
        'hour' => null,
        'fDuration' => null, // @deprecated use duration instead
        'duration' => null,
        'timestamp' => null,
        'amount' => null,
        'description' => null,
        'rate' => null,
        'comment' => null,
        'username' => null,
        'useralias' => null,
        'location' => null,
        'trackingNr' => null,
        'projectID' => null,
        'projectName' => null,
        'projectComment' => null,
        'date' => null,
    );
}

function ext_invoice_sort_by_date_asc($a, $b)
{
    $aTime = $a['timestamp'];
    $bTime = $b['timestamp'];

    if ($aTime == $bTime) {
        return 0;
    }
    return ($aTime < $bTime) ? -1 : 1;
}

function ext_invoice_sort_by_date_desc($a, $b)
{
    $aTime = $a['timestamp'];
    $bTime = $b['timestamp'];

    if ($aTime == $bTime) {
        return 0;
    }
    return ($aTime > $bTime) ? -1 : 1;
}

function ext_invoice_sort_by_name($a, $b)
{
    return strcasecmp($a['desc'], $b['desc']);
}

function ext_invoice_round_value($value, $precision)
{
    // suppress division by zero error
    if ($precision == 0.0) {
        $precision = 1.0;
    }

    return floor($value / $precision + 0.5) * $precision;
}
