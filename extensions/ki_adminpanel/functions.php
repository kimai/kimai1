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

function getEditUserList(Kimai_Database_Mysql $database, $kgaUser, $viewOtherGroups)
{
    $users = array();

    if ($database->global_role_allows($kgaUser['globalRoleID'], 'core-user-otherGroup-view')) {
        $dbUsers = $database->get_users(get_cookie('adminPanel_extension_show_deleted_users', 0));
    } else {
        $dbUsers = $database->get_users(get_cookie('adminPanel_extension_show_deleted_users', 0), $kgaUser['groups']);
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
                if (!$viewOtherGroups && array_search($group, $kgaUser['groups']) === false) {
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