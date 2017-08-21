<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team since 2006
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

$all_column_headers = array(
    'date',
    'from',
    'to',
    'time',
    'dec_time',
    'rate',
    'wage',
    'budget',
    'approved',
    'status',
    'billable',
    'customer',
    'project',
    'activity',
    'description',
    'comment',
    'location',
    'trackingNumber',
    'user',
    'cleared'
);
// Determine if the expenses extension is used.
$expense_ext_available = false;
if (file_exists('../ki_expenses/private_db_layer_mysql.php')) {
    include('../ki_expenses/private_db_layer_mysql.php');
    $expense_ext_available = true;
}
include('private_db_layer_mysql.php');

/**
 * Get a combined array with time recordings and expenses to export.
 *
 * @param int $start Time from which to take entries into account.
 * @param int $end Time until which to take entries into account.
 * @param array $users Array of user IDs to filter by.
 * @param array $customers Array of customer IDs to filter by.
 * @param array $projects Array of project IDs to filter by.
 * @param array $activities Array of activity IDs to filter by.
 * @param bool $limit sbould the amount of entries be limited
 * @param bool $reverse_order should the entries be put out in reverse order
 * @param string $default_location use this string if no location is set for the entry
 * @param int $filter_cleared (-1: show all, 0:only cleared 1: only not cleared) entries
 * @param int $filter_type (-1 show time and expenses, 0: only show time entries, 1: only show expenses)
 * @param int $limitCommentSize should comments be cut off, when they are too long
 * @return array with time recordings and expenses chronologically sorted
 */
function export_get_data($start, $end, $users = null, $customers = null, $projects = null, $activities = null, $limit = false, $reverse_order = false, $default_location = '', $filter_cleared = -1, $filter_type = -1, $limitCommentSize = true, $filter_refundable = -1)
{
    global $expense_ext_available;
    $database = Kimai_Registry::getDatabase();
    $timeSheetEntries = array();
    $expenses = array();
    if ($filter_type != 1) {
        $timeSheetEntries = $database->get_timeSheet($start, $end, $users, $customers, $projects, $activities, $limit, $reverse_order, $filter_cleared);
    }

    if ($filter_type != 0 && $expense_ext_available) {
        $expenses = get_expenses($start, $end, $users, $customers, $projects, $limit, $reverse_order, $filter_refundable, $filter_cleared);
    }
    $result_arr = array();
    $timeSheetEntries_index = 0;
    $expenses_index = 0;
    $keys = array(
        'type',
        'id',
        'time_in',
        'time_out',
        'duration',
        'formattedDuration',
        'decimalDuration',
        'rate',
        'wage',
        'wage_decimal',
        'budget',
        'approved',
        'statusID',
        'status',
        'billable',
        'customerID',
        'customerName',
        'projectID',
        'projectName',
        'description',
        'projectComment',
        'activityID',
        'activityName',
        'comment',
        'commentType',
        'location',
        'trackingNumber',
        'username',
        'cleared'
    );
    while ($timeSheetEntries_index < count($timeSheetEntries) && $expenses_index < count($expenses)) {
        $arr = array();
        foreach ($keys as $key) {
            $arr[$key] = null;
        }
        $arr['location'] = $default_location;

        if ((! $reverse_order && ($timeSheetEntries[$timeSheetEntries_index]['start'] > $expenses[$expenses_index]['timestamp'])) || ($reverse_order && ($timeSheetEntries[$timeSheetEntries_index]['start'] < $expenses[$expenses_index]['timestamp']))) {
            if ($timeSheetEntries[$timeSheetEntries_index]['end'] != 0) {
                // active recordings will be omitted
                $arr['type'] = 'timeSheet';
                $arr['id'] = $timeSheetEntries[$timeSheetEntries_index]['timeEntryID'];
                $arr['time_in'] = $timeSheetEntries[$timeSheetEntries_index]['start'];
                $arr['time_out'] = $timeSheetEntries[$timeSheetEntries_index]['end'];
                $arr['duration'] = $timeSheetEntries[$timeSheetEntries_index]['duration'];
                $arr['formattedDuration'] = $timeSheetEntries[$timeSheetEntries_index]['formattedDuration'];
                $arr['decimalDuration'] = sprintf("%01.2f", $timeSheetEntries[$timeSheetEntries_index]['duration'] / 3600);
                $arr['rate'] = $timeSheetEntries[$timeSheetEntries_index]['rate'];
                $arr['wage'] = $timeSheetEntries[$timeSheetEntries_index]['wage'];
                $arr['wage_decimal'] = $timeSheetEntries[$timeSheetEntries_index]['wage_decimal'];
                $arr['budget'] = $timeSheetEntries[$timeSheetEntries_index]['budget'];
                $arr['approved'] = $timeSheetEntries[$timeSheetEntries_index]['approved'];
                $arr['statusID'] = $timeSheetEntries[$timeSheetEntries_index]['statusID'];
                $arr['status'] = $timeSheetEntries[$timeSheetEntries_index]['status'];
                $arr['billable'] = $timeSheetEntries[$timeSheetEntries_index]['billable'];
                $arr['customerID'] = $timeSheetEntries[$timeSheetEntries_index]['customerID'];
                $arr['customerName'] = $timeSheetEntries[$timeSheetEntries_index]['customerName'];
                $arr['projectID'] = $timeSheetEntries[$timeSheetEntries_index]['projectID'];
                $arr['projectName'] = $timeSheetEntries[$timeSheetEntries_index]['projectName'];
                $arr['description'] = $timeSheetEntries[$timeSheetEntries_index]['description'];
                $arr['projectComment'] = $timeSheetEntries[$timeSheetEntries_index]['projectComment'];
                $arr['activityID'] = $timeSheetEntries[$timeSheetEntries_index]['activityID'];
                $arr['activityName'] = $timeSheetEntries[$timeSheetEntries_index]['activityName'];
                if ($limitCommentSize) {
                    $arr['comment'] = Kimai_Format::addEllipsis($timeSheetEntries[$timeSheetEntries_index]['comment'], 150);
                } else {
                    $arr['comment'] = $timeSheetEntries[$timeSheetEntries_index]['comment'];
                }
                $arr['commentType'] = $timeSheetEntries[$timeSheetEntries_index]['commentType'];
                $arr['location'] = $timeSheetEntries[$timeSheetEntries_index]['location'];
                $arr['trackingNumber'] = $timeSheetEntries[$timeSheetEntries_index]['trackingNumber'];
                $arr['username'] = $timeSheetEntries[$timeSheetEntries_index]['userName'];
                $arr['cleared'] = $timeSheetEntries[$timeSheetEntries_index]['cleared'];
                $result_arr[] = $arr;
            }
            $timeSheetEntries_index++;
        } else {
            $arr['type'] = 'expense';
            $arr['id'] = $expenses[$expenses_index]['expenseID'];
            $arr['time_in'] = $expenses[$expenses_index]['timestamp'];
            $arr['time_out'] = $expenses[$expenses_index]['timestamp'];
            $arr['wage'] = sprintf("%01.2f", $expenses[$expenses_index]['value'] * $expenses[$expenses_index]['multiplier']);
            $arr['customerID'] = $expenses[$expenses_index]['customerID'];
            $arr['customerName'] = $expenses[$expenses_index]['customerName'];
            $arr['projectID'] = $expenses[$expenses_index]['projectID'];
            $arr['projectName'] = $expenses[$expenses_index]['projectName'];
            $arr['description'] = $expenses[$expenses_index]['designation'];
            $arr['projectComment'] = $expenses[$expenses_index]['projectComment'];
            if ($limitCommentSize) {
                $arr['comment'] = Kimai_Format::addEllipsis($expenses[$expenses_index]['comment'], 150);
            } else {
                $arr['comment'] = $expenses[$expenses_index]['comment'];
            }
            $arr['activityName'] = $expenses[$expenses_index]['designation'];
            $arr['comment'] = $expenses[$expenses_index]['comment'];
            $arr['commentType'] = $expenses[$expenses_index]['commentType'];
            $arr['username'] = $expenses[$expenses_index]['userName'];
            $arr['cleared'] = $expenses[$expenses_index]['cleared'];
            $result_arr[] = $arr;
            $expenses_index++;
        }
    }
    while ($timeSheetEntries_index < count($timeSheetEntries)) {
        if ($timeSheetEntries[$timeSheetEntries_index]['end'] != 0) {
            // active recordings will be omitted
            $arr = array();
            foreach ($keys as $key) {
                $arr[$key] = null;
            }
            $arr['location'] = $default_location;

            $arr['type'] = 'timeSheet';
            $arr['id'] = $timeSheetEntries[$timeSheetEntries_index]['timeEntryID'];
            $arr['time_in'] = $timeSheetEntries[$timeSheetEntries_index]['start'];
            $arr['time_out'] = $timeSheetEntries[$timeSheetEntries_index]['end'];
            $arr['duration'] = $timeSheetEntries[$timeSheetEntries_index]['duration'];
            $arr['formattedDuration'] = $timeSheetEntries[$timeSheetEntries_index]['formattedDuration'];
            $arr['decimalDuration'] = sprintf("%01.2f", $timeSheetEntries[$timeSheetEntries_index]['duration'] / 3600);
            $arr['rate'] = $timeSheetEntries[$timeSheetEntries_index]['rate'];
            $arr['wage'] = $timeSheetEntries[$timeSheetEntries_index]['wage'];
            $arr['wage_decimal'] = $timeSheetEntries[$timeSheetEntries_index]['wage_decimal'];
            $arr['budget'] = $timeSheetEntries[$timeSheetEntries_index]['budget'];
            $arr['approved'] = $timeSheetEntries[$timeSheetEntries_index]['approved'];
            $arr['statusID'] = $timeSheetEntries[$timeSheetEntries_index]['statusID'];
            $arr['status'] = $timeSheetEntries[$timeSheetEntries_index]['status'];
            $arr['billable'] = $timeSheetEntries[$timeSheetEntries_index]['billable'];
            $arr['customerID'] = $timeSheetEntries[$timeSheetEntries_index]['customerID'];
            $arr['customerName'] = $timeSheetEntries[$timeSheetEntries_index]['customerName'];
            $arr['projectID'] = $timeSheetEntries[$timeSheetEntries_index]['projectID'];
            $arr['projectName'] = $timeSheetEntries[$timeSheetEntries_index]['projectName'];
            $arr['projectComment'] = $timeSheetEntries[$timeSheetEntries_index]['projectComment'];
            $arr['activityID'] = $timeSheetEntries[$timeSheetEntries_index]['activityID'];
            $arr['activityName'] = $timeSheetEntries[$timeSheetEntries_index]['activityName'];
            $arr['description'] = $timeSheetEntries[$timeSheetEntries_index]['description'];
            if ($limitCommentSize) {
                $arr['comment'] = Kimai_Format::addEllipsis($timeSheetEntries[$timeSheetEntries_index]['comment'], 150);
            } else {
                $arr['comment'] = $timeSheetEntries[$timeSheetEntries_index]['comment'];
            }
            $arr['commentType'] = $timeSheetEntries[$timeSheetEntries_index]['commentType'];
            $arr['location'] = $timeSheetEntries[$timeSheetEntries_index]['location'];
            $arr['trackingNumber'] = $timeSheetEntries[$timeSheetEntries_index]['trackingNumber'];
            $arr['username'] = $timeSheetEntries[$timeSheetEntries_index]['userName'];
            $arr['cleared'] = $timeSheetEntries[$timeSheetEntries_index]['cleared'];
            $result_arr[] = $arr;
        }
        $timeSheetEntries_index++;
    }
    while ($expenses_index < count($expenses)) {
        $arr = array();
        foreach ($keys as $key) {
            $arr[$key] = null;
        }
        $arr['location'] = $default_location;

        $arr['type'] = 'expense';
        $arr['id'] = $expenses[$expenses_index]['expenseID'];
        $arr['time_in'] = $expenses[$expenses_index]['timestamp'];
        $arr['time_out'] = $expenses[$expenses_index]['timestamp'];
        $arr['wage'] = sprintf("%01.2f", $expenses[$expenses_index]['value'] * $expenses[$expenses_index]['multiplier']);
        $arr['customerID'] = $expenses[$expenses_index]['customerID'];
        $arr['customerName'] = $expenses[$expenses_index]['customerName'];
        $arr['projectID'] = $expenses[$expenses_index]['projectID'];
        $arr['projectName'] = $expenses[$expenses_index]['projectName'];
        $arr['description'] = $expenses[$expenses_index]['designation'];
        $arr['projectComment'] = $expenses[$expenses_index]['projectComment'];
        if ($limitCommentSize) {
            $arr['comment'] = Kimai_Format::addEllipsis($expenses[$expenses_index]['comment'], 150);
        } else {
            $arr['comment'] = $expenses[$expenses_index]['comment'];
        }
        $arr['commentType'] = $expenses[$expenses_index]['commentType'];
        $arr['username'] = $expenses[$expenses_index]['userName'];
        $arr['cleared'] = $expenses[$expenses_index]['cleared'];
        $expenses_index++;
        $result_arr[] = $arr;
    }

    return $result_arr;
}

/**
 * Merge the expense annotations with the timesheet annotations. The result will
 * be the timesheet array, which has to be passed as the first argument.
 *
 * @param array the timesheet annotations array
 * @param array the expense annotations array
 */
function merge_annotations(&$timeSheetEntries, &$expenses)
{
    foreach ($expenses as $id => $costs) {
        if (! isset($timeSheetEntries[$id])) {
            $timeSheetEntries[$id]['costs'] = $costs;
        } else {
            $timeSheetEntries[$id]['costs'] += $costs;
        }
    }
}

/**
 * Get annotations for the user sub list. Currently it's just the time, like
 * in the timesheet extension.
 *
 * @param int $start Time from which to take entries into account.
 * @param int $end Time until which to take entries into account.
 * @param array $users Array of user IDs to filter by.
 * @param array $customers Array of customer IDs to filter by.
 * @param array $projects Array of project IDs to filter by.
 * @param array $activities Array of activity IDs to filter by.
 * @return array Array which assigns every user (via his ID) the data to show.
 */
function export_get_user_annotations($start, $end, $users = null, $customers = null, $projects = null, $activities = null)
{
    global $expense_ext_available;
    $database = Kimai_Registry::getDatabase();
    $arr = $database->get_time_users($start, $end, $users, $customers, $projects, $activities);
    if ($expense_ext_available) {
        $expenses = expenses_by_user($start, $end, $users, $customers, $projects);
        merge_annotations($arr, $expenses);
    }

    return $arr;
}

/**
 * Get annotations for the customer sub list. Currently it's just the time, like
 * in the timesheet extension.
 *
 * @param int $start Time from which to take entries into account.
 * @param int $end Time until which to take entries into account.
 * @param array $users Array of user IDs to filter by.
 * @param array $customers Array of customer IDs to filter by.
 * @param array $projects Array of project IDs to filter by.
 * @param array $activities Array of activity IDs to filter by.
 * @return array Array which assigns every customer (via his ID) the data to show.
 */
function export_get_customer_annotations($start, $end, $users = null, $customers = null, $projects = null, $activities = null)
{
    global $expense_ext_available;
    $database = Kimai_Registry::getDatabase();
    $arr = $database->get_time_customers($start, $end, $users, $customers, $projects, $activities);
    if ($expense_ext_available) {
        $expenses = expenses_by_customer($start, $end, $users, $customers, $projects);
        merge_annotations($arr, $expenses);
    }

    return $arr;
}

/**
 * Get annotations for the project sub list. Currently it's just the time, like
 * in the timesheet extension.
 *
 * @param int $start Time from which to take entries into account.
 * @param int $end Time until which to take entries into account.
 * @param array $users Array of user IDs to filter by.
 * @param array $customers Array of customer IDs to filter by.
 * @param array $projects Array of project IDs to filter by.
 * @param array $activities Array of activity IDs to filter by.
 * @return array Array which assigns every project (via his ID) the data to show.
 */
function export_get_project_annotations($start, $end, $users = null, $customers = null, $projects = null, $activities = null)
{
    global $expense_ext_available;
    $database = Kimai_Registry::getDatabase();
    $arr = $database->get_time_projects($start, $end, $users, $customers, $projects, $activities);
    if ($expense_ext_available) {
        $expenses = expenses_by_project($start, $end, $users, $customers, $projects);
        merge_annotations($arr, $expenses);
    }

    return $arr;
}

/**
 * Get annotations for the activity sub list. Currently it's just the time, like
 * in the timesheet extension.
 *
 * @param int $start Time from which to take entries into account.
 * @param int $end Time until which to take entries into account.
 * @param array $users Array of user IDs to filter by.
 * @param array $customers Array of customer IDs to filter by.
 * @param array $projects Array of project IDs to filter by.
 * @param array $activities Array of activity IDs to filter by.
 * @return array Array which assigns every taks (via his ID) the data to show.
 */
function export_get_activity_annotations($start, $end, $users = null, $customers = null, $projects = null, $activities = null)
{
    $database = Kimai_Registry::getDatabase();
    return $database->get_time_activities($start, $end, $users, $customers, $projects, $activities);
}

/**
 * Prepare a string to be printed as a single field in the csv file.
 *
 * @param string $field String to prepare.
 * @param string $column_delimiter Character used to delimit columns.
 * @param string $quote_char Character used to quote strings.
 * @return string Correctly formatted string.
 */
function csv_prepare_field($field, $column_delimiter, $quote_char)
{
    if (strpos($field, $column_delimiter) === false && strpos($field, $quote_char) === false && strpos($field, "\n") === false) {
        return $field;
    }
    $field = str_replace($quote_char, $quote_char . $quote_char, $field);
    $field = $quote_char . $field . $quote_char;

    return $field;
}

/**
 * @param $seconds
 * @return string
 */
function secsToHTime($seconds)
{
    $hours = floor($seconds / (60 * 60));

    $divisor_for_minutes = $seconds % (60 * 60);
    $minutes = floor($divisor_for_minutes / 60);

    // extract the remaining seconds
    $divisor_for_seconds = $divisor_for_minutes % 60;
    $seconds = ceil($divisor_for_seconds);

    return $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'm' : '');
}

/**
 * @param array $rawData
 * @return string
 */
function prepareInddXML(array $rawData)
{
    global $kga;
    
    $dec_point = ".";
    $thousands_sep = "'";
    $dateformat = "Y m d";
    $prettifyXML = false; // true messes with Indd Newlines
    $collect_kndall = array();

    $line = 0;    // Line counter, to get an accurate "aid:trows" value
    // CS6 won't explain if these figures are wrong... it just fails.

    foreach ($rawData as $id => $vals) {
        // ruud re-arranging

        if ($vals['type'] == 'timeSheet') {
            $collect_kndall[$vals['customerID']]['customerID'] = $vals['customerID'];
            $collect_kndall[$vals['customerID']]['customerName'] = $vals['customerName'];
            $collect_kndall[$vals['customerID']]['pct_ARR'][$vals['projectID']]['projectID'] = $vals['projectID'];
            $collect_kndall[$vals['customerID']]['pct_ARR'][$vals['projectID']]['projectName'] = $vals['projectName'];
            $collect_kndall[$vals['customerID']]['pct_ARR'][$vals['projectID']]['zef_ARR'][] = $vals;
        } elseif ($vals['type'] == 'exp') {
            $collect_kndall[$vals['customerID']]['customerID'] = $vals['customerID'];
            $collect_kndall[$vals['customerID']]['customerName'] = $vals['customerName'];
            $collect_kndall[$vals['customerID']]['pct_ARR'][$vals['projectID']]['projectID'] = $vals['projectID'];
            $collect_kndall[$vals['customerID']]['pct_ARR'][$vals['projectID']]['projectName'] = $vals['projectName'];
            $collect_kndall[$vals['customerID']]['pct_ARR'][$vals['projectID']]['exp_ARR'][] = $vals;
        }
    }
    $starter = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Root></Root>';

    define('ADOBE_NAMESPACE', 'http://ns.adobe.com/AdobeInDesign/4.0/');
    $base = new SimpleXMLElement($starter);

    $Holder = $base->addChild('ProtocolHolder'); // Indd takes this as a Text Box
    $Protocol = $Holder->addChild('ProtocolTable');
    $Protocol->addAttribute('aid:table', 'table', ADOBE_NAMESPACE);

    // Defined at the End, to add the right numbar of lines:
    // $Protocol->addAttribute('aid:trows', $line, ADOBE_NAMESPACE);
    // $Protocol->addAttribute('aid:tcols', '7', ADOBE_NAMESPACE);

    // Aid:
    // Table: "aid:trows" and "aid:tcols" tell Indd what to expect later. Must be right. Indd CS6 won't explain. (I implemented $line therefor)
    // Cells: "aid:crows" and "aid:ccols" are eqivalent to colspan and rowspan in HTML. Must sum up right.
    // BUT: I think these spans sometimes prevent Indd CS6 from reloading/replacing a completely different XML.
    // Maybe the specs would allow a ROW parent to cells, just for visible debug ease, maybe not.

    foreach ($collect_kndall AS $pct_kndID => $pct_kndIDVALS) {
        $grandtotalALLPROJ = 0;
        $projcount = 0;

        // FULL LINE
        // More variants in formatting here, since these are titles... The Dot (.) is just aesthetics.
        $ProtocolTableCust = $Protocol->addChild('ProtocCustTitle', "");
        $ProtocolTableCust->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
        $ProtocolTableCust->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
        $ProtocolTableCust->addAttribute('aid:ccols', '7', ADOBE_NAMESPACE);
        $ProtocolTableCust->addChild('ProtocCustTitle.CUSTSTRING', "Protokoll: ");
        $ProtocolTableCust->addChild('ProtocCustTitle.CUSTVALUE', $pct_kndIDVALS['customerName']);
        $line++;

        foreach ($pct_kndIDVALS['pct_ARR'] AS $pct_ID => $pct_IDVALS) {
            $thisprojectname = $pct_IDVALS['projectName'];
            $grandtotal = 0;
            $ivehadatitle = false;
            if (isset($pct_IDVALS['zef_ARR'])) {
                $firsttodo = true;
                $total = 0;
                $time = 0;
                foreach ($pct_IDVALS['zef_ARR'] AS $zef_ARRVALS) {
                    // DOING WORKTIME

                    if ($firsttodo) {    // Strictly speaking this is not elegant...
                        $firsttodo = false;
                        // FULL LINE
                        $ProtocolTableTopic = $Protocol->addChild('ProtocType', "");
                        $ProtocolTableTopic->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableTopic->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableTopic->addAttribute('aid:ccols', '7', ADOBE_NAMESPACE);
                        $ProtocolTableTopic->addChild('ProtocType.PROJECTSTRING', "Projekt: ");
                        $ProtocolTableTopic->addChild('ProtocType.PROJECTVALUE', $thisprojectname);
                        $ivehadatitle = true;
                        $line++;
                        // FULL LINE
                        $ProtocolTableTopic = $Protocol->addChild('ProtocType', "");
                        $ProtocolTableTopic->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableTopic->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableTopic->addAttribute('aid:ccols', '7', ADOBE_NAMESPACE);
                        $ProtocolTableTopic->addChild('ProtocType.TYPESTRING', "Arbeit"); // Work (ZEF)
                        $line++;

                        // FULL LINE
                        // BTW these widths are repeated exactly at the title route in EXP below
                        $ProtocolTableZEFHeader = $Protocol->addChild('ProtocZEFHeader', 'Datum');
                        $ProtocolTableZEFHeader->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:ccolwidth', "57.54330708661416", ADOBE_NAMESPACE);

                        $ProtocolTableZEFHeader = $Protocol->addChild('ProtocZEFHeader', 'Dauer');
                        $ProtocolTableZEFHeader->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:ccolwidth', "41.10236220481494", ADOBE_NAMESPACE);

                        $ProtocolTableZEFHeader = $Protocol->addChild('ProtocZEFHeader', 'Satz');
                        $ProtocolTableZEFHeader->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:ccolwidth', "24.094488188885833", ADOBE_NAMESPACE);

                        $ProtocolTableZEFHeader = $Protocol->addChild('ProtocZEFHeader', 'Betrag');
                        $ProtocolTableZEFHeader->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:ccolwidth', "52.44094488188976", ADOBE_NAMESPACE);

                        $ProtocolTableZEFHeader = $Protocol->addChild('ProtocZEFHeader', 'Arbeit');
                        $ProtocolTableZEFHeader->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:ccolwidth', "92.62598425187798", ADOBE_NAMESPACE);

                        $ProtocolTableZEFHeader = $Protocol->addChild('ProtocZEFHeader', 'Kommentar');
                        $ProtocolTableZEFHeader->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:ccolwidth', "229.106299212689", ADOBE_NAMESPACE);

                        $ProtocolTableZEFHeader = $Protocol->addChild('ProtocZEFHeader', 'C');
                        $ProtocolTableZEFHeader->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                        $ProtocolTableZEFHeader->addAttribute('aid:ccolwidth', "15.023622047153538", ADOBE_NAMESPACE);
                        $line++;
                    }

                    // The acctual ZEF entries
                    // FULL LINE
                    $ProtocolTableZEFEntry = $Protocol->addChild('ProtocZEFEntries', date($dateformat, $zef_ARRVALS['time_in']));
                    $ProtocolTableZEFEntry->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                    $ProtocolTableZEFEntry->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                    $ProtocolTableZEFEntry->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);

                    $ProtocolTableZEFEntry = $Protocol->addChild('ProtocZEFEntries', secsToHTime($zef_ARRVALS['duration']));
                    $ProtocolTableZEFEntry->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                    $ProtocolTableZEFEntry->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                    $ProtocolTableZEFEntry->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                    $time += $zef_ARRVALS['duration'];

                    $ProtocolTableZEFEntry = $Protocol->addChild('ProtocZEFEntries', intval($zef_ARRVALS['rate']));
                    $ProtocolTableZEFEntry->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                    $ProtocolTableZEFEntry->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                    $ProtocolTableZEFEntry->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);

                    $ProtocolTableZEFEntry = $Protocol->addChild('ProtocZEFEntriesWage', $zef_ARRVALS['wage']);
                    $ProtocolTableZEFEntry->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                    $ProtocolTableZEFEntry->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                    $ProtocolTableZEFEntry->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                    $total += $zef_ARRVALS['wage'];

                    $ProtocolTableZEFEntry = $Protocol->addChild('ProtocZEFEntries', $zef_ARRVALS['activityName']);
                    $ProtocolTableZEFEntry->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                    $ProtocolTableZEFEntry->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                    $ProtocolTableZEFEntry->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);

                    // Rubish Cleanup
                    $thisentry = str_replace("Fastin:", "", $zef_ARRVALS['comment']);
                    $thisentry = str_replace("F:", "", $thisentry);
                    $thisentry = str_replace("\n", "", $thisentry); // may have to fiddle with this one. Indd CS6 takes a lot for a newline.
                    $thisentry = preg_replace('/^\s*/', "", $thisentry);
                    $thisentry = preg_replace('/\s*$/', "", $thisentry); // is this trim()? ;-)
                    $ProtocolTableZEFEntry = $Protocol->addChild('ProtocZEFEntries', $thisentry);
                    $ProtocolTableZEFEntry->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                    $ProtocolTableZEFEntry->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                    $ProtocolTableZEFEntry->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);

                    $ProtocolTableZEFEntry = $Protocol->addChild('ProtocZEFEntries', $zef_ARRVALS['cleared'] == 1 ? "c" : "");
                    $ProtocolTableZEFEntry->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                    $ProtocolTableZEFEntry->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                    $ProtocolTableZEFEntry->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                    $line++;
                } // FI foreach($pct_IDVALS['zef_ARR'] 

                // Sum ups with special formatting on "wage"
                // FULL LINE
                $ProtocolTableZEFFooter = $Protocol->addChild('ProtocZEFFooter', '');
                $ProtocolTableZEFFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter = $Protocol->addChild('ProtocZEFFooter', secsToHTime($time));
                $ProtocolTableZEFFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter = $Protocol->addChild('ProtocZEFFooter', '');
                $ProtocolTableZEFFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter = $Protocol->addChild('ProtocZEFFooterWage', number_format($total, 2, $dec_point, $thousands_sep) . "");

                $ProtocolTableZEFFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                $grandtotal += $total;

                $ProtocolTableZEFFooter = $Protocol->addChild('ProtocZEFFooter', '');
                $ProtocolTableZEFFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter = $Protocol->addChild('ProtocZEFFooter', '');
                $ProtocolTableZEFFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter = $Protocol->addChild('ProtocZEFFooter', '');
                $ProtocolTableZEFFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                $line++;

                // FULL LINE
                $ProtocolTableZEFFooter = $Protocol->addChild('ProtocZEFSpacer', '');
                $ProtocolTableZEFFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableZEFFooter->addAttribute('aid:ccols', '7', ADOBE_NAMESPACE);
                $line++;
            }

            if (isset($pct_IDVALS['exp_ARR'])) {
                $firsttodo = true;
                $total = 0;
                foreach ($pct_IDVALS['exp_ARR'] AS $exp_ARRVALS) {
                    // DOING EXPENSES
                    if ($firsttodo) {
                        $firsttodo = false;
                        // FULL LINE
                        if (!$ivehadatitle) {
                            $ProtocolTableEXPFooter = $Protocol->addChild('ProtocType', "");
                            $ProtocolTableEXPFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                            $ProtocolTableEXPFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                            $ProtocolTableEXPFooter->addAttribute('aid:ccols', '7', ADOBE_NAMESPACE);
                            $ProtocolTableEXPFooter->addChild('ProtocType.PROJECTSTRING', "Projekt: ");
                            $ProtocolTableEXPFooter->addChild('ProtocType.PROJECTVALUE', $thisprojectname);
                        }
                        $line++;
                        // FULL LINE
                        $ProtocolTableEXPFooter = $Protocol->addChild('ProtocType', "");
                        $ProtocolTableEXPFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableEXPFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableEXPFooter->addAttribute('aid:ccols', '7', ADOBE_NAMESPACE);
                        $ProtocolTableEXPFooter->addChild('ProtocType.TYPESTRING', "Spesen"); // Espenses
                        $line++;

                        // FULL LINE
                        $ProtocolTableEXPHeader = $Protocol->addChild('ProtocEXPHeader', 'Datum');
                        $ProtocolTableEXPHeader->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:ccolwidth', "57.54330708661416", ADOBE_NAMESPACE);

                        $ProtocolTableEXPHeader = $Protocol->addChild('ProtocEXPHeader', '');
                        $ProtocolTableEXPHeader->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:ccolwidth', "41.10236220481494", ADOBE_NAMESPACE);

                        $ProtocolTableEXPHeader = $Protocol->addChild('ProtocEXPHeader', '');
                        $ProtocolTableEXPHeader->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:ccolwidth', "24.094488188885833", ADOBE_NAMESPACE);

                        $ProtocolTableEXPHeader = $Protocol->addChild('ProtocEXPHeader', 'Betrag');
                        $ProtocolTableEXPHeader->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:ccolwidth', "52.44094488188976", ADOBE_NAMESPACE);

                        $ProtocolTableEXPHeader = $Protocol->addChild('ProtocEXPHeader', '');
                        $ProtocolTableEXPHeader->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:ccolwidth', "92.62598425187798", ADOBE_NAMESPACE);

                        $ProtocolTableEXPHeader = $Protocol->addChild('ProtocEXPHeader', 'Kommentar');
                        $ProtocolTableEXPHeader->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:ccolwidth', "229.106299212689", ADOBE_NAMESPACE);

                        $ProtocolTableEXPHeader = $Protocol->addChild('ProtocEXPHeader', 'C');
                        $ProtocolTableEXPHeader->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                        $ProtocolTableEXPHeader->addAttribute('aid:ccolwidth', "15.023622047153538", ADOBE_NAMESPACE);
                        $line++;
                    }

                    // The acctual EXP entries

                    // FULL LINE
                    $ProtocolTableEXPEntry = $Protocol->addChild('ProtocEXPEntries', date($dateformat, $exp_ARRVALS['time_in']));
                    $ProtocolTableEXPEntry->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                    $ProtocolTableEXPEntry->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                    $ProtocolTableEXPEntry->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);

                    $ProtocolTableEXPEntry = $Protocol->addChild('ProtocEXPEntries', "");
                    $ProtocolTableEXPEntry->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                    $ProtocolTableEXPEntry->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                    $ProtocolTableEXPEntry->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);

                    $ProtocolTableEXPEntry = $Protocol->addChild('ProtocEXPEntries', "");
                    $ProtocolTableEXPEntry->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                    $ProtocolTableEXPEntry->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                    $ProtocolTableEXPEntry->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);

                    $ProtocolTableEXPEntry = $Protocol->addChild('ProtocEXPEntriesWage', $exp_ARRVALS['wage']);
                    $ProtocolTableEXPEntry->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                    $ProtocolTableEXPEntry->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                    $ProtocolTableEXPEntry->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                    $total += $exp_ARRVALS['wage'];

                    $ProtocolTableEXPEntry = $Protocol->addChild('ProtocEXPEntries', "");
                    $ProtocolTableEXPEntry->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                    $ProtocolTableEXPEntry->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                    $ProtocolTableEXPEntry->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);

                    // Rubish Cleanup
                    $thisentry = str_replace("Fastin:", "", $exp_ARRVALS['comment']);
                    $thisentry = str_replace("F:", "", $thisentry);
                    $thisentry = str_replace("\n", "", $thisentry); // may have to fiddle with this one. Indd CS6 takes a lot for a newline.
                    $thisentry = preg_replace('/^\s*/', "", $thisentry);
                    $thisentry = preg_replace('/\s*$/', "", $thisentry);
                    $ProtocolTableEXPEntry = $Protocol->addChild('ProtocEXPEntries', $thisentry);
                    $ProtocolTableEXPEntry->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                    $ProtocolTableEXPEntry->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                    $ProtocolTableEXPEntry->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);

                    $ProtocolTableEXPEntry = $Protocol->addChild('ProtocEXPEntries', $exp_ARRVALS['cleared'] == 1 ? "c" : "");
                    $ProtocolTableEXPEntry->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                    $ProtocolTableEXPEntry->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                    $ProtocolTableEXPEntry->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                    $line++;
                }
                
                // Sum ups with special formatting on "wage"
                // FULL LINE
                $ProtocolTableEXPFooter = $Protocol->addChild('ProtocEXPFooter', '');
                $ProtocolTableEXPFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter = $Protocol->addChild('ProtocEXPFooter', '');
                $ProtocolTableEXPFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter = $Protocol->addChild('ProtocEXPFooter', '');
                $ProtocolTableEXPFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter = $Protocol->addChild('ProtocEXPFooterWage', number_format($total, 2, $dec_point, $thousands_sep) . "");
                $ProtocolTableEXPFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                $grandtotal += $total;
                $ProtocolTableEXPFooter = $Protocol->addChild('ProtocEXPFooter', '');
                $ProtocolTableEXPFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter = $Protocol->addChild('ProtocEXPFooter', '');
                $ProtocolTableEXPFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter = $Protocol->addChild('ProtocEXPFooter', '');
                $ProtocolTableEXPFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:ccols', '1', ADOBE_NAMESPACE);
                $line++;

                // FULL LINE
                $ProtocolTableEXPFooter = $Protocol->addChild('ProtocEXPSpacer', '');
                $ProtocolTableEXPFooter->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
                $ProtocolTableEXPFooter->addAttribute('aid:ccols', '7', ADOBE_NAMESPACE);
                $line++;
            }

            // FULL LINE
            // In Switzerland we write 500.- Fr. instead of 500.00 Fr.
            $numform = str_replace($dec_point . '00', $dec_point . '-', number_format($grandtotal, 2, $dec_point, $thousands_sep));
            $ProtocolTableLastspacer = $Protocol->addChild('ProtocCustGrandtotalTXT', "$thisprojectname");
            $ProtocolTableLastspacer->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
            $ProtocolTableLastspacer->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
            $ProtocolTableLastspacer->addAttribute('aid:ccols', '3', ADOBE_NAMESPACE);
            $ProtocolTableLastspacer = $Protocol->addChild('ProtocCustGrandtotalVAL', $numform . ' ' . $kga['conf']['currency_sign']);
            $ProtocolTableLastspacer->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
            $ProtocolTableLastspacer->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
            $ProtocolTableLastspacer->addAttribute('aid:ccols', '4', ADOBE_NAMESPACE);
            $grandtotalALLPROJ += $grandtotal;
            $line++;

            // FULL LINE
            $ProtocolTableLastspacer = $Protocol->addChild('ProtocProJLastSpacer', '');
            $ProtocolTableLastspacer->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
            $ProtocolTableLastspacer->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
            $ProtocolTableLastspacer->addAttribute('aid:ccols', '7', ADOBE_NAMESPACE);
            $line++;

            $projcount++;
        }

        if ($projcount > 1) {
            // Helper Line, possibly useless. Combines all Projects' totals.
            // FULL LINE
            $numform = str_replace($dec_point . "00", $dec_point . "-", number_format($grandtotalALLPROJ, 2, $dec_point, $thousands_sep));
            $ProtocolTableLastspacer = $Protocol->addChild('ProtocCustGrandtotalAllproj', "Zusammen " . $numform . ' ' . $kga['conf']['currency_sign']);
            $ProtocolTableLastspacer->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
            $ProtocolTableLastspacer->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
            $ProtocolTableLastspacer->addAttribute('aid:ccols', '7', ADOBE_NAMESPACE);
            $line++;
        }

        // FULL LINE
        $ProtocolTableLastspacer = $Protocol->addChild('ProtocCustLastSpacer', "");
        $ProtocolTableLastspacer->addAttribute('aid:table', 'cell', ADOBE_NAMESPACE);
        $ProtocolTableLastspacer->addAttribute('aid:crows', '1', ADOBE_NAMESPACE);
        $ProtocolTableLastspacer->addAttribute('aid:ccols', '7', ADOBE_NAMESPACE);
        $line++;
    }

    // because $line
    $Protocol->addAttribute('aid:trows', $line, ADOBE_NAMESPACE);
    $Protocol->addAttribute('aid:tcols', '7', ADOBE_NAMESPACE);

    // Roundtrip for debugging the XML structure. $prettifyXML = false, means passthrough
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->loadXML($base->asXML()); //$base handover
    $dom->formatOutput = $prettifyXML; // TRUE is prettify

    return $dom->saveXml();
}