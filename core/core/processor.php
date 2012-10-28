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
     * Remember which project and activity the user has selected for 
     * the quick recording via the buzzer.
     */
    case 'saveBuzzerPreselection':
      if (!isset($kga['user'])) return;

      $data= array();
      if (isset($_REQUEST['project']))
        $data['lastProject'] = $_REQUEST['project'];
      if (isset($_REQUEST['activity']))
        $data['lastActivity']   = $_REQUEST['activity'];

      $database->user_edit($kga['user']['userID'],$data);
    break;


    /**
     * Store the user preferences entered in the preferences dialog.
     */
    case 'editPrefs':
        if (isset($kga['customer'])) die();
    
        $preferences['skin']                    = $_REQUEST['skin'];
        $preferences['autoselection']           = getRequestBool('autoselection');
        $preferences['quickdelete']             = $_REQUEST['quickdelete'];
        $preferences['rowlimit']                = $_REQUEST['rowlimit'];
        $preferences['lang']                    = $_REQUEST['lang'];
        $preferences['flip_project_display']    = getRequestBool('flip_project_display');
        $preferences['project_comment_flag']    = getRequestBool('project_comment_flag');
        $preferences['showIDs']                 = getRequestBool('showIDs');
        $preferences['noFading']                = getRequestBool('noFading');
        $preferences['user_list_hidden']        = getRequestBool('user_list_hidden');
        $preferences['hideClearedEntries']      = getRequestBool('hideClearedEntries');
        $preferences['showCommentsByDefault']   = getRequestBool('showCommentsByDefault');
        $preferences['showTrackingNumber']      = getRequestBool('showTrackingNumber');
        $preferences['sublistAnnotations']      = $_REQUEST['sublistAnnotations'];
        $preferences['hideOverlapLines']        = getRequestBool('hideOverlapLines');

        $database->user_set_preferences($preferences,'ui.');
        $database->user_set_preferences(array('timezone'=>$_REQUEST['timezone']));

        $rate = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['rate']);
        if (is_numeric($rate))
          $database->save_rate($kga['user']['userID'],null,NULL,$rate);
        else
          $database->remove_rate($kga['user']['userID'],null,NULL);
        
        // If the password field is empty don't overwrite the old password.
        if (trim($_REQUEST['password']) != "") {
            $userData['password'] = md5($kga['password_salt'].$_REQUEST['password'].$kga['password_salt']);
            $database->user_edit($kga['user']['userID'], $userData);
        }
        
        
    break;
    
    /**
     * When the user changes the timeframe it is stored in the database so
     * it can be restored, when the user reloads the page.
     */
    case 'setTimeframe':
        if (!isset($kga['user'])) die();
    
        $timeframe = explode('|',$axValue);
         
        $timeframe_in  = explode('-',$timeframe[0]);
        $timeframe_in  = (int)mktime(0,0,0,$timeframe_in[0],$timeframe_in[1],$timeframe_in[2]);
        if ($timeframe_in < 950000000) $timeframe_in = $in;
        
        $timeframe_out = explode('-',$timeframe[1]);
        $timeframe_out = (int)mktime(23,59,59,$timeframe_out[0],$timeframe_out[1],$timeframe_out[2]);
        if ($timeframe_out < 950000000) $timeframe_out = $out;
        
        $database->save_timeframe($timeframe_in,$timeframe_out,$kga['user']['userID']);
    break;

    /**
     * The user started the recording of an activity via the buzzer. If this method
     * is called while another recording is running the first one will be stopped.
     */
    case 'startRecord':
        if (isset($kga['customer'])) die();
    
        $IDs = explode('|',$axValue);
        $newID = $database->startRecorder($IDs[0],$IDs[1],$id, $_REQUEST['startTime']);
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
     * See get_watchable_users.
     */
    case 'reload_users':
        if (isset($kga['customer'])) {
            $view->users = array();
        } else {
            $view->users = $database->get_watchable_users($kga['user']);
        }

        echo $view->render("lists/users.php");
    break;

    /**
     * Return a list of customers. A customer can only see himself.
     */
    case 'reload_customers':
        if (isset($kga['customer'])) {
          $customers = array(array(
              'customerID'=>$kga['customer']['customerID'],
              'name'=>$kga['customer']['name'],
              'visible'=>$kga['customer']['visible']));
        } else {
          $customers = $database->get_customers($kga['user']['groups']);
        }
        $view->customers = $customers;
        echo $view->render("lists/customers.php");
    break;

    /**
     * Return a list of projects. Customers are only shown their projects.
     */
    case 'reload_projects':
        if (isset($kga['customer']))
          $projects = $database->get_projects_by_customer(($kga['customer']['customerID']));
        else
          $projects = $database->get_projects($kga['user']['groups']);

        if (count($projects)>0) {
            $view->projects = $projects;
        } else {
            $view->projects = 0;
        }
        echo $view->render("lists/projects.php");
    break;

    /**
     * Return a list of tasks. Customers are only shown tasks which are
     * used for them. If a project is set as filter via the project parameter
     * only tasks for that project are shown.
     */
    case 'reload_activities':
        if (isset($kga['customer']))
          $activities = $database->get_activities_by_customer($kga['customer']['customerID']);
        else if (isset($_REQUEST['project']))
          $activities = $database->get_activities_by_project($_REQUEST['project'],$kga['user']['groups']);
        else
          $activities = $database->get_activities($kga['user']['groups']);
        if (count($activities)>0) {
            $view->activities = $activities;
        } else {
            $view->activities = 0;
        }
        echo $view->render("lists/activities.php");
    break;


    /**
     * Add a new customer, project or activity. This is a core function as it's
     * used at least by the admin panel and the timesheet extension.
     */
    case 'add_edit_CustomerProjectActivity':
    
        if(isset($kga['customer']) || $kga['user']['status']==2) die(); // only admins and groupleaders can do this ...
        
    	
        switch($axValue) {
            /**
             * add or edit a customer
             */
            case "customer":
              if (count($_REQUEST['customerGroups']) == 0) die(); // no group would mean it is never accessable

            	$data['name']     = $_REQUEST['name'];
            	$data['comment']  = $_REQUEST['comment'];
            	$data['company']  = $_REQUEST['company'];
                $data['vat']      = $_REQUEST['vat'];
                $data['contact']  = $_REQUEST['contactPerson'];
                $data['timezone'] = $_REQUEST['timezone'];
            	$data['street']   = $_REQUEST['street'];
            	$data['zipcode']  = $_REQUEST['zipcode'];
            	$data['city']     = $_REQUEST['city'];
            	$data['phone']    = $_REQUEST['phone'];
            	$data['fax']      = $_REQUEST['fax'];
            	$data['mobile']   = $_REQUEST['mobile'];
            	$data['mail']     = $_REQUEST['mail'];
            	$data['homepage'] = $_REQUEST['homepage'];
            	$data['visible']  = getRequestBool('visible');
            	$data['filter']   = $_REQUEST['customerFilter'];
        
              // If password field is empty dont overwrite the password.
              if (isset($_REQUEST['password']) && $_REQUEST['password'] != "") {
                $data['password'] = md5($kga['password_salt'].$_REQUEST['password'].$kga['password_salt']);
              }
              if (isset($_REQUEST['no_password']) && $_REQUEST['no_password']) {
                $data['password'] = '';
              }

              // validate data
              $errorMessages = array();
              $success = false;

              if ($database->user_name2id($data['name']) !== false)
                $errorMessages['name'] = $kga['lang']['errorMessages']['userWithSameName'];
              
              if (count($errorMessages) == 0) {
            	
                // add or update the customer
                  if (!$id) {
                      $id = $database->customer_create($data);
                  } else {
                      $database->customer_edit($id, $data);
                  }

                // set the customer group mappings
                $database->assign_customerToGroups($id, $_REQUEST['customerGroups']);
                $success = true;
              }
              
              header('Content-Type: application/json;charset=utf-8');
              echo json_encode(array(
                'errors' => $errorMessages,
                'success' => $success));
              
            break;
            
            /**
             * add or edit a project
             */
            case "project":
              if (count($_REQUEST['projectGroups']) == 0) die(); // no group would mean it is never accessable

              $data['name']         = $_REQUEST['name'];
              $data['customerID']   = $_REQUEST['customerID'];
              $data['comment']      = $_REQUEST['projectComment'];
              $data['visible']      = getRequestBool('visible');
              $data['internal']     = getRequestBool('internal');
              $data['filter']       = $_REQUEST['projectFilter'];
              $data['budget']       = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['budget']);
              $data['effort']       = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['effort']);
              $data['approved']       = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['approved']);
              $data['defaultRate'] = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['defaultRate']);
              $data['myRate']      = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['myRate']);
              $data['fixedRate']      = 
                  str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['fixedRate']);
                
                // add or update the project
              if (!$id) {
                $id = $database->project_create($data);
              } else {
                $database->project_edit($id, $data);
              }

              // set the project group mappings
              if (isset($_REQUEST['projectGroups']))
                $database->assign_projectToGroups($id, $_REQUEST['projectGroups']);
              if (isset($_REQUEST['assignedActivities'])) {
                $database->assignProjectToActivitiesForGroup($id, array_values($_REQUEST['assignedActivities']), $kga['user']['groups']);
                foreach($_REQUEST['assignedActivities'] as $index => $activityID) {
                	if($activityID <= 0) {
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
               		$database->project_activity_edit($id, $activityID, array('budget' => $_REQUEST['budget'][$index], 'effort' => $_REQUEST['effort'][$index], 'approved' => $_REQUEST['approved'][$index]));
                }
              }
            break;
            
            /**
             * add or edit a task
             */
            case "activity":
              if (count($_REQUEST['activityGroups']) == 0) die(); // no group would mean it is never accessable

              $data['name']         = $_REQUEST['name'];
              $data['comment']      = $_REQUEST['comment'];
              $data['visible']      = getRequestBool('visible');
              $data['filter']       = $_REQUEST['activityFilter'];
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
                $database->assign_activityToGroups($id, $_REQUEST['activityGroups']);
              else
                $database->assign_activityToGroups($id, array());

              if (isset($_REQUEST['projects']))
                $database->assignActivityToProjectsForGroup($id, $_REQUEST['projects'], $kga['user']['groups']);
              else
                $database->assignActivityToProjectsForGroup($id, array(), $kga['user']['groups']);
            break;
        }
    break;

}
