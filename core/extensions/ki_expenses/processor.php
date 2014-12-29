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
$dir_templates = "templates/";
require("../../includes/kspi.php");


function expenseAccessAllowed($entry, $action, &$errors) {
  global $database, $kga;

  if (!isset($kga['user'])) {
    $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
    return false;
  }

  // check if expense is too far in the past to allow editing (or deleting)
  if (isset($entry['id']) && $kga['conf']['editLimit'] != "-" && time()-$entry['timestamp'] > $kga['conf']['editLimit']) {
    $errors[''] =  $kga['lang']['editLimitError'];
    break;
  }

  $groups = $database->getGroupMemberships($entry['userID']);

  if ($entry['userID'] == $kga['user']['userID']) {
    $permissionName = 'ki_expenses-ownEntry-' . $action;
    if ($database->global_role_allows($kga['user']['globalRoleID'], $permissionName)) {
      return true;
    } else {
      Logger::logfile("missing global permission $permissionName for user " . $kga['user']['name'] . " to access expense");
      $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
      return false;
    }
  }

  $assignedOwnGroups = array_intersect($groups, $database->getGroupMemberships($kga['user']['userID']));
  
  if (count($assignedOwnGroups) > 0) {
    $permissionName = 'ki_expenses-otherEntry-ownGroup-' . $action;
    if ($database->checkMembershipPermission($kga['user']['userID'],$assignedOwnGroups, $permissionName)) {
      return true;
    } else {
      Logger::logfile("missing membership permission $permissionName of own group(s) " . implode(", ", $assignedOwnGroups) . " for user " . $kga['user']['name']);
      $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
      return false;
    }

  }

  $permissionName = 'ki_expenses-otherEntry-otherGroup-' . $action;
  if ($database->global_role_allows($kga['user']['globalRoleID'], $permissionName)) {
    return true;
  } else {
    Logger::logfile("missing global permission $permissionName for user " . $kga['user']['name'] . " to access expense");
    $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
    return false;
  }
  
}


include('private_db_layer_'.$kga['server_conn'].'.php');

switch ($axAction) {

    // ===========================================
    // = Load expense data from DB and return it =
    // ===========================================
    case 'reload_exp':
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

      // if no userfilter is set, set it to current user
      if (isset($kga['user']) && count($filterUsers) == 0)
        array_push($filterUsers,$kga['user']['userID']);
        
      if (isset($kga['customer']))
        $filterCustomers = array($kga['customer']['customerID']);

      $view->expenses= get_expenses($in,$out,$filterUsers,$filterCustomers,$filterProjects,1);
      $view->total = Format::formatCurrency(array_reduce($view->expenses, function($sum, $expense) { return $sum + $expense['multiplier'] * $expense['value']; }, 0));


      $ann = expenses_by_user($in,$out,$filterUsers,$filterCustomers,$filterProjects);
      $ann = Format::formatCurrency($ann);
      $view->user_annotations = $ann;

      // TODO: function for loops or convert it in template with new function
      $ann = expenses_by_customer($in,$out,$filterUsers,$filterCustomers,$filterProjects);
      $ann = Format::formatCurrency($ann);
      $view->customer_annotations = $ann;

      $ann = expenses_by_project($in,$out,$filterUsers,$filterCustomers,$filterProjects);
      $ann = Format::formatCurrency($ann);
      $view->project_annotations = $ann;

      $view->activity_annotations = array();

      if (isset($kga['user']))
        $view->hideComments = $database->user_get_preference('ui.showCommentsByDefault')!=1;
      else
        $view->hideComments = true;

      echo $view->render("expenses.php");
    break;

    // =======================================
    // = Erase expense entry via quickdelete =
    // =======================================
    case 'quickdelete':
      $errors = array();
      
      $data = expense_get($id);

      expenseAccessAllowed($data,'delete',$errors);

      if (count($errors) == 0) {
        expense_delete($id);
      }

      header('Content-Type: application/json;charset=utf-8');
      echo json_encode(array(
        'errors' => $errors));
    break;

    // =============================
    // = add / edit expense record =
    // =============================
    case 'add_edit_record':
        header('Content-Type: application/json;charset=utf-8');
        $errors = array();

        // determine action for permission check
        $action = 'add';
        if ($id)
          $action = 'edit';
        if (isset($_REQUEST['erase']))
          $action = 'delete';

        if ($id) {
          $data = expense_get($id);

          // check if editing or deleting with the old values would be allowed            
          if (!expenseAccessAllowed($data, $action, $errors)) {
            echo json_encode(array('errors'=>$errors));
            break;
          }
        }

          // delete now because next steps don't need to be taken for deleted entries
        if (isset($_REQUEST['erase'])) {
          expense_delete($id);
          echo json_encode(array('errors'=>$errors));
          break;
        }

        // get new data
        $data['projectID']    = $_REQUEST['projectID'];
        $data['designation']  = $_REQUEST['designation'];
        $data['comment']      = $_REQUEST['comment'];
        $data['commentType']  = $_REQUEST['commentType'];
        $data['refundable']   = getRequestBool('refundable');
        $data['multiplier']   = getRequestDecimal($_REQUEST['multiplier']);
        $data['value']        = getRequestDecimal($_REQUEST['edit_value']);
        $data['userID']       = $kga['user']['userID'];

        // parse new day and time
        $edit_day  = Format::expand_date_shortcut($_REQUEST['edit_day']);
        $edit_time = Format::expand_time_shortcut($_REQUEST['edit_time']);

        // validate day and time
        $new = "${edit_day}-${edit_time}";
        if (!Format::check_time_format($new)) {
            $errors[''] = $kga['lang']['TimeDateInputError'];
            break;
        }

        // convert to internal time format
        $new_time = convert_time_strings($new,$new);
        $data['timestamp'] = $new_time['in'];

        if (!is_numeric($data['projectID']))
          $errors['projectID'] = $kga['lang']['errorMessages']['noProjectSelected'];

        if (!is_numeric($data['multiplier']) || $data['multiplier'] <= 0)
          $errors['multiplier'] = $kga['lang']['errorMessages']['multiplierNegative'];

        expenseAccessAllowed($data,$action,$errors);

        if (count($errors) > 0) {
          echo json_encode(array('errors'=>$errors));
          break;
        }

        if ($id)
            expense_edit($id,$data);
        else
            expense_create($kga['user']['userID'],$data);

      
        echo json_encode(array('errors'=>$errors));
    break;

}
