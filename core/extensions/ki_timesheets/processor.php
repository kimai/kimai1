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

        $zefData = $database->timeSheet_get_data($id);

        $zefData['start'] = time();
        $zefData['end'] = 0;
        $zefData['duration'] = 0;

        // copied from check_zef_data and inverted assignments
        $zefData['projectID'] = $zefData['projectID'];
        $zefData['activityID'] = $zefData['activityID'];
        $zefData['zlocation'] = $zefData['location'];
        $zefData['trackingNumber'] = $zefData['trackingNumber'];
        $zefData['description'] = $zefData['description'];
        $zefData['comment'] = $zefData['comment'];
        $zefData['commentType'] = $zefData['commentType'];
        $zefData['rate'] = $zefData['rate'];
        $zefData['cleared'] = $zefData['cleared'];
        //fcw: status hatte hier noch gefehlt
        $zefData['status'] = $zefData['status'];
        $zefData['userID'] = $kga['usr']['userID'];

        $newZefId = $database->timeEntry_create($zefData);

        $usrData = array();
        $usrData['lastRecord'] = $newZefId;
        $usrData['lastProject'] = $zefData['projectID'];
        $usrData['lastActivity'] = $zefData['activityID'];
        $database->user_edit($kga['usr']['userID'], $usrData);


        $pctdata = $database->project_get_data($zefData['projectID']);
        $return =  'pct_name = "' . $pctdata['name'] .'"; ';

        $return .=  'knd = "' . $pctdata['customerID'] .'"; ';

        $knddata = $database->customer_get_data($pctdata['customerID']);
        $return .=  'knd_name = "' . $knddata['name'] .'"; ';

        $evtdata = $database->activity_get_data($zefData['activityID']);
        $return .= 'evt_name = "' . $evtdata['name'] .'"; ';
        
        $return .= "currentRecording = $newZefId; ";

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

    // ===================================
    // = set comment for a running event =
    // ===================================
    case 'edit_running_project':
        if (isset($kga['customer'])) die();

        $database->timeEntry_edit_project(
            $_REQUEST['id'],
            $_REQUEST['project']);
        echo 1;
    break;

    // ===================================
    // = set comment for a running event =
    // ===================================
    case 'edit_running_task':
        if (isset($kga['customer'])) die();

        $database->timeEntry_edit_activity(
            $_REQUEST['id'],
            $_REQUEST['task']);
        echo 1;
    break;

    // =========================================
    // = Erase timesheet entry via quickdelete =
    // =========================================
    case 'quickdelete':
        $database->timeEntry_delete($id);
        echo 1;
    break;

    // ===============================================
    // = Get the best rate for the project and event =
    // ===============================================
    case 'bestFittingRates':
        if (isset($kga['customer'])) die();

        $data = array(
          'hourlyRate' => $database->get_best_fitting_rate($kga['usr']['userID'],$_REQUEST['project_id'],$_REQUEST['event_id']),
          'fixedRate' => $database->get_best_fitting_fixed_rate($_REQUEST['project_id'],$_REQUEST['event_id'])
        );
        echo json_encode($data);
    break;


    // ===============================================
    // = Get the new budget data after changing project or event =
    // ===============================================
    case 'budgets':
        if (isset($kga['customer'])) die();
        $zefData = $database->timeSheet_get_data($_REQUEST['zef_id']);
        // we subtract the used data in case the event is the same as in the db, otherwise
        // it would get counted twice. For all aother cases, just set the values to 0
        // so we don't subtract too much
        if($zefData['activityID'] != $_REQUEST['event_id'] || $zefData['projectID'] != $_REQUEST['project_id']) {
        	$zefData['budget'] = 0;
        	$zefData['approved'] = 0;
        	$zefData['rate'] = 0;
        }
        $data = array(
          'eventBudgets' => $database->get_activity_budget($_REQUEST['project_id'],$_REQUEST['event_id']),
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

        $rates = $database->allFittingRates($kga['usr']['userID'],$_REQUEST['project'],$_REQUEST['task']);
        $processedData = array();

        if ($rates !== false)
          foreach ($rates as $rate) {
            $line = Format::formatCurrency($rate['rate']);

            $setFor = array(); // contains the list of "types" for which this rate was set
            if ($rate['userID'] != null)
              $setFor[] = $kga['lang']['username'];
            if ($rate['projectID'] != null)
              $setFor[] =  $kga['lang']['pct'];
            if ($rate['eventID'] != null)
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
            if ($rate['projectID'] != null)
              $setFor[] =  $kga['lang']['pct'];
            if ($rate['eventID'] != null)
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
        $arr_evt = $database->get_arr_activities_by_project($_REQUEST['pct'],$kga['usr']['groups']);
        foreach ($arr_evt as $event) {
          if (!$event['visible'])
            continue;
          echo '<option value="'.$event['activityID'].'">'.
          $event['name'].'</option>\n';
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
          array_push($filterUsr,$kga['usr']['userID']);

        if (isset($kga['customer']))
          $filterKnd = array($kga['customer']['customerID']);

        $arr_zef = $database->get_arr_timeSheet($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt,1);
        if (count($arr_zef)>0) {
            $tpl->assign('arr_zef', $arr_zef);
        } else {
            $tpl->assign('arr_zef', 0);
        }
        $tpl->assign('total', Format::formatDuration($database->get_duration($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt)));

        $ann = $database->get_arr_time_users($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        Format::formatAnnotations($ann);
        $tpl->assign('usr_ann',$ann);

        $ann = $database->get_arr_time_customers($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        Format::formatAnnotations($ann);
        $tpl->assign('knd_ann',$ann);

        $ann = $database->get_arr_time_projects($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        Format::formatAnnotations($ann);
        $tpl->assign('pct_ann',$ann);

        $ann = $database->get_arr_time_activities($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        Format::formatAnnotations($ann);
        $tpl->assign('evt_ann',$ann);

        if (isset($kga['usr']))
          $tpl->assign('hideComments',$database->user_get_preference('ui.showCommentsByDefault')!=1);
        else
          $tpl->assign('hideComments',true);

        if (isset($kga['usr']))
          $tpl->assign('showOverlapLines',$database->user_get_preference('ui.hideOverlapLines')!=1);
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
      $data['status']          = $_REQUEST['status'];
      $data['billable']        = $_REQUEST['billable'];
      $data['budget']          = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['budget']);
      $data['approved']        = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['approved']);

      // only take the given user id if it is in the list of watchable users
      $users = $database->get_arr_watchable_users($kga['usr']);
      foreach ($users as $user) {
        if ($user['userID'] == $_REQUEST['user']) {
          $data['userID'] = $user['userID'];
          break;
        }
      }

      if (!isset($data['userID']))
        $data['userID'] = $kga['usr']['userID'];

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
          check_zef_data($id,$data);

      } else {

          // TIME RIGHT - NEW ENTRY
          Logger::logfile("timeEntry_create");
          $database->timeEntry_create($data);
      }

      echo json_encode(array('result'=>'ok'));

    break;

}

?>