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

        if ($database->get_rec_state($kga['usr']['usr_ID'])) {
            $database->stopRecorder($kga['usr']['usr_ID']);
        }

        $zefData = $database->zef_get_data($id);

        $zefData['in'] = time();
        $zefData['out'] = 0;
        $zefData['diff'] = 0;

        // copied from check_zef_data and inverted assignments
        $zefData['pct_ID'] = $zefData['zef_pctID'];
        $zefData['evt_ID'] = $zefData['zef_evtID'];
        $zefData['zlocation'] = $zefData['zef_location'];
        $zefData['trackingnr'] = $zefData['zef_trackingnr'];
        $zefData['description'] = $zefData['zef_description'];
        $zefData['comment'] = $zefData['zef_comment'];
        $zefData['comment_type'] = $zefData['zef_comment_type'];
        $zefData['rate'] = $zefData['zef_rate'];
        $zefData['cleared'] = $zefData['zef_cleared'];
        //fcw: status hatte hier noch gefehlt
        $zefData['status'] = $zefData['zef_status'];
        $zefData['usr_ID'] = $kga['usr']['usr_ID'];

        $newZefId = $database->zef_create_record($zefData);

        $usrData = array();
        $usrData['lastRecord'] = $newZefId;
        $usrData['lastProject'] = $zefData['pct_ID'];
        $usrData['lastEvent'] = $zefData['evt_ID'];
        $database->usr_edit($kga['usr']['usr_ID'], $usrData);


        $pctdata = $database->pct_get_data($zefData['zef_pctID']);
        $return =  'pct_name = "' . $pctdata['pct_name'] .'"; ';

        $return .=  'knd = "' . $pctdata['pct_kndID'] .'"; ';

        $knddata = $database->knd_get_data($pctdata['pct_kndID']);
        $return .=  'knd_name = "' . $knddata['knd_name'] .'"; ';

        $evtdata = $database->evt_get_data($zefData['zef_evtID']);
        $return .= 'evt_name = "' . $evtdata['evt_name'] .'"; ';

        echo $return;
        // TODO return false if error
    break;

    // ==================
    // = stop recording =
    // ==================
    case 'stop':
        if (isset($kga['customer'])) die();
        $database->stopRecorder($kga['usr']['usr_ID']);
        echo 1;
    break;

    // ===================================
    // = set comment for a running event =
    // ===================================
    case 'edit_running_project':
        if (isset($kga['customer'])) die();

        $last_event = $database->get_event_last();

        $database->zef_edit_pct(
            $last_event['zef_ID'],
            $_REQUEST['project']);
        echo 1;
    break;

    // ===================================
    // = set comment for a running event =
    // ===================================
    case 'edit_running_task':
        if (isset($kga['customer'])) die();

        $last_event = $database->get_event_last();

        $database->zef_edit_evt(
            $last_event['zef_ID'],
            $_REQUEST['task']);
        echo 1;
    break;

    // ===================================
    // = set comment for a running event =
    // ===================================
    case 'edit_running_comment':
        if (isset($kga['customer'])) die();

        $database->zef_edit_comment(
            $axValue,
            $_REQUEST['comment_type'],
            $_REQUEST['comment']);
        echo 1;
    break;

    // ===================================
    // = set time for a running event =
    // ===================================
    case 'edit_running_starttime':
        if (isset($kga['customer'])) die();
            // fcw: 2011-07-23: Neue Startzeit aus heutigem Datum holen und aus dem REQUEST.
            // Schon fuer convert_time_strings (aus /includes/func.php) passend machen (als String, z.B.: "23.07.2011-16:25:57")
            $new_starttime = Format::expand_date_shortcut($_REQUEST['startday']).'-'.Format::expand_time_shortcut($_REQUEST['starttime']);
            // UNIX-Time holen, zwei Mal den selben Parameter, nur einer wird gebraucht
            $new_time = convert_time_strings($new_starttime, $new_starttime);
            // neue Startuhrzeit in die DB schreiben
            $database->zef_edit_starttime(
                $axValue,
                $new_time['in']);
        echo $new_time['in'];
    break;

    // =========================================
    // = Erase timesheet entry via quickdelete =
    // =========================================
    case 'quickdelete':
        $database->zef_delete_record($id);
        echo 1;
    break;

    // ===============================================
    // = Get the best rate for the project and event =
    // ===============================================
    case 'bestFittingRates':
        if (isset($kga['customer'])) die();

        $data = array(
          'hourlyRate' => $database->get_best_fitting_rate($kga['usr']['usr_ID'],$_REQUEST['project_id'],$_REQUEST['event_id']),
          'fixedRate' => $database->get_best_fitting_fixed_rate($_REQUEST['project_id'],$_REQUEST['event_id'])
        );
        echo json_encode($data);
    break;


    // ===============================================
    // = Get the new budget data after changing project or event =
    // ===============================================
    case 'budgets':
        if (isset($kga['customer'])) die();
        $zefData = $database->zef_get_data($_REQUEST['zef_id']);
        // we subtract the used data in case the event is the same as in the db, otherwise
        // it would get counted twice. For all aother cases, just set the values to 0
        // so we don't subtract too much
        if($zefData['zef_evtID'] != $_REQUEST['event_id'] || $zefData['zef_pctID'] != $_REQUEST['project_id']) {
        	$zefData['zef_budget'] = 0;
        	$zefData['zef_approved'] = 0;
        	$zefData['zef_rate'] = 0;
        }
        $data = array(
          'eventBudgets' => $database->get_evt_budget($_REQUEST['project_id'],$_REQUEST['event_id']),
          'eventUsed' => $database->get_budget_used($_REQUEST['project_id'],$_REQUEST['event_id']),
          'zefData' => $zefData
        );
        echo json_encode($data);
    break;

    // ===========================================
    // = Get all rates for the project and event =
    // ===========================================
    case 'allFittingRates':
        if (isset($kga['customer'])) die();

        $rates = $database->allFittingRates($kga['usr']['usr_ID'],$_REQUEST['project'],$_REQUEST['task']);
        $processedData = array();

        if ($rates !== false)
          foreach ($rates as $rate) {
            $line = Format::formatCurrency($rate['rate']);

            $setFor = array(); // contains the list of "types" for which this rate was set
            if ($rate['user_id'] != null)
              $setFor[] = $kga['lang']['username'];
            if ($rate['project_id'] != null)
              $setFor[] =  $kga['lang']['pct'];
            if ($rate['event_id'] != null)
              $setFor[] =  $kga['lang']['evt'];

            if (count($setFor) != 0)
              $line .= ' ('.implode($setFor,', ').')';

            $processedData[] = array('value'=>$rate['rate'], 'desc'=>$line);
          }

        echo json_encode($processedData);
    break;

    // ===========================================
    // = Get all rates for the project and event =
    // ===========================================
    case 'allFittingFixedRates':
        if (isset($kga['customer'])) die();

        $rates = $database->allFittingFixedRates($_REQUEST['project'],$_REQUEST['task']);
        $processedData = array();

        if ($rates !== false)
          foreach ($rates as $rate) {
            $line = Format::formatCurrency($rate['rate']);

            $setFor = array(); // contains the list of "types" for which this rate was set
            if ($rate['project_id'] != null)
              $setFor[] =  $kga['lang']['pct'];
            if ($rate['event_id'] != null)
              $setFor[] =  $kga['lang']['evt'];

            if (count($setFor) != 0)
              $line .= ' ('.implode($setFor,', ').')';

            $processedData[] = array('value'=>$rate['rate'], 'desc'=>$line);
          }

        echo json_encode($processedData);
    break;

    // ===============================================
    // = Get the best rate for the project and event =
    // ===============================================
    case 'reload_evt_options':
        if (isset($kga['customer'])) die();
        $arr_evt = $database->get_arr_evt_by_pct($_REQUEST['pct'],$kga['usr']['groups']);
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

        $arr_zef = $database->get_arr_zef($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt,1);
        if (count($arr_zef)>0) {
            $tpl->assign('arr_zef', $arr_zef);
        } else {
            $tpl->assign('arr_zef', 0);
        }
        $tpl->assign('total', Format::formatDuration($database->get_zef_time($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt)));

        $ann = $database->get_arr_time_usr($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        Format::formatAnnotations($ann);
        $tpl->assign('usr_ann',$ann);

        $ann = $database->get_arr_time_knd($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        Format::formatAnnotations($ann);
        $tpl->assign('knd_ann',$ann);

        $ann = $database->get_arr_time_pct($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        Format::formatAnnotations($ann);
        $tpl->assign('pct_ann',$ann);

        $ann = $database->get_arr_time_evt($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        Format::formatAnnotations($ann);
        $tpl->assign('evt_ann',$ann);

        if (isset($kga['usr']))
          $tpl->assign('hideComments',$database->usr_get_preference('ui.showCommentsByDefault')!=1);
        else
          $tpl->assign('hideComments',true);

        if (isset($kga['usr']))
          $tpl->assign('showOverlapLines',$database->usr_get_preference('ui.hideOverlapLines')!=1);
        else
          $tpl->assign('showOverlapLines',false);

        $tpl->display("zef.tpl");
    break;


    // =========================
    // = add / edit zef record =
    // =========================
    case 'add_edit_record':
      if (isset($kga['customer'])) die();

      if ($id) {
        $data = $database->zef_get_data($id);
        if ($kga['conf']['editLimit'] != "-" && time()-$data['zef_out'] > $kga['conf']['editLimit']) {
          echo json_encode(array('result'=>'error','message'=>$kga['lang']['editLimitError']));
          return;
        }
      }

      if (isset($_REQUEST['erase'])) {
        // delete checkbox set ?
        // then the record is simply dropped and processing stops at this point
          $database->zef_delete_record($id);
          echo json_encode(array('result'=>'ok'));
          break;
      }

      $data['pct_ID']          = $_REQUEST['pct_ID'];
      $data['evt_ID']          = $_REQUEST['evt_ID'];
      $data['zlocation']       = $_REQUEST['zlocation'];
      $data['trackingnr']      = $_REQUEST['trackingnr'];
      $data['description']     = $_REQUEST['description'];
      $data['comment']         = $_REQUEST['comment'];
      $data['comment_type']    = $_REQUEST['comment_type'];
      $data['rate']            = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['rate']);
      $data['fixed_rate']      = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['fixed_rate']);
      $data['cleared']         = isset($_REQUEST['cleared']);
      $data['status']          = $_REQUEST['status'];
      $data['billable']        = $_REQUEST['billable'];
      $data['budget']          = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['budget']);
      $data['approved']        = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['approved']);

      // only take the given user id if it is in the list of watchable users
      $users = $database->get_arr_watchable_users($kga['usr']);
      foreach ($users as $user) {
        if ($user['usr_ID'] == $_REQUEST['user']) {
          $data['zef_usrID'] = $user['usr_ID'];
          break;
        }
      }

      if (!isset($data['zef_usrID']))
        $data['zef_usrID'] = $kga['usr']['usr_ID'];

      // check if the posted time values are possible
      $setTimeValue = 0; // 0 means the values are incorrect. now we check if this is true ...

      $edit_in_day       = Format::expand_date_shortcut($_REQUEST['edit_in_day']);
      $edit_out_day      = Format::expand_date_shortcut($_REQUEST['edit_out_day']);
      $edit_in_time   = Format::expand_time_shortcut($_REQUEST['edit_in_time']);
      $edit_out_time  = Format::expand_time_shortcut($_REQUEST['edit_out_time']);
      $new_in  = "${edit_in_day}-${edit_in_time}";
      $new_out = "${edit_out_day}-${edit_out_time}";

      if (Format::check_time_format($new_in) && Format::check_time_format($new_out)) {
          // if this is TRUE the values PASSED the test!
          $setTimeValue = 1;
      }
      else {
        echo json_encode(array('result'=>'error','message'=>$kga['lang']['TimeDateInputError']));
        break;
      }

      $new_time = convert_time_strings($new_in,$new_out);

      // if the difference between in and out value is zero or below this can't be correct ...

      // TIME WRONG - NEW ENTRY

      if ($kga['conf']['editLimit'] != "-" && time()-$new_time['out'] > $kga['conf']['editLimit']) {
        echo json_encode(array('result'=>'error','message'=>$kga['lang']['editLimitError']));
        return;
      }


      if (!$new_time['diff'] && !$id) {
          // if this is an ADD record dialog it makes no sense to create the record
          // when it doesn't have any TIME attached ... so this stops the processing.
          // TODO: throw a warning message when this happens ...
          echo json_encode(array('result'=>'error','message'=>$kga['lang']['TimeDateInputError']));
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
          $database->zef_edit_record($id,$data);
          echo json_encode(array('result'=>'ok'));
          break;

      } else {

          // TIME RIGHT !

          $data['in']   = $new_time['in'];
          $data['out']  = $new_time['out'];
          $data['diff'] = $new_time['diff'];

          if ($id) { // TIME RIGHT - NEW OR EDIT ?

              // TIME RIGHT - EDIT ENTRY
              Logger::logfile("zef_edit_record: " .$id);
              check_zef_data($id,$data);

          } else {

              // TIME RIGHT - NEW ENTRY
              Logger::logfile("zef_create_record");
              $database->zef_create_record($data);

          }


      }
      echo json_encode(array('result'=>'ok'));

    break;

}

?>