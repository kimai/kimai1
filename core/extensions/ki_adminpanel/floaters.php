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

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates";
require("../../includes/kspi.php");

switch ($axAction) {

    case "editUser":
    // =============================
    // = Builds edit-user dialogue =
    // =============================

        $userDetails = $database->user_get_data($id);

        $userDetails['rate'] = $database->get_rate($userDetails['userID'],NULL,NULL);
        
        $view->selectedGroups = $database->getGroupMemberships($id);
        
        $view->groups = makeSelectBox('group',null,null,true);
                    
        $view->user_details = $userDetails;
        echo $view->render("floaters/edituser.php");  
        
    break;

    case "editGroup":    
    // =============================
    // = Builds edit-group dialogue =
    // =============================
        
        $groupDetails = $database->group_get_data($_REQUEST['id']);
                
        $selectedUsers = $database->group_get_groupleaders($_REQUEST['id']);
        $view->selectedUsers = $selectedUsers;
                      
        $view->users = makeSelectBox('user',null,null,true);
        
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

}

?>