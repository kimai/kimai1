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
// = TS PROCESSOR =
// ================

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require("../../includes/kspi.php");

// ==================
// = handle request =
// ==================
switch ($axAction) {

    // ==============================================
    // = start a new recording based on another one =
    // ==============================================
    case 'record':
        if (isset($kga['customer'])) die();

        $timeSheetEntry = $database->timeSheet_get_data($id);

        $timeSheetEntry['start'] = time();
        $timeSheetEntry['end'] = 0;
        $timeSheetEntry['duration'] = 0;

        $newTimeSheetEntryID = $database->timeEntry_create($timeSheetEntry);

        $userData = array();
        $userData['lastRecord'] = $newTimeSheetEntryID;
        $userData['lastProject'] = $timeSheetEntry['projectID'];
        $userData['lastActivity'] = $timeSheetEntry['activityID'];
        $database->user_edit($kga['user']['userID'], $userData);


        $project = $database->project_get_data($timeSheetEntry['projectID']);
        $return =  'projectName = "' . $project['name'] .'"; ';

        $return .=  'customer = "' . $project['customerID'] .'"; ';

        $customer = $database->customer_get_data($project['customerID']);
        $return .=  'customerName = "' . $customer['name'] .'"; ';

        $activity = $database->activity_get_data($timeSheetEntry['activityID']);
        $return .= 'activityName = "' . $activity['name'] .'"; ';
        
        $return .= "currentRecording = $newTimeSheetEntryID; ";

        echo $return;
        // TODO return false if error
    break;

    // ==================
    // = stop recording =
    // ==================
    case 'stop':
        if (isset($kga['customer'])) die();
        $database->stopRecorder($id);
        echo 1;
    break;

    // =======================================
    // = set comment for a running recording =
    // =======================================
    case 'edit_running':
        if (isset($kga['customer'])) die();

        if (isset($_REQUEST['project']))
          $database->timeEntry_edit_project($_REQUEST['id'], $_REQUEST['project']);

        if (isset($_REQUEST['activity']))
          $database->timeEntry_edit_activity($_REQUEST['id'], $_REQUEST['activity']);
        echo 1;
    break;

    // =========================================
    // = Erase timesheet entry via quickdelete =
    // =========================================
    case 'quickdelete':
        $database->timeEntry_delete($id);
        echo 1;
    break;

    // ==================================================
    // = Get the best rate for the project and activity =
    // ==================================================
    case 'bestFittingRates':
        if (isset($kga['customer'])) die();

        $data = array(
          'hourlyRate' => $database->get_best_fitting_rate($kga['user']['userID'],$_REQUEST['project_id'],$_REQUEST['activity_id']),
          'fixedRate' => $database->get_best_fitting_fixed_rate($_REQUEST['project_id'],$_REQUEST['activity_id'])
        );
        echo json_encode($data);
    break;


    // ==============================================================
    // = Get the new budget data after changing project or activity =
    // ==============================================================
    case 'budgets':
        if (isset($kga['customer'])) die();
        $timeSheetEntry = $database->timeSheet_get_data($_REQUEST['timeSheetEntryID']);
        // we subtract the used data in case the activity is the same as in the db, otherwise
        // it would get counted twice. For all aother cases, just set the values to 0
        // so we don't subtract too much
        if($timeSheetEntry['activityID'] != $_REQUEST['activity_id'] || $timeSheetEntry['projectID'] != $_REQUEST['project_id']) {
        	$timeSheetEntry['budget'] = 0;
        	$timeSheetEntry['approved'] = 0;
        	$timeSheetEntry['rate'] = 0;
        }
        $data = array(
          'activityBudgets' => $database->get_activity_budget($_REQUEST['project_id'],$_REQUEST['activity_id']),
          'activityUsed' => $database->get_budget_used($_REQUEST['project_id'],$_REQUEST['activity_id']),
          'timeSheetEntry' => $timeSheetEntry
        );
        echo json_encode($data);
    break;

    // ==============================================
    // = Get all rates for the project and activity =
    // ==============================================
    case 'allFittingRates':
        if (isset($kga['customer'])) die();

        $rates = $database->allFittingRates($kga['user']['userID'],$_REQUEST['project'],$_REQUEST['task']);
        $processedData = array();

        if ($rates !== false)
          foreach ($rates as $rate) {
            $line = Format::formatCurrency($rate['rate']);

            $setFor = array(); // contains the list of "types" for which this rate was set
            if ($rate['userID'] != null)
              $setFor[] = $kga['lang']['username'];
            if ($rate['projectID'] != null)
              $setFor[] =  $kga['lang']['project'];
            if ($rate['activityID'] != null)
              $setFor[] =  $kga['lang']['activity'];

            if (count($setFor) != 0)
              $line .= ' ('.implode($setFor,', ').')';

            $processedData[] = array('value'=>$rate['rate'], 'desc'=>$line);
          }

        echo json_encode($processedData);
    break;

    // ==============================================
    // = Get all rates for the project and activity =
    // ==============================================
    case 'allFittingFixedRates':
        if (isset($kga['customer'])) die();

        $rates = $database->allFittingFixedRates($_REQUEST['project'],$_REQUEST['task']);
        $processedData = array();

        if ($rates !== false)
          foreach ($rates as $rate) {
            $line = Format::formatCurrency($rate['rate']);

            $setFor = array(); // contains the list of "types" for which this rate was set
            if ($rate['projectID'] != null)
              $setFor[] =  $kga['lang']['project'];
            if ($rate['activityID'] != null)
              $setFor[] =  $kga['lang']['activity'];

            if (count($setFor) != 0)
              $line .= ' ('.implode($setFor,', ').')';

            $processedData[] = array('value'=>$rate['rate'], 'desc'=>$line);
          }

        echo json_encode($processedData);
    break;

    // ==================================================
    // = Get the best rate for the project and activity =
    // ==================================================
    case 'reload_activities_options':
        if (isset($kga['customer'])) die();
        $activities = $database->get_activities_by_project($_REQUEST['project'],$kga['user']['groups']);
        foreach ($activities as $activity) {
          if (!$activity['visible'])
            continue;
          echo '<option value="'.$activity['activityID'].'">'.
          $activity['name'].'</option>\n';
        }
    break;

    // =============================================
    // = Load timesheet data from DB and return it =
    // =============================================
    case 'reload_timeSheet':
        $filters = explode('|',$axValue);
        if ($filters[0] == "")
          $filterUsers = array();
        else
          $filterUsers = explode(':',$filters[0]);

        if ($filters[1] == "")
          $filterCustomers = array();
        else
          $filterCustomers = explode(':',$filters[1]);

        if ($filters[2] == "")
          $filterProjects = array();
        else
          $filterProjects = explode(':',$filters[2]);

        if ($filters[3] == "")
          $filterActivities = array();
        else
          $filterActivities = explode(':',$filters[3]);

        // if no userfilter is set, set it to current user
        if (isset($kga['user']) && count($filterUsers) == 0)
          array_push($filterUsers,$kga['user']['userID']);

        if (isset($kga['customer']))
          $filterCustomers = array($kga['customer']['customerID']);

        $timeSheetEntries = $database->get_timeSheet($in,$out,$filterUsers,$filterCustomers,$filterProjects,$filterActivities,1);
        if (count($timeSheetEntries)>0) {
            $view->timeSheetEntries = $timeSheetEntries;
        } else {
            $view->timeSheetEntries = 0;
        }
        $view->total = Format::formatDuration($database->get_duration($in,$out,$filterUsers,$filterCustomers,$filterProjects,$filterActivities));

        $ann = $database->get_time_users($in,$out,$filterUsers,$filterCustomers,$filterProjects,$filterActivities);
        Format::formatAnnotations($ann);
        $view->user_annotations = $ann;

        $ann = $database->get_time_customers($in,$out,$filterUsers,$filterCustomers,$filterProjects,$filterActivities);
        Format::formatAnnotations($ann);
        $view->customer_annotations = $ann;

        $ann = $database->get_time_projects($in,$out,$filterUsers,$filterCustomers,$filterProjects,$filterActivities);
        Format::formatAnnotations($ann);
        $view->project_annotations = $ann;

        $ann = $database->get_time_activities($in,$out,$filterUsers,$filterCustomers,$filterProjects,$filterActivities);
        Format::formatAnnotations($ann);
        $view->activity_annotations = $ann;

        $view->hideComments = true;
        $view->showOverlapLines = false;
        $view->showTrackingNumber = true;

        // user can change these settings
        if (isset($kga['user'])) {
            $view->hideComments = $database->user_get_preference('ui.showCommentsByDefault')!=1;
            $view->showOverlapLines = $database->user_get_preference('ui.hideOverlapLines')!=1;
            $view->showTrackingNumber = $database->user_get_preference('ui.showTrackingNumber')!=0;
        }

        echo $view->render("timeSheet.php");
    break;


    // ==============================
    // = add / edit timeSheet entry =
    // ==============================
    case 'add_edit_timeSheetEntry':
      if (isset($kga['customer'])) die();

      if ($id) {
        $data = $database->timeSheet_get_data($id);
        if ($kga['conf']['editLimit'] != "-" && time()-$data['end'] > $kga['conf']['editLimit']) {
          echo json_encode(array('result'=>'error','message'=>$kga['lang']['editLimitError']));
          return;
        }
      }

      if (isset($_REQUEST['erase'])) {
        // delete checkbox set ?
        // then the record is simply dropped and processing stops at this point
          $database->timeEntry_delete($id);
          echo json_encode(array('result'=>'ok'));
          break;
      }

      $data['projectID']          = $_REQUEST['projectID'];
      $data['activityID']          = $_REQUEST['activityID'];
      $data['location']       = $_REQUEST['location'];
      $data['trackingNumber']      = $_REQUEST['trackingNumber'];
      $data['description']     = $_REQUEST['description'];
      $data['comment']         = $_REQUEST['comment'];
      $data['commentType']    = $_REQUEST['commentType'];
      $data['rate']            = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['rate']);
      $data['fixedRate']      = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['fixedRate']);
      $data['cleared']         = isset($_REQUEST['cleared']);
      $data['statusID']          = $_REQUEST['statusID'];
      $data['billable']        = $_REQUEST['billable'];
      $data['budget']          = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['budget']);
      $data['approved']        = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['approved']);

      if (isset($_REQUEST['userID'])) {
        // only take the given user id if it is in the list of watchable users
        $users = $database->get_watchable_users($kga['user']);
        foreach ($users as $user) {
          if ($user['userID'] == $_REQUEST['userID']) {
            $data['userID'] = $user['userID'];
            break;
          }
        }
      }

      if (!isset($data['userID']))
        $data['userID'] = $kga['user']['userID'];

      // check if the posted time values are possible

      $validateDate = new Zend_Validate_Date(array('format' => 'dd.MM.yyyy'));
      $validateTime = new Zend_Validate_Date(array('format' => 'HH:mm:ss'));

      if (!$validateDate->isValid($_REQUEST['start_day']) ||
          !$validateTime->isValid($_REQUEST['start_time'])) {
        echo json_encode(array('result'=>'error','message'=>$kga['lang']['TimeDateInputError']));
          return;
      }

      if ( ($_REQUEST['end_day'] != '' || $_REQUEST['end_time'] != '') && (
          !$validateDate->isValid($_REQUEST['start_day']) ||
          !$validateTime->isValid($_REQUEST['start_time']))) {
        echo json_encode(array('result'=>'error','message'=>$kga['lang']['TimeDateInputError']));
          return;
      }

      $edit_in_day = Zend_Locale_Format::getDate($_REQUEST['start_day'],
                                          array('date_format' => 'dd.MM.yyyy'));
      $edit_in_time = Zend_Locale_Format::getTime($_REQUEST['start_time'],
                                          array('date_format' => 'HH:mm:ss'));

      $edit_in = array_merge($edit_in_day, $edit_in_time);

      $inDate = new Zend_Date($edit_in);

      if ($_REQUEST['end_day'] != '' || $_REQUEST['end_time'] != '') {
        $edit_out_day = Zend_Locale_Format::getDate($_REQUEST['end_day'],
                                            array('date_format' => 'dd.MM.yyyy'));
        $edit_out_time = Zend_Locale_Format::getTime($_REQUEST['end_time'],
                                            array('date_format' => 'HH:mm:ss'));

        $edit_out = array_merge($edit_out_day, $edit_out_time);

        $outDate = new Zend_Date($edit_out);
      }
      else {
        $outDate = null;
      }

      $data['start']   = $inDate->getTimestamp();

      if ($outDate != null) {
        $data['end']  = $outDate->getTimestamp();
        $data['duration'] = $data['end'] - $data['start'];
      }

      if ($id) { // TIME RIGHT - NEW OR EDIT ?

          // TIME RIGHT - EDIT ENTRY
          Logger::logfile("timeEntry_edit: " .$id);
          $database->timeEntry_edit($id,$data);

      } else {

          // TIME RIGHT - NEW ENTRY
          Logger::logfile("timeEntry_create");
          $database->timeEntry_create($data);
      }

      echo json_encode(array('result'=>'ok'));

    break;

}

?>