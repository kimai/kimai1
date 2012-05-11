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
        Logger::logfile("JavaScript: " . $axValue);
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
        $data['lastActivity']   = $_REQUEST['event'];

      $database->user_edit($kga['usr']['userID'],$data);
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
        $preferences['showCommentsByDefault'] = isset($_REQUEST['showCommentsByDefault'])?1:0;
        $preferences['sublistAnnotations'] = $_REQUEST['sublistAnnotations'];
        $preferences['hideOverlapLines']   = isset($_REQUEST['hideOverlapLines'])?1:0;

        $database->user_set_preferences($preferences,'ui.');
        $database->user_set_preferences(array('timezone'=>$_REQUEST['timezone']));

        $rate = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['rate']);
        if (is_numeric($rate))
          $database->save_rate($kga['usr']['userID'],null,NULL,$rate);
        else
          $database->remove_rate($kga['usr']['userID'],null,NULL);
        
        // If the password field is empty don't overwrite the old password.
        if ($_REQUEST['password'] != "") {
        	$usr_data['password'] = md5($kga['password_salt'].$_REQUEST['pw'].$kga['password_salt']);
          $database->user_edit($kga['usr']['userID'], $usr_data);
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
        
        $database->save_timeframe($timespace_in,$timespace_out,$kga['usr']['userID']);
    break;

    /**
     * The user started the recording of an event via the buzzer. If this method
     * is called while another recording is running the first one will be stopped.
     */
    case 'startRecord':
        if (isset($kga['customer'])) die();
    
        $IDs = explode('|',$axValue);
        $newID = $database->startRecorder($IDs[0],$IDs[1],$id);
        echo json_encode(array(
          'id' =>  $newID
        ));
    break;
    
    /**
     * Stop the running recording.
     */
    case 'stopRecord':
        $database->stopRecorder($id);
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
          $arr_usr = $database->get_arr_watchable_users($kga['usr']);

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
              'knd_ID'=>$kga['customer']['customerID'],
              'knd_name'=>$kga['customer']['knd_name'],
              'knd_visible'=>$kga['customer']['knd_visible']));
        else
          $arr_knd = $database->get_arr_customers($kga['usr']['groups']);

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
          $arr_pct = $database->get_arr_projects_by_customer(($kga['customer']['customerID']));
        else
          $arr_pct = $database->get_arr_projects($kga['usr']['groups']);

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
          $arr_evt = $database->get_arr_activities_by_customer($kga['customer']['customerID']);
        else if (isset($_REQUEST['pct']))
          $arr_evt = $database->get_arr_activities_by_project($_REQUEST['pct'],$kga['usr']['groups']);
        else
          $arr_evt = $database->get_arr_activities($kga['usr']['groups']);
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
    
        if(isset($kga['customer']) || $kga['usr']['status']==2) die(); // only admins and grpleaders can do this ...
        
    	
        switch($axValue) {
            /**
             * add or edit a customer
             */
            case "knd":
              if (count($_REQUEST['customerGroups']) == 0) die(); // no group would mean it is never accessable

            	$data['name']     = $_REQUEST['name'];
            	$data['comment']  = $_REQUEST['comment'];
            	$data['company']  = $_REQUEST['company'];
                $data['vat']      = $_REQUEST['vat'];
                $data['contact']  = $_REQUEST['contact'];
                $data['timezone'] = $_REQUEST['timezone'];
            	$data['street']   = $_REQUEST['street'];
            	$data['zipcode']  = $_REQUEST['zipcode'];
            	$data['city']     = $_REQUEST['city'];
            	$data['phone']    = $_REQUEST['phone'];
            	$data['fax']      = $_REQUEST['fax'];
            	$data['mobile']   = $_REQUEST['mobile'];
            	$data['mail']     = $_REQUEST['mail'];
            	$data['homepage'] = $_REQUEST['homepage'];
            	$data['visible']  = $_REQUEST['visible'];
            	$data['filter']   = $_REQUEST['filter'];
        
              // If password field is empty dont overwrite the password.
              if (isset($_REQUEST['password']) && $_REQUEST['password'] != "") {
                $data['password'] = md5($kga['password_salt'].$_REQUEST['password'].$kga['password_salt']);
              }
              if (isset($_REQUEST['no_password'])) {
                $data['password'] = '';
              }
            	
              // add or update the customer
            	if (!$id) {
                    $id = $database->customer_create($data);
            	} else {
            	    $database->customer_edit($id, $data);
            	}

              // set the customer group mappings
              $database->assign_customerToGroups($id, $_REQUEST['customerGroups']);
            break;
            
            /**
             * add or edit a project
             */
            case "pct":
              if (count($_REQUEST['projectGroups']) == 0) die(); // no group would mean it is never accessable

              $data['name']         = $_REQUEST['name'];
              $data['customerID']        = $_REQUEST['customerID'];
              $data['comment']      = $_REQUEST['comment'];
              $data['visible']      = isset($_REQUEST['visible'])?1:0;
              $data['internal']     = isset($_REQUEST['internal'])?1:0;
              $data['filter']       = $_REQUEST['filter'];
              $data['budget']       = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['budget']);
              $data['effort']       = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['effort']);
              $data['approved']       = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['approved']);
              $data['defaultRate'] = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['default_rate']);
              $data['myRate']      = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['my_rate']);
              $data['fixedRate']      = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['fixed_rate']);
                
                // add or update the project
              if (!$id) {
                $id = $database->project_create($data);
            	} else {
                $database->project_edit($id, $data);
            	}

              // set the project group mappings
              if (isset($_REQUEST['projectGroups']))
                $database->assign_projectToGroups($id, $_REQUEST['projectGroups']);
              if (isset($_REQUEST['assignedActivities']))
                $database->assignProjectsToActivityForGroup($id, $_REQUEST['assignedActivities'], $kga['usr']['groups']);
                foreach($_REQUEST['assignedActivitiest'] as $index => $evt_id) {
                	if($evt_id <= 0) {
                		continue;
                	}
                	if($_REQUEST['budget'][$index] <= 0) {
                		$_REQUEST['budget'][$index] = 0;
                	}
                	if($_REQUEST['effort'][$index] <= 0) {
                		$_REQUEST['effort'][$index] = 0;
                	}
                	if($_REQUEST['approved'][$index] <= 0) {
                		$_REQUEST['approved'][$index] = 0;
                	}
               		$database->projects_activities_edit($id, $evt_id, array('budget' => $_REQUEST['budget'][$index], 'effort' => $_REQUEST['effort'][$index], 'approved' => $_REQUEST['approved'][$index]));
                }
            break;
            
            /**
             * add or edit a task
             */
            case "evt":
              if (count($_REQUEST['activityGroups']) == 0) die(); // no group would mean it is never accessable

              $data['name']         = $_REQUEST['name'];
              $data['comment']      = $_REQUEST['comment'];
              $data['visible']      = $_REQUEST['visible'];
              $data['filter']       = $_REQUEST['filter'];
              $data['assignable']   = isset($_REQUEST['assignable'])?1:0;
              $data['defaultRate'] = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['defaultRate']);
              $data['myRate']      = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['myRate']);
              $data['fixedRate']      = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['fixedRate']);
                
                // add or update the project
              if (!$id) {
                $id = $database->activity_create($data);
            	} else {
                $database->activity_edit($id, $data);
            	}

              // set the task group and task project mappings
              if (isset($_REQUEST['activityGroups']))
                $database->assign_activityToGroups($id, $_REQUEST['groups']);
              else
                $database->assign_activityToGroups($id, array());

              if (isset($_REQUEST['projects']))
                $database->assignActivityToProjectsForGroup($id, $_REQUEST['projects'], $kga['usr']['groups']);
              else
                $database->assignActivityToProjectsForGroup($id, array(), $kga['usr']['groups']);
            break;
        }
    break;

}

?>
