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

// ================
// = TS PROCESSOR =
// ================

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require("../../includes/kspi.php");
require ("private_func.php");
  
  $filters = explode('|',$axValue);
  
  if ($filters[0] == "")
    $filterUsr = array();
  else
    $filterUsr = explode(':',$filters[0]);

  if ($filters[1] == "")
    $filterKnd = array();
  else
    $filterKnd = explode(':',$filters[1]);

  if ($filters[2] == "")
    $filterPct = array();
  else
    $filterPct = explode(':',$filters[2]);

  if ($filters[3] == "")
    $filterEvt = array();
  else
    $filterEvt = explode(':',$filters[3]);

  // if no userfilter is set, set it to current user
  if (isset($kga['usr']) && count($filterUsr) == 0)
    array_push($filterUsr,$kga['usr']['usr_ID']);
    
  if (isset($kga['customer']))
    $filterKnd = array($kga['customer']['knd_ID']);

// ==================
// = handle request =
// ==================
switch ($axAction) {
    

    // ===========================================
    // = Filter the charts by projects and events =
    // ===========================================
    case 'reload':
    	
    	// track which events we want to see, so we can exclude them when we create the plot
$eventsFilter = false;
$projectsFilter = false;
$customerFilter = false;
if (is_array($filterPct) && count($filterPct) > 0) {
	$projectsFilter = $filterPct;
	$projectsSelected = $projectsFilter;
}
if (is_array($filterEvt) && count($filterEvt) > 0) {
	$eventsFilter = $filterEvt;
	$eventsSelected = $eventsFilter;
}
// Get all project for the logged in customer or the current user.
if (isset($kga['customer'])) {
	$arr_pct = $database->get_arr_pct_by_knd($kga['customer']['knd_ID']);
	$arr_evt = $database->get_arr_evt();
	$customerValues = false;
}
else {
	$arr_knd = $database->get_arr_knd($kga['usr']['groups']);
	if (is_array($filterKnd) && count($filterKnd) > 0) {
		$customerFilter = $filterKnd;
		$arr_pct = array();
		foreach ($customerFilter as $customerId) {
			$arr_pct = array_merge($database->get_arr_pct_by_knd($customerId), $arr_pct);
		}
	}
	else {
		$arr_pct = $database->get_arr_pct($kga['usr']['groups']);
		// add all customers as selected
		foreach($arr_knd as $customer) {
			$customerFilter[] = $customer['knd_ID'];
		}
	}
	$arr_evt = $database->get_arr_evt($kga['usr']['groups']);
	foreach ($arr_knd as $customer) {
		$customerValues[] = $customer['knd_ID'];
		$customerNames[] = $customer['knd_name'];
	}
}
if(is_array($arr_pct) && count($arr_pct) > 0) {
	foreach ($arr_pct as $index => $project) {
		if ($projectsFilter === false) {
			$projectsSelected[] = $project['pct_ID'];
		}
		$projectValues[] = $project['pct_ID'];
		$projectNames[] = $project['pct_name'];
		$arr_pct[$index]['events'] = $database->get_arr_evt_by_pct($project['pct_ID']);
		
			foreach ($arr_pct[$index]['events'] as $index => $event) {
				if ($eventsFilter === false) {
					$eventsSelected[] = $event['evt_ID'];
				}
				$eventValues[] = $event['evt_ID'];
				$eventNames[] = $event['evt_name'];
			}
	}
}
$expensesOccured = false;
// If there are any projects create the plot data.
if (count($arr_pct) > 0) {
	$arr_plotdata = budget_plot_data($arr_pct, $projectsSelected, $eventsSelected, $expensesOccured, $kga);
	$tpl->assign('javascript_arr_plotdata', json_encode($arr_plotdata));
	$tpl->assign('arr_plotdata', $arr_plotdata);
	$tpl->assign('arr_pct', $arr_pct);
	$tpl->assign('arr_evt', $arr_evt);
}
else {
	$tpl->assign('arr_pct', 0);
}
$tpl->assign('knd_selected', $customerFilter);
$tpl->assign('pct_selected', $projectsSelected);
$tpl->assign('evt_selected', $eventsSelected);
$tpl->assign('pct_values', $projectValues);
$tpl->assign('evt_values', $eventValues);
$tpl->assign('knd_values', $customerValues);
$tpl->assign('pct_names', $projectNames);
$tpl->assign('knd_names', $customerNames);
$tpl->assign('evt_names', $eventNames);

$chartColors = array("#efefef", "#4bb2c5", "#EAA228", "#c5b47f", "#579575", "#839557", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc");
$tpl->assign('chartColors', json_encode($chartColors));
// Create the keys which explain to the user which color means what for the project based charts
$keys = array();
$keys[] = array('color' => $chartColors[0], 'name' => $kga['lang']['ext_budget']['unusedBudget']);
if ($expensesOccured)
	$keys[] = array('color' => $chartColors[1], 'name' => $kga['lang']['xp_ext']['expenses']);
/*for ($i = 0; $i < count($usedEvents); $i++) {
	$keys[] = array('color' => $chartColors[($i + 2) % (count($chartColors) - 1)], 'name' => $usedEvents[$i]['evt_name']);
}*/
// the event based charts only need numbers
$tpl->assign('arr_keys', $keys);
$tpl->display("charts.tpl");
        
//if (is_array($_REQUEST['projects'])) {
//	// HERE ARE ONLY IDS!!!
//	$pcts = $_REQUEST['projects'];
//	if (is_array($_REQUEST['events'])) {
//		$evts = $_REQUEST['events'];
//	}
//	else {
//		foreach ($pcts as $index => $project) {
//			$arr_pct[$index]['events'] = $database->get_arr_evt_by_pct($project);
//		}
//	}
//}
//if (count($arr_pct) > 0) {
//	$arr_plotdata = budget_plot_data($arr_pct, $usedEvents, $expensesOccured, $kga);
//	$tpl->assign('javascript_arr_plotdata', json_encode($arr_plotdata));
//	$tpl->assign('arr_plotdata', $arr_plotdata);
//	$tpl->assign('arr_pct', $arr_pct);
//	$tpl->assign('arr_evt', $arr_evt);
//}
//else {
//	$tpl->assign('arr_pct', 0);
//}
//$chartColors = array("#efefef", "#4bb2c5", "#EAA228", "#c5b47f", "#579575", "#839557", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc");
//$tpl->assign('chartColors', json_encode($chartColors));
//
//        echo json_encode($arr_plotdata);
    break;
}

?>