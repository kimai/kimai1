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

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates";
require '../../includes/kspi.php';

$settings = parse_ini_file("config.ini");

$view = new Kimai_View();
$view->addBasePath(__DIR__ . '/templates/');

require 'functions.php';

switch ($axAction) {

    // =============================
    // = Builds edit-user dialogue =
    // =============================
    case "editUser":

        $userDetails = $database->user_get_data($id);

        $userDetails['rate'] = $database->get_rate($userDetails['userID'], NULL, NULL);

        $view->globalRoles = array();
        foreach ($database->global_roles() as $role) {
            $view->globalRoles[$role['globalRoleID']] = $role['name'];
        }

        $view->memberships = array();
        foreach ($database->getGroupMemberships($id) as $groupId) {
            $view->memberships[$groupId] = $database->user_get_membership_role($id, $groupId);
        }

        $groups = $database->get_groups(get_cookie('adminPanel_extension_show_deleted_groups', 0));
        if ($database->global_role_allows($kga['user']['globalRoleID'], 'core-group-otherGroup-view')) {
            $view->groups = $groups;
        } else {
            $view->groups = array_filter(
                $groups,
                function ($group) {
                    global $kga;
                    return array_search($group['groupID'], $kga['user']['groups']) !== false;
                }
            );
        }

        $view->membershipRoles = array();
        foreach ($database->membership_roles() as $role) {
            $view->membershipRoles[$role['membershipRoleID']] = $role['name'];
        }

        $view->user_details = $userDetails;
        echo $view->render("floaters/edituser.php");

        break;

    // =============================
    // = Builds edit-group dialogue =
    // =============================
    case "editGroup":

        $groupDetails = $database->group_get_data($_REQUEST['id']);

        $view->users = makeSelectBox('sameGroupUser', null, null, true);
        $view->group_details = $groupDetails;

        echo $view->render("floaters/editgroup.php");

        break;

    // =============================
    // = Builds edit-status dialogue =
    // =============================
    case "editStatus":

        $statusDetails = $database->status_get_data($_REQUEST['id']);

        $view->status_details = $statusDetails;

        echo $view->render("floaters/editstatus.php");

        break;

    // =============================
    // = Builds edit-group dialogue =
    // =============================
    case "editGlobalRole":

        $globalRoleDetails = $database->globalRole_get_data($_REQUEST['id']);

        $view->id = $globalRoleDetails['globalRoleID'];
        $view->name = $globalRoleDetails['name'];
        $view->action = 'editGlobalRole';
        $view->reloadSubtab = 'globalRoles';
        $view->title = $kga['lang']['editGlobalRole'];
        $view->permissions = $globalRoleDetails;
        unset($view->permissions['globalRoleID']);
        unset($view->permissions['name']);

        echo $view->render("floaters/editglobalrole.php");

        break;

    // =============================
    // = Builds edit-group dialogue =
    // =============================
    case "editMembershipRole":

        $membershipRoleDetails = $database->membershipRole_get_data($_REQUEST['id']);

        $view->id = $membershipRoleDetails['membershipRoleID'];
        $view->name = $membershipRoleDetails['name'];
        $view->action = 'editMembershipRole';
        $view->reloadSubtab = 'membershipRoles';
        $view->title = $kga['lang']['editMembershipRole'];
        $view->permissions = $membershipRoleDetails;
        unset($view->permissions['membershipRoleID']);
        unset($view->permissions['name']);

        echo $view->render("floaters/editglobalrole.php");

        break;

}
