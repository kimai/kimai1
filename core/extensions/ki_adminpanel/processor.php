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
    	$usr_data['usr_name'] = htmlspecialchars(trim($axValue));
    	$usr_data['usr_grp'] = 1;
    	$usr_data['usr_sts'] = 2;
    	$usr_data['usr_active'] = 0;
    	$usr_data['usr_mail'] = "";
    	$usr_data['pw'] = "";
    	$usr_data['rowlimit'] = 100;
    	$usr_data['skin'] = "standard";
    	echo usr_create($usr_data);
    break;
    
    case "createGrp":
    // create new group
		$grp_data['grp_name'] = htmlspecialchars(trim($axValue));
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

    case "deletePct":
    // set the trashflag of a project
        switch ($axValue) {
            case 0:
            // Fire JavaScript confirm when a project is about to be deleted
            echo $kga['lang']['sure'];
        break;
            case 1: 
            // If the confirmation is returned the project gets the trash-flag. 
            pct_delete($id);
        break;
        }
    break;

    case "deleteKnd":
    // set the trashflag of a customer
        switch ($axValue) {
            case 0:
            // Fire JavaScript confirm when a customer is about to be deleted
            echo $kga['lang']['sure'];
        break;
            case 1: 
            // If the confirmation is returned the customer gets the trash-flag. 
            knd_delete($id);
        break;
        }
    break;

    case "deleteEvt":
    // set the trashflag of an event
        switch ($axValue) {
            case 0:
            // Fire JavaScript confirm when an event is about to be deleted
            echo $kga['lang']['sure'];
        break;
            case 1: 
            // If the confirmation is returned the event gets the trash-flag. 
            evt_delete($id);
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
        $usr_data['usr_name']  = htmlspecialchars(trim($_REQUEST['usr_name']));
        $usr_data['usr_grp']   = $_REQUEST['usr_grp'];
        $usr_data['usr_sts']   = $_REQUEST['usr_sts'];
        $usr_data['usr_mail']  = $_REQUEST['usr_mail'];
        $usr_data['usr_alias'] = $_REQUEST['usr_alias'];
        $usr_data['usr_rate']  = $_REQUEST['usr_rate'];
        
        // if password field is empty => password unchanged (not overwritten with "")
        if ($_REQUEST['usr_pw'] != "") {
        	$usr_data['pw'] = md5($kga['password_salt'].$_REQUEST['usr_pw'].$kga['password_salt']);
        }
        usr_edit($id, $usr_data); 
    break;
    
    case "sendEditGrp":
    // process editGrp form
        $grp_data['grp_name'] = htmlspecialchars(trim($_REQUEST['grp_name']));
        grp_edit($id, $grp_data);
        
        $ldrs = $_REQUEST['grp_leader'];
        assign_grp2ldrs($id, $ldrs);
        
    break;
    
    case "sendEditAdvanced":
    // process AdvancedOptions form
    
        $var_data['adminmail']              = $_REQUEST['adminmail'];
        $var_data['loginTries']             = $_REQUEST['logintries'];
        $var_data['loginBanTime']           = $_REQUEST['loginbantime'];
        $var_data['show_sensible_data']     = $_REQUEST['show_sensible_data']==1?1:0;
        $var_data['show_update_warn']       = $_REQUEST['show_update_warn']==1?1:0;
        $var_data['check_at_startup']       = $_REQUEST['check_at_startup']==1?1:0;
        $var_data['show_daySeperatorLines'] = $_REQUEST['show_daySeperatorLines']==1?1:0;
        $var_data['show_gabBreaks']         = $_REQUEST['show_gabBreaks']==1?1:0;
        $var_data['show_RecordAgain']       = $_REQUEST['show_RecordAgain']==1?1:0;
        $var_data['show_TrackingNr']        = $_REQUEST['show_TrackingNr']==1?1:0;
        $var_data['currency_name']          = $_REQUEST['currency_name'];
        $var_data['currency_sign']          = $_REQUEST['currency_sign'];
        $var_data['date_format_0']          = $_REQUEST['date_format_0'];
        $var_data['date_format_1']          = $_REQUEST['date_format_1'];
        $var_data['date_format_2']          = $_REQUEST['date_format_2'];
        $var_data['language']               = $_REQUEST['language'];
        
        $success = var_edit($var_data);

        // do whatever you like
        // and return one of these:
        
        echo $success?$kga['lang']['updated']:$kga['lang']['error'];
    break;
    

    case "toggleDeletedUsers":
        setcookie("ap_ext_show_deleted_users",$axValue); 
    break;

}

?>
