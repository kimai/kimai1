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

    case "add_edit_record":  
        if (isset($kga['customer'])) die();  
    // ==============================================
    // = display edit dialog for timesheet record   =
    // ==============================================
    $selected = explode('|',$axValue);
    if ($id) {
        $zef_entry = $database->timeSheet_get_data($id);
        $tpl->assign('id', $id);
        $tpl->assign('location', $zef_entry['location']);
        
        $tpl->assign('trackingNumber', $zef_entry['trackingNumber']);
        $tpl->assign('description', $zef_entry['description']);
        $tpl->assign('comment', $zef_entry['comment']);
        
        $tpl->assign('rate', $zef_entry['rate']);
        $tpl->assign('fixedRate', $zef_entry['fixedRate']);
        
        $tpl->assign('cleared', $zef_entry['cleared']!=0);

        $tpl->assign('userID', $zef_entry['userID']);
    
        $tpl->assign('start_day', date("d.m.Y",$zef_entry['start']));
        $tpl->assign('start_time',  date("H:i:s",$zef_entry['start']));

        if ($zef_entry['end'] == 0) {
          $tpl->assign('end_day', '');
          $tpl->assign('end_time', '');
        }
        else {
          $tpl->assign('end_day', date("d.m.Y",$zef_entry['end']));
          $tpl->assign('end_time', date("H:i:s",$zef_entry['end']));
        }

        $tpl->assign('approved', $zef_entry['approved']);
        $tpl->assign('budget', $zef_entry['budget']);
        
        // preselected
        $tpl->assign('projectID', $zef_entry['projectID']);
        $tpl->assign('activityID', $zef_entry['activityID']);
    
        $tpl->assign('commentType', $zef_entry['zef_commentType']);
        $tpl->assign('status', $zef_entry['status']);
        $tpl->assign('billable', $zef_entry['billable']);

        // budget
        $eventBudgets = $database->get_activity_budget($zef_entry['projectID'], $zef_entry['activityID']);
        $eventUsed = $database->get_budget_used($zef_entry['projectID'], $zef_entry['activityID']);
        $tpl->assign('budget_event', round($eventBudgets['budget'], 2));
        $tpl->assign('approved_event', round($eventBudgets['approved'], 2));
        $tpl->assign('budget_event_used', $eventUsed);

    } else {
        $tpl->assign('id', 0);
        
        $tpl->assign('start_day', date("d.m.Y"));
        $tpl->assign('end_day', date("d.m.Y"));

        $tpl->assign('userID', $kga['usr']['userID']);

        if($kga['conf']['roundTimesheetEntries'] != '') {
	        $zefData = $database->timeSheet_get_data(false);
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
	        $dayEntry = date("d", $zefData['end']);
	        if($day == $dayEntry) {
	        	$tpl->assign('start_time',  date("H:i:s", $zefData['end']));
	        } else {
	        	$tpl->assign('start_time',  date("H:i:s"));
	        }
	        $tpl->assign('end_time', date("H:i:s", $end));
        } else {
	        $tpl->assign('start_time', date("H:i:s"));
	        $tpl->assign('end_time', date("H:i:s"));
        }
        $tpl->assign('rate',$database->get_best_fitting_rate($kga['usr']['userID'],$selected[0],$selected[1]));
        $tpl->assign('fixedRate',$database->get_best_fitting_fixed_rate($selected[0],$selected[1]));
    }

    $tpl->assign('status', $kga['conf']['status']);
    
    $billableValues = Config::getConfig('billable');
    $tpl->assign('billableValues', $billableValues); 
    foreach($billableValues as $index => $billableValue) {
    	$billableValues[$index] = $billableValue.'%';
    }
    $tpl->assign('billable', $billableValues);
    $tpl->assign('commentTypes', $comment_types);
    $tpl->assign('commentValues', array('0','1','2'));

    $users = $database->get_arr_watchable_users($kga['usr']);
    $userIds = array();
    $userNames = array();

    foreach ($users as $user) {
      $userIds[] = $user['userID'];
      $userNames[] = $user['name'];
    }

    $tpl->assign('userIds', $userIds);
    $tpl->assign('userNames', $userNames);

    // select for projects
    $sel = makeSelectBox("pct",$kga['usr']['groups']);
    $tpl->assign('projectNames', $sel[0]);
    $tpl->assign('projectIDs',   $sel[1]);

    // select for events
    $sel = makeSelectBox("evt",$kga['usr']['groups']);
    $tpl->assign('activityNames', $sel[0]);
    $tpl->assign('activityIDs',   $sel[1]);



    $tpl->display("add_edit_record.tpl"); 

    break;        

}

?>

    