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

    // ===================================================
    // = Load timesheet data (zef) from DB and return it =
    // ===================================================
    case 'reload_exp':
      $filters = explode('|',$axValue);
      if ($filters[0] == "")
        $filterUsr = array();
      else
        $filterUsr = explode(':',$filters[0]);

      if ($filters[1] == "")
        $filterKnd = array();
      else
        $filterKnd = explode(':',$filters[1]);

      if ($filters[2] == "")
        $filterPct = array();
      else
        $filterPct = explode(':',$filters[2]);

      // if no userfilter is set, set it to current user
      if (isset($kga['usr']) && count($filterUsr) == 0)
        array_push($filterUsr,$kga['usr']['usr_ID']);
        
      if (isset($kga['customer']))
        $filterKnd = array($kga['customer']['knd_ID']);

      $arr_exp = get_arr_exp($in,$out,$filterUsr,$filterKnd,$filterPct,1);
      if (count($arr_exp)>0) {
          $tpl->assign('arr_exp', $arr_exp);
      } else {
          $tpl->assign('arr_exp', 0);
      }
      $tpl->assign('total', "");


      $ann = get_arr_exp_usr($in,$out,$filterUsr,$filterKnd,$filterPct);
      $ann = formatCurrency($ann);
      $tpl->assign('usr_ann',$ann);

      // TODO: function for loops or convert it in template with new function
      $ann = get_arr_exp_knd($in,$out,$filterUsr,$filterKnd,$filterPct);
      $ann = formatCurrency($ann);
      $tpl->assign('knd_ann',$ann);

      $ann = get_arr_exp_pct($in,$out,$filterUsr,$filterKnd,$filterPct);
      $ann = formatCurrency($ann);
      $tpl->assign('pct_ann',$ann);

      $tpl->assign('evt_ann',array());

      $tpl->display("exp.tpl");
    break;

    // =======================================
    // = Erase expense entry via quickdelete =
    // =======================================
    case 'quickdelete':
      exp_delete_record($id);
      echo 1;
    break;

    // =========================
    // = add / edit zef record =
    // =========================
    case 'add_edit_record':
    if (!is_array($kga['usr']))
      break;
    
      $data['exp_pctID']        = $_REQUEST['pct_ID'];
      $data['exp_designation']  = $_REQUEST['designation'];
      $data['exp_multiplier']   = $_REQUEST['multiplier'];
      $data['exp_value']        = $_REQUEST['edit_value'];
      $data['exp_comment']      = $_REQUEST['comment'];
      $data['exp_comment_type'] = $_REQUEST['comment_type'];
      $data['exp_refundable']   = isset($_REQUEST['refundable']);
      $data['erase']            = isset($_REQUEST['erase']);


      if ($data['erase']) {
        // delete checkbox set ?
        // then the record is simply dropped and processing stops at this point
        exp_delete_record($id);
        break;
      }

      logfile(implode(" : ",$data));
    
      // check if the posted time values are possible
      $setTimeValue = 0; // 0 means the values are incorrect. now we check if this is true ...
        
      $edit_day  = expand_date_shortcut($_REQUEST['edit_day']);
      $edit_time = expand_time_shortcut($_REQUEST['edit_time']);
    
      $new = "${edit_day}-${edit_time}";
      if (!check_time_format($new)) {
        // if this is TRUE the values PASSED the test! 
        //$setTimeValue = 1;   
        break;
      }
      $new_time = convert_time_strings($new,$new);        
      $data['exp_timestamp'] = $new_time['in'];
      //logfile("new_time: " .serialize($new_time));

      $data['exp_multiplier'] = str_replace($kga['conf']['decimalSeparator'],'.',$data['exp_multiplier']);
      $data['exp_value'] = str_replace($kga['conf']['decimalSeparator'],'.',$data['exp_value']);
        
      if ($id) { // TIME RIGHT - NEW OR EDIT ?

        // TIME RIGHT - EDIT ENTRY
        logfile("exp_edit_record: " .$id);
        exp_edit_record($id,$data);
    
      } else {
          
        // TIME RIGHT - NEW ENTRY
        logfile("exp_create_record");
        exp_create_record($kga['usr']['usr_ID'],$data);
          
      }

    break;

}

?>
