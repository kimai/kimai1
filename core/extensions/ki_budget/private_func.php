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
  $expenseSum = 0;
  $expenses = get_expenses(0,time(),null,null,array($projectId));

  foreach ($expenses as $expense) {
    $expenseSum += $expense['value'];
  }

  return $expenseSum;
}

/**
 * Create an array of arrays which hold the size of the pie chart elements
 * for every projects.
 * The first element in the inner arrays represents the unused budget costs,
 * the second element in the inner arrays represents the expense costs,
 * the third and all other elements in the inner arrays represents the
 * costs for individual activities.
 * 
 * An visual example for two projects with the ID 2 and 5:
 * $array = {
 *   2 => array (budget left , expenses cost, activity1, activity2 ),
 *   5 => array (budget left , expenses cost, activity1, activity2 ),
 * };
 * 
 * @param array $projects IDs of all projects to include in the plot data
 * @param array $usedActivities array of all used activities (each as an array of its data)
 * @return array containing arrays for every project which hold the size of the pie chart elements
 * 
 */
function budget_plot_data($projects,$projectsFilter, $activitiesFilter,&$expensesOccured, &$kga) {
global $database;

$wages = array();
$expensesOccured = false;

$billableLangString = $kga['lang']['billable'];
$timebillableLangString = $kga['lang']['time_billable'];

 /*
  * sum up expenses
  */
foreach ($projects as $project) {
  if(is_array($projectsFilter) && !empty($projectsFilter)) {
  	if(!in_array($project['projectID'], $projectsFilter)) {
  		continue;
  	}
  }
  
  $projectID = $project['projectID'];
  // in "activity 0" we will track the available budget, while in the project array directly,
  // we will track the total budget for the project
  $wages[$projectID][0]['budget']   = $project['budget'];
  $wages[$projectID][0]['approved']   = $project['approved'];
  $wages[$projectID]['budget']   = $project['budget'];
  $wages[$projectID]['approved']   = $project['approved'];
  $wages[$projectID]['billable_total']   = 0;
  $wages[$projectID]['total']   = 0;
  $wages[$projectID][$timebillableLangString] = 0;

  $expenses = calculate_expenses_sum($project['projectID']);
  if($expenses > 0) {
   $wages[$projectID][0]['expenses'] = $expenses;
  }

  if ($expenses > 0)
    $expensesOccured = true;

  if ($wages[$projectID][0]['budget'] < 0) {
    //Costs over budget, set remaining budget to 0.
    $wages[$projectID][0]['budget'] = 0;
    $wages[$projectID][0]['exceeded'] = true;
  }
  
  $projectActivities = $database->get_activities_by_project($projectID);
  foreach($projectActivities as $activity) {
  if(is_array($activitiesFilter) && !empty($activitiesFilter)) {
  	if(!in_array($activity['activityID'], $activitiesFilter)) {
  		continue;
  	}
  }
    $wages[$projectID][$activity['activityID']] = array('name' => $activity['name'], 'budget' => 0, 'budget_total' => 0, 'approved' => 0, 'approved_total' => 0, 'total' => 0);
    if(!isset( $activity['budget'] ) || $activity['budget'] <= 0) {
    	continue;
    }
    $wages[$projectID][$activity['activityID']]['budget'] = $activity['budget'];
    $wages[$projectID][$activity['activityID']]['budget_total'] = $activity['budget'];
    // this budget shall not be added, otherwise we have the project budget in all activities
    // so they would be doubled.
//  	$wages[$projectID][$activity['evt_ID']]['budget_total'] += $project['pct_budget'];
//  	$wages[$projectID][$activity['evt_ID']]['approved_total'] = $project['pct_approved'];
    $wages[$projectID][$activity['activityID']]['approved_total'] += $activity['approved'];
    $wages[$projectID][$activity['activityID']]['approved'] = $activity['approved'];
    $wages[$projectID][$activity['activityID']]['total'] = 0;
    // add to the project budget
    $wages[$projectID][0]['budget'] += $activity['budget'];
    $wages[$projectID][0]['approved'] += $activity['approved'];
    // add to the total budget
    $wages[$projectID]['budget'] += $activity['budget'];
    $wages[$projectID]['approved'] += $activity['approved'];
  }
}
/*
 * sum up wages for every project and every activity
 */
foreach ($projects as $project) {
  $projectId = $project['projectID'];
  $timeSheetEntries = $database->get_timeSheet(0,time(),null,null,array($projectId));
  foreach ($timeSheetEntries as $timeSheetEntry) {
    $projectID = $projectId;
    if (isset($wages[$projectID][$timeSheetEntry['activityID']]) && is_array($wages[$projectID][$timeSheetEntry['activityID']])) {
      $tmpCost = $timeSheetEntry['wage_decimal'] * $timeSheetEntry['billable'] / 100;
      if($timeSheetEntry['wage_decimal'] - $tmpCost <= 0 && $tmpCost <= 0) {
      	continue;
      } else {
      }
      // decrease budget by "already used up" amount
      $wages[$projectID][$timeSheetEntry['activityID']]['budget_total'] += $timeSheetEntry['budget'];
      $wages[$projectID][$timeSheetEntry['activityID']]['budget']       -= $timeSheetEntry['wage_decimal'];
      $wages[$projectID][$timeSheetEntry['activityID']]['budget']       += $timeSheetEntry['budget'];
      $wages[$projectID][$timeSheetEntry['activityID']]['approved']     += $timeSheetEntry['approved'];
      $wages[$projectID][$timeSheetEntry['activityID']]['approved_total'] += $timeSheetEntry['approved'];
      $wages[$projectID][$timeSheetEntry['activityID']]['approved']     -= $tmpCost;
      $wages[$projectID][$timeSheetEntry['activityID']]['total']+= $timeSheetEntry['wage_decimal'];
      // decrease budget by "already used up" amount also for the total budget for the project
      $wages[$projectID][0]['budget']       -= $timeSheetEntry['wage_decimal'];
      $wages[$projectID][0]['approved']       -= $tmpCost;
      $wages[$projectID][0]['budget']       += $timeSheetEntry['budget'];
      $wages[$projectID][0]['approved']     += $timeSheetEntry['approved'];
      if($tmpCost > 0) {
          if (!isset($wages[$projectID][0][$timeSheetEntry['userName'].' '.$billableLangString]))
            $wages[$projectID][0][$timeSheetEntry['userName'].' '.$billableLangString] = 0;

	      if (isset($wages[$projectID][0][$timeSheetEntry['userName'].' '.$billableLangString]))
          $wages[$projectID][0][$timeSheetEntry['userName'].' '.$billableLangString] += $tmpCost;
        else
          $wages[$projectID][0][$timeSheetEntry['userName'].' '.$billableLangString] = $tmpCost;

        if (isset($wages[$projectID][$timeSheetEntry['activityID']][$billableLangString]))
          $wages[$projectID][$timeSheetEntry['activityID']][$billableLangString]+= $tmpCost;
        else
          $wages[$projectID][$timeSheetEntry['activityID']][$billableLangString] = $tmpCost;
      }
      if($timeSheetEntry['wage_decimal'] - $tmpCost > 0) {
          if (!isset($wages[$projectID][0][$timeSheetEntry['userName']]))
            $wages[$projectID][0][$timeSheetEntry['userName']] = 0;

          $wages[$projectID][0][$timeSheetEntry['userName']] += $timeSheetEntry['wage_decimal'] - $tmpCost;

          if (!isset($wages[$projectID][$timeSheetEntry['activityID']][$timeSheetEntry['userName']]))
            $wages[$projectID][$timeSheetEntry['activityID']][$timeSheetEntry['userName']] = 0;

          $wages[$projectID][$timeSheetEntry['activityID']][$timeSheetEntry['userName']] += $timeSheetEntry['wage_decimal'] - $tmpCost;
      }
    // add to the total budget
      $wages[$projectID]['budget']       += $timeSheetEntry['budget'];
      $wages[$projectID]['approved']     += $timeSheetEntry['approved'];
      $wages[$projectID]['billable_total']+= $tmpCost;
      $wages[$projectID]['total']     += $timeSheetEntry['wage_decimal'];
      $wages[$projectID][$timebillableLangString] += $tmpCost;
      // mark entries which are over budget
      if ($wages[$projectID][$timeSheetEntry['activityID']]['budget'] < 0) {
      	$wages[$projectID][$timeSheetEntry['activityID']]['budget'] = 0;
	    $wages[$projectID][$timeSheetEntry['activityID']]['exceeded'] = true;
	  }
      if ($wages[$projectID][$timeSheetEntry['activityID']]['approved'] < 0) {
      	$wages[$projectID][$timeSheetEntry['activityID']]['approved'] = 0;
	  	$wages[$projectID][$timeSheetEntry['activityID']]['approved_exceeded'] = true;
	  }
    }
  }

  if(!isset($wages[$projectId]))
    continue;

  //cleanup: don't show charts without any data
  foreach($wages[$projectId] as $activityId => $entry) {
    if($activityId == 0) {
      continue;
    }
    if(!isset($entry['total']) || is_null($entry['total'])) {
      unset($wages[$projectId][$activityId]);
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