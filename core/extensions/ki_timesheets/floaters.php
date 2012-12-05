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

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require("../../includes/kspi.php");
require('../../core/Config.php');

switch ($axAction) {

    case "add_edit_timeSheetEntry":  
        if (isset($kga['customer'])) die();  
    // ==============================================
    // = display edit dialog for timesheet record   =
    // ==============================================
    $selected = explode('|',$axValue);

    $view->users = makeSelectBox("user",$kga['user']['groups']);
    $view->projects = makeSelectBox("project",$kga['user']['groups']);
    $view->activities = makeSelectBox("activity",$kga['user']['groups']);

    // edit record
    if ($id) {
        $timeSheetEntry = $database->timeSheet_get_data($id);
        $view->id = $id;
        $view->location = $timeSheetEntry['location'];
        
        $view->trackingNumber = $timeSheetEntry['trackingNumber'];
        $view->description = $timeSheetEntry['description'];
        $view->comment = $timeSheetEntry['comment'];
        
        $view->rate = $timeSheetEntry['rate'];
        $view->fixedRate = $timeSheetEntry['fixedRate'];
        
        $view->cleared = $timeSheetEntry['cleared']!=0;

        $view->userID = $timeSheetEntry['userID'];
    
        $view->start_day = date("d.m.Y",$timeSheetEntry['start']);
        $view->start_time = date("H:i:s",$timeSheetEntry['start']);

        if ($timeSheetEntry['end'] == 0) {
          $view->end_day = '';
          $view->end_time = '';
        }
        else {
          $view->end_day = date("d.m.Y",$timeSheetEntry['end']);
          $view->end_time = date("H:i:s",$timeSheetEntry['end']);
        }

        $view->approved = $timeSheetEntry['approved'];
        $view->budget = $timeSheetEntry['budget'];
        
        // preselected
        $view->projectID = $timeSheetEntry['projectID'];
        $view->activityID = $timeSheetEntry['activityID'];
    
        $view->commentType = $timeSheetEntry['commentType'];
        $view->statusID = $timeSheetEntry['statusID'];
        $view->billable = $timeSheetEntry['billable'];

        // budget
        $activityBudgets = $database->get_activity_budget($timeSheetEntry['projectID'], $timeSheetEntry['activityID']);
        $activityUsed = $database->get_budget_used($timeSheetEntry['projectID'], $timeSheetEntry['activityID']);
        $view->budget_activity = round($activityBudgets['budget'], 2);
        $view->approved_activity = round($activityBudgets['approved'], 2);
        $view->budget_activity_used = $activityUsed;


        if (!isset($view->projects[$timeSheetEntry['projectID']])) {
          // add the currently assigned project to the list
          $projectData = $database->project_get_data($timeSheetEntry['projectID']);
          $customerData = $database->customer_get_data($projectData['customerID']);
          $view->projects[$projectData['projectID']] = $customerData['name'] . ':' . $projectData['name'];
        }

    } else {
        // create new record
        //$view->id = 0;
        
        $view->start_day = date("d.m.Y");
        $view->end_day = date("d.m.Y");

        $view->userID = $kga['user']['userID'];

        if($kga['user']['lastRecord'] != 0 && $kga['conf']['roundTimesheetEntries'] != '') {
          $timeSheetData = $database->timeSheet_get_data($kga['user']['lastRecord']);
          $minutes = date('i');
          if($kga['conf']['roundMinutes'] < 60) {
            if($kga['conf']['roundMinutes'] <= 0) {
                    $minutes = 0;
            } else {
              while($minutes % $kga['conf']['roundMinutes'] != 0) {
                if($minutes >= 60) {
                  $minutes = 0;
                } else {
                  $minutes++;
                }
              }
            }
          }
          $seconds = date('s');
          if($kga['conf']['roundSeconds'] < 60) {
            if($kga['conf']['roundSeconds'] <= 0) {
                    $seconds = 0;
            } else {
              while($seconds % $kga['conf']['roundSeconds'] != 0) {
                if($seconds >= 60) {
                  $seconds = 0;
                } else {
                  $seconds++;
                }
              }
            }
          }
          $end = mktime(date("H"), $minutes, $seconds);
          $day = date("d");
          $dayEntry = date("d", $timeSheetData['end']);

          if($day == $dayEntry) {
                  $view->start_time = date("H:i:s", $timeSheetData['end']);
          } else {
                  $view->start_time = date("H:i:s");
          }
          $view->end_time = date("H:i:s", $end);
        } else {
          $view->start_time = date("H:i:s");
          $view->end_time = date("H:i:s");
        }
        $view->rate = $database->get_best_fitting_rate($kga['user']['userID'],$selected[0],$selected[1]);
        $view->fixedRate = $database->get_best_fitting_fixed_rate($selected[0],$selected[1]);
        
        $view->cleared = false;
    }

    $view->status = $kga['conf']['status'];
    
    $billableValues = Config::getConfig('billable');
    $billableText = array();
    foreach($billableValues as $billableValue) {
    	$billableText[] = $billableValue.'%';
    }
    $view->billable = array_combine($billableValues, $billableText);
    $view->commentTypes = $commentTypes;

    echo $view->render("floaters/add_edit_timeSheetEntry.php"); 

    break;        

}
