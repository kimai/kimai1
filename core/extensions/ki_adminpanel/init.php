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

    $usr = $database->checkUser();
    // ============================================
    // = initialize currently displayed timespace =
    // ============================================
    $timespace = get_timespace();
    $in = $timespace[0];
    $out = $timespace[1];

    // set smarty config
    require_once('../../libraries/smarty/Smarty.class.php');
    $tpl = new Smarty();
    $tpl->template_dir = 'templates/';
    $tpl->compile_dir  = 'compile/';

    $tpl->assign('kga', $kga);

    // ==========================
    // = display customer table =
    // ==========================
    if ($kga['usr']['status']==0)
      $arr_knd = $database->get_arr_customers();
    else
      $arr_knd = $database->get_arr_customers($kga['usr']['groups']);

    foreach ($arr_knd as $row=>$knd_data) {
      $grp_names = array();
      $groups = $database->customer_get_groupIDs($knd_data['customerID']);
      if ($groups !== false) {
        foreach ($groups as $groupID) {
          $data = $database->group_get_data($groupID);
          $grp_names[] = $data['name'];
        }
        $arr_knd[$row]['groups'] = implode(", ",$grp_names);
      }
    }

    if (count($arr_knd)>0) {
      $tpl->assign('arr_knd', $arr_knd);
    } else {
      $tpl->assign('arr_knd', '0');
    }
    $tpl->assign('knd_display', $tpl->fetch("knd.tpl"));

    // =========================
    // = display project table =
    // =========================
    if ($kga['usr']['status']==0)
      $arr_pct = $database->get_arr_projects();
    else
      $arr_pct = $database->get_arr_projects($kga['usr']['groups']);

    foreach ($arr_pct as $row=>$pct_data) {
      $grp_names = array();
      foreach ($database->project_get_groupIDs($pct_data['projectID']) as $groupID) {
        $data = $database->group_get_data($groupID);
         $grp_names[] = $data['name'];
      }
      $arr_pct[$row]['groups'] = implode(", ",$grp_names);
    }

    if (count($arr_pct)>0) {
      $tpl->assign('arr_pct', $arr_pct);
    } else {
      $tpl->assign('arr_pct', '0');
    }
    $tpl->assign('pct_display', $tpl->fetch("pct.tpl"));

    // ========================
    // = display events table =
    // ========================
    if ($kga['usr']['status']==0)
      $arr_evt = $database->get_arr_activities_by_project(-2);
    else
      $arr_evt = $database->get_arr_activities_by_project(-2,$kga['usr']['groups']);

    foreach ($arr_evt as $row=>$evt_data) {
      $grp_names = array();
      foreach ($database->activity_get_groups($evt_data['activityID']) as $grp_id) {
        $data = $database->group_get_data($grp_id);
         $grp_names[] = $data['name'];
      }
      $arr_evt[$row]['groups'] = implode(", ",$grp_names);
    }

    if (count($arr_evt)>0) {
      $tpl->assign('arr_evt', $arr_evt);
    } else {
      $tpl->assign('arr_evt', '0');
    }

    $tpl->assign('evt_display', $tpl->fetch("evt.tpl"));
    $tpl->assign('selected_evt_filter',-2);

    $tpl->assign('curr_user', $kga['usr']['name']);

    if ($kga['usr']['status']==0)
      $tpl->assign('arr_grp', $database->get_arr_groups(get_cookie('ap_ext_show_deleted_groups',0)));
    else
      $tpl->assign('arr_grp', $database->get_arr_groups_by_leader($kga['usr']['userID'],
        get_cookie('ap_ext_show_deleted_groups',0)));

      $tpl->assign('arr_statuses', $database->get_arr_statuses());
        
    if ($kga['usr']['status']==0)
      $arr_usr = $database->get_arr_users(get_cookie('ap_ext_show_deleted_users',0));
    else
      $arr_usr = $database->get_arr_watchable_users($kga['usr']);

    // get group names
    foreach ($arr_usr as &$user) {
      $groups = $database->getGroupMemberships($user['userID']);
      if(is_array($groups)) {
	      foreach ($groups as $group) {
	        $groupData = $database->group_get_data($group);
	        $user['groups'][] = $groupData['name'];
	      }
      }
    }

    $tpl->assign('arr_usr',$arr_usr);

    $tpl->assign('showDeletedGroups', get_cookie('ap_ext_show_deleted_groups',0));
    $tpl->assign('showDeletedUsers', get_cookie('ap_ext_show_deleted_users',0));
    $tpl->assign('languages', Translations::langs());

    $tpl->assign('timezones', timezoneList());
    $status = $database->get_arr_statuses();
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