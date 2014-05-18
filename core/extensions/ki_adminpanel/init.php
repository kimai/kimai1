<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team
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

$view = new Kimai_View();
$view->addBasePath(dirname(__FILE__).'/templates/');

$view->kga = $kga;

// ==========================
// = display customer table =
// ==========================
if ($database->global_role_allows($kga['user']['globalRoleID'], 'core-customer-otherGroup-view'))
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
if ($database->global_role_allows($kga['user']['globalRoleID'], 'core-project-otherGroup-view'))
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
if ($database->global_role_allows($kga['user']['globalRoleID'], 'core-activity-otherGroup-view'))
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

$groups = $database->get_groups(get_cookie('adminPanel_extension_show_deleted_groups',0));
if ($database->global_role_allows($kga['user']['globalRoleID'], 'core-group-otherGroup-view'))
  $view->groups = $groups;
else
  $view->groups = array_filter($groups, function($group) {global $kga; return array_search($group['groupID'], $kga['user']['groups']) !== false; });

$view->arr_statuses = $database->get_statuses();

if ($database->global_role_allows($kga['user']['globalRoleID'], 'core-user-otherGroup-view'))
  $users = $database->get_users(get_cookie('adminPanel_extension_show_deleted_users',0));
else
  $users = $database->get_users(get_cookie('adminPanel_extension_show_deleted_users',0),$kga['user']['groups']);

// get group names
foreach ($users as &$user) {
  $user['groups'] = array();
  $groups = $database->getGroupMemberships($user['userID']);
  if(is_array($groups)) {
      foreach ($groups as $group) {
        $groupData = $database->group_get_data($group);
        $user['groups'][] = $groupData['name'];
      }
  }
}

$view->users = $users;

// ==============================
// = display global roles table =
// ==============================
$view->globalRoles = $database->global_roles();
$view->globalRoles_display = $view->render("globalRoles.php");

// ==================================
// = display membership roles table =
// ==================================
$view->membershipRoles = $database->membership_roles();
$view->membershipRoles_display = $view->render("membershipRoles.php");


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

$view->showAdvancedTab = $database->global_role_allows($kga['user']['globalRoleID'], 'adminPanel_extension-editAdvanced');
if ($view->showAdvancedTab) {
  $admin['advanced'] = $view->render("advanced.php");
  $admin['database'] = $view->render("database.php");
}

$view->admin = $admin;

echo $view->render('main.php');
