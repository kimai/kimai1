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

// Determine if the expenses extension is used.
$expense_ext_available = false;
if (file_exists('../ki_expenses/private_db_layer_mysql.php')) {
    include('../ki_expenses/private_db_layer_mysql.php');
    $expense_ext_available = true;
}

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
    global $expense_ext_available, $database;

    $limitCommentSize = true;

    $expenses = array();
    $results = array();
    $timeSheetEntries_index = 0;
    $expenses_index = 0;

    $timeSheetEntries = $database->get_timeSheet($start, $end, null, null, $projects, null, false, false, $filter_cleared);
    if ($expense_ext_available) {
        $expenses = get_expenses($start, $end, null, null, $projects, false, false, -1, $filter_cleared);
    }

    while ($timeSheetEntries_index < count($timeSheetEntries) && $expenses_index < count($expenses))
    {
        $arr = ext_invoice_empty_entry();

        if ($timeSheetEntries[$timeSheetEntries_index]['start'] > $expenses[$expenses_index]['timestamp'])
        {
            $index = $timeSheetEntries_index++;

            // active recordings will be omitted
            if ($timeSheetEntries[$index]['end'] == 0) {
                continue;
            }

            $arr['type'] = 'timeSheet';
            $arr['desc'] = $timeSheetEntries[$index]['activityName'];
            $arr['hour'] = $timeSheetEntries[$index]['duration'] / 3600;
            $arr['duration'] = $timeSheetEntries[$index]['formattedDuration'];
            $arr['amount'] = $timeSheetEntries[$index]['wage'];
            $arr['date'] = date("m/d/Y", $timeSheetEntries[$index]['start']);
            $arr['description'] = $timeSheetEntries[$index]['description'];
            $arr['rate'] = $timeSheetEntries[$index]['rate'];
            $arr['comment'] = $timeSheetEntries[$index]['comment'];
            $arr['username'] = $timeSheetEntries[$index]['userName'];
            $arr['useralias'] = $timeSheetEntries[$index]['userAlias'];
            $arr['location'] = $timeSheetEntries[$index]['location'];
            $arr['trackingNr'] = $timeSheetEntries[$index]['trackingNumber'];
        }
        else
        {
            $arr['type'] = 'expense';
            $arr['desc'] = $expenses[$expenses_index]['designation'];
            $arr['hour'] = null;
            $arr['duration'] = $expenses[$expenses_index]['multiplier'];
            $arr['amount'] = sprintf("%01.2f", $expenses[$expenses_index]['value'] * $expenses[$expenses_index]['multiplier']);
            $arr['date'] = date("m/d/Y", $expenses[$expenses_index]['timestamp']);
            $arr['description'] = $expenses[$expenses_index]['designation'];
            $arr['rate'] = $expenses[$expenses_index]['value'];
            $arr['comment'] = $expenses[$expenses_index]['comment'];
            $arr['username'] = $expenses[$expenses_index]['userName'];
            $arr['useralias'] = $expenses[$expenses_index]['userAlias'];
            $arr['location'] = null;
            $arr['trackingNr'] = null;

            // TODO these are only available here, can we delete them?
            $arr['activityName'] = $expenses[$expenses_index]['designation'];
            $arr['multiplier'] = $expenses[$expenses_index]['multiplier'];
            $arr['value'] = $expenses[$expenses_index]['value'];
            $expenses_index++;
        }

        invoice_add_to_array($results, $arr, $short_form);
    }

    // timesheet entries
    while ($timeSheetEntries_index < count($timeSheetEntries))
    {
        $index = $timeSheetEntries_index++;

        // active recordings will be omitted
        if ($timeSheetEntries[$index]['end'] == 0) {
            continue;
        }

        $arr = ext_invoice_empty_entry();

        $arr['type'] = 'timeSheet';
        $arr['desc'] = $timeSheetEntries[$index]['activityName'];
        $arr['hour'] = $timeSheetEntries[$index]['duration'] / 3600;
        $arr['duration'] = $timeSheetEntries[$index]['formattedDuration'];
        $arr['amount'] = $timeSheetEntries[$index]['wage'];
        $arr['date'] = date("m/d/Y", $timeSheetEntries[$index]['start']);
        $arr['description'] = $timeSheetEntries[$index]['description'];
        $arr['rate'] = $timeSheetEntries[$index]['rate'];
        $arr['comment'] = $timeSheetEntries[$index]['comment'];
        $arr['username'] = $timeSheetEntries[$index]['userName'];
        $arr['useralias'] = $timeSheetEntries[$index]['userAlias'];
        $arr['location'] = $timeSheetEntries[$index]['location'];
        $arr['trackingNr'] = $timeSheetEntries[$index]['trackingNumber'];

        invoice_add_to_array($results, $arr, $short_form);
    }

    // expenses entries
    while ($expenses_index < count($expenses)) {
        $arr = ext_invoice_empty_entry();

        $arr['type'] = 'expense';
        $arr['desc'] = $expenses[$expenses_index]['designation'];
        $arr['hour'] = null;
        $arr['duration'] = $expenses[$expenses_index]['multiplier'];
        $arr['amount'] = sprintf("%01.2f", $expenses[$expenses_index]['value'] * $expenses[$expenses_index]['multiplier']);
        $arr['date'] = date("m/d/Y", $expenses[$expenses_index]['timestamp']);
        $arr['description'] = $expenses[$expenses_index]['designation'];
        $arr['rate'] = $expenses[$expenses_index]['value'];
        $arr['comment'] = $expenses[$expenses_index]['comment'];
        $arr['username'] = $expenses[$expenses_index]['userName'];
        $arr['useralias'] = $expenses[$expenses_index]['userAlias'];
        $arr['location'] = null;
        $arr['trackingNr'] = null;

        // TODO these are only available here, can we delete them?
        $arr['activityName'] = $expenses[$expenses_index]['designation'];
        $arr['multiplier'] = $expenses[$expenses_index]['multiplier'];
        $arr['value'] = $expenses[$expenses_index]['value'];

        $expenses_index++;

        invoice_add_to_array($results, $arr, $short_form);
    }

    $allEntries = array();
    foreach($results as $entry)
    {
        if ($limitCommentSize) {
            $entry['comment'] = Kimai_Format::addEllipsis($entry['comment'], 150);
        }

        $allEntries[] = $entry;
    }

    return $allEntries;
}

function ext_invoice_empty_entry()
{
    return array(
        'type' => null,
        'desc' => null,
        'hour' => null,
        'duration' => null,
        'amount' => null,
        'date' => null,
        'description' => null,
        'rate' => null,
        'comment' => null,
        'username' => null,
        'useralias' => null,
        'location' => null,
        'trackingNr => null'
    );
}

function invoice_add_to_array(&$array, $row, $short_form)
{
    global $activityIndexMap;

    if ($short_form && $row['type'] == 'timeSheet') {
        if (isset($activityIndexMap[$row['desc']])) {
            $index = $activityIndexMap[$row['desc']];
            $totalTime = $array[$index]['hour'];
            $totalAmount = $array[$index]['amount'];
            $array[$index] = array(
                'type' => 'timeSheet',
                'location' => $row['location'],
                'desc' => $row['desc'],
                'hour' => $totalTime + $row['hour'],
                'duration' => $row['duration'],
                'amount' => $totalAmount + $row['amount'],
                'date' => $row['date'],
                'description' => $row['description'],
                'rate' => ($totalAmount + $row['amount']) / ($totalTime + $row['hour']),
                'trackingNr' => $row['trackingNr'],
                'comment' => $row['comment'],
                'username' => $row['username'],
                'useralias' => $row['useralias']
            );
            return;
        } else {
            $activityIndexMap[$row['desc']] = count($array);
        }
    }
    $array[] = $row;
}

function ext_invoice_sort_by_date_asc($a, $b)
{
    $aTime = DateTime::createFromFormat('m/d/Y', $a['date'])->getTimestamp();
    $bTime = DateTime::createFromFormat('m/d/Y', $b['date'])->getTimestamp();

    if ($aTime == $bTime) {
        return 0;
    }
    return ($aTime < $bTime) ? -1 : 1;
}

function ext_invoice_sort_by_date_desc($a, $b)
{
    $aTime = DateTime::createFromFormat('m/d/Y', $a['date'])->getTimestamp();
    $bTime = DateTime::createFromFormat('m/d/Y', $b['date'])->getTimestamp();

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
