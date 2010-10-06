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
      $exp_entry = get_entry_exp($id);
      $tpl->assign('id', $id);
      $tpl->assign('comment', $exp_entry['exp_comment']);
  
      $tpl->assign('edit_day', date("d.m.Y",$exp_entry['exp_timestamp']));
  
      $tpl->assign('edit_time',  date("H:i:s",$exp_entry['exp_timestamp']));
  
      $tpl->assign('multiplier',  $exp_entry['exp_multiplier']);
  
      $tpl->assign('edit_value',  $exp_entry['exp_value']);
  
      $tpl->assign('designation', $exp_entry['exp_designation']);

      // preselected
      $tpl->assign('pres_pct', $exp_entry['pct_ID']);
  
      $tpl->assign('comment_active', $exp_entry['exp_comment_type']);
      $tpl->assign('refundable', $exp_entry['exp_refundable']);

    } else {
      
      $tpl->assign('id', 0);
      
      $tpl->assign('edit_day', date("d.m.Y"));
  
      $tpl->assign('edit_time',  date("H:i:s"));
  
      $tpl->assign('multiplier',  '1'.$kga['conf']['decimalSeparator'].'0');



    }
    
    $tpl->assign('comment_types', $comment_types);
    $tpl->assign('comment_values', array('0','1','2'));

    // select for projects
    $sel = makeSelectBox("pct",$kga['usr']['usr_grp']);
    $tpl->assign('sel_pct_names', $sel[0]);
    $tpl->assign('sel_pct_IDs',   $sel[1]);

    // select for events
    $sel = makeSelectBox("evt",$kga['usr']['usr_grp']);
    $tpl->assign('sel_evt_names', $sel[0]);
    $tpl->assign('sel_evt_IDs',   $sel[1]);



    $tpl->display("add_edit_record.tpl"); 

    break;        

}

?>

    