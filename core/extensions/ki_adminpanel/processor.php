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
 * along with Kimai; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 */

// ================
// = AP PROCESSOR =
// ================

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require("../../includes/kspi.php");

// logfile("AP: processor triggered");   
// logfile("AP: axAction: " . $axAction);     

switch ($axAction) {
    
    case "createUsr":
    // create new user account
        $randomUsrID = random_number(9);
        $usr_data['usr_ID'] = $randomUsrID;
    	$usr_data['usr_name'] = $axValue;
    	$usr_data['usr_grp'] = 1;
    	$usr_data['usr_sts'] = 2;
    	$usr_data['usr_active'] = 0;
    	$usr_data['usr_mail'] = "";
    	$usr_data['pw'] = "";
    	$usr_data['rowlimit'] = 100;
    	$usr_data['skin'] = "standard";
    	usr_create($usr_data);
		echo $randomUsrID;
    break;
    
    case "createGrp":
    // create new group
		$grp_data['grp_name'] = $axValue;
		$new_grp_id = grp_create($grp_data);
		if ($new_grp_id != false) {
			assign_grp2ldrs($new_grp_id, array($kga['usr']['usr_ID']));
		}
    break;
    
    case "refreshSubtab":
    // builds either user/group/advanced/DB subtab
        $tpl->assign('curr_user', $kga['usr']['usr_name']);
        $tpl->assign('arr_grp',  get_arr_grp(get_cookie('ap_ext_show_deleted_groups',0)));
        $tpl->assign('arr_usr',  get_arr_usr(get_cookie('ap_ext_show_deleted_users',0)));
        $tpl->assign('showDeletedGroups', get_cookie('ap_ext_show_deleted_groups',0));
        $tpl->assign('showDeletedUsers', get_cookie('ap_ext_show_deleted_users',0));
        switch ($axValue) {
            case "usr": $tpl->display("users.tpl");    break;
            case "grp": $tpl->display("groups.tpl");   break;
            case "adv": $tpl->display("advanced.tpl"); break;
            case "db":  $tpl->display("database.tpl"); break;
            
            case "knd":
                $arr_knd = get_arr_knd("all");
                if (count($arr_knd)>0) {
                $tpl->assign('arr_knd', $arr_knd);
                } else {
                $tpl->assign('arr_knd', '0');
                }
                $tpl->display("knd.tpl");      
                break;
                
            case "pct":
                $arr_pct = get_arr_pct("all");
                if (count($arr_pct)>0) {
                $tpl->assign('arr_pct', $arr_pct);
                } else {
                $tpl->assign('arr_pct', '0');
                }
                $tpl->display("pct.tpl");      
                break;
                
            case "evt": 
                $arr_evt = get_arr_evt("all");
                if (count($arr_evt)>0) {
                $tpl->assign('arr_evt', $arr_evt);
                } else {
                $tpl->assign('arr_evt', '0');
                }
                $tpl->display("evt.tpl");
                break;
        }
    break;

    case "deleteUsr":
    // set the trashflag of a user
        switch ($axValue) {
            case 0:
            // Fire JavaScript confirm when a user is about to be deleted
            echo $kga['lang']['sure'];
        break;
            case 1: 
            // If the confirmation is returned the user gets the trash-flag. 
            // TODO: Users with trashflag can be deleted by 'empty trashcan' or so ...
            usr_delete($id);
        break;
        }
    break;

    case "deleteGrp":
    // set the trashflag of a group
        switch ($axValue) {
            case 0:
            // Fire JavaScript confirm when a group is about to be deleted
            echo $kga['lang']['sure'];
        break;
            case 1: 
            // If the confirmation is returned the group gets the trash-flag. 
            // TODO: Users with trashflag can be deleted by 'empty trashcan' or so ...
            grp_delete($id);
        break;
        }
    break;

    case "banUsr":
    // Ban a user from login
    $sts['usr_active'] = 0;
    usr_edit($id, $sts);
    echo sprintf("<img border='0' title='%s' alt='%s' src='../skins/%s/grfx/lock.png' width='16' height='16' />",
                  $kga['lang']['bannedusr'], $kga['lang']['bannedusr'], $kga['conf']['skin']);
    break;
    
    case "unbanUsr":
    // Unban a user from login
    $sts['usr_active'] = 1;
    usr_edit($id, $sts);
    echo sprintf("<img border='0' title='%s' alt='%s' src='../skins/%s/grfx/jipp.gif' width='16' height='16' />",
                  $kga['lang']['activeusr'], $kga['lang']['activeusr'], $kga['conf']['skin']);
    break;
    
    case "sendEditUsr":
    // process editUsr form
        $usr_data['usr_name']  = $_REQUEST['usr_name'];
        $usr_data['usr_grp']   = $_REQUEST['usr_grp'];
        $usr_data['usr_sts']   = $_REQUEST['usr_sts'];
        $usr_data['usr_mail']  = $_REQUEST['usr_mail'];
        $usr_data['usr_alias'] = $_REQUEST['usr_alias'];
        $usr_data['usr_rate']  = $_REQUEST['usr_rate'];
        
        // if password field is empty => password unchanged (not overwritten with "")
        if ($_REQUEST['usr_pw'] != "") {
        	$usr_data['pw'] = crypt($_REQUEST['usr_pw'], $kga['cryptmethod']);
        }
        usr_edit($id, $usr_data); 
    break;
    
    case "sendEditGrp":
    // process editGrp form
        $grp_data['grp_name'] = $_REQUEST['grp_name'];
        grp_edit($id, $grp_data);
        
        $ldrs = $_REQUEST['grp_leader'];
        assign_grp2ldrs($id, $ldrs);
        
    break;
    
    case "sendEditAdvanced":
    // process AdvancedOptions form
    
        $var_data['adminmail']    = $_REQUEST['adminmail'];
        $var_data['loginTries']   = $_REQUEST['logintries'];
        $var_data['loginBanTime'] = $_REQUEST['loginbantime'];
        $var_data['charset']      = $_REQUEST['charset'];
        
        var_edit($var_data);

        // do whatever you like
        // and return one of these:
        
        $return = $kga['lang']['error'];   // on error
        $return = $kga['lang']['updated']; // on success;
        
        echo $return;
    break;
    

    case "toggleDeletedUsers":
        setcookie("ap_ext_show_deleted_users",$axValue); 
    break;

}

?>
