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

/**
 * ==================
 * = Core Processor =
 * ==================
 * 
 * Called via AJAX from the Kimai user interface. Depending on $axAction
 * actions are performed, e.g. editing preferences or returning a list
 * of customers.
 */

// insert KSPI 
$isCoreProcessor = 1;
$dir_templates = "templates/core/";
require("../includes/kspi.php");

switch ($axAction) {
    
    /**
     * Append a new entry to the logfile.
     */
    case 'logfile':
        logfile("JavaScript: " . $axValue);
    break;

    /**
     * Remember which project and event the user has selected for 
     * the quick recording via the buzzer.
     */
    case 'saveBuzzerPreselection':
      if (!isset($kga['usr'])) return;

      $data= array();
      if (isset($_REQUEST['project']))
        $data['lastProject'] = $_REQUEST['project'];
      if (isset($_REQUEST['event']))
        $data['lastEvent']   = $_REQUEST['event'];

      usr_edit($kga['usr']['usr_ID'],$data);
    break;


    /**
     * Store the user preferences entered in the preferences dialog.
     */
    case 'editPrefs':
        if (isset($kga['customer'])) die();
    
        $preferences['skin']               = $_REQUEST['skin'];
        $preferences['autoselection']      = isset($_REQUEST['autoselection'])?1:0;
        $preferences['quickdelete']        = $_REQUEST['quickdelete'];
        $preferences['rowlimit']           = $_REQUEST['rowlimit'];
        $preferences['lang']               = $_REQUEST['lang'];
        $preferences['flip_pct_display']   = isset($_REQUEST['flip_pct_display'])?1:0;
        $preferences['pct_comment_flag']   = isset($_REQUEST['pct_comment_flag'])?1:0;
        $preferences['showIDs']            = isset($_REQUEST['showIDs'])?1:0;
        $preferences['noFading']           = isset($_REQUEST['noFading'])?1:0;
        $preferences['user_list_hidden']   = isset($_REQUEST['user_list_hidden'])?1:0;
        $preferences['hideClearedEntries'] = isset($_REQUEST['hideClearedEntries'])?1:0;
        $preferences['sublistAnnotations'] = $_REQUEST['sublistAnnotations'];

        usr_set_preferences($preferences,'ui.');
        usr_set_preferences(array('timezone'=>$_REQUEST['timezone']));

        $rate = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['rate']);
        if (is_numeric($rate))
          save_rate($kga['usr']['usr_ID'],null,NULL,$rate);
        else
          remove_rate($kga['usr']['usr_ID'],null,NULL);
        
        // If the password field is empty don't overwrite the old password.
        if ($_REQUEST['pw'] != "") {
        	$usr_data['pw'] = md5($kga['password_salt'].$_REQUEST['pw'].$kga['password_salt']);
          usr_edit($kga['usr']['usr_ID'], $usr_data);
        }
        
        
    break;
    
    /**
     * When the user changes the timespace it is stored in the database so
     * it can be restored, when the user reloads the page.
     */
    case 'setTimespace':
        if (!isset($kga['usr'])) die();
    
        $timespace = explode('|',$axValue);
         
        $timespace_in  = explode('-',$timespace[0]);
        $timespace_in  = (int)mktime(0,0,0,$timespace_in[0],$timespace_in[1],$timespace_in[2]);
        if ($timespace_in < 950000000) $timespace_in = $in;
        
        $timespace_out = explode('-',$timespace[1]);
        $timespace_out = (int)mktime(23,59,59,$timespace_out[0],$timespace_out[1],$timespace_out[2]);
        if ($timespace_out < 950000000) $timespace_out = $out;
        
        save_timespace($timespace_in,$timespace_out,$kga['usr']['usr_ID']);
    break;

    /**
     * The user started the recording of an event via the buzzer. If this method
     * is called while another recording is running the first one will be stopped.
     */
    case 'startRecord':
        if (isset($kga['customer'])) die();

        if (get_rec_state($kga['usr']['usr_ID'])) {
            stopRecorder();
        }
    
        $IDs = explode('|',$axValue);
        startRecorder($IDs[0],$IDs[1],$id);
        echo 1;
    break;
    
    /**
     * Stop the running recording.
     */
    case 'stopRecord':
        stopRecorder();
        echo 1;
    break;

    /**
     * Return a list of users. Customers are not shown any users. The
     * type of the current user decides which users are shown to him.
     * See get_arr_watchable_users.
     */
    case 'reload_usr':
        if (isset($kga['customer']))
          $arr_usr = array();
        else
          $arr_usr = get_arr_watchable_users($kga['usr']['usr_ID']);

        if (count($arr_usr)>0) {
            $tpl->assign('arr_usr', $arr_usr);
        } else {
            $tpl->assign('arr_usr', 0);
        }
        $tpl->display("../lists/usr.tpl");
    break;

    /**
     * Return a list of customers. A customer can only see himself.
     */
    case 'reload_knd':
        if (isset($kga['customer']))
          $arr_knd = array(array(
              'knd_ID'=>$kga['customer']['knd_ID'],
              'knd_name'=>$kga['customer']['knd_name'],
              'knd_visible'=>$kga['customer']['knd_visible']));
        else
          $arr_knd = get_arr_knd($kga['usr']['usr_grp']);

        if (count($arr_knd)>0) {
            $tpl->assign('arr_knd', $arr_knd);
        } else {
            $tpl->assign('arr_knd', 0);
        }
        $tpl->display("../lists/knd.tpl");
    break;

    /**
     * Return a list of projects. Customers are only shown their projects.
     */
    case 'reload_pct':
        if (isset($kga['customer']))
          $arr_pct = get_arr_pct_by_knd("all",$kga['customer']['knd_ID']);
        else
          $arr_pct = get_arr_pct($kga['usr']['usr_grp']);

        if (count($arr_pct)>0) {
            $tpl->assign('arr_pct', $arr_pct);
        } else {
            $tpl->assign('arr_pct', 0);
        }
        $tpl->display("../lists/pct.tpl");
    break;

    /**
     * Return a list of tasks. Customers are only shown tasks which are
     * used for them. If a project is set as filter via the pct parameter
     * only tasks for that project are shown.
     */
    case 'reload_evt':
        if (isset($kga['customer']))
          $arr_evt = get_arr_evt_by_knd($kga['customer']['knd_ID']);
        else if (isset($_REQUEST['pct']))
          $arr_evt = get_arr_evt_by_pct($kga['usr']['usr_grp'],
              $_REQUEST['pct']);
        else
          $arr_evt = get_arr_evt($kga['usr']['usr_grp']);
        if (count($arr_evt)>0) {
            $tpl->assign('arr_evt', $arr_evt);
        } else {
            $tpl->assign('arr_evt', 0);
        }
        $tpl->display("../lists/evt.tpl");
    break;


    /**
     * Add a new customer, project or event. This is a core function as it's
     * used at least by the admin panel and the timesheet extension.
     */
    case 'add_edit_KndPctEvt':
    
        if(isset($kga['customer']) || $kga['usr']['usr_sts']==2) die(); // only admins and grpleaders can do this ...
        
    	
        switch($axValue) {
            /**
             * add or edit a customer
             */
            case "knd":
              if (count($_REQUEST['knd_grp']) == 0) die(); // no group would mean it is never accessable

            	$data['knd_name']     = $_REQUEST['knd_name'];
            	$data['knd_comment']  = $_REQUEST['knd_comment'];
            	$data['knd_company']  = $_REQUEST['knd_company'];
                $data['knd_vat']      = $_REQUEST['knd_vat'];
              $data['knd_contact']  = $_REQUEST['knd_contact'];
            	$data['knd_street']   = $_REQUEST['knd_street'];
            	$data['knd_zipcode']  = $_REQUEST['knd_zipcode'];
            	$data['knd_city']     = $_REQUEST['knd_city'];
            	$data['knd_tel']      = $_REQUEST['knd_tel'];
            	$data['knd_fax']      = $_REQUEST['knd_fax'];
            	$data['knd_mobile']   = $_REQUEST['knd_mobile'];
            	$data['knd_mail']     = $_REQUEST['knd_mail'];
            	$data['knd_homepage'] = $_REQUEST['knd_homepage'];
            	$data['knd_visible']  = $_REQUEST['knd_visible'];
            	$data['knd_filter']   = $_REQUEST['knd_filter'];
        
              // If password field is empty dont overwrite the password.
              if ($_REQUEST['knd_password'] != "") {
                $data['knd_password'] = md5($kga['password_salt'].$_REQUEST['knd_password'].$kga['password_salt']);
              }
            	
              // add or update the customer
            	if (!$id) {
                    $id = knd_create($data);
            	} else {
            	    knd_edit($id, $data);
            	}

              // set the customer group mappings
              $grp_array = $_REQUEST['knd_grp'];
              assign_knd2grps($id, $grp_array);
            break;
            
            /**
             * add or edit a project
             */
            case "pct":
              if (count($_REQUEST['pct_grp']) == 0) die(); // no group would mean it is never accessable

              $data['pct_name']         = $_REQUEST['pct_name'];
              $data['pct_kndID']        = $_REQUEST['pct_kndID'];
              $data['pct_comment']      = $_REQUEST['pct_comment'];
              $data['pct_visible']      = isset($_REQUEST['pct_visible'])?1:0;
              $data['pct_internal']     = isset($_REQUEST['pct_internal'])?1:0;
              $data['pct_filter']       = $_REQUEST['pct_filter'];
              $data['pct_budget']       = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['pct_budget']);
              $data['pct_default_rate'] = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['pct_default_rate']);
              $data['pct_my_rate']      = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['pct_my_rate']);
                
                // add or update the project
              if (!$id) {
                $id = pct_create($data);
            	} else {
                pct_edit($id, $data);
            	}

              // set the project group mappings
              if (isset($_REQUEST['pct_grp']))
                assign_pct2grps($id, $_REQUEST['pct_grp']);
              if (isset($_REQUEST['pct_evt']))
                assign_pct2evts($id, $_REQUEST['pct_evt']);
            break;
            
            /**
             * add or edit a task
             */
            case "evt":
              if (count($_REQUEST['evt_grp']) == 0) die(); // no group would mean it is never accessable

              $data['evt_name']         = $_REQUEST['evt_name'];
              $data['evt_comment']      = $_REQUEST['evt_comment'];
              $data['evt_visible']      = $_REQUEST['evt_visible'];
              $data['evt_filter']       = $_REQUEST['evt_filter'];
              $data['evt_default_rate'] = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['evt_default_rate']);
              $data['evt_my_rate']      = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['evt_my_rate']);
                
                // add or update the project
              if (!$id) {
                $id = evt_create($data);
            	} else {
                evt_edit($id, $data);
            	}

              // set the task group and task project mappings
              if (isset($_REQUEST['evt_grp']))
                assign_evt2grps($id, $_REQUEST['evt_grp']);
              else
                assign_evt2grps($id, array());

              if (isset($_REQUEST['evt_pct']))
                assign_evt2pcts($id, $_REQUEST['evt_pct']);
              else
                assign_evt2pcts($id, array());
            break;
        }
    break;

}

?>
