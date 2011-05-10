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

// ================
// = AP PROCESSOR =
// ================

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require("../../includes/kspi.php");

switch ($axAction) {
    
    case "createUsr":
    // create new user account
    	$usr_data['usr_name'] = trim($axValue);
    	$usr_data['usr_grp'] = $kga['usr']['usr_grp'];
    	$usr_data['usr_sts'] = 2;
    	$usr_data['usr_active'] = 0;
    	echo usr_create($usr_data);
    break;
    
    case "createGrp":
    // create new group
		$grp_data['grp_name'] = trim($axValue);
		$new_grp_id = grp_create($grp_data);
		if ($new_grp_id != false) {
			assign_grp2ldrs($new_grp_id, array($kga['usr']['usr_ID']));
		}
    break;
    
    case "refreshSubtab":
    // builds either user/group/advanced/DB subtab
        $tpl->assign('curr_user', $kga['usr']['usr_name']);

        if ($kga['usr']['usr_sts']==0)
          $tpl->assign('arr_grp', get_arr_grp(get_cookie('ap_ext_show_deleted_groups',0)));
        else
          $tpl->assign('arr_grp', get_arr_grp_by_leader($kga['usr']['usr_ID'],
            get_cookie('ap_ext_show_deleted_groups',0)));

        if ($kga['usr']['usr_sts']==0)
          $tpl->assign('arr_usr',  get_arr_usr(get_cookie('ap_ext_show_deleted_users',0)));
        else
          $tpl->assign('arr_usr',get_arr_watchable_users($kga['usr']['usr_ID']));
        $tpl->assign('showDeletedGroups', get_cookie('ap_ext_show_deleted_groups',0));
        $tpl->assign('showDeletedUsers', get_cookie('ap_ext_show_deleted_users',0));
        switch ($axValue) {
            case "usr": $tpl->display("users.tpl");    break;
            case "grp": $tpl->display("groups.tpl");   break;
            case "adv": $tpl->display("advanced.tpl"); break;
            case "db":  $tpl->display("database.tpl"); break;
            
            case "knd":
		if ($kga['usr']['usr_sts']==0)
		  $arr_knd = get_arr_knd("all");
		else
		  $arr_knd = get_arr_knd($kga['usr']['usr_grp']);

    foreach ($arr_knd as $row=>$knd_data) {
      $grp_names = array();
      foreach (knd_get_grps($knd_data['knd_ID']) as $grp_id) {
        $data = grp_get_data($grp_id);
         $grp_names[] = $data['grp_name'];
      }
      $arr_knd[$row]['groups'] = implode(", ",$grp_names);
    }

                if (count($arr_knd)>0) {
                $tpl->assign('arr_knd', $arr_knd);
                } else {
                $tpl->assign('arr_knd', '0');
                }
                $tpl->display("knd.tpl");      
                break;
                
            case "pct":
		if ($kga['usr']['usr_sts']==0)
		  $arr_pct = get_arr_pct("all");
		else
		  $arr_pct = get_arr_pct($kga['usr']['usr_grp']);


    foreach ($arr_pct as $row=>$pct_data) {
      $grp_names = array();
      foreach (pct_get_grps($pct_data['pct_ID']) as $grp_id) {
        $data = grp_get_data($grp_id);
         $grp_names[] = $data['grp_name'];
      }
      $arr_pct[$row]['groups'] = implode(", ",$grp_names);
    }
                if (count($arr_pct)>0) {
                $tpl->assign('arr_pct', $arr_pct);
                } else {
                $tpl->assign('arr_pct', '0');
                }
                $tpl->display("pct.tpl");      
                break;
                
            case "evt": 
		if ($kga['usr']['usr_sts']==0)
		  $group = "all";
		else
		  $group = $kga['usr']['usr_grp'];
                if (!isset($_REQUEST['evt_filter']))
                  $arr_evt = get_arr_evt($group);
                else
                  switch ($_REQUEST['evt_filter']) {
                      case -1:
                      $arr_evt = get_arr_evt($group);
                      break;
                    case -2:
                      // -2 is to get unassigned events. As -2 is never
                      // an id of a project this will give us all unassigned
                      // events.
                    default:
                      $arr_evt = 
                        get_arr_evt_by_pct($group,$_REQUEST['evt_filter']);
                  }

                foreach ($arr_evt as $row=>$evt_data) {
                  $grp_names = array();
                  foreach (evt_get_grps($evt_data['evt_ID']) as $grp_id) {
                    $data = grp_get_data($grp_id);
                    $grp_names[] = $data['grp_name'];
                  }
                  $arr_evt[$row]['groups'] = implode(", ",$grp_names);
                }
                  
                if (count($arr_evt)>0) {
                $tpl->assign('arr_evt', $arr_evt);
                } else {
                $tpl->assign('arr_evt', '0');
                }
                
                
                $arr_pct = get_arr_pct($group);
                $tpl->assign('arr_pct', $arr_pct);
                
                $tpl->assign('selected_evt_filter',$_REQUEST['evt_filter']);
                
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
        $usr_data['usr_name']  = trim($_REQUEST['usr_name']);
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
        $grp_data['grp_name'] = trim($_REQUEST['grp_name']);
        grp_edit($id, $grp_data);
        
        $ldrs = $_REQUEST['grp_leader'];
        assign_grp2ldrs($id, $ldrs);
        
    break;
    
    case "sendEditAdvanced":
    // process AdvancedOptions form
    
        $var_data['adminmail']              = $_REQUEST['adminmail'];
        $var_data['loginTries']             = $_REQUEST['logintries'];
        $var_data['loginBanTime']           = $_REQUEST['loginbantime'];
        $var_data['show_sensible_data']     = isset($_REQUEST['show_sensible_data']);
        $var_data['show_update_warn']       = isset($_REQUEST['show_update_warn']);
        $var_data['check_at_startup']       = isset($_REQUEST['check_at_startup']);
        $var_data['show_daySeperatorLines'] = isset($_REQUEST['show_daySeperatorLines']);
        $var_data['show_gabBreaks']         = isset($_REQUEST['show_gabBreaks']);
        $var_data['show_RecordAgain']       = isset($_REQUEST['show_RecordAgain']);
        $var_data['show_TrackingNr']        = isset($_REQUEST['show_TrackingNr']);
        $var_data['currency_name']          = $_REQUEST['currency_name'];
        $var_data['currency_sign']          = $_REQUEST['currency_sign'];
        $var_data['currency_first']         = isset($_REQUEST['currency_first']);
        $var_data['date_format_0']          = $_REQUEST['date_format_0'];
        $var_data['date_format_1']          = $_REQUEST['date_format_1'];
        $var_data['date_format_2']          = $_REQUEST['date_format_2'];
        $var_data['language']               = $_REQUEST['language'];
        $var_data['roundPrecision']         = $_REQUEST['roundPrecision'];
        $var_data['decimalSeparator']       = $_REQUEST['decimalSeparator'];
        $var_data['durationWithSeconds']    = isset($_REQUEST['durationWithSeconds']);
        $var_data['defaultTimezone']        = $_REQUEST['defaultTimezone'];
        $var_data['exactSums']              = isset($_REQUEST['exactSums']);
        
        $success = var_edit($var_data);

        // do whatever you like
        // and return one of these:
        
        echo $success?"ok":$kga['lang']['error'];
    break;
    

    case "toggleDeletedUsers":
        setcookie("ap_ext_show_deleted_users",$axValue); 
    break;

}

?>
