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

    // Include Basics
    include('../../includes/basics.php');

    $user = checkUser();
    // ============================================
    // = initialize currently displayed timeframe =
    // ============================================
    $timeframe = get_timeframe();
    $in = $timeframe[0];
    $out = $timeframe[1];

    // set smarty config
    require_once('../../libraries/smarty/Smarty.class.php');
    $tpl = new Smarty();
    $tpl->template_dir = 'templates/';
    $tpl->compile_dir  = 'compile/';

    $tpl->assign('kga', $kga);

    // ==========================
    // = display customer table =
    // ==========================
    if ($kga['user']['status']==0)
      $customers = $database->get_customers();
    else
      $customers = $database->get_customers($kga['user']['groups']);

    foreach ($customers as $row=>$data) {
      $groupNames = array();
      $groups = $database->customer_get_groupIDs($data['customerID']);
      if ($groups !== false) {
        foreach ($groups as $groupID) {
          $data = $database->group_get_data($groupID);
          $groupNames[] = $data['name'];
        }
        $customers[$row]['groups'] = implode(", ",$groupNames);
      }
    }

    if (count($customers)>0) {
      $tpl->assign('customers', $customers);
    } else {
      $tpl->assign('customers', '0');
    }
    $tpl->assign('customer_display', $tpl->fetch("customers.tpl"));

    // =========================
    // = display project table =
    // =========================
    if ($kga['user']['status']==0)
      $projects = $database->get_projects();
    else
      $projects = $database->get_projects($kga['user']['groups']);

    foreach ($projects as $row=>$project) {
      $groupNames = array();
      foreach ($database->project_get_groupIDs($project['projectID']) as $groupID) {
        $data = $database->group_get_data($groupID);
         $groupNames[] = $data['name'];
      }
      $projects[$row]['groups'] = implode(", ",$groupNames);
    }

    if (count($projects)>0) {
      $tpl->assign('projects', $projects);
    } else {
      $tpl->assign('projects', '0');
    }
    $tpl->assign('project_display', $tpl->fetch("projects.tpl"));

    // ========================
    // = display activity table =
    // ========================
    if ($kga['user']['status']==0)
      $activities = $database->get_activities_by_project(-2);
    else
      $activities = $database->get_activities_by_project(-2,$kga['user']['groups']);

    foreach ($activities as $row=>$activity) {
      $groupNames = array();
      foreach ($database->activity_get_groups($activity['activityID']) as $groupID) {
        $data = $database->group_get_data($groupID);
         $groupNames[] = $data['name'];
      }
      $activities[$row]['groups'] = implode(", ",$groupNames);
    }

    if (count($activities)>0) {
      $tpl->assign('activities', $activities);
    } else {
      $tpl->assign('activities', '0');
    }

    $tpl->assign('activity_display', $tpl->fetch("activities.tpl"));
    $tpl->assign('selected_activity_filter',-2);

    $tpl->assign('curr_user', $kga['user']['name']);

    if ($kga['user']['status']==0)
      $tpl->assign('groups', $database->get_groups(get_cookie('adminPanel_extension_show_deleted_groups',0)));
    else
      $tpl->assign('groups', $database->get_groups_by_leader($kga['user']['userID'],
        get_cookie('adminPanel_extension_show_deleted_groups',0)));

      $tpl->assign('arr_statuses', $database->get_statuses());
        
    if ($kga['user']['status']==0)
      $users = $database->get_users(get_cookie('adminPanel_extension_show_deleted_users',0));
    else
      $users = $database->get_watchable_users($kga['user']);

    // get group names
    foreach ($users as &$user) {
      $groups = $database->getGroupMemberships($user['userID']);
      if(is_array($groups)) {
	      foreach ($groups as $group) {
	        $groupData = $database->group_get_data($group);
	        $user['groups'][] = $groupData['name'];
	      }
      }
    }

    $tpl->assign('users',$users);

    $tpl->assign('showDeletedGroups', get_cookie('adminPanel_extension_show_deleted_groups',0));
    $tpl->assign('showDeletedUsers', get_cookie('adminPanel_extension_show_deleted_users',0));
    $tpl->assign('languages', Translations::langs());

    $tpl->assign('timezones', timezoneList());
    $status = $database->get_statuses();
    $tpl->assign('arr_status', $status);

    $admin['users'] = $tpl->fetch("users.tpl");
    $admin['groups'] = $tpl->fetch("groups.tpl");
    $admin['status'] = $tpl->fetch("status.tpl");



    if ($kga['conf']['editLimit'] != '-') {
      $tpl->assign('editLimitEnabled',true);
      $editLimit = $kga['conf']['editLimit']/(60*60); // convert to hours
      $tpl->assign('editLimitDays',(int) ($editLimit/24) );
      $tpl->assign('editLimitHours',(int) ($editLimit%24) );
    }
    else {
      $tpl->assign('editLimitEnabled',false);
      $tpl->assign('editLimitDays','');
      $tpl->assign('editLimitHours','');
    }
        if ($kga['conf']['roundTimesheetEntries'] != '') {
      $tpl->assign('roundTimesheetEntries',true);
      $tpl->assign('roundMinutes',$kga['conf']['roundMinutes']);
      $tpl->assign('roundSeconds',$kga['conf']['roundSeconds']);
    }
    else {
      $tpl->assign('roundTimesheetEntries',false);
      $tpl->assign('roundMinutes','');
      $tpl->assign('roundSeconds','');
    }
    $admin['advanced'] = $tpl->fetch("advanced.tpl");
    
    if ($kga['show_sensible_data']) {
        $admin['database'] = $tpl->fetch("database.tpl");
    } else {
        $admin['database'] = "You don't have permission to see this information ...";
    }

    $tpl->assign('admin',  $admin);

    $tpl->display('main.tpl');
?>