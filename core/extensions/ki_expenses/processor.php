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

      if ($filters[1] == "")
        $filterCustomers = array();
      else
        $filterCustomers = explode(':',$filters[1]);

      if ($filters[2] == "")
        $filterProjects = array();
      else
        $filterProjects = explode(':',$filters[2]);

      // if no userfilter is set, set it to current user
      if (isset($kga['user']) && count($filterUsers) == 0)
        array_push($filterUsers,$kga['user']['userID']);
        
      if (isset($kga['customer']))
        $filterCustomers = array($kga['customer']['customerID']);

      $expenses = get_expenses($in,$out,$filterUsers,$filterCustomers,$filterProjects,1);
      if (count($expenses)>0) {
          $view->expenses = $expenses;
      } else {
          $view->expenses = 0;
      }
      $view->total = "";


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
      expense_delete($id);
      echo 1;
    break;

    // =============================
    // = add / edit expense record =
    // =============================
    case 'add_edit_record':
        if (!is_array($kga['user'])) {
            break;
        }

        if ($id) {
            $data = expense_get($id);
            if ($kga['conf']['editLimit'] != "-" && time()-$data['timestamp'] > $kga['conf']['editLimit']) {
              echo json_encode(array('result'=>'error','message'=>$kga['lang']['editLimitError']));
              return;
            }
        }

        $data['projectID']    = $_REQUEST['projectID'];
        $data['designation']  = $_REQUEST['designation'];
        $data['multiplier']   = $_REQUEST['multiplier'];
        $data['value']        = $_REQUEST['edit_value'];
        $data['comment']      = $_REQUEST['comment'];
        $data['commentType']  = $_REQUEST['commentType'];
        $data['refundable']   = getRequestBool('refundable');
        $data['erase']        = getRequestBool('erase');

        // delete checkbox set ? the record is dropped and processing stops
        if ($id && $data['erase']) {
            expense_delete($id);
            echo json_encode(array('result'=>'ok'));
            break;
        }

        // check if the posted time values are possible
        $setTimeValue = 0; // 0 means the values are incorrect. now we check if this is true ...

        $edit_day  = Format::expand_date_shortcut($_REQUEST['edit_day']);
        $edit_time = Format::expand_time_shortcut($_REQUEST['edit_time']);

        $new = "${edit_day}-${edit_time}";
        if (!Format::check_time_format($new)) {
            // if this is TRUE the values PASSED the test!
            //$setTimeValue = 1;
            echo json_encode(array('result'=>'error','message'=>$kga['lang']['TimeDateInputError']));
            break;
        }
        $new_time = convert_time_strings($new,$new);

        if ($kga['conf']['editLimit'] != "-" && time()-$new_time['in'] > $kga['conf']['editLimit']) {
            echo json_encode(array('result'=>'error','message'=>$kga['lang']['editLimitError']));
            return;
        }

        $data['timestamp'] = $new_time['in'];
        //Logger::logfile("new_time: " .serialize($new_time));

        $data['multiplier'] = str_replace($kga['conf']['decimalSeparator'],'.',$data['multiplier']);
        $data['value'] = str_replace($kga['conf']['decimalSeparator'],'.',$data['value']);

        if ($id) {
            // TIME RIGHT - EDIT ENTRY
            Logger::logfile("expense_edit: " .$id);
            expense_edit($id,$data);
        } else {
            // TIME RIGHT - NEW ENTRY
            Logger::logfile("expense_create");
            expense_create($kga['user']['userID'],$data);
        }
        echo json_encode(array('result'=>'ok'));

    break;

}
