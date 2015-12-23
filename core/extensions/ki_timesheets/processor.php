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

function timesheetAccessAllowed($entry, $action, &$errors) {
  global $database, $kga;

  if (!isset($kga['user'])) {
    $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
    return false;
  }


  if ($kga['conf']['editLimit'] != "-" && time()-$entry['end'] > $kga['conf']['editLimit'] && $entry['end']!= 0) {
    $errors[''] = $kga['lang']['editLimitError'];
    return;
  }


  $groups = $database->getGroupMemberships($entry['userID']);

  if ($entry['userID'] == $kga['user']['userID']) {
    $permissionName = 'ki_timesheets-ownEntry-' . $action;
    if ($database->global_role_allows($kga['user']['globalRoleID'], $permissionName)) {
      return true;
    } else {
      Logger::logfile("missing global permission $permissionName for user " . $kga['user']['name']);
      $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
      return false;
    }
  }

  $assignedOwnGroups = array_intersect($groups, $database->getGroupMemberships($kga['user']['userID']));

  if (count($assignedOwnGroups) > 0) {
    $permissionName = 'ki_timesheets-otherEntry-ownGroup-' . $action;
    if ($database->checkMembershipPermission($kga['user']['userID'],$assignedOwnGroups, $permissionName)) {
      return true;
    } else {
      Logger::logfile("missing membership permission $permissionName of own group(s) " . implode(", ", $assignedOwnGroups) . " for user " . $kga['user']['name']);
      $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
      return false;
    }

  }

  $permissionName = 'ki_timesheets-otherEntry-otherGroup-' . $action;
  if ($database->global_role_allows($kga['user']['globalRoleID'], $permissionName)) {
    return true;
  } else {
    Logger::logfile("missing global permission $permissionName for user " . $kga['user']['name']);
    $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
    return false;
  }

}

// ==================
// = handle request =
// ==================
switch ($axAction) {

    // ==============================================
    // = start a new recording based on another one =
    // ==============================================
    case 'record':
        $response = array();

        $timeSheetEntry = $database->timeSheet_get_data($id);

        $timeSheetEntry['start'] = time();
        $timeSheetEntry['end'] = 0;
        $timeSheetEntry['duration'] = 0;
        $timeSheetEntry['cleared'] = 0;

        $errors = array();
        timesheetAccessAllowed($timeSheetEntry,'edit',$errors);
        $response['errors'] = $errors;

        if (count($errors) == 0) {

          $newTimeSheetEntryID = $database->timeEntry_create($timeSheetEntry);

          $userData = array();
          $userData['lastRecord'] = $newTimeSheetEntryID;
          $userData['lastProject'] = $timeSheetEntry['projectID'];
          $userData['lastActivity'] = $timeSheetEntry['activityID'];
          $database->user_edit($kga['user']['userID'], $userData);


          $project = $database->project_get_data($timeSheetEntry['projectID']);
          $customer = $database->customer_get_data($project['customerID']);
          $activity = $database->activity_get_data($timeSheetEntry['activityID']);

          $response['customer'] = $customer['customerID'];
          $response['projectName'] = $project['name'];
          $response['customerName'] = $customer['name'];
          $response['activityName'] = $activity['name'];
          $response['currentRecording'] = $newTimeSheetEntryID;
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode($response);
    break;

    // ==================
    // = stop recording =
    // ==================
    case 'stop':
        $errors = array();

        $data = $database->timeSheet_get_data($id);

        timesheetAccessAllowed($data,'edit',$errors);

        if (count($errors) == 0)
          $database->stopRecorder($id);

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
          'errors' => $errors));
    break;

    // =======================================
    // = set comment for a running recording =
    // =======================================
    case 'edit_running':
        $errors = array();

        $data = $database->timeSheet_get_data($id);

        timesheetAccessAllowed($data,'edit',$errors);

        if (count($errors) == 0) {
          if (isset($_REQUEST['project']))
            $database->timeEntry_edit_project($id, $_REQUEST['project']);

          if (isset($_REQUEST['activity']))
            $database->timeEntry_edit_activity($id, $_REQUEST['activity']);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
          'errors' => $errors));
    break;

    // =========================================
    // = Erase timesheet entry via quickdelete =
    // =========================================
    case 'quickdelete':
        $errors = array();

        $data = $database->timeSheet_get_data($id);

        timesheetAccessAllowed($data,'delete',$errors);

        if (count($errors) == 0) {
          $database->timeEntry_delete($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(array(
          'errors' => $errors));
    break;

    // ==================================================
    // = Get the best rate for the project and activity =
    // ==================================================
    case 'bestFittingRates':
        $data = array('errors' => array());

        if (!isset($kga['user']))
          $data['errors'][] = $kga['lang']['editLimitError'];

        if (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_timesheets-showRates'))
          $data['errors'][] = $kga['lang']['editLimitError'];

        if (count($data['errors']) == 0) {
          $data['hourlyRate'] = $database->get_best_fitting_rate($kga['user']['userID'],$_REQUEST['project_id'],$_REQUEST['activity_id']);
          $data['fixedRate']  = $database->get_best_fitting_fixed_rate($_REQUEST['project_id'],$_REQUEST['activity_id']);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode($data);
    break;


    // ==============================================================
    // = Get the new budget data after changing project or activity =
    // ==============================================================
    case 'budgets':
        $data = array('errors' => array());

        if (!isset($kga['user']))
          $data['errors'][] = $kga['lang']['editLimitError'];

        if (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_timesheets-showRates'))
          $data['errors'][] = $kga['lang']['editLimitError'];

        if (count($data['errors']) == 0) {
          $timeSheetEntry = $database->timeSheet_get_data($_REQUEST['timeSheetEntryID']);
          // we subtract the used data in case the activity is the same as in the db, otherwise
          // it would get counted twice. For all aother cases, just set the values to 0
          // so we don't subtract too much
          if($timeSheetEntry['activityID'] != $_REQUEST['activity_id'] || $timeSheetEntry['projectID'] != $_REQUEST['project_id']) {
                  $timeSheetEntry['budget'] = 0;
                  $timeSheetEntry['approved'] = 0;
                  $timeSheetEntry['rate'] = 0;
          }
          $data['activityBudgets'] = $database->get_activity_budget($_REQUEST['project_id'],$_REQUEST['activity_id']);
          $data['activityUsed']    = $database->get_budget_used($_REQUEST['project_id'],$_REQUEST['activity_id']);
          $data['timeSheetEntry']  = $timeSheetEntry;
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode($data);
    break;

    // ==============================================
    // = Get all rates for the project and activity =
    // ==============================================
    case 'allFittingRates':
        $data = array('errors' => array());

        if (!isset($kga['user']))
          $data['errors'][] = $kga['lang']['editLimitError'];

        if (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_timesheets-showRates'))
          $data['errors'][] = $kga['lang']['editLimitError'];

        if (count($data['errors']) == 0) {
          $rates = $database->allFittingRates($kga['user']['userID'],$_REQUEST['project'],$_REQUEST['activity']);

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

              $data['rates'][] = array('value'=>$rate['rate'], 'desc'=>$line);
            }
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode($data);
    break;

    // ==============================================
    // = Get all rates for the project and activity =
    // ==============================================
    case 'allFittingFixedRates':
        $data = array('errors' => array());

        if (!isset($kga['user']))
          $data['errors'][] = $kga['lang']['editLimitError'];

        if (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_timesheets-showRates'))
          $data['errors'][] = $kga['lang']['editLimitError'];

        if (count($data['errors']) == 0) {
          $rates = $database->allFittingFixedRates($_REQUEST['project'],$_REQUEST['activity']);

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

              $data['rates'][] = array('value'=>$rate['rate'], 'desc'=>$line);
            }
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode($data);
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

        $filterCustomers = array_map(function($customer) {
          return $customer['customerID'];
        }, $database->get_customers($kga['user']['groups']));
        if ($filters[1] != "")
          $filterCustomers = array_intersect($filterCustomers, explode(':',$filters[1]));

        $filterProjects = array_map(function($project) {
          return $project['projectID'];
        }, $database->get_projects($kga['user']['groups']));
        if ($filters[2] != "")
          $filterProjects = array_intersect($filterProjects, explode(':',$filters[2]));

        $filterActivities = array_map(function($activity) {
          return $activity['activityID'];
        }, $database->get_activities($kga['user']['groups']));
        if ($filters[3] != "")
          $filterActivities = array_intersect($filterActivities, explode(':',$filters[3]));

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
        $view->latest_running_entry = $database->get_latest_running_entry();
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

        $view->showRates = isset($kga['user']) && $database->global_role_allows($kga['user']['globalRoleID'],'ki_timesheets-showRates');

        echo $view->render("timeSheet.php");
    break;


    // ==============================
    // = add / edit timeSheet entry =
    // ==============================
    case 'add_edit_timeSheetEntry':
      header('Content-Type: application/json;charset=utf-8');
      $errors = array();

      $action = 'add';
      if ($id)
        $action = 'edit';
      if (isset($_REQUEST['erase']))
        $action = 'delete';

      if ($id) {
        $data = $database->timeSheet_get_data($id);

        // check if editing or deleting with the old values would be allowed
        if (!timesheetAccessAllowed($data,$action,$errors)) {
          echo json_encode(array('errors'=>$errors));
          break;
        }
      }

      if (isset($_REQUEST['erase'])) {
        // delete checkbox set ?
        // then the record is simply dropped and processing stops at this point
          $database->timeEntry_delete($id);
          echo json_encode(array('errors'=>$errors));
          break;
      }

      $data['projectID']      = $_REQUEST['projectID'];
      $data['activityID']     = $_REQUEST['activityID'];
      $data['location']       = $_REQUEST['location'];
      $data['trackingNumber'] = $_REQUEST['trackingNumber'];
      $data['description']    = $_REQUEST['description'];
      $data['comment']        = $_REQUEST['comment'];
      $data['commentType']    = $_REQUEST['commentType'];
      if ($database->global_role_allows($kga['user']['globalRoleID'],'ki_timesheets-editRates')) {
        $data['rate']         = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['rate']);
        $data['fixedRate']      = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['fixedRate']);
      } else if (!$id) {
        $data['rate']         = $database->get_best_fitting_rate($kga['user']['userID'],$data['projectID'],$data['activityID']);
        $data['fixedRate']      = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['fixedRate']);
      }
      $data['cleared']        = isset($_REQUEST['cleared']);
      $data['statusID']       = $_REQUEST['statusID'];
      $data['billable']       = $_REQUEST['billable'];
      $data['budget']         = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['budget']);
      $data['approved']       = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['approved']);
      $data['userID']         = $_REQUEST['userID'];


      // check if the posted time values are possible

      $validateDate = new Zend_Validate_Date(array('format' => 'dd.MM.yyyy'));
      $validateTime = new Zend_Validate_Date(array('format' => 'HH:mm:ss'));

      if (!$validateDate->isValid($_REQUEST['start_day']))
          $errors['start_day'] = $kga['lang']['TimeDateInputError'];

      if (!$validateTime->isValid($_REQUEST['start_time'])) {
        $_REQUEST['start_time'] = $_REQUEST['start_time'] . ':00';
        if (!$validateTime->isValid($_REQUEST['start_time']))
          $errors['start_time'] = $kga['lang']['TimeDateInputError'];
      }

      if ( $_REQUEST['end_day'] != '' && !$validateDate->isValid($_REQUEST['end_day']) )
        $errors['end_day'] = $kga['lang']['TimeDateInputError'];

      if ( $_REQUEST['end_time'] != '' && !$validateTime->isValid($_REQUEST['end_time']) ) {
        $_REQUEST['end_time'] = $_REQUEST['end_time'] . ':00';
        if (!$validateTime->isValid($_REQUEST['end_time']))
          $errors['end_time'] = $kga['lang']['TimeDateInputError'];
      }

      if (!is_numeric($data['activityID']))
        $errors['activityID'] = $kga['lang']['errorMessages']['noActivitySelected'];

      if (!is_numeric($data['projectID']))
        $errors['projectID'] = $kga['lang']['errorMessages']['noProjectSelected'];

      if (count($errors) > 0) {
          echo json_encode(array('errors'=>$errors));
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

          if (!timesheetAccessAllowed($data,$action,$errors)) {
            echo json_encode(array('errors'=>$errors));
            break;
          }

          // TIME RIGHT - EDIT ENTRY
          Logger::logfile("timeEntry_edit: " .$id);
          $database->timeEntry_edit($id,$data);

      } else {

        // TIME RIGHT - NEW ENTRY

        $database->transaction_begin();

        foreach ($_REQUEST['userID'] as $userID) {
          $data['userID'] = $userID;

          if (!timesheetAccessAllowed($data,$action,$errors)) {
            echo json_encode(array('errors'=>$errors));
            $database->transaction_rollback();
            break 2;
          }

          Logger::logfile("timeEntry_create");
          $database->timeEntry_create($data);
        }

        $database->transaction_end();
      }

      echo json_encode(array('errors'=>$errors));
    break;

    // ===================================
    // = add / edit timeSheet quick note =
    // ===================================
    case 'add_edit_timeSheetQuickNote':
        header('Content-Type: application/json;charset=utf-8');
        $errors = array();

        $action = 'add';

        if ($id) {
            $action = 'edit';
            $data = $database->timeSheet_get_data($id);

            // check if editing or deleting with the old values would be allowed
            if ( ! timesheetAccessAllowed($data, $action, $errors)) {
                echo json_encode(array('errors' => $errors));
                break;
            }
        }

        $data['location'] = $_REQUEST['location'];
        $data['trackingNumber'] = $_REQUEST['trackingNumber'];
        $data['comment'] = $_REQUEST['comment'];
        $data['commentType'] = $_REQUEST['commentType'];
        $data['userID'] = $_REQUEST['userID'];

        if ( ! timesheetAccessAllowed($data, $action, $errors)) {
            echo json_encode(array('errors' => $errors));
            break;
        }
        if ($id) { // TIME RIGHT - NEW OR EDIT ?
            // TIME RIGHT - EDIT ENTRY
            Logger::logfile("timeNote_edit: " . $id);
            $database->timeEntry_edit($id, $data);
        } else {
            // TIME RIGHT - NEW ENTRY
            Logger::logfile("timeNote_create");
            $database->timeEntry_create($data);
        }
        echo json_encode(array('errors' => $errors));
        break;
}
