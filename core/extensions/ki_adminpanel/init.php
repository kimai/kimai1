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

    $dir_templates = "templates/";
    $datasrc = "config.ini";
    $settings = parse_ini_file($datasrc);
    $dir_ext = $settings['EXTENSION_DIR'];

    $user = checkUser();
    // ============================================
    // = initialize currently displayed timeframe =
    // ============================================
    $timeframe = get_timeframe();
    $in = $timeframe[0];
    $out = $timeframe[1];

    $view = new Zend_View();
    $view->setBasePath(WEBROOT . 'extensions/' . $dir_ext . '/' . $dir_templates);
    $view->addHelperPath(WEBROOT.'/templates/helpers','Zend_View_Helper');

    $view->kga = $kga;

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

    $view->customers = $customers;
    $view->customer_display = $view->render("customers.php");

    // =========================
    // = display project table =
    // =========================
    if ($kga['user']['status']==0)
      $projects = $database->get_projects();
    else
      $projects = $database->get_projects($kga['user']['groups']);

    $view->projects = array();
    if ($projects !== null && is_array($projects)) {
        foreach ($projects as $row=>$project) {
            $groupNames = array();
            foreach ($database->project_get_groupIDs($project['projectID']) as $groupID) {
                $data = $database->group_get_data($groupID);
                $groupNames[] = $data['name'];
            }
            $projects[$row]['groups'] = implode(", ",$groupNames);
        }
        $view->projects = $projects;
    }
    $view->project_display = $view->render("projects.php");

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

    $view->activities = $activities;

    $view->activity_display = $view->render("activities.php");
    $view->selected_activity_filter = -2;

    $view->curr_user = $kga['user']['name'];

    if ($kga['user']['status']==0)
      $view->groups = $database->get_groups(get_cookie('adminPanel_extension_show_deleted_groups',0));
    else
      $view->groups = $database->get_groups_by_leader($kga['user']['userID'],
        get_cookie('adminPanel_extension_show_deleted_groups',0));

      $view->arr_statuses = $database->get_statuses();
        
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

    $view->users = $users;

    $view->showDeletedGroups = get_cookie('adminPanel_extension_show_deleted_groups',0);
    $view->showDeletedUsers = get_cookie('adminPanel_extension_show_deleted_users',0);
    $view->languages = Translations::langs();

    $view->timezones = timezoneList();
    $status = $database->get_statuses();
    $view->arr_status = $status;

    $admin['users'] = $view->render("users.php");
    $admin['groups'] = $view->render("groups.php");
    $admin['status'] = $view->render("status.php");



    if ($kga['conf']['editLimit'] != '-') {
      $view->editLimitEnabled = true;
      $editLimit = $kga['conf']['editLimit']/(60*60); // convert to hours
      $view->editLimitDays = (int) ($editLimit/24) ;
      $view->editLimitHours = (int) ($editLimit%24) ;
    }
    else {
      $view->editLimitEnabled = false;
      $view->editLimitDays = '';
      $view->editLimitHours = '';
    }
        if ($kga['conf']['roundTimesheetEntries'] != '') {
      $view->roundTimesheetEntries = true;
      $view->roundMinutes = $kga['conf']['roundMinutes'];
      $view->roundSeconds = $kga['conf']['roundSeconds'];
    }
    else {
      $view->roundTimesheetEntries = false;
      $view->roundMinutes = '';
      $view->roundSeconds = '';
    }
    $admin['advanced'] = $view->render("advanced.php");
    
    if ($kga['show_sensible_data']) {
        $admin['database'] = $view->render("database.php");
    } else {
        $admin['database'] = "You don't have permission to see this information ...";
    }

    $view->admin = $admin;

    echo $view->render('main.php');
?>