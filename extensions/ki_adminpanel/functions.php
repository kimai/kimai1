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

/**
 * @param Kimai_Database_Mysql $database
 * @param array $kgaUser
 * @param bool $viewOtherGroupsAllowed
 * @return array
 */
function getGroupsData(Kimai_Database_Mysql $database, $kgaUser, $viewOtherGroupsAllowed)
{
    $groups = $database->get_groups();
    $allowedGroups = $groups;
    if (!$viewOtherGroupsAllowed) {
        $allowedGroups = array_filter(
            $groups,
            function ($group) use ($kgaUser) {
                return array_search($group['groupID'], $kgaUser['groups']) !== false;
            }
        );
    }

    return array(
        'groups' => $allowedGroups
    );
}

/**
 * @param Kimai_Database_Mysql $database
 * @param array $kgaUser
 * @param bool $viewOtherGroupsAllowed
 * @return array
 */
function getProjectsData(Kimai_Database_Mysql $database, $kgaUser, $viewOtherGroupsAllowed)
{
    if ($database->global_role_allows($kgaUser['globalRoleID'], 'core-project-otherGroup-view')) {
        $projects = $database->get_projects();
    } else {
        $projects = $database->get_projects($kgaUser['groups']);
    }

    $result = array();

    if ($projects !== null && is_array($projects)) {
        foreach ($projects as $row => $project) {
            $groupNames = array();
            foreach ($database->project_get_groupIDs($project['projectID']) as $groupID) {
                if (!$viewOtherGroupsAllowed && array_search($groupID, $kgaUser['groups']) === false) {
                    continue;
                }
                $data = $database->group_get_data($groupID);
                $groupNames[] = $data['name'];
            }
            $projects[$row]['groups'] = implode(", ", $groupNames);
        }
        $result['projects'] = $projects;
    }

    return $result;
}

/**
 * @param Kimai_Database_Mysql $database
 * @param array $kgaUser
 * @param bool $viewOtherGroupsAllowed
 * @return array
 */
function getCustomersData(Kimai_Database_Mysql $database, $kgaUser, $viewOtherGroupsAllowed)
{
    if ($database->global_role_allows($kgaUser['globalRoleID'], 'core-customer-otherGroup-view')) {
        $customers = $database->get_customers();
    } else {
        $customers = $database->get_customers($kgaUser['groups']);
    }

    foreach ($customers as $row => $data) {
        $groupNames = array();
        $groups = $database->customer_get_groupIDs($data['customerID']);
        if ($groups !== false) {
            foreach ($groups as $groupID) {
                if (!$viewOtherGroupsAllowed && array_search($groupID, $kgaUser['groups']) === false) {
                    continue;
                }
                $data = $database->group_get_data($groupID);
                $groupNames[] = $data['name'];
            }
            $customers[$row]['groups'] = implode(", ", $groupNames);
        }
    }

    return array(
        'customers' => $customers
    );
}

/**
 * @param Kimai_Database_Mysql $database
 * @param array $kgaUser
 * @param bool $viewOtherGroupsAllowed
 * @return array
 */
function getUsersData(Kimai_Database_Mysql $database, $kgaUser, $viewOtherGroupsAllowed)
{
    $result = array(
        'showDeletedUsers' => get_cookie('adminPanel_extension_show_deleted_users', 0),
        'curr_user' => $kgaUser['name'],
        'users' => getEditUserList($database, $kgaUser, $viewOtherGroupsAllowed)
    );
    return $result;
}

/**
 * @param Kimai_Database_Mysql $database
 * @param array $kgaUser
 * @param bool $viewOtherGroupsAllowed
 * @return array
 * @throws Zend_View_Exception
 */
function getActivitiesData(Kimai_Database_Mysql $database, $kgaUser, $viewOtherGroupsAllowed)
{
    $groups = null;
    if (!$database->global_role_allows($kgaUser['globalRoleID'], 'core-activity-otherGroup-view')) {
        $groups = $kgaUser['groups'];
    }

    $activity_filter = isset($_REQUEST['activity_filter']) ? intval($_REQUEST['activity_filter']) : -2;

    switch ($activity_filter) {
        case -2:
            // -2 is not a valid project id this will give us all unassigned activities.
            $activities = $database->get_activities_by_project(-2, $groups);
            break;
        case -1:
            $activities = $database->get_activities($groups);
            break;
        default:
            $activities = $database->get_activities_by_project($activity_filter, $groups);
    }

    foreach ($activities as $row => $activity) {
        $groupNames = array();
        foreach ($database->activity_get_groups($activity['activityID']) as $groupID) {
            if (!$viewOtherGroupsAllowed && array_search($groupID, $kgaUser['groups']) === false) {
                continue;
            }
            $data = $database->group_get_data($groupID);
            $groupNames[] = $data['name'];
        }
        $activities[$row]['groups'] = implode(", ", $groupNames);
        $activities[$row]['projects'] = $database->activity_get_projects($activity['activityID']) ?: array();
    }

    $result = array();
    if (count($activities) > 0) {
        $result['activities'] = $activities;
    } else {
        $result['activities'] = array();
    }
    $result['projects'] = $database->get_projects($groups);
    $result['selected_activity_filter'] = $activity_filter;

    return $result;
}

/**
 * @param Kimai_Database_Mysql $database
 * @param array $kgaUser
 * @param bool $viewOtherGroupsAllowed
 * @return array
 */
function getEditUserList(Kimai_Database_Mysql $database, $kgaUser, $viewOtherGroupsAllowed)
{
    $users = array();
    $showDeletedUsers = get_cookie('adminPanel_extension_show_deleted_users', 0);

    if ($database->global_role_allows($kgaUser['globalRoleID'], 'core-user-otherGroup-view')) {
        $dbUsers = $database->get_users($showDeletedUsers);
    } else {
        $dbUsers = $database->get_users($showDeletedUsers, $kgaUser['groups']);
    }

    $roles = $database->global_roles();

    foreach ($dbUsers as $user)
    {
        $user['globalRoleName'] = 'Unknown ('.$user['globalRoleID'].')';

        foreach($roles as $role) {
            if ($role['globalRoleID'] == $user['globalRoleID']) {
                $user['globalRoleName'] = $role['name'];
                break;
            }
        }

        $user['groups'] = array();

        $groups = $database->getGroupMemberships($user['userID']);
        if (is_array($groups)) {
            foreach ($groups as $group) {
                if (!$viewOtherGroupsAllowed && array_search($group, $kgaUser['groups']) === false) {
                    continue;
                }
                $groupData = $database->group_get_data($group);
                $user['groups'][] = $groupData['name'];
            }
        }

        $users[] = $user;
    }

    return $users;
}