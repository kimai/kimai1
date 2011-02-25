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
    
    // =========================
    // = record an event AGAIN =
    // =========================
    case 'record':
        if (isset($kga['customer'])) die();
        
        if (get_rec_state($kga['usr']['usr_ID'])) {
            stopRecorder($kga['usr']['usr_ID']);
        }
        
        $zefData = zef_get_data($id);

        $zefData['in'] = time();
        $zefData['out'] = 0;
        $zefData['diff'] = 0;

        // copied from check_zef_data and inverted assignments
        $zefData['pct_ID'] = $zefData['zef_pctID'];
        $zefData['evt_ID'] = $zefData['zef_evtID'];
        $zefData['zlocation'] = $zefData['zef_location'];
        $zefData['trackingnr'] = $zefData['zef_trackingnr'];
        $zefData['comment'] = $zefData['zef_comment'];
        $zefData['comment_type'] = $zefData['zef_comment_type'];
        $zefData['rate'] = $zefData['zef_rate'];
        $zefData['cleared'] = $zefData['zef_cleared'];

        $newZefId = zef_create_record($kga['usr']['usr_ID'],$zefData);
        
        $usrData = array();
        $usrData['lastRecord'] = $newZefId;
        $usrData['lastProject'] = $zefData['pct_ID'];
        $usrData['lastEvent'] = $zefData['evt_ID'];
        usr_edit($kga['usr']['usr_ID'], $usrData);
        
        
        $pctdata = pct_get_data($zefData['zef_pctID']);        
        $return =  'pct_name = "' . $pctdata['pct_name'] .'"; ';
        
        $return .=  'knd = "' . $pctdata['pct_kndID'] .'"; ';
        
        $knddata = knd_get_data($pctdata['pct_kndID']);        
        $return .=  'knd_name = "' . $knddata['knd_name'] .'"; ';
                
        $evtdata = evt_get_data($zefData['zef_evtID']);
        $return .= 'evt_name = "' . $evtdata['evt_name'] .'"; ';

        echo $return;
        // TODO return false if error
    break;

    // ==================
    // = stop recording =
    // ==================
    case 'stop':
        if (isset($kga['customer'])) die();
        stopRecorder($kga['usr']['usr_ID']);
        echo 1;
    break;

    // ===================================
    // = set comment for a running event =
    // ===================================
    case 'edit_running_project':
        if (isset($kga['customer'])) die();

        $last_event = get_event_last();

        zef_edit_pct(
            $last_event['zef_ID'],
            $_REQUEST['project']);
        echo 1;
    break;

    // ===================================
    // = set comment for a running event =
    // ===================================
    case 'edit_running_task':
        if (isset($kga['customer'])) die();

        $last_event = get_event_last();

        zef_edit_evt(
            $last_event['zef_ID'],
            $_REQUEST['task']);
        echo 1;
    break;

    // ===================================
    // = set comment for a running event =
    // ===================================
    case 'edit_running_comment':
        if (isset($kga['customer'])) die();

        zef_edit_comment(
            $axValue,
            $_REQUEST['comment_type'],
            $_REQUEST['comment']);
        echo 1;
    break;

    // =========================================
    // = Erase timesheet entry via quickdelete =
    // =========================================
    case 'quickdelete':
        zef_delete_record($id);
        echo 1;
    break;

    // ===============================================
    // = Get the best rate for the project and event =
    // ===============================================
    case 'bestFittingRate':
        if (isset($kga['customer'])) die();
        $rate = get_best_fitting_rate($kga['usr']['usr_ID'],$_REQUEST['project_id'],$_REQUEST['event_id']);
        if ($rate === false)
          echo -1;
        else
	  echo $rate;
    break;

    // ===============================================
    // = Get the best rate for the project and event =
    // ===============================================
    case 'reload_evt_options':
        if (isset($kga['customer'])) die();
        $arr_evt = get_arr_evt_by_pct($kga['usr']['usr_grp'],
              $_REQUEST['pct']);
        foreach ($arr_evt as $event) {
          if (!$event['evt_visible'])
            continue;
          echo '<option value="'.$event['evt_ID'].'">'.
          $event['evt_name'].'</option>\n';
        }
    break;

    // ===================================================
    // = Load timesheet data (zef) from DB and return it =
    // ===================================================
    case 'reload_zef':
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

        if ($filters[3] == "")
          $filterEvt = array();
        else
          $filterEvt = explode(':',$filters[3]);

        // if no userfilter is set, set it to current user
        if (isset($kga['usr']) && count($filterUsr) == 0)
          array_push($filterUsr,$kga['usr']['usr_ID']);
          
        if (isset($kga['customer']))
          $filterKnd = array($kga['customer']['knd_ID']);

        $arr_zef = get_arr_zef($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt,1);
        if (count($arr_zef)>0) {
            $tpl->assign('arr_zef', $arr_zef);
        } else {
            $tpl->assign('arr_zef', 0);
        }
        $tpl->assign('total', formatDuration(get_zef_time($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt)));

        $ann = get_arr_time_usr($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        formatAnnotations($ann);
        $tpl->assign('usr_ann',$ann);
        
        $ann = get_arr_time_knd($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        formatAnnotations($ann);
        $tpl->assign('knd_ann',$ann);

        $ann = get_arr_time_pct($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        formatAnnotations($ann);
        $tpl->assign('pct_ann',$ann);

        $ann = get_arr_time_evt($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        formatAnnotations($ann);
        $tpl->assign('evt_ann',$ann);

        $tpl->display("zef.tpl");
    break;
    
    
    // =========================
    // = add / edit zef record =
    // =========================
    case 'add_edit_record': 
      if (isset($kga['customer'])) die();
  
      $data['pct_ID']          = $_REQUEST['pct_ID'];
      $data['evt_ID']          = $_REQUEST['evt_ID'];
      $data['zlocation']       = $_REQUEST['zlocation'];
      $data['trackingnr']      = $_REQUEST['trackingnr'];
      $data['comment']         = $_REQUEST['comment'];
      $data['comment_type']    = $_REQUEST['comment_type'];
      $data['erase']           = isset($_REQUEST['erase']);
      $data['rate']            = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['rate']);
      $data['cleared']         = isset($_REQUEST['cleared']);

      if ($data['erase']) {
        // delete checkbox set ?
        // then the record is simply dropped and processing stops at this point
          zef_delete_record($id);
          break;
      }
      logfile(implode(" : ",$data));

      // check if the posted time values are possible
      $setTimeValue = 0; // 0 means the values are incorrect. now we check if this is true ...
      
      $edit_in_day       = expand_date_shortcut($_REQUEST['edit_in_day']);
      $edit_out_day      = expand_date_shortcut($_REQUEST['edit_out_day']);
      $edit_in_time   = expand_time_shortcut($_REQUEST['edit_in_time']);
      $edit_out_time  = expand_time_shortcut($_REQUEST['edit_out_time']);
      $new_in  = "${edit_in_day}-${edit_in_time}";
      $new_out = "${edit_out_day}-${edit_out_time}";  
      
      if (check_time_format($new_in) && check_time_format($new_out)) {
          // if this is TRUE the values PASSED the test! 
          $setTimeValue = 1;   
      }
      else
        break;
        
      $new_time = convert_time_strings($new_in,$new_out);
      
      logfile("new_time: " .serialize($new_time));
      
      // if the difference between in and out value is zero or below this can't be correct ...
      
      // TIME WRONG - NEW ENTRY
      
      
      if (!$new_time['diff'] && !$id) {
          // if this is an ADD record dialog it makes no sense to create the record
          // when it doesn't have any TIME attached ... so this stops the processing.
          // TODO: throw a warning message when this happens ...
          break;
      }
      
      // TIME WRONG - EDIT ENTRY
      
      if (!$new_time['diff']) {
          // obviously this is an edit of an existing record. but still it contains no correct timespan.
          // here somebody didn't mean to change the timespace like that. so we leave the timespan as is.
          // TODO: throw a warning message when this happens ...
          $data['in']   = 0;
          $data['out']  = 0;
          $data['diff'] = 0;
          // we send zeros instead of unix timestamps to the db-layer 
          zef_edit_record($id,$data);
          break; 
          
      } else {
          
          // TIME RIGHT !
                                                                              
          $data['in']   = $new_time['in'];
          $data['out']  = $new_time['out'];
          $data['diff'] = $new_time['diff'];
              
          if ($id) { // TIME RIGHT - NEW OR EDIT ?

              // TIME RIGHT - EDIT ENTRY
              logfile("zef_edit_record: " .$id);
              check_zef_data($id,$data);
          
          } else {
              
              // TIME RIGHT - NEW ENTRY
              logfile("zef_create_record");
              zef_create_record($kga['usr']['usr_ID'],$data);
              
          }
          
          
      }
      
    break;

}

?>
