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
include '../../includes/basics.php';
$settings = parse_ini_file("config.ini");

$user = checkUser();
// ============================================
// = initialize currently displayed timeframe =
// ============================================
$timeframe = get_timeframe();
$in = $timeframe[0];
$out = $timeframe[1];

$view = new Kimai_View();
$view->addBasePath(__DIR__ . '/templates/');

require 'functions.php';

$viewOtherGroupsAllowed = $database->global_role_allows($kga['user']['globalRoleID'], 'core-group-otherGroup-view');

// ==========================
// = display customer table =
// ==========================
if ($database->global_role_allows($kga['user']['globalRoleID'], 'core-customer-otherGroup-view')) {
    $customers = $database->get_customers();
} else {
    $customers = $database->get_customers($kga['user']['groups']);
}

foreach ($customers as $row => $data) {
    $groupNames = array();
    $groups = $database->customer_get_groupIDs($data['customerID']);
    if ($groups !== false) {
        foreach ($groups as $groupID) {
            if (!$viewOtherGroupsAllowed && array_search($groupID, $kga['user']['groups']) === false) {
                continue;
            }
            $data = $database->group_get_data($groupID);
            $groupNames[] = $data['name'];
        }
        $customers[$row]['groups'] = implode(", ", $groupNames);
    }
}

$view->assign('customers', $customers);
$view->assign('customer_display', $view->render("customers.php"));

// =========================
// = display project table =
// =========================
if ($database->global_role_allows($kga['user']['globalRoleID'], 'core-project-otherGroup-view')) {
    $projects = $database->get_projects();
} else {
    $projects = $database->get_projects($kga['user']['groups']);
}

$view->assign('projects', array());
if ($projects !== null && is_array($projects)) {
    foreach ($projects as $row => $project) {
        $groupNames = array();
        foreach ($database->project_get_groupIDs($project['projectID']) as $groupID) {
            if (!$viewOtherGroupsAllowed && array_search($groupID, $kga['user']['groups']) === false) {
                continue;
            }
            $data = $database->group_get_data($groupID);
            $groupNames[] = $data['name'];
        }
        $projects[$row]['groups'] = implode(", ", $groupNames);
    }
    $view->assign('projects', $projects);
}
$view->assign('project_display', $view->render("projects.php"));

// ========================
// = display activity table =
// ========================
if ($database->global_role_allows($kga['user']['globalRoleID'], 'core-activity-otherGroup-view')) {
    $activities = $database->get_activities_by_project(-2);
} else {
    $activities = $database->get_activities_by_project(-2, $kga['user']['groups']);
}

foreach ($activities as $row => $activity) {
    $groupNames = array();
    foreach ($database->activity_get_groups($activity['activityID']) as $groupID) {
        if (!$viewOtherGroupsAllowed && array_search($groupID, $kga['user']['groups']) === false) {
            continue;
        }
        $data = $database->group_get_data($groupID);
        $groupNames[] = $data['name'];
    }
    $activities[$row]['groups'] = implode(", ", $groupNames);
}

$view->assign('activities', $activities);

$view->assign('activity_display', $view->render("activities.php"));
$view->assign('selected_activity_filter', -2);

$view->assign('curr_user', $kga['user']['name']);

$groups = $database->get_groups(get_cookie('adminPanel_extension_show_deleted_groups', 0));
if ($database->global_role_allows($kga['user']['globalRoleID'], 'core-group-otherGroup-view')) {
    $view->assign('groups', $groups);
} else {
    $view->assign('groups', array_filter(
        $groups,
        function ($group) {
            global $kga;
            return array_search($group['groupID'], $kga['user']['groups']) !== false;
        }
    ));
}

$view->assign('arr_statuses', $database->get_statuses());

$view->assign('users', getEditUserList($database, $kga['user'], $viewOtherGroupsAllowed));

// ==============================
// = display global roles table =
// ==============================
$view->assign('globalRoles', $database->global_roles());
$view->assign('globalRoles_display', $view->render("globalRoles.php"));

// ==================================
// = display membership roles table =
// ==================================
$view->assign('membershipRoles', $database->membership_roles());
$view->assign('membershipRoles_display', $view->render("membershipRoles.php"));

$view->assign('showDeletedGroups', get_cookie('adminPanel_extension_show_deleted_groups', 0));
$view->assign('showDeletedUsers', get_cookie('adminPanel_extension_show_deleted_users', 0));
$view->assign('languages', Kimai_Translation_Service::getAvailableLanguages());

$view->assign('timezones', timezoneList());
$status = $database->get_statuses();
$view->assign('arr_status', $status);

$admin['users'] = $view->render("users.php");
$admin['groups'] = $view->render("groups.php");
$admin['status'] = $view->render("status.php");

$view->assign('editLimitEnabled', false);
$view->assign('editLimitDays', '');
$view->assign('editLimitHours', '');

if ($kga->isEditLimit()) {
    $view->assign('editLimitEnabled', true);
    $editLimit = $kga->getEditLimit() / (60 * 60); // convert to hours
    $view->assign('editLimitDays', (int)($editLimit / 24));
    $view->assign('editLimitHours', (int)($editLimit % 24));
}

$view->assign('roundTimesheetEntries', false);
$view->assign('roundMinutes', '');
$view->assign('roundSeconds', '');

if ($kga['conf']['roundTimesheetEntries'] != '') {
    $view->assign('roundTimesheetEntries', true);
    $view->assign('roundMinutes', $kga['conf']['roundMinutes']);
    $view->assign('roundSeconds', $kga['conf']['roundSeconds']);
}

$view->assign('showAdvancedTab', $database->global_role_allows($kga['user']['globalRoleID'], 'adminPanel_extension-editAdvanced'));
if ($view->showAdvancedTab) {
    $admin['advanced'] = $view->render("advanced.php");
    $admin['database'] = $view->render("database.php");
}

$view->assign('admin', $admin);

echo $view->render('main.php');
