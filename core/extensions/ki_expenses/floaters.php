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
$dir_templates = "templates/floaters/";
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
      $tpl->assign('id', $id);
      $tpl->assign('comment', $expense['comment']);
  
      $tpl->assign('edit_day', date("d.m.Y",$expense['timestamp']));
  
      $tpl->assign('edit_time',  date("H:i:s",$expense['timestamp']));
  
      $tpl->assign('multiplier',  $expense['multiplier']);
  
      $tpl->assign('edit_value',  $expense['value']);
  
      $tpl->assign('designation', $expense['designation']);

      // preselected
      $tpl->assign('preselected_project', $expense['projectID']);
  
      $tpl->assign('comment_active', $expense['commentType']);
      $tpl->assign('refundable', $expense['refundable']);

    } else {
      
      $tpl->assign('id', 0);
      
      $tpl->assign('edit_day', date("d.m.Y"));
  
      $tpl->assign('edit_time',  date("H:i:s"));
  
      $tpl->assign('multiplier',  '1'.$kga['conf']['decimalSeparator'].'0');



    }
    
    $tpl->assign('commentTypes', $commentTypes);
    $tpl->assign('commentValues', array('0','1','2'));

    // select for projects
    $sel = makeSelectBox("project",$kga['user']['groups']);
    $tpl->assign('projectNames', $sel[0]);
    $tpl->assign('projectIDs',   $sel[1]);

    // select for activities
    $sel = makeSelectBox("activity",$kga['user']['groups']);
    $tpl->assign('activityNames', $sel[0]);
    $tpl->assign('activityIDs',   $sel[1]);



    $tpl->display("add_edit_record.tpl"); 

    break;        

}

?>

    