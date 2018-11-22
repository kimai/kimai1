<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
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

$isCoreProcessor = 0;
$dir_templates = 'templates/';
require '../../includes/kspi.php';

$database = Kimai_Registry::getDatabase();

require 'functions.php';

switch ($axAction) {
    case 'createUser':
        // create new user account
        $userData['name'] = trim($axValue);
        $userData['globalRoleID'] = $kga['user']['globalRoleID'];
        $userData['active'] = 1;

        $groupsWithAddPermission = [];
        foreach ($kga['user']['groups'] as $group) {
            $membershipRoleID = $database->user_get_membership_role($kga['user']['userID'], $group);
            if ($database->membership_role_allows($membershipRoleID, 'core-user-add')) {
                $groupsWithAddPermission[$group] = $membershipRoleID;
            }
        }

        // validate data
        $errors = [];

        // check if user exists already
        if ($database->user_name2id($userData['name']) !== false) {
            $errors[] = $kga['lang']['errorMessages']['userExistsAlready'];
        }

        // check for customer with same name
        if ($database->customer_nameToID($userData['name']) !== false) {
            $errors[] = $kga['lang']['errorMessages']['customerWithSameName'];
        }

        if (count($groupsWithAddPermission) == 0) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        $userId = false;
        if (count($errors) == 0) {
            $userId = $database->user_create($userData);
            $database->setGroupMemberships($userId, $groupsWithAddPermission);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(
            [
                'errors' => $errors,
                'userId' => $userId
            ]
        );
        break;

    case 'createStatus':
        $status_data['status'] = trim($axValue);

        // validate data
        $errors = [];

        if (!isset($kga['user']) || !$database->global_role_allows($kga['user']['globalRoleID'], 'core-status-add')) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        // create new status
        $new_status_id = $database->status_create($status_data);

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(
            [
                'errors' => $errors,
                'statusId' => $new_status_id
            ]
        );
        break;

    case 'createGroup' :
        $group['name'] = trim($axValue);

        // validate data
        $errors = [];

        if (!isset($kga['user']) || !$database->global_role_allows($kga['user']['globalRoleID'], 'core-group-add')) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        // create new group
        $newGroupID = $database->group_create($group);

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(
            [
                'errors' => $errors,
                'groupId' => $newGroupID
            ]
        );
        break;

    case 'refreshSubtab' :
        $viewOtherGroupsAllowed = $database->global_role_allows($kga['user']['globalRoleID'], 'core-group-otherGroup-view');

        switch ($axValue) {
            case 'users':
                $userData = getUsersData($database, $kga['user'], $viewOtherGroupsAllowed);
                foreach($userData as $key => $value) {
                    $view->assign($key, $value);
                }
                echo $view->render('users.php');
                break;

            case 'groups':
                $groupsData = getGroupsData($database, $kga['user'], $viewOtherGroupsAllowed);
                foreach($groupsData as $key => $value) {
                    $view->assign($key, $value);
                }
                echo $view->render('groups.php');
                break;

            case 'status':
                $view->assign('statuses', $database->get_statuses());
                echo $view->render('status.php');
                break;

            case 'database':
                echo $view->render('database.php');
                break;

            case 'customers':
                $customersData = getCustomersData($database, $kga['user'], $viewOtherGroupsAllowed);
                foreach ($customersData as $key => $value) {
                    $view->assign($key, $value);
                }
                echo $view->render('customers.php');
                break;

            case 'projects':
                $projectsData = getProjectsData($database, $kga['user'], $viewOtherGroupsAllowed);
                foreach ($projectsData as $key => $value) {
                    $view->assign($key, $value);
                }
                echo $view->render('projects.php');
                break;

            case 'activities':
                $activitiesData = getActivitiesData($database, $kga['user'], $viewOtherGroupsAllowed);
                foreach($activitiesData as $key => $data) {
                    $view->assign($key, $data);
                }
                echo $view->render('activities.php');
                break;

            case 'globalRoles':
                $view->assign('globalRoles', $database->global_roles());
                echo $view->render('globalRoles.php');
                break;

            case 'membershipRoles':
                $view->assign('membershipRoles', $database->membership_roles());
                echo $view->render('membershipRoles.php');
                break;
        }
        break;

    case 'deleteUser':
        $oldGroups = $database->getGroupMemberships($id);
        $errors = [];

        if (!checkGroupedObjectPermission('user', 'delete', $oldGroups, $oldGroups)) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        if (count($errors) == 0) {
            switch ($axValue) {
                // User is re-activated, moving back from trash to "normal state"
                case 0:
                    $database->user_edit($id, ['trash' => 0]);
                    break;

                // If the confirmation is returned the user is moved to trash
                case 1:
                    $database->user_delete($id, true);
                    break;

                // User is finally deleted after confirmed through trash view
                case 2:
                    $database->user_delete($id, false);
                    break;

                // unknown action, display an error message
                default:
                    $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
                    break;
            }
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;

    case 'deleteGroup':
        $errors = [];

        if (!checkGroupedObjectPermission('group', 'delete', [$id], [$id])) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        // removes a group
        if (count($errors) == 0) {
            $database->group_delete($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;

    case 'deleteStatus':
        $errors = [];
        if (!isset($kga['user']) || !$database->global_role_allows($kga['user']['globalRoleID'], 'core-status-delete')) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        // If the confirmation is returned the status gets deleted.
        if (count($errors) == 0) {
            $database->status_delete($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;

    case 'deleteProject':
        $errors = [];
        $oldGroups = $database->project_get_groupIDs($id);

        if (!checkGroupedObjectPermission('project', 'delete', $oldGroups, $oldGroups)) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        // If the confirmation is returned the project gets the trash-flag.
        if (count($errors) == 0) {
            $database->project_delete($id);
            break;
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;

    case 'deleteCustomer':
        $errors = [];
        $oldGroups = $database->customer_get_groupIDs($id);

        if (!checkGroupedObjectPermission('project', 'delete', $oldGroups, $oldGroups)) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        // If the confirmation is returned the customer gets the trash-flag.
        if (count($errors) == 0) {
            $database->customer_delete($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;

    case 'deleteActivity':
        $errors = [];
        $oldGroups = $database->activity_get_groupIDs($id);

        if (!checkGroupedObjectPermission('activity', 'delete', $oldGroups, $oldGroups)) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        // If the confirmation is returned the activity gets the trash-flag.
        if (count($errors) == 0) {
            $database->activity_delete($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;

    case 'banUser':
        // Ban a user from login
        $sts['active'] = 0;
        $database->user_edit($id, $sts);
        echo sprintf('<img border="0" title="%s" alt="%s" src="../skins/%s/grfx/lock.png" width="16" height="16" />', $kga['lang']['bannedUser'], $kga['lang']['bannedUser'], $view->skin()->getName());
        break;

    case 'unbanUser':
        // Unban a user from login
        $sts['active'] = 1;
        $database->user_edit($id, $sts);
        echo sprintf('<img border="0" title="%s" alt="%s" src="../skins/%s/grfx/jipp.gif" width="16" height="16" />', $kga['lang']['activeAccount'], $kga['lang']['activeAccount'], $view->skin()->getName());
        break;

    case 'sendEditUser':
        // process editUser form
        $userData['name'] = trim($_REQUEST['name']);
        $userData['mail'] = $_REQUEST['mail'];
        $userData['alias'] = $_REQUEST['alias'];
        $userData['globalRoleID'] = $_REQUEST['globalRoleID'];
        $userData['rate'] = str_replace($kga['conf']['decimalSeparator'], '.', $_REQUEST['rate']);
        // if password field is empty => password unchanged (not overwritten with "")
        if (!empty($_REQUEST['password'])) {
            $userData['password'] = encode_password($_REQUEST['password']);
        }

        $oldGroups = $database->getGroupMemberships($id);

        // validate data
        $errorMessages = [];

        if ($database->customer_nameToID($userData['name']) !== false) {
            $errorMessages['name'] = $kga['lang']['errorMessages']['customerWithSameName'];
        }

        $assignedGroups = isset($_REQUEST['assignedGroups']) ? $_REQUEST['assignedGroups'] : [];
        $membershipRoles = isset($_REQUEST['membershipRoles']) ? $_REQUEST['membershipRoles'] : [];

        if (!checkGroupedObjectPermission('user', 'edit', $oldGroups, $assignedGroups)) {
            $errorMessages[''] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        if (count($errorMessages) == 0) {
            $database->user_edit($id, $userData);
            $groups = array_combine($assignedGroups, $membershipRoles);
            $database->setGroupMemberships($id, $groups);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(
            [
                'errors' => $errorMessages
            ]
        );
        break;

    case 'sendEditGroup':
        // process editGroup form
        $group['name'] = trim($_REQUEST['name']);

        $errors = [];

        if (!checkGroupedObjectPermission('group', 'edit', [$id], [$id])) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        if (count($errors) == 0) {
            $database->group_edit($id, $group);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;

    case 'sendEditStatus':
        // process editStatus form
        $status_data['status'] = trim($_REQUEST['status']);

        $errors = [];

        if (!isset($kga['user']) || !$database->global_role_allows($kga['user']['globalRoleID'], 'core-status-edit')) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        if (count($errors) == 0) {
            $database->status_edit($id, $status_data);
            $database->configuration_edit(['defaultStatusID' => $id]);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;

    case 'sendEditAdvanced':
        $errors = [];
        if (!isset($kga['user']) || !$database->global_role_allows($kga['user']['globalRoleID'], 'adminPanel_extension-editAdvanced')) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        if (count($errors) == 0) {
            // process AdvancedOptions form
            $config_data['adminmail'] = $_REQUEST['adminmail'];
            $config_data['loginTries'] = $_REQUEST['logintries'];
            $config_data['loginBanTime'] = $_REQUEST['loginbantime'];
            $config_data['show_update_warn'] = getRequestBool('show_update_warn');
            $config_data['check_at_startup'] = getRequestBool('check_at_startup');
            $config_data['show_daySeperatorLines'] = getRequestBool('show_daySeperatorLines');
            $config_data['show_gabBreaks'] = getRequestBool('show_gabBreaks');
            $config_data['show_RecordAgain'] = getRequestBool('show_RecordAgain');
            $config_data['show_TrackingNr'] = getRequestBool('show_TrackingNr');
            $config_data['currency_name'] = $_REQUEST['currency_name'];
            $config_data['currency_sign'] = $_REQUEST['currency_sign'];
            $config_data['currency_first'] = getRequestBool('currency_first');
            $config_data['date_format_0'] = $_REQUEST['date_format_0'];
            $config_data['date_format_1'] = $_REQUEST['date_format_1'];
            $config_data['date_format_2'] = $_REQUEST['date_format_2'];
            $config_data['date_format_3'] = $_REQUEST['date_format_3'];
            $config_data['table_time_format'] = $_REQUEST['table_time_format'];
            $config_data['language'] = $_REQUEST['language'];
            if (isset($_REQUEST['status']) && is_array($_REQUEST['status'])) {
                $config_data['status'] = implode(',', $_REQUEST['status']);
            }
            $config_data['roundPrecision'] = $_REQUEST['roundPrecision'];
            $config_data['allowRoundDown'] = getRequestBool('allowRoundDown');
            $config_data['roundMinutes'] = $_REQUEST['roundMinutes'];
            $config_data['roundSeconds'] = $_REQUEST['roundSeconds'];
            $config_data['roundTimesheetEntries'] = $_REQUEST['roundTimesheetEntries'];
            $config_data['decimalSeparator'] = $_REQUEST['decimalSeparator'];
            $config_data['durationWithSeconds'] = getRequestBool('durationWithSeconds');
            $config_data['exactSums'] = getRequestBool('exactSums');
            $editLimit = false;
            if (getRequestBool('editLimitEnabled')) {
                $hours = (int)$_REQUEST['editLimitHours'];
                $days = (int)$_REQUEST['editLimitDays'];
                $editLimit = $hours + $days * 24;
                $editLimit *= 60 * 60; // convert to seconds
            }
            if ($editLimit === false || $editLimit === 0) {
                $config_data['editLimit'] = 0;
            } else {
                $config_data['editLimit'] = $editLimit;
            }

            if (!$database->configuration_edit($config_data)) {
                $errors[] = $kga['lang']['error'];
            }
        }

        if (count($errors) == 0) {
            write_config_file(
                $kga['server_database'],
                $kga['server_hostname'],
                $kga['server_username'],
                $kga['server_password'],
                $kga['server_charset'],
                $kga['server_prefix'],
                $_REQUEST['language'],
                $kga['password_salt'],
                $_REQUEST['defaultTimezone']
            );
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;

    case 'toggleDeletedUsers' :
        setcookie("adminPanel_extension_show_deleted_users", $axValue);
        break;

    case 'createGlobalRole':
        $role_data['name'] = trim($axValue);

        $errors = [];

        if (!isset($kga['user'])) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        } else if ($database->globalRole_find($role_data)) {
            $errors[] = $kga['lang']['errorMessages']['sameGlobalRoleName'];
        }

        // create new status
        if (count($errors) == 0) {
            $database->global_role_create($role_data);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;

    case 'createMembershipRole':
        $role_data['name'] = trim($axValue);

        $errors = [];

        if (!isset($kga['user'])) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        if ($database->membershipRole_find($role_data)) {
            $errors[] = $kga['lang']['errorMessages']['sameMembershipRoleName'];
        }

        // create new status
        if (count($errors) == 0) {
            $database->membership_role_create($role_data);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;

    case 'editGlobalRole':
        $id = $_REQUEST['id'];
        $newData = $_REQUEST;
        unset($newData['id']);
        unset($newData['axAction']);

        $roleData = $database->globalRole_get_data($id);

        foreach ($roleData as $key => &$value) {
            if (isset($newData[$key])) {
                $value = $newData[$key];
            } else if ($key != "globalRoleID" && $key != "name") {
                $value = 0;
            }
        }

        $errors = [];

        if (!isset($kga['user'])) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        if (count($errors) == 0) {
            $database->global_role_edit($id, $roleData);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;

    case 'editMembershipRole':
        $id = $_REQUEST['id'];
        $newData = $_REQUEST;
        unset($newData['id']);
        unset($newData['axAction']);

        $roleData = $database->membershipRole_get_data($id);

        foreach ($roleData as $key => &$value) {
            if (isset($newData[$key])) {
                $value = $newData[$key];
            } else if ($key != "membershipRoleID" && $key != "name") {
                $value = 0;
            }
        }

        $errors = [];

        if (!isset($kga['user'])) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        if (count($errors) == 0) {
            $database->membership_role_edit($id, $roleData);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;

    case 'deleteGlobalRole':
        $errors = [];

        if (!isset($kga['user'])) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        if (count($errors) == 0) {
            $database->global_role_delete($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;

    case 'deleteMembershipRole':
        $errors = [];

        if (!isset($kga['user'])) {
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        if (count($errors) == 0) {
            $database->membership_role_delete($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;
}
