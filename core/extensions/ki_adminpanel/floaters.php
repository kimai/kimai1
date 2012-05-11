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

    case "editUsr":
    // =============================
    // = Builds edit-user dialogue =
    // =============================

        $usr_details = $database->user_get_data($id);        
        $arr = $database->get_arr_groups();
        
        $i=0;
        foreach ($arr as $row) {
            $arr_grp_name[$i] = $row['name'];
            $arr_grp_ID[$i]   = $row['groupID'];
            $i++;
        }
        
        $tpl->assign('selectedGroups',$database->getGroupMemberships($id));
        
        $tpl->assign('groupIDs',   $arr_grp_ID);
        $tpl->assign('groupNames', $arr_grp_name);
                    
        $tpl->assign('user_details', $usr_details);
        $tpl->display("edituser.tpl");  
        
    break;

    case "editGrp":    
    // =============================
    // = Builds edit-group dialogue =
    // =============================
        
        $grp_details = $database->group_get_data($_REQUEST['id']);
        $arr = $database->get_arr_users();
        
        $i=0;
        foreach ($arr as $row) {
            $arr_usr_name[$i] = $row['name'];
            $arr_usr_ID[$i]   = $row['userID'];
            $i++;
        }
                
        $selectedUsers = $database->group_get_groupleaders($_REQUEST['id']);
        $tpl->assign('selectedUsers', $selectedUsers);
                      
        $tpl->assign('userIDs',   $arr_usr_ID);
        $tpl->assign('userNames', $arr_usr_name);
        
        $tpl->assign('group_details', $grp_details);
        $tpl->display("editgroup.tpl"); 
        
    break;     
    
    case "editStatus":    
    // =============================
    // = Builds edit-status dialogue =
    // =============================
        
        $status_details = $database->status_get_data($_REQUEST['id']);
        
        $tpl->assign('status_details', $status_details);
        $tpl->display("editstatus.tpl"); 
        
    break;       

}

?>