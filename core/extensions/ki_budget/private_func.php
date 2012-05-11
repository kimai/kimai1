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

include('../ki_expenses/private_db_layer_'.$kga['server_conn'].'.php');


/**
 * Sum up expenses for the project.
 */
function calculate_expenses_sum($projectId) {
  $expSum = 0;
  $exp_arr = get_arr_exp(0,time(),null,null,array($projectId));

  foreach ($exp_arr as $exp) {
    $expSum += $exp['exp_value'];
  }

  return $expSum;
}

/**
 * Create an array of arrays which hold the size of the pie chart elements
 * for every projects.
 * The first element in the inner arrays represents the unused budget costs,
 * the second element in the inner arrays represents the expense costs,
 * the third and all other elements in the inner arrays represents the
 * costs for individual events.
 * 
 * An visual example for two projects with the ID 2 and 5:
 * $array = {
 *   2 => array (budget left , expenses cost, task1, task2 ),
 *   5 => array (budget left , expenses cost, task1, task2 ),
 * };
 * 
 * @param array $projects IDs of all projects to include in the plot data
 * @param array $usedEvents array of all used events (each as an array of its data)
 * @return array containing arrays for every project which hold the size of the pie chart elements
 * 
 */
function budget_plot_data($projects,$projectsFilter, $eventsFilter,&$expensesOccured, &$kga) {
global $database;

$wages = array();
$expensesOccured = false;

 /*
  * sum up expenses
  */
foreach ($projects as $project) {
  if(is_array($projectsFilter) && !empty($projectsFilter)) {
  	if(!in_array($project['projectID'], $projectsFilter)) {
  		continue;
  	}
  }
  
  $pctId = $project['projectID'];
  // in "event 0" we will track the available budget, while in the project array directly,
  // we will track the total budget for the project
  $wages[$pctId][0]['budget']   = $project['budget'];
  $wages[$pctId][0]['approved']   = $project['approved'];
  $wages[$pctId]['budget']   = $project['budget'];
  $wages[$pctId]['approved']   = $project['approved'];
  $expenses = calculate_expenses_sum($project['projectID']);
  if($expenses > 0) {
   $wages[$pctId][0]['expenses'] = $expenses;
  }

  if ($expenses > 0)
    $expensesOccured = true;

  if ($wages[$pctId][0]['budget'] < 0) {
    //Costs over budget, set remaining budget to 0.
    $wages[$pctId][0]['budget'] = 0;
    $wages[$pctId][0]['exceeded'] = true;
  }
  
  $pct_evts = $database->get_arr_activities_by_project($pctId);
  foreach($pct_evts as $event) {
  if(is_array($eventsFilter) && !empty($eventsFilter)) {
  	if(!in_array($event['activityID'], $eventsFilter)) {
  		continue;
  	}
  }
    $wages[$pctId][$event['activityID']] = array();
    if($event['budget'] <= 0) {
    	continue;
    }
    $wages[$pctId][$event['activityID']]['budget'] = $event['budget'];
    $wages[$pctId][$event['activityID']]['budget_total'] = $event['budget'];
    // this budget shall not be added, otherwise we have the project budget in all tasks
    // so they would be doubled.
//  	$wages[$pctId][$event['evt_ID']]['budget_total'] += $project['pct_budget'];
//  	$wages[$pctId][$event['evt_ID']]['approved_total'] = $project['pct_approved'];
    $wages[$pctId][$event['activityID']]['approved_total'] += $event['approved'];
    $wages[$pctId][$event['activityID']]['approved'] = $event['approved'];
    // add to the project budget
    $wages[$pctId][0]['budget'] += $event['budget'];
    $wages[$pctId][0]['approved'] += $event['approved'];
    // add to the total budget
    $wages[$pctId]['budget'] += $event['budget'];
    $wages[$pctId]['approved'] += $event['approved'];
  }
}
/*
 * sum up wages for every project and every event
 */
foreach ($projects as $project) {
  $projectId = $project['projectID'];
  $zef_arr = $database->get_arr_timeSheet(0,time(),null,null,array($projectId));
  foreach ($zef_arr as $zef) {
    $pctId = $projectId;
	$billableLangString = $kga['lang']['billable'];
	$timebillableLangString = $kga['lang']['time_billable'];
    if (is_array($wages[$pctId][$zef['activityID']])) {
      $tmpCost = $zef['wage_decimal'] * $zef['billable'] / 100;
      if($zef['wage_decimal'] - $tmpCost <= 0 && $tmpCost <= 0) {
      	continue;
      } else {
      }
      // decrease budget by "already used up" amount
      $wages[$pctId][$zef['activityID']]['budget_total'] += $zef['budget'];
      $wages[$pctId][$zef['activityID']]['budget']       -= $zef['wage_decimal'];
      $wages[$pctId][$zef['activityID']]['budget']       += $zef['budget'];
      $wages[$pctId][$zef['activityID']]['approved']     += $zef['approved'];
      $wages[$pctId][$zef['activityID']]['approved_total'] += $zef['approved'];
      $wages[$pctId][$zef['activityID']]['approved']     -= $tmpCost;
      $wages[$pctId][$zef['activityID']]['total']+= $zef['wage_decimal'];
      // decrease budget by "already used up" amount also for the total budget for the project
      $wages[$pctId][0]['budget']       -= $zef['wage_decimal'];
      $wages[$pctId][0]['approved']       -= $tmpCost;
      $wages[$pctId][0]['budget']       += $zef['budget'];
      $wages[$pctId][0]['approved']     += $zef['approved'];
      if($tmpCost > 0) {
	      $wages[$pctId][0][$zef['name'].' '.$billableLangString]+= $tmpCost;
      	  $wages[$pctId][$zef['activityID']][$billableLangString]+= $tmpCost;
      }
      if($zef['wage_decimal'] - $tmpCost > 0) {
	     $wages[$pctId][0][$zef['name']] += $zef['wage_decimal'] - $tmpCost;
      	 $wages[$pctId][$zef['activityID']][$zef['name']] += $zef['wage_decimal'] - $tmpCost;
      }
    // add to the total budget
      $wages[$pctId]['budget']       += $zef['budget'];
      $wages[$pctId]['approved']     += $zef['approved'];
      $wages[$pctId]['billable_total']+= $tmpCost;
      $wages[$pctId]['total']     += $zef['wage_decimal'];
      $wages[$pctId][$timebillableLangString]+= $tmpCost;
      // mark entries which are over budget
      if ($wages[$pctId][$zef['activityID']]['budget'] < 0) {
      	$wages[$pctId][$zef['activityID']]['budget'] = 0;
	    $wages[$pctId][$zef['activityID']]['exceeded'] = true;
	  }
      if ($wages[$pctId][$zef['activityID']]['approved'] < 0) {
      	$wages[$pctId][$zef['activityID']]['approved'] = 0;
	  	$wages[$pctId][$zef['activityID']]['approved_exceeded'] = true;
	  }
    }
  }
  //cleanup: don't show charts without any data
  if(is_array($wages[$projectId])) {
  foreach($wages[$projectId] as $eventId => $entry) {
  	if($eventId == 0) {
  		continue;
  	}
  	if(!isset($entry['total']) || is_null($entry['total'])) {
  		unset($wages[$projectId][$eventId]);
  	}
  }
  }
  
  if ($wages[$projectId][0]['budget'] < 0) {
    //Costs over budget, set remaining budget to 0.
    $wages[$projectId][0]['budget'] = 0;
 	$wages[$projectId][0]['exceeded'] = true;
  }
  if ($wages[$projectId][0]['approved'] < 0) {
    //Costs over budget approved, set remaining approved to 0.
    $wages[$projectId][0]['approved'] = 0;
	$wages[$projectId][0]['approved_exceeded'] = true;
  }
}

return $wages;
}


?>