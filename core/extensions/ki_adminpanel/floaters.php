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

require("../../includes/kspi.php");
$view->addBasePath(dirname(__FILE__).'/templates/');

switch ($axAction) {

    case "editUser":
        // =============================
        // = Builds edit-user dialogue =
        // =============================

        $userDetails = $database->user_get_data($id);

        $userDetails['rate'] = $database->get_rate($userDetails['userID'],NULL,NULL);
        
        $view->globalRoles = array();
        foreach ($database->global_roles() as $role) {
          $view->globalRoles[$role['globalRoleID']] = $role['name'];
        }
        
        $view->memberships = array();
        foreach ($database->getGroupMemberships($id) as $groupId) {
          $view->memberships[$groupId] = $database->user_get_membership_role($id, $groupId);
        }

        $view->groups = $database->get_groups();        
        $view->membershipRoles = array();
        foreach ($database->membership_roles() as $role)
          $view->membershipRoles[$role['membershipRoleID']] = $role['name'];

        $view->user_details = $userDetails;
        echo $view->render("floaters/edituser.php");  
        
    break;

    case "editGroup":    
        // =============================
        // = Builds edit-group dialogue =
        // =============================
        
        $groupDetails = $database->group_get_data($_REQUEST['id']);
                      
        $view->users = makeSelectBox('sameGroupUser',null,null,true);
        
        $view->group_details = $groupDetails;
        echo $view->render("floaters/editgroup.php"); 
        
    break;     
    
    case "editStatus":    
        // =============================
        // = Builds edit-status dialogue =
        // =============================
        
        $statusDetails = $database->status_get_data($_REQUEST['id']);
        
        $view->status_details = $statusDetails;
        echo $view->render("floaters/editstatus.php"); 
        
    break;    

    case "editGlobalRole":    
        // =============================
        // = Builds edit-group dialogue =
        // =============================
        
        $globalRoleDetails = $database->globalRole_get_data($_REQUEST['id']);
        
        $view->id = $globalRoleDetails['globalRoleID'];
        $view->name = $globalRoleDetails['name'];
        $view->action = 'editGlobalRole';
        $view->reloadSubtab = 'globalRoles';
        $view->title =  $kga['lang']['editGlobalRole'];
        $view->permissions = $globalRoleDetails;
        unset($view->permissions['globalRoleID']);
        unset($view->permissions['name']);
        echo $view->render("floaters/editglobalrole.php"); 
        
    break;     

    case "editMembershipRole":    
        // =============================
        // = Builds edit-group dialogue =
        // =============================
        
        $membershipRoleDetails = $database->membershipRole_get_data($_REQUEST['id']);
        
        $view->id = $membershipRoleDetails['membershipRoleID'];
        $view->name = $membershipRoleDetails['name'];
        $view->action = 'editMembershipRole';
        $view->reloadSubtab = 'membershipRoles';
        $view->title =  $kga['lang']['editMembershipRole'];
        $view->permissions = $membershipRoleDetails;
        unset($view->permissions['membershipRoleID']);
        unset($view->permissions['name']);
        echo $view->render("floaters/editglobalrole.php"); 
        
    break;     

}
