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
$all_column_headers = array('date', 'from', 'to', 'time', 'dec_time', 'rate', 'wage', 'knd', 'pct', 'evt', 'comment', 'location', 'trackingnr', 'user', 'cleared');
// Determine if the expenses extension is used.
$expense_ext_available = false;
if (file_exists('../ki_expenses/private_db_layer_' . $kga['server_conn'] . '.php')) {
	include ('../ki_expenses/private_db_layer_' . $kga['server_conn'] . '.php');
	$expense_ext_available = true;
}
include ('private_db_layer_' . $kga['server_conn'] . '.php');

/**
 * Get a combined array with time recordings and expenses to export.
 *
 * @param int $start Time from which to take entries into account.
 * @param int $end Time until which to take entries into account.
 * @param array $users Array of user IDs to filter by.
 * @param array $customers Array of customer IDs to filter by.
 * @param array $projects Array of project IDs to filter by.
 * @param array $events Array of event IDs to filter by.
 * @param bool $limit sbould the amount of entries be limited
 * @param bool $reverse_order should the entries be put out in reverse order
 * @param string $default_location use this string if no location is set for the entry
 * @param int $filter_cleared (-1: show all, 0:only cleared 1: only not cleared) entries
 * @param int $filter_type (-1 show time and expenses, 0: only show time entries, 1: only show expenses)
 * @param int $limitCommentSize should comments be cut off, when they are too long
 * @return array with time recordings and expenses chronologically sorted
 */
function xp_get_arr($start, $end, $users = null, $customers = null, $projects = null, $events = null, $limit = false, $reverse_order = false, $default_location = '', $filter_cleared = -1, $filter_type = -1, $limitCommentSize = true, $filter_refundable = -1) {
	global $expense_ext_available, $database;
	$zef_arr = array();
	$exp_arr = array();
	if ($filter_type != 1)
		$zef_arr = $database->get_arr_timeSheet($start, $end, $users, $customers, $projects, $events, $limit, $reverse_order, $filter_cleared);
		
		if ($filter_type != 0 && $expense_ext_available)
		$exp_arr = get_arr_exp($start, $end, $users, $customers, $projects, $limit, $reverse_order, $filter_refundable, $filter_cleared);
	$result_arr = array();
	$zef_arr_index = 0;
	$exp_arr_index = 0;
	while ($zef_arr_index < count($zef_arr) && $exp_arr_index < count($exp_arr)) {
		$arr = array();
		if ((! $reverse_order && ($zef_arr[$zef_arr_index]['zef_in'] > $exp_arr[$exp_arr_index]['exp_timestamp'])) || ($reverse_order && ($zef_arr[$zef_arr_index]['zef_in'] < $exp_arr[$exp_arr_index]['exp_timestamp']))) {
			if ($zef_arr[$zef_arr_index]['zef_out'] != 0) {
				// active recordings will be omitted
				$arr['type'] = 'zef';
				$arr['id'] = $zef_arr[$zef_arr_index]['timeEntryID'];
				$arr['time_in'] = $zef_arr[$zef_arr_index]['start'];
				$arr['time_out'] = $zef_arr[$zef_arr_index]['end'];
				$arr['zef_time'] = $zef_arr[$zef_arr_index]['duration'];
				$arr['zef_duration'] = $zef_arr[$zef_arr_index]['formattedDuration'];
				$arr['dec_zef_time'] = sprintf("%01.2f", $zef_arr[$zef_arr_index]['duration'] / 3600);
				$arr['zef_rate'] = $zef_arr[$zef_arr_index]['rate'];
				$arr['wage'] = $zef_arr[$zef_arr_index]['wage'];
				$arr['wage_decimal'] = $zef_arr[$zef_arr_index]['wage_decimal'];
				$arr['budget'] = $zef_arr[$zef_arr_index]['budget'];
				$arr['approved'] = $zef_arr[$zef_arr_index]['approved'];
				$arr['status'] = $zef_arr[$zef_arr_index]['status'];
				$arr['billable'] = $zef_arr[$zef_arr_index]['billable'];
				$arr['pct_kndID'] = $zef_arr[$zef_arr_index]['customerID'];
				$arr['knd_name'] = $zef_arr[$zef_arr_index]['customerName'];
				$arr['pct_ID'] = $zef_arr[$zef_arr_index]['projectID'];
				$arr['pct_name'] = $zef_arr[$zef_arr_index]['projectName'];
				$arr['description'] = $zef_arr[$zef_arr_index]['description'];
				$arr['pct_comment'] = $zef_arr[$zef_arr_index]['projectComment'];
				$arr['activityID'] = $zef_arr[$zef_arr_index]['activityID'];
				$arr['evt_name'] = $zef_arr[$zef_arr_index]['activityName'];
				if ($limitCommentSize)
					$arr['comment'] = Format::addEllipsis($zef_arr[$zef_arr_index]['comment'], 150);
				else
					$arr['comment'] = $zef_arr[$zef_arr_index]['comment'];
				$arr['comment_type'] = $zef_arr[$zef_arr_index]['commentType'];
				$arr['location'] = $zef_arr[$zef_arr_index]['location'];
				if (empty($arr['location']))
					$arr['location'] = $default_location;
				$arr['trackingnr'] = $zef_arr[$zef_arr_index]['trackingNumber'];
				$arr['username'] = $zef_arr[$zef_arr_index]['userName'];
				$arr['cleared'] = $zef_arr[$zef_arr_index]['cleared'];
			}
			$zef_arr_index++;
		}
		else {
			$arr['type'] = 'exp';
			$arr['id'] = $exp_arr[$exp_arr_index]['expenseID'];
			$arr['time_in'] = $exp_arr[$exp_arr_index]['timestamp'];
			$arr['time_out'] = $exp_arr[$exp_arr_index]['timestamp'];
			$arr['zef_time'] = null;
			$arr['zef_apos'] = null;
			$arr['dec_zef_time'] = null;
			$arr['zef_rate'] = null;
			$arr['wage'] = sprintf("%01.2f", $exp_arr[$exp_arr_index]['value'] * $exp_arr[$exp_arr_index]['multiplier']);
			$arr['pct_kndID'] = $exp_arr[$exp_arr_index]['customerID'];
			$arr['knd_name'] = $exp_arr[$exp_arr_index]['customerName'];
			$arr['pct_ID'] = $exp_arr[$exp_arr_index]['projectID'];
			$arr['pct_name'] = $exp_arr[$exp_arr_index]['projectName'];
			$arr['zef_description'] = $zef_arr[$zef_arr_index]['description'];
			if ($limitCommentSize)
				$arr['comment'] = Format::addEllipsis($exp_arr[$exp_arr_index]['comment'], 150);
			else
				$arr['comment'] = $exp_arr[$exp_arr_index]['comment'];
			$arr['evt_name'] = $exp_arr[$exp_arr_index]['designation'];
			$arr['comment'] = $exp_arr[$exp_arr_index]['comment'];
			$arr['comment_type'] = $exp_arr[$exp_arr_index]['commentType'];
			$arr['location'] = $default_location;
			$arr['trackingnr'] = null;
			$arr['username'] = $exp_arr[$exp_arr_index]['userName'];
			$arr['cleared'] = $exp_arr[$exp_arr_index]['cleared'];
			$exp_arr_index++;
		}
		$result_arr[] = $arr;
	}
	while ($zef_arr_index < count($zef_arr)) {
		if ($zef_arr[$zef_arr_index]['zef_out'] != 0) {
			// active recordings will be omitted
			$arr = array();
			$arr['type'] = 'zef';
			$arr['id'] = $zef_arr[$zef_arr_index]['timeEntryID'];
			$arr['time_in'] = $zef_arr[$zef_arr_index]['start'];
			$arr['time_out'] = $zef_arr[$zef_arr_index]['end'];
			$arr['zef_time'] = $zef_arr[$zef_arr_index]['duration'];
			$arr['zef_duration'] = $zef_arr[$zef_arr_index]['formattedDuration'];
			$arr['dec_zef_time'] = sprintf("%01.2f", $zef_arr[$zef_arr_index]['duration'] / 3600);
			$arr['zef_rate'] = $zef_arr[$zef_arr_index]['rate'];
			$arr['wage'] = $zef_arr[$zef_arr_index]['wage'];
			$arr['wage_decimal'] = $zef_arr[$zef_arr_index]['wage_decimal'];
			$arr['budget'] = $zef_arr[$zef_arr_index]['budget'];
			$arr['approved'] = $zef_arr[$zef_arr_index]['approved'];
			$arr['status'] = $zef_arr[$zef_arr_index]['status'];
			$arr['billable'] = $zef_arr[$zef_arr_index]['billable'];
			$arr['pct_kndID'] = $zef_arr[$zef_arr_index]['customerID'];
			$arr['knd_name'] = $zef_arr[$zef_arr_index]['customerName'];
			$arr['pct_ID'] = $zef_arr[$zef_arr_index]['projectID'];
			$arr['pct_name'] = $zef_arr[$zef_arr_index]['projectName'];
			$arr['pct_comment'] = $zef_arr[$zef_arr_index]['projectComment'];
			$arr['activityID'] = $zef_arr[$zef_arr_index]['activityID'];
			$arr['evt_name'] = $zef_arr[$zef_arr_index]['activityName'];
			$arr['description'] = $zef_arr[$zef_arr_index]['description'];
			if ($limitCommentSize)
				$arr['comment'] = Format::addEllipsis($zef_arr[$zef_arr_index]['comment'], 150);
			else
				$arr['comment'] = $zef_arr[$zef_arr_index]['comment'];
			$arr['comment_type'] = $zef_arr[$zef_arr_index]['commentType'];
			$arr['location'] = $zef_arr[$zef_arr_index]['location'];
			if (empty($arr['location']))
				$arr['location'] = $default_location;
			$arr['trackingnr'] = $zef_arr[$zef_arr_index]['trackingNumber'];
			$arr['username'] = $zef_arr[$zef_arr_index]['userName'];
			$arr['cleared'] = $zef_arr[$zef_arr_index]['cleared'];
			$result_arr[] = $arr;
		}
		$zef_arr_index++;
	}
	while ($exp_arr_index < count($exp_arr)) {
		$arr = array();
		$arr['type'] = 'exp';
		$arr['id'] = $exp_arr[$exp_arr_index]['customerID'];
		$arr['time_in'] = $exp_arr[$exp_arr_index]['timestamp'];
		$arr['time_out'] = $exp_arr[$exp_arr_index]['timestamp'];
		$arr['zef_time'] = null;
		$arr['zef_apos'] = null;
		$arr['dec_zef_time'] = null;
		$arr['zef_rate'] = null;
		$arr['wage'] = sprintf("%01.2f", $exp_arr[$exp_arr_index]['exp_value'] * $exp_arr[$exp_arr_index]['exp_multiplier']);
		$arr['pct_kndID'] = $exp_arr[$exp_arr_index]['customerID'];
		$arr['knd_name'] = $exp_arr[$exp_arr_index]['customerName'];
		$arr['pct_ID'] = $exp_arr[$exp_arr_index]['projectID'];
		$arr['pct_name'] = $exp_arr[$exp_arr_index]['projectName'];
		if ($limitCommentSize)
			$arr['comment'] = Format::addEllipsis($exp_arr[$exp_arr_index]['comment'], 150);
		else
			$arr['comment'] = $exp_arr[$exp_arr_index]['comment'];
		$arr['evt_name'] = $exp_arr[$exp_arr_index]['designation'];
		$arr['comment'] = $exp_arr[$exp_arr_index]['comment'];
		$arr['comment_type'] = $exp_arr[$exp_arr_index]['commentType'];
		$arr['username'] = $exp_arr[$exp_arr_index]['userName'];
		$arr['cleared'] = $exp_arr[$exp_arr_index]['cleared'];
		$exp_arr_index++;
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
function merge_annotations(&$zef_arr, &$exp_arr) {
	foreach ($exp_arr as $id => $costs) {
		if (! isset($zef_arr[$id]))
			$zef_arr[$id]['costs'] = $costs;
		else
			$zef_arr[$id]['costs'] += $costs;
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
 * @param array $events Array of event IDs to filter by.
 * @return array Array which assigns every user (via his ID) the data to show.
 */
function xp_get_arr_usr($start, $end, $users = null, $customers = null, $projects = null, $events = null) {
	global $expense_ext_available, $database;
	$arr = $database->get_arr_time_users($start, $end, $users, $customers, $projects, $events);
	if ($expense_ext_available) {
		$exp_arr = get_arr_exp_usr($start, $end, $users, $customers, $projects);
		merge_annotations($arr, $exp_arr);
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
 * @param array $events Array of event IDs to filter by.
 * @return array Array which assigns every customer (via his ID) the data to show.
 */
function xp_get_arr_knd($start, $end, $users = null, $customers = null, $projects = null, $events = null) {
	global $expense_ext_available, $database;
	$arr = $database->get_arr_time_customers($start, $end, $users, $customers, $projects, $events);
	if ($expense_ext_available) {
		$exp_arr = get_arr_exp_knd($start, $end, $users, $customers, $projects);
		merge_annotations($arr, $exp_arr);
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
 * @param array $events Array of event IDs to filter by.
 * @return array Array which assigns every project (via his ID) the data to show.
 */
function xp_get_arr_pct($start, $end, $users = null, $customers = null, $projects = null, $events = null) {
	global $expense_ext_available, $database;
	$arr = $database->get_arr_time_projects($start, $end, $users, $customers, $projects, $events);
	if ($expense_ext_available) {
		$exp_arr = get_arr_exp_pct($start, $end, $users, $customers, $projects);
		merge_annotations($arr, $exp_arr);
	}
	return $arr;
}

/**
 * Get annotations for the task sub list. Currently it's just the time, like
 * in the timesheet extension.
 * 
 * @param int $start Time from which to take entries into account.
 * @param int $end Time until which to take entries into account.
 * @param array $users Array of user IDs to filter by.
 * @param array $customers Array of customer IDs to filter by.
 * @param array $projects Array of project IDs to filter by.
 * @param array $events Array of event IDs to filter by.
 * @return array Array which assigns every taks (via his ID) the data to show.
 */
function xp_get_arr_activities($start, $end, $users = null, $customers = null, $projects = null, $events = null) {
	global $database;
	$arr = $database->get_arr_time_activities($start, $end, $users, $customers, $projects, $events);
	return $arr;
}

/**
 * Prepare a string to be printed as a single field in the csv file.
 * @param string $field String to prepare.
 * @param string $column_delimiter Character used to delimit columns.
 * @param string $quote_char Character used to quote strings.
 * @return string Correctly formatted string.
 */
function csv_prepare_field($field, $column_delimiter, $quote_char) {
	if (strpos($field, $column_delimiter) === false && strpos($field, $quote_char) === false && strpos($field, "\n") === false)
		return $field;
	$field = str_replace($quote_char, $quote_char . $quote_char, $field);
	$field = $quote_char . $field . $quote_char;
	return $field;
}
?>