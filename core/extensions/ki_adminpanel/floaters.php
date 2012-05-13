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
$dir_templates = "templates/floaters/";
require("../../includes/kspi.php");

switch ($axAction) {

    case "editUser":
    // =============================
    // = Builds edit-user dialogue =
    // =============================

        $userDetails = $database->user_get_data($id);        
        $arr = $database->get_groups();
        
        $i=0;
        foreach ($arr as $row) {
            $groupNames[$i] = $row['name'];
            $groupIDs[$i]   = $row['groupID'];
            $i++;
        }
        
        $tpl->assign('selectedGroups',$database->getGroupMemberships($id));
        
        $tpl->assign('groupIDs',   $groupIDs);
        $tpl->assign('groupNames', $groupNames);
                    
        $tpl->assign('user_details', $userDetails);
        $tpl->display("edituser.tpl");  
        
    break;

    case "editGroup":    
    // =============================
    // = Builds edit-group dialogue =
    // =============================
        
        $groupDetails = $database->group_get_data($_REQUEST['id']);
        $arr = $database->get_users();
        
        $i=0;
        foreach ($arr as $row) {
            $userNames[$i] = $row['name'];
            $userIDs[$i]   = $row['userID'];
            $i++;
        }
                
        $selectedUsers = $database->group_get_groupleaders($_REQUEST['id']);
        $tpl->assign('selectedUsers', $selectedUsers);
                      
        $tpl->assign('userIDs',   $userIDs);
        $tpl->assign('userNames', $userNames);
        
        $tpl->assign('group_details', $groupDetails);
        $tpl->display("editgroup.tpl"); 
        
    break;     
    
    case "editStatus":    
    // =============================
    // = Builds edit-status dialogue =
    // =============================
        
        $statusDetails = $database->status_get_data($_REQUEST['id']);
        
        $tpl->assign('status_details', $statusDetails);
        $tpl->display("editstatus.tpl"); 
        
    break;       

}

?>