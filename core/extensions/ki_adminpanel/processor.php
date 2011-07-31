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
    	$usr_data['usr_sts'] = 2;
    	$usr_data['usr_active'] = 0;
    	$userId = $database->usr_create($usr_data);
        $database->setGroupMemberships($userId,$kga['usr']['groups']);
        echo $userId;
    break;
    
    case "createGrp":
    // create new group
		$grp_data['grp_name'] = trim($axValue);
		$new_grp_id = $database->grp_create($grp_data);
		if ($new_grp_id != false) {
			$database->assign_grp2ldrs($new_grp_id, array($kga['usr']['usr_ID']));
		}
    break;
    
    case "refreshSubtab":
    // builds either user/group/advanced/DB subtab
        $tpl->assign('curr_user', $kga['usr']['usr_name']);

        if ($kga['usr']['usr_sts']==0)
          $tpl->assign('arr_grp', $database->get_arr_grp(get_cookie('ap_ext_show_deleted_groups',0)));
        else
          $tpl->assign('arr_grp', $database->get_arr_grp_by_leader($kga['usr']['usr_ID'],
            get_cookie('ap_ext_show_deleted_groups',0)));

        if ($kga['usr']['usr_sts']==0)
          $arr_usr = $database->get_arr_usr(get_cookie('ap_ext_show_deleted_users',0));
        else
          $arr_usr = $database->get_arr_watchable_users($kga['usr']);

        // get group names
        foreach ($arr_usr as &$user) {
          $groups = $database->getGroupMemberships($user['usr_ID']);
          foreach ($groups as $group) {
            $groupData = $database->grp_get_data($group);
            $user['groups'][] = $groupData['grp_name'];
          }
        }

        $tpl->assign('arr_usr',$arr_usr);

        $tpl->assign('showDeletedGroups', get_cookie('ap_ext_show_deleted_groups',0));
        $tpl->assign('showDeletedUsers', get_cookie('ap_ext_show_deleted_users',0));
        switch ($axValue) {
            case "usr": $tpl->display("users.tpl");    break;
            case "grp": $tpl->display("groups.tpl");   break;
            case "adv":

                if ($kga['conf']['editLimit'] != '-') {
                  $tpl->assign('editLimitEnabled',true);
                  $editLimit = $kga['conf']['editLimit']/(60*60); // convert to hours
                  $tpl->assign('editLimitDays',(int) ($editLimit/24) );
                  $tpl->assign('editLimitHours',(int) ($editLimit%24) );
                }
                else {
                  $tpl->assign('editLimitEnabled',false);
                  $tpl->assign('editLimitDays','');
                  $tpl->assign('editLimitHours','');
                }

                $tpl->display("advanced.tpl");
            break;
            case "db":  $tpl->display("database.tpl"); break;
            
            case "knd":
		if ($kga['usr']['usr_sts']==0)
		  $arr_knd = $database->get_arr_knd();
		else
		  $arr_knd = $database->get_arr_knd($kga['usr']['groups']);

    foreach ($arr_knd as $row=>$knd_data) {
      $grp_names = array();
      $grps = $database->knd_get_grps($knd_data['knd_ID']);
      if ($grps !== false) {
        foreach ($grps as $grp_id) {
          $data = $database->grp_get_data($grp_id);
          $grp_names[] = $data['grp_name'];
        }
        $arr_knd[$row]['groups'] = implode(", ",$grp_names);
      }
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
		  $arr_pct = $database->get_arr_pct();
		else
		  $arr_pct = $database->get_arr_pct($kga['usr']['groups']);


    foreach ($arr_pct as $row=>$pct_data) {
      $grp_names = array();
      foreach ($database->pct_get_grps($pct_data['pct_ID']) as $grp_id) {
        $data = $database->grp_get_data($grp_id);
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
		  $groups = null;
		else
		  $groups = $kga['usr']['groups'];
                if (!isset($_REQUEST['evt_filter']))
                  $arr_evt = $database->get_arr_evt($groups);
                else
                  switch ($_REQUEST['evt_filter']) {
                      case -1:
                      $arr_evt = $database->get_arr_evt($groups);
                      break;
                    case -2:
                      // -2 is to get unassigned events. As -2 is never
                      // an id of a project this will give us all unassigned
                      // events.
                    default:
                      $arr_evt = 
                        $database->get_arr_evt_by_pct($_REQUEST['evt_filter'],$groups);
                  }

                foreach ($arr_evt as $row=>$evt_data) {
                  $grp_names = array();
                  foreach ($database->evt_get_grps($evt_data['evt_ID']) as $grp_id) {
                    $data = $database->grp_get_data($grp_id);
                    $grp_names[] = $data['grp_name'];
                  }
                  $arr_evt[$row]['groups'] = implode(", ",$grp_names);
                }
                  
                if (count($arr_evt)>0) {
                $tpl->assign('arr_evt', $arr_evt);
                } else {
                $tpl->assign('arr_evt', '0');
                }
                
                
                $arr_pct = $database->get_arr_pct($groups);
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
            $database->usr_delete($id);
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
            $database->grp_delete($id);
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
            $database->pct_delete($id);
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
            $database->knd_delete($id);
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
            $database->evt_delete($id);
        break;
        }
    break;

    case "banUsr":
    // Ban a user from login
    $sts['usr_active'] = 0;
    $database->usr_edit($id, $sts);
    echo sprintf("<img border='0' title='%s' alt='%s' src='../skins/%s/grfx/lock.png' width='16' height='16' />",
                  $kga['lang']['bannedusr'], $kga['lang']['bannedusr'], $kga['conf']['skin']);
    break;
    
    case "unbanUsr":
    // Unban a user from login
    $sts['usr_active'] = 1;
    $database->usr_edit($id, $sts);
    echo sprintf("<img border='0' title='%s' alt='%s' src='../skins/%s/grfx/jipp.gif' width='16' height='16' />",
                  $kga['lang']['activeusr'], $kga['lang']['activeusr'], $kga['conf']['skin']);
    break;
    
    case "sendEditUsr":
    // process editUsr form
        $usr_data['usr_name']  = trim($_REQUEST['usr_name']);
        $usr_data['usr_sts']   = $_REQUEST['usr_sts'];
        $usr_data['usr_mail']  = $_REQUEST['usr_mail'];
        $usr_data['usr_alias'] = $_REQUEST['usr_alias'];
        $usr_data['usr_rate']  = $_REQUEST['usr_rate'];
        
        // if password field is empty => password unchanged (not overwritten with "")
        if ($_REQUEST['usr_pw'] != "") {
        	$usr_data['pw'] = md5($kga['password_salt'].$_REQUEST['usr_pw'].$kga['password_salt']);
        }
        $database->usr_edit($id, $usr_data); 

        $database->setGroupMemberships($id,$_REQUEST['groups']);
    break;
    
    case "sendEditGrp":
    // process editGrp form
        $grp_data['grp_name'] = trim($_REQUEST['grp_name']);
        $database->grp_edit($id, $grp_data);
        
        $ldrs = $_REQUEST['grp_leader'];
        $database->assign_grp2ldrs($id, $ldrs);
        
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
        
        $editLimit = false;
        if (isset($_REQUEST['editLimitEnabled'])) {
          $hours = (int)$_REQUEST['editLimitHours'];
          $days = (int)$_REQUEST['editLimitDays'];
          $editLimit = $hours+$days*24;
          $editLimit *= 60*60; // convert to seconds
        }

        if ($editLimit === false || $editLimit === 0)
            $var_data['editLimit']          = '-';
        else 
            $var_data['editLimit']          = $editLimit; 
        
        $success = $database->var_edit($var_data);

        // do whatever you like
        // and return one of these:
        
        echo $success?"ok":$kga['lang']['error'];
    break;
    

    case "toggleDeletedUsers":
        setcookie("ap_ext_show_deleted_users",$axValue); 
    break;

}

?>
