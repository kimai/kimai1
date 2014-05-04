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

// ================
// = AP PROCESSOR =
// ================

require("../../includes/kspi.php");
$view->addBasePath(dirname(__FILE__).'/templates/');

switch ($axAction) {
    case "createUser" :
        // create new user account
        $userData['name'] = trim($axValue);
        $userData['globalRoleID'] = $kga['user']['globalRoleID'];
        $userData['active'] = 1;

        // validate data
        $errors = array();
        if ($database->customer_nameToID($userData['name']) !== false)
            $errors[] = $kga['lang']['errorMessages']['customerWithSameName'];

        if (!checkGroupedObjectPermission('user', 'add', array(), $kga['user']['groups']))
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];

        $userId = false;
        if (count($errors) == 0) {
            $userId = $database->user_create($userData);
            $groups = array();
            foreach ($kga['user']['groups'] as $group) {
                $groups[$group] = $database->user_get_membership_role($kga['user']['userID'], $group);
            }
            $database->setGroupMemberships($userId, $groups);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array('errors' => $errors, 'userId' => $userId));
        break;

    case "createStatus" :
        $status_data['status'] = trim($axValue);

        // validate data
        $errors = array();

        if (!isset($kga['user']) || !$database->global_role_allows($kga['user']['globalRoleID'], 'core-status-add'))
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];

        // create new status
        $new_status_id = $database->status_create($status_data);

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors,
            'statusId' => $new_status_id));
        break;

    case "createGroup" :
        $group['name'] = trim($axValue);

        // validate data
        $errors = array();

        if (!isset($kga['user']) || !$database->global_role_allows($kga['user']['globalRoleID'], 'core-group-add'))
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];

        // create new group
        $newGroupID = $database->group_create($group);

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors,
            'groupId' => $newGroupID));
        break;

    case "refreshSubtab" :
        // builds either user/group/advanced/DB subtab
        $view->curr_user = $kga['user']['name'];
        $groups = $database->get_groups(get_cookie('adminPanel_extension_show_deleted_groups', 0));
        if ($database->global_role_allows($kga['user']['globalRoleID'], 'core-group-otherGroup-view'))
            $view->groups = $groups;
        else
            $view->groups = array_filter($groups, function ($group) {
                global $kga;
                return array_search($group['groupID'], $kga['user']['groups']) !== false;
            });

        if ($database->global_role_allows($kga['user']['globalRoleID'], 'core-user-otherGroup-view'))
            $users = $database->get_users(get_cookie('adminPanel_extension_show_deleted_users', 0));
        else
            $users = $database->get_users(get_cookie('adminPanel_extension_show_deleted_users', 0), $kga['user']['groups']);

        // get group names
        foreach ($users as &$user) {
            $user['groups'] = array();

            $groups = $database->getGroupMemberships($user['userID']);
            if (is_array($groups)) {
                foreach ($groups as $group) {
                    $groupData = $database->group_get_data($group);
                    $user['groups'][] = $groupData['name'];
                }
            }
        }
        $arr_status = $database->get_statuses();
        $view->users = $users;
        $view->arr_status = $arr_status;
        $view->showDeletedGroups = get_cookie('adminPanel_extension_show_deleted_groups', 0);
        $view->showDeletedUsers = get_cookie('adminPanel_extension_show_deleted_users', 0);

        switch ($axValue) {
            case "users" :
                echo $view->render('users.php');
                break;

            case "groups" :
                echo $view->render('groups.php');
                break;

            case "status" :
                echo $view->render('status.php');
                break;

            case "advanced" :
                if ($kga['conf']['editLimit'] != '-') {
                    $view->editLimitEnabled = true;
                    $editLimit = $kga['conf']['editLimit'] / (60 * 60); // convert to hours
                    $view->editLimitDays = (int)($editLimit / 24);
                    $view->editLimitHours = (int)($editLimit % 24);
                } else {
                    $view->editLimitEnabled = false;
                    $view->editLimitDays = '';
                    $view->editLimitHours = '';
                }
                echo $view->render('advanced.php');
                break;

            case "database" :
                echo $view->render('database.php');
                break;

            case "customers" :
                if ($database->global_role_allows($kga['user']['globalRoleID'], 'core-customer-otherGroup-view'))
                    $customers = $database->get_customers();
                else
                    $customers = $database->get_customers($kga['user']['groups']);

                foreach ($customers as $row => $data) {
                    $groupNames = array();
                    $groups = $database->customer_get_groupIDs($data['customerID']);
                    if ($groups !== false) {
                        foreach ($groups as $groupID) {
                            $data = $database->group_get_data($groupID);
                            $groupNames[] = $data['name'];
                        }
                        $customers[$row]['groups'] = implode(", ", $groupNames);
                    }
                }
                if (count($customers) > 0) {
                    $view->customers = $customers;
                } else {
                    $view->customers = '0';
                }
                echo $view->render('customers.php');
                break;

            case "projects" :
                if ($database->global_role_allows($kga['user']['globalRoleID'], 'core-project-otherGroup-view'))
                    $projects = $database->get_projects();
                else
                    $projects = $database->get_projects($kga['user']['groups']);

                if ($projects !== null && is_array($projects)) {
                    foreach ($projects as $row => $project) {
                        $groupNames = array();
                        foreach ($database->project_get_groupIDs($project['projectID']) as $groupID) {
                            $data = $database->group_get_data($groupID);
                            $groupNames[] = $data['name'];
                        }
                        $projects[$row]['groups'] = implode(", ", $groupNames);
                    }
                    $view->projects = $projects;
                }

                echo $view->render('projects.php');
                break;

            case "activities" :
                $groups = null;
                if (!$database->global_role_allows($kga['user']['globalRoleID'], 'core-activity-otherGroup-view'))
                    $groups = $kga['user']['groups'];

                if (isset($_REQUEST['activity_filter']) && $_REQUEST['activity_filter'] == -1)
                    $activities = $database->get_activities($groups);
                else {
                    // -2 is to get unassigned activities. As -2 is never
                    // an id of a project this will give us all unassigned
                    // activities.
                    $activities = $database->get_activities_by_project(-2, $groups);
                }

                foreach ($activities as $row => $activity) {
                    $groupNames = array();
                    foreach ($database->activity_get_groups($activity['activityID']) as $groupID) {
                        $data = $database->group_get_data($groupID);
                        $groupNames[] = $data['name'];
                    }
                    $activities[$row]['groups'] = implode(", ", $groupNames);
                }
                if (count($activities) > 0) {
                    $view->activities = $activities;
                } else {
                    $view->activities = '0';
                }
                $projects = $database->get_projects($groups);
                $view->projects = $projects;
                $view->selected_activity_filter = isset($_REQUEST['activity_filter']) ? $_REQUEST['activity_filter'] : -2;
                echo $view->render('activities.php');
                break;

            case "globalRoles":
                $view->globalRoles = $database->global_roles();
                echo $view->render('globalRoles.php');
                break;

            case "membershipRoles":
                $view->membershipRoles = $database->membership_roles();
                echo $view->render('membershipRoles.php');
                break;
        }
        break;

    case "deleteUser":
        $oldGroups = $database->getGroupMemberships($id);
        $errors = array();

        if (!checkGroupedObjectPermission('user', 'delete', $oldGroups, $oldGroups)) {
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
        }

        if (count($errors) == 0) {

            switch ($axValue) {
                case 1 :
                    // If the confirmation is returned the user gets the trash-flag.
                    $database->user_delete($id, true);
                    break;
                case 2 :
                    // User is finally deleted after confirmed through trash view
                    $database->user_delete($id, false);
                    break;
            }
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors));
        break;

    case "deleteGroup" :
        $errors = array();

        if (!checkGroupedObjectPermission('group', 'delete', array($id), array($id)))
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];

        if (count($errors) == 0) {
            // removes a group
            $database->group_delete($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors));
        break;

    case "deleteStatus" :
        $errors = array();
        if (!isset($kga['user']) || !$database->global_role_allows($kga['user']['globalRoleID'], 'core-status-delete'))
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];

        if (count($errors) == 0) {
            // If the confirmation is returned the status gets deleted.
            $database->status_delete($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors));
        break;

    case "deleteProject" :
        $errors = array();
        $oldGroups = $database->project_get_groupIDs($id);

        if (!checkGroupedObjectPermission('project', 'delete', $oldGroups, $oldGroups))
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];

        if (count($errors) == 0) {
            // If the confirmation is returned the project gets the trash-flag.
            $database->project_delete($id);
            break;
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors));
        break;

    case "deleteCustomer" :
        $errors = array();
        $oldGroups = $database->customer_get_groupIDs($id);

        if (!checkGroupedObjectPermission('project', 'delete', $oldGroups, $oldGroups))
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];

        if (count($errors) == 0) {
            // If the confirmation is returned the customer gets the trash-flag.
            $database->customer_delete($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors));
        break;

    case "deleteActivity" :
        $errors = array();
        $oldGroups = $database->activity_get_groupIDs($id);

        if (!checkGroupedObjectPermission('activity', 'delete', $oldGroups, $oldGroups))
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];

        if (count($errors) == 0) {
            // If the confirmation is returned the activity gets the trash-flag.
            $database->activity_delete($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors));
        break;

    case "banUser" :
        // Ban a user from login
        $sts['active'] = 0;
        $database->user_edit($id, $sts);
        echo $view->icons('locked');
        break;

    case "unbanUser" :
        // Unban a user from login
        $sts['active'] = 1;
        $database->user_edit($id, $sts);
        echo $view->icons('unlocked');
        break;

    case "sendEditUser" :

        // process editUser form
        $userData['name'] = trim($_REQUEST['name']);
        $userData['mail'] = $_REQUEST['mail'];
        $userData['alias'] = $_REQUEST['alias'];
        $userData['globalRoleID'] = $_REQUEST['globalRoleID'];
        $userData['rate'] = str_replace($kga['conf']['decimalSeparator'], '.', $_REQUEST['rate']);
        // if password field is empty => password unchanged (not overwritten with "")
        if ($_REQUEST['password'] != "") {
            $userData['password'] = md5($kga['password_salt'] . $_REQUEST['password'] . $kga['password_salt']);
        }

        $oldGroups = $database->getGroupMemberships($id);

        // validate data
        $errorMessages = array();

        if ($database->customer_nameToID($userData['name']) !== false)
            $errorMessages['name'] = $kga['lang']['errorMessages']['customerWithSameName'];

        $assignedGroups = isset($_REQUEST['assignedGroups']) ? $_REQUEST['assignedGroups'] : array();
        $membershipRoles = isset($_REQUEST['membershipRoles']) ? $_REQUEST['membershipRoles'] : array();


        if (!checkGroupedObjectPermission('user', 'edit', $oldGroups, $assignedGroups))
            $errorMessages[''] = $kga['lang']['errorMessages']['permissionDenied'];

        if (count($errorMessages) == 0) {
            $database->user_edit($id, $userData);
            $groups = array_combine($assignedGroups, $membershipRoles);
            $database->setGroupMemberships($id, $groups);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errorMessages));
        break;

    case "sendEditGroup" :
        // process editGroup form
        $group['name'] = trim($_REQUEST['name']);

        $errors = array();

        if (!checkGroupedObjectPermission('group', 'edit', array($id), array($id)))
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];

        if (count($errors) == 0) {
            $database->group_edit($id, $group);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors));
        break;

    case "sendEditStatus" :
        // process editStatus form
        $status_data['status'] = trim($_REQUEST['status']);

        $errors = array();

        if (!isset($kga['user']) || !$database->global_role_allows($kga['user']['globalRoleID'], 'core-status-edit'))
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];

        if (count($errors) == 0) {
            $database->status_edit($id, $status_data);
            $database->configuration_edit(array('defaultStatusID' => $id));
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors));
        break;

    case "sendEditAdvanced" :
        $errors = array();
        if (!isset($kga['user']) || !$database->global_role_allows($kga['user']['globalRoleID'], 'adminPanel_extension-editAdvanced'))
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];

        if (count($errors) == 0) {
            // process AdvancedOptions form
            $config_data['adminmail'] = $_REQUEST['adminmail'];
            $config_data['loginTries'] = $_REQUEST['logintries'];
            $config_data['loginBanTime'] = $_REQUEST['loginbantime'];
            $config_data['show_sensible_data'] = isset($_REQUEST['show_sensible_data']);
            $config_data['show_update_warn'] = isset($_REQUEST['show_update_warn']);
            $config_data['check_at_startup'] = isset($_REQUEST['check_at_startup']);
            $config_data['show_daySeperatorLines'] = isset($_REQUEST['show_daySeperatorLines']);
            $config_data['show_gabBreaks'] = isset($_REQUEST['show_gabBreaks']);
            $config_data['show_RecordAgain'] = isset($_REQUEST['show_RecordAgain']);
            $config_data['show_TrackingNr'] = isset($_REQUEST['show_TrackingNr']);
            $config_data['currency_name'] = $_REQUEST['currency_name'];
            $config_data['currency_sign'] = $_REQUEST['currency_sign'];
            $config_data['currency_first'] = isset($_REQUEST['currency_first']);
            $config_data['date_format_0'] = $_REQUEST['date_format_0'];
            $config_data['date_format_1'] = $_REQUEST['date_format_1'];
            $config_data['date_format_2'] = $_REQUEST['date_format_2'];
            $config_data['language'] = $_REQUEST['language'];
            if (isset($_REQUEST['status']) && is_array($_REQUEST['status'])) {
                $config_data['status'] = implode(',', $_REQUEST['status']);
            }
            $config_data['roundPrecision'] = $_REQUEST['roundPrecision'];
            $config_data['allowRoundDown'] = isset($_REQUEST['allowRoundDown']);
            $config_data['roundMinutes'] = $_REQUEST['roundMinutes'];
            $config_data['roundSeconds'] = $_REQUEST['roundSeconds'];
            $config_data['roundTimesheetEntries'] = $_REQUEST['roundTimesheetEntries'];
            $config_data['decimalSeparator'] = $_REQUEST['decimalSeparator'];
            $config_data['durationWithSeconds'] = isset($_REQUEST['durationWithSeconds']);
            $config_data['exactSums'] = isset($_REQUEST['exactSums']);
            $editLimit = false;
            if (isset($_REQUEST['editLimitEnabled'])) {
                $hours = (int)$_REQUEST['editLimitHours'];
                $days = (int)$_REQUEST['editLimitDays'];
                $editLimit = $hours + $days * 24;
                $editLimit *= 60 * 60; // convert to seconds
            }
            if ($editLimit === false || $editLimit === 0)
                $config_data['editLimit'] = '-';
            else
                $config_data['editLimit'] = $editLimit;

            if (!$database->configuration_edit($config_data))
                $errors[''] = $kga['lang']['error'];
        }

        if (count($errors) == 0) {
            write_config_file(
                $kga['server_database'],
                $kga['server_hostname'],
                $kga['server_username'],
                $kga['server_password'],
                $kga['server_conn'],
                $kga['server_type'],
                $kga['server_prefix'],
                $kga['language'],
                $kga['password_salt'],
                $_REQUEST['defaultTimezone']);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors));
        break;

    case "toggleDeletedUsers" :
        setcookie("adminPanel_extension_show_deleted_users", $axValue);
        break;

    case "createGlobalRole":
        $role_data['name'] = trim($axValue);

        $errors = array();

        if (!isset($kga['user']))
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];

        else if ($database->globalRole_find($role_data))
            $errors[] = $kga['lang']['errorMessages']['sameGlobalRoleName'];

        if (count($errors) == 0) {
            // create new status
            $database->global_role_create($role_data);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors));
        break;

    case "createMembershipRole":
        $role_data['name'] = trim($axValue);

        $errors = array();

        if (!isset($kga['user']))
            $errors[] = $kga['lang']['errorMessages']['permissionDenied'];

        if ($database->membershipRole_find($role_data))
            $errors[] = $kga['lang']['errorMessages']['sameMembershipRoleName'];

        if (count($errors) == 0) {
            // create new status
            $database->membership_role_create($role_data);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors));
        break;

    case "editGlobalRole":
        $id = $_REQUEST['id'];
        $newData = $_REQUEST;
        unset($newData['id']);
        unset($newData['axAction']);

        $roleData = $database->globalRole_get_data($id);

        foreach ($roleData as $key => &$value) {
            if (isset($newData[$key]))
                $value = $newData[$key];
            else if ($key != "globalRoleID" && $key != "name")
                $value = 0;
        }

        $errors = array();

        if (!isset($kga['user']))
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];

        if (count($errors) == 0) {
            $database->global_role_edit($id, $roleData);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors));
        break;

    case "editMembershipRole":
        $id = $_REQUEST['id'];
        $newData = $_REQUEST;
        unset($newData['id']);
        unset($newData['axAction']);

        $roleData = $database->membershipRole_get_data($id);

        foreach ($roleData as $key => &$value) {
            if (isset($newData[$key]))
                $value = $newData[$key];
            else if ($key != "membershipRoleID" && $key != "name")
                $value = 0;
        }

        $errors = array();

        if (!isset($kga['user']))
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];

        if (count($errors) == 0) {
            $database->membership_role_edit($id, $roleData);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors));
        break;

    case "deleteGlobalRole":
        $errors = array();

        if (!isset($kga['user']))
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];

        if (count($errors) == 0) {
            $database->global_role_delete($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors));
        break;

    case "deleteMembershipRole":
        $errors = array();

        if (!isset($kga['user']))
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];

        if (count($errors) == 0) {
            $database->membership_role_delete($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
            'errors' => $errors));
        break;
}
