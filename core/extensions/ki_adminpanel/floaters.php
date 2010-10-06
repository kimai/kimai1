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

        $usr_details = get_usr($id);        
        $arr = get_arr_grp();
        
        $i=0;
        foreach ($arr as $row) {
            $arr_grp_name[$i] = $row['grp_name'];
            $arr_grp_ID[$i]   = $row['grp_ID'];
            $i++;
        }
        
        $tpl->assign('arr_grp_ID',   $arr_grp_ID);
        $tpl->assign('arr_grp_name', $arr_grp_name);
                    
        $tpl->assign('usr_details', $usr_details);
        $tpl->display("edituser.tpl");  
        
    break;

    case "editGrp":    
    // =============================
    // = Builds edit-group dialogue =
    // =============================
        
        $grp_details = grp_get_data($_REQUEST['id']);
        $arr = get_arr_usr();
        
        $i=0;
        foreach ($arr as $row) {
            $arr_usr_name[$i] = $row['usr_name'];
            $arr_usr_ID[$i]   = $row['usr_ID'];
            $i++;
        }
                
        $grp_selection=grp_get_ldrs($_REQUEST['id']);
        $tpl->assign('grp_selection', $grp_selection);
                      
        $tpl->assign('arr_usr_ID',   $arr_usr_ID);
        $tpl->assign('arr_usr_name', $arr_usr_name);
        
        $tpl->assign('grp_details', $grp_details);
        $tpl->display("editgroup.tpl"); 
        
    break;        

}

?>