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
   
    // =================================================
    // = displays edit comment dialog for running task =
    // =================================================
    case 'edit_running_comment':
        if (isset($kga['customer'])) die();

        $last_event = $database->get_event_last();
        $tpl->assign('id', $last_event['zef_ID']);
        $tpl->assign('comment', $last_event['zef_comment']);
        $tpl->assign('comment_active', $last_event['zef_comment_type']);
        $tpl->assign('comment_types', $comment_types);
        $tpl->assign('comment_values', array('0','1','2'));
        $tpl->display("edit_running_comment.tpl");
    break;

    // =================================================
    // = displays edit time dialog for running task =
    // =================================================
    case 'edit_running_starttime':
        if (isset($kga['customer'])) die();

        $last_event = $database->get_event_last();
        $tpl->assign('id', $last_event['zef_ID']);
        $tpl->assign('startday', date("d.m.Y",$last_event['zef_in']));
        $tpl->assign('starttime', date("H:i:s",$last_event['zef_in']));
        $tpl->display("edit_running_starttime.tpl");
    break;

    case "add_edit_record":  
        if (isset($kga['customer'])) die();  
    // ==============================================
    // = display edit dialog for timesheet record   =
    // ==============================================
    $selected = explode('|',$axValue);
    if ($id) {
        $zef_entry = $database->zef_get_data($id);
        $tpl->assign('id', $id);
        $tpl->assign('zlocation', $zef_entry['zef_location']);
        
        $tpl->assign('trackingnr', $zef_entry['zef_trackingnr']);
        $tpl->assign('description', $zef_entry['zef_description']);
        $tpl->assign('comment', $zef_entry['zef_comment']);
        
        $tpl->assign('rate', $zef_entry['zef_rate']);
        $tpl->assign('fixed_rate', $zef_entry['zef_fixed_rate']);
        
        $tpl->assign('cleared', $zef_entry['zef_cleared']!=0);

        $tpl->assign('user', $zef_entry['zef_usrID']);
    
        $tpl->assign('edit_in_day', date("d.m.Y",$zef_entry['zef_in']));
        $tpl->assign('edit_out_day', date("d.m.Y",$zef_entry['zef_out']));
    
        $tpl->assign('edit_in_time',  date("H:i:s",$zef_entry['zef_in']));
        $tpl->assign('edit_out_time', date("H:i:s",$zef_entry['zef_out']));

        $tpl->assign('approved', $zef_entry['zef_approved']);
        $tpl->assign('budget', $zef_entry['zef_budget']);
        
        // preselected
        $tpl->assign('pres_pct', $zef_entry['zef_pctID']);
        $tpl->assign('pres_evt', $zef_entry['zef_evtID']);
    
        $tpl->assign('comment_active', $zef_entry['zef_comment_type']);
        $tpl->assign('status_active', $zef_entry['zef_status']);
        $tpl->assign('billable_active', $zef_entry['zef_billable']);

        // budget
        $eventBudgets = $database->get_evt_budget($zef_entry['zef_pctID'], $zef_entry['zef_evtID']);
        $eventUsed = $database->get_budget_used($zef_entry['zef_pctID'], $zef_entry['zef_evtID']);
        $tpl->assign('budget_event', round($eventBudgets['evt_budget'], 2));
        $tpl->assign('approved_event', round($eventBudgets['evt_approved'], 2));
        $tpl->assign('budget_event_used', $eventUsed);

    } else {
        $tpl->assign('id', 0);
        
        $tpl->assign('edit_in_day', date("d.m.Y"));
        $tpl->assign('edit_out_day', date("d.m.Y"));

        $tpl->assign('user', $kga['usr']['usr_ID']);

        if($kga['conf']['roundTimesheetEntries'] != '') {
	        $zefData = $database->zef_get_data(false);
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
	        $dayEntry = date("d", $zefData['zef_out']);
	        if($day == $dayEntry) {
	        	$tpl->assign('edit_in_time',  date("H:i:s", $zefData['zef_out']));
	        } else {
	        	$tpl->assign('edit_in_time',  date("H:i:s"));
	        }
	        $tpl->assign('edit_out_time', date("H:i:s", $end));
        } else {
	        $tpl->assign('edit_in_time', date("H:i:s"));
	        $tpl->assign('edit_out_time', date("H:i:s"));
        }
        $tpl->assign('rate',$database->get_best_fitting_rate($kga['usr']['usr_ID'],$selected[0],$selected[1]));
        $tpl->assign('fixed_rate',$database->get_best_fitting_fixed_rate($selected[0],$selected[1]));
    }

    $tpl->assign('status', $kga['conf']['status']);
    
    $billableValues = Config::getConfig('billable');
    $tpl->assign('billableValues', $billableValues); 
    foreach($billableValues as $index => $billableValue) {
    	$billableValues[$index] = $billableValue.'%';
    }
    $tpl->assign('billable', $billableValues);
    $tpl->assign('comment_types', $comment_types);
    $tpl->assign('comment_values', array('0','1','2'));

    $users = $database->get_arr_watchable_users($kga['usr']);
    $userIds = array();
    $userNames = array();

    foreach ($users as $user) {
      $userIds[] = $user['usr_ID'];
      $userNames[] = $user['usr_name'];
    }

    $tpl->assign('userIds', $userIds);
    $tpl->assign('userNames', $userNames);

    // select for projects
    $sel = makeSelectBox("pct",$kga['usr']['groups']);
    $tpl->assign('sel_pct_names', $sel[0]);
    $tpl->assign('sel_pct_IDs',   $sel[1]);

    // select for events
    $sel = makeSelectBox("evt",$kga['usr']['groups']);
    $tpl->assign('sel_evt_names', $sel[0]);
    $tpl->assign('sel_evt_IDs',   $sel[1]);



    $tpl->display("add_edit_record.tpl"); 

    break;        

}

?>

    