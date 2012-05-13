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
$dir_templates = "templates/floaters/";
require("../../includes/kspi.php");
require('../../core/Config.php');

switch ($axAction) {

    case "add_edit_timeSheetEntry":  
        if (isset($kga['customer'])) die();  
    // ==============================================
    // = display edit dialog for timesheet record   =
    // ==============================================
    $selected = explode('|',$axValue);
    if ($id) {
        $timeSheetEntry = $database->timeSheet_get_data($id);
        $tpl->assign('id', $id);
        $tpl->assign('location', $timeSheetEntry['location']);
        
        $tpl->assign('trackingNumber', $timeSheetEntry['trackingNumber']);
        $tpl->assign('description', $timeSheetEntry['description']);
        $tpl->assign('comment', $timeSheetEntry['comment']);
        
        $tpl->assign('rate', $timeSheetEntry['rate']);
        $tpl->assign('fixedRate', $timeSheetEntry['fixedRate']);
        
        $tpl->assign('cleared', $timeSheetEntry['cleared']!=0);

        $tpl->assign('userID', $timeSheetEntry['userID']);
    
        $tpl->assign('start_day', date("d.m.Y",$timeSheetEntry['start']));
        $tpl->assign('start_time',  date("H:i:s",$timeSheetEntry['start']));

        if ($timeSheetEntry['end'] == 0) {
          $tpl->assign('end_day', '');
          $tpl->assign('end_time', '');
        }
        else {
          $tpl->assign('end_day', date("d.m.Y",$timeSheetEntry['end']));
          $tpl->assign('end_time', date("H:i:s",$timeSheetEntry['end']));
        }

        $tpl->assign('approved', $timeSheetEntry['approved']);
        $tpl->assign('budget', $timeSheetEntry['budget']);
        
        // preselected
        $tpl->assign('projectID', $timeSheetEntry['projectID']);
        $tpl->assign('activityID', $timeSheetEntry['activityID']);
    
        $tpl->assign('commentType', $timeSheetEntry['commentType']);
        $tpl->assign('status', $timeSheetEntry['status']);
        $tpl->assign('billable', $timeSheetEntry['billable']);

        // budget
        $activityBudgets = $database->get_activity_budget($timeSheetEntry['projectID'], $timeSheetEntry['activityID']);
        $activityUsed = $database->get_budget_used($timeSheetEntry['projectID'], $timeSheetEntry['activityID']);
        $tpl->assign('budget_activity', round($activityBudgets['budget'], 2));
        $tpl->assign('approved_activity', round($activityBudgets['approved'], 2));
        $tpl->assign('budget_activity_used', $activityUsed);

    } else {
        $tpl->assign('id', 0);
        
        $tpl->assign('start_day', date("d.m.Y"));
        $tpl->assign('end_day', date("d.m.Y"));

        $tpl->assign('userID', $kga['user']['userID']);

        if($kga['conf']['roundTimesheetEntries'] != '') {
	        $timeSheetData = $database->timeSheet_get_data(false);
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
	        	$tpl->assign('start_time',  date("H:i:s", $timeSheetData['end']));
	        } else {
	        	$tpl->assign('start_time',  date("H:i:s"));
	        }
	        $tpl->assign('end_time', date("H:i:s", $end));
        } else {
	        $tpl->assign('start_time', date("H:i:s"));
	        $tpl->assign('end_time', date("H:i:s"));
        }
        $tpl->assign('rate',$database->get_best_fitting_rate($kga['user']['userID'],$selected[0],$selected[1]));
        $tpl->assign('fixedRate',$database->get_best_fitting_fixed_rate($selected[0],$selected[1]));
    }

    $tpl->assign('status', $kga['conf']['status']);
    
    $billableValues = Config::getConfig('billable');
    $tpl->assign('billableValues', $billableValues); 
    foreach($billableValues as $index => $billableValue) {
    	$billableValues[$index] = $billableValue.'%';
    }
    $tpl->assign('billable', $billableValues);
    $tpl->assign('commentTypes', $commentTypes);
    $tpl->assign('commentValues', array('0','1','2'));

    $users = $database->get_arr_watchable_users($kga['user']);
    $userIds = array();
    $userNames = array();

    foreach ($users as $user) {
      $userIds[] = $user['userID'];
      $userNames[] = $user['name'];
    }

    $tpl->assign('userIds', $userIds);
    $tpl->assign('userNames', $userNames);

    // select for projects
    $sel = makeSelectBox("project",$kga['user']['groups']);
    $tpl->assign('projectNames', $sel[0]);
    $tpl->assign('projectIDs',   $sel[1]);

    // select for activities
    $sel = makeSelectBox("activity",$kga['user']['groups']);
    $tpl->assign('activityNames', $sel[0]);
    $tpl->assign('activityIDs',   $sel[1]);



    $tpl->display("add_edit_timeSheetEntry.tpl"); 

    break;        

}

?>

    