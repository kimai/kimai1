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
if (file_exists('../ki_expenses/private_db_layer_' . $kga['server_conn'] . '.php')) {
    include('../ki_expenses/private_db_layer_' . $kga['server_conn'] . '.php');
    $expense_ext_available = true;
}


$activityIndexMap = array(); // when creating the short form contains index of each activity in the array
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
                'fduration' => $row['fduration'],
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

/**
 * Get a combined array with time recordings and expenses to export.
 *
 * TODO this method is the worst nightmare i have seen in month - kevin
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
    $limit = false;
    $reverse_order = false;
    $limitCommentSize = true;
    $filter_refundable = -1;
    $timeSheetEntries = array();
    $expenses = array();
    $timeSheetEntries = $database->get_timeSheet($start, $end, null, null, $projects, null, $limit, $reverse_order, $filter_cleared);
    if ($expense_ext_available)
        $expenses = get_expenses($start, $end, null, null, $projects, $limit, $reverse_order, $filter_refundable, $filter_cleared);
    $result_arr = array();
    $timeSheetEntries_index = 0;
    $expenses_index = 0;
    $keys = array('type', 'desc', 'hour', 'fduration', 'amount', 'date', 'description', 'rate', 'comment', 'username', 'useralias', 'location');
    while ($timeSheetEntries_index < count($timeSheetEntries) && $expenses_index < count($expenses)) {
        $arr = array();
        foreach ($keys as $key)
            $arr[$key] = null;

        if ((!$reverse_order && ($timeSheetEntries[$timeSheetEntries_index]['start'] > $expenses[$expenses_index]['timestamp'])) || ($reverse_order && ($timeSheetEntries[$timeSheetEntries_index]['start'] < $expenses[$expenses_index]['timestamp']))) {
            if ($timeSheetEntries[$timeSheetEntries_index]['end'] != 0) {
                // active recordings will be omitted
                $arr['type'] = 'timeSheet';
                $arr['location'] = $timeSheetEntries[$timeSheetEntries_index]['location'];
                $arr['desc'] = $timeSheetEntries[$timeSheetEntries_index]['activityName'];
                $arr['hour'] = $timeSheetEntries[$timeSheetEntries_index]['duration'] / 3600;
                $arr['fDuration'] = $timeSheetEntries[$timeSheetEntries_index]['formattedDuration'];
                $arr['amount'] = $timeSheetEntries[$timeSheetEntries_index]['wage'];
                $arr['date'] = date("m/d/Y", $timeSheetEntries[$timeSheetEntries_index]['start']);
                $arr['description'] = $timeSheetEntries[$timeSheetEntries_index]['description'];
                $arr['rate'] = $timeSheetEntries[$timeSheetEntries_index]['rate'];
                $arr['trackingNr'] = $timeSheetEntries[$timeSheetEntries_index]['trackingNumber'];
                if ($limitCommentSize)
                    $arr['comment'] = Format::addEllipsis($timeSheetEntries[$timeSheetEntries_index]['comment'], 150);
                else
                    $arr['comment'] = $timeSheetEntries[$timeSheetEntries_index]['comment'];
                $arr['username'] = $timeSheetEntries[$timeSheetEntries_index]['userName'];
                $arr['useralias'] = $timeSheetEntries[$timeSheetEntries_index]['userAlias'];
            }
            $timeSheetEntries_index++;
        } else {
            $arr['type'] = 'expense';
            $arr['desc'] = $expenses[$expenses_index]['designation'];
            $arr['multiplier'] = $expenses[$expenses_index]['multiplier'];
            $arr['value'] = $expenses[$expenses_index]['value'];
            $arr['fDuration'] = $expenses[$expenses_index]['multiplier'];
            $arr['amount'] = sprintf("%01.2f", $expenses[$expenses_index]['value'] * $expenses[$expenses_index]['multiplier']);
            $arr['date'] = date("m/d/Y", $expenses[$expenses_index]['timestamp']);
            $arr['rate'] = $expenses[$expenses_index]['value'];
            if ($limitCommentSize)
                $arr['comment'] = Format::addEllipsis($expenses[$expenses_index]['comment'], 150);
            else
                $arr['comment'] = $expenses[$expenses_index]['comment'];
            $arr['activityName'] = $expenses[$expenses_index]['designation'];
            $arr['username'] = $expenses[$expenses_index]['userName'];
            $arr['useralias'] = $expenses[$expenses_index]['userAlias'];
            $expenses_index++;
        }

        invoice_add_to_array($result_arr, $arr, $short_form);
    }

    // timesheet entries
    while ($timeSheetEntries_index < count($timeSheetEntries)) {
        if ($timeSheetEntries[$timeSheetEntries_index]['end'] != 0) {
            // active recordings will be omitted
            $arr = array();
            foreach ($keys as $key)
                $arr[$key] = null;

            $arr['type'] = 'timeSheet';
            $arr['location'] = $timeSheetEntries[$timeSheetEntries_index]['location'];
            $arr['desc'] = $timeSheetEntries[$timeSheetEntries_index]['activityName'];
            $arr['hour'] = $timeSheetEntries[$timeSheetEntries_index]['duration'] / 3600;
            $arr['fDuration'] = $timeSheetEntries[$timeSheetEntries_index]['formattedDuration'];
            $arr['amount'] = $timeSheetEntries[$timeSheetEntries_index]['wage'];
            $arr['date'] = date("m/d/Y", $timeSheetEntries[$timeSheetEntries_index]['start']);
            $arr['description'] = $timeSheetEntries[$timeSheetEntries_index]['description'];
            $arr['rate'] = $timeSheetEntries[$timeSheetEntries_index]['rate'];
            $arr['trackingNr'] = $timeSheetEntries[$timeSheetEntries_index]['trackingNumber'];
            if ($limitCommentSize)
                $arr['comment'] = Format::addEllipsis($timeSheetEntries[$timeSheetEntries_index]['comment'], 150);
            else
                $arr['comment'] = $timeSheetEntries[$timeSheetEntries_index]['comment'];
            $arr['username'] = $timeSheetEntries[$timeSheetEntries_index]['userName'];
            $arr['useralias'] = $timeSheetEntries[$timeSheetEntries_index]['userAlias'];
            invoice_add_to_array($result_arr, $arr, $short_form);
        }
        $timeSheetEntries_index++;
    }

    // expenses entries
    while ($expenses_index < count($expenses)) {
        $arr = array();
        foreach ($keys as $key)
            $arr[$key] = null;

        $arr['type'] = 'expense';
        $arr['desc'] = $expenses[$expenses_index]['designation'];
        $arr['multiplier'] = $expenses[$expenses_index]['multiplier'];
        $arr['value'] = $expenses[$expenses_index]['value'];
        $arr['fDuration'] = $expenses[$expenses_index]['multiplier'];
        $arr['amount'] = sprintf("%01.2f", $expenses[$expenses_index]['value'] * $expenses[$expenses_index]['multiplier']);
        $arr['date'] = date("m/d/Y", $expenses[$expenses_index]['timestamp']);
        $arr['rate'] = $expenses[$expenses_index]['value'];
        if ($limitCommentSize)
            $arr['comment'] = Format::addEllipsis($expenses[$expenses_index]['comment'], 150);
        else
            $arr['comment'] = $expenses[$expenses_index]['comment'];
        $arr['activityName'] = $expenses[$expenses_index]['designation'];
        $arr['username'] = $expenses[$expenses_index]['userName'];
        $arr['useralias'] = $expenses[$expenses_index]['userAlias'];
        $expenses_index++;
        invoice_add_to_array($result_arr, $arr, $short_form);
    }

    return $result_arr;
}