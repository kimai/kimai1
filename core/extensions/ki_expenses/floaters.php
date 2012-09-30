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

  case "add_edit_record":  
    if (isset($kga['customer'])) die();  
    // ==============================================
    // = display edit dialog for timesheet record   =
    // ==============================================
    $selected = explode('|',$axValue);
    if ($id) {
      $expense = get_expense($id);
      $view->id = $id;
      $view->comment = $expense['comment'];
  
      $view->edit_day = date("d.m.Y",$expense['timestamp']);
  
      $view->edit_time = date("H:i:s",$expense['timestamp']);
  
      $view->multiplier = $expense['multiplier'];
  
      $view->edit_value = $expense['value'];
  
      $view->designation = $expense['designation'];

      // preselected
      $view->preselected_project = $expense['projectID'];
  
      $view->comment_active = $expense['commentType'];
      $view->refundable = $expense['refundable'];

    } else {
      
      $view->id = 0;
      
      $view->edit_day = date("d.m.Y");
  
      $view->edit_time = date("H:i:s");
  
      $view->multiplier = '1'.$kga['conf']['decimalSeparator'].'0';



    }
    
    $view->commentTypes = $commentTypes;

    // select for projects
    $view->projects = makeSelectBox("project",$kga['user']['groups']);

    // select for activities
    $view->activities = makeSelectBox("activity",$kga['user']['groups']);



    echo $view->render("floaters/add_edit_record.php"); 

    break;        

}

?>

    