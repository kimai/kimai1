<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team since 2006
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

$user = checkUser();

$view = new Kimai_View();
$view->addBasePath(__DIR__ . '/templates/');

require 'functions.php';

$viewOtherGroupsAllowed = $database->global_role_allows($kga['user']['globalRoleID'], 'core-group-otherGroup-view');

// ========================
// = display groups table =
// ========================
$groupsData = getGroupsData($database, $kga['user'], $viewOtherGroupsAllowed);
foreach($groupsData as $key => $value) {
    $view->assign($key, $value);
}
$view->tab_groups = $view->render("groups.php");

// ==========================
// = display customer table =
// ==========================
$customersData = getCustomersData($database, $kga['user'], $viewOtherGroupsAllowed);
foreach ($customersData as $key => $value) {
    $view->assign($key, $value);
}
$view->tab_customer = $view->render("customers.php");

// =========================
// = display project table =
// =========================
$projectsData = getProjectsData($database, $kga['user'], $viewOtherGroupsAllowed);
foreach ($projectsData as $key => $value) {
    $view->assign($key, $value);
}
$view->tab_project = $view->render("projects.php");

// ==========================
// = display activity table =
// ==========================
$activitiesData = getActivitiesData($database, $kga['user'], $viewOtherGroupsAllowed);
foreach ($activitiesData as $key => $data) {
    $view->assign($key, $data);
}
$view->assign('tab_activity', $view->render("activities.php"));

// =======================
// = display users table =
// =======================
$userData = getUsersData($database, $kga['user'], $viewOtherGroupsAllowed);
foreach ($userData as $key => $value) {
    $view->assign($key, $value);
}
$view->assign("tab_users", $view->render("users.php"));

// ==============================
// = display global roles table =
// ==============================
$view->assign('globalRoles', $database->global_roles());
$view->assign('tab_globalrole', $view->render("globalRoles.php"));

// ==================================
// = display membership roles table =
// ==================================
$view->assign('membershipRoles', $database->membership_roles());
$view->assign('tab_membershiprole', $view->render("membershipRoles.php"));

// ========================
// = display status table =
// ========================
$view->assign('statuses', $database->get_statuses());
$view->assign('tab_status', $view->render("status.php"));

// ========================
// = display advanced tab =
// ========================
$showAdvancedTab = $database->global_role_allows($kga['user']['globalRoleID'], 'adminPanel_extension-editAdvanced');
if ($showAdvancedTab) {
    $view->assign('languages', Kimai_Translations::languages());
    $view->assign('timezones', timezoneList());

    $view->assign('editLimitEnabled', false);
    $view->assign('editLimitDays', '');
    $view->assign('editLimitHours', '');

    if ($kga['conf']['editLimit'] != '-') {
        $view->assign('editLimitEnabled', true);
        $editLimit = $kga['conf']['editLimit'] / (60 * 60); // convert to hours
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

    $view->assign('tab_advanced', $view->render("advanced.php"));
    $view->assign('tab_database', $view->render("database.php"));
}

echo $view->render('main.php');
