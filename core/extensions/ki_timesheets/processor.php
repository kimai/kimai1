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
 * along with Kimai; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
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
    
    // ==========================
    // = record new event AGAIN =
    // ==========================
    case 'record':
        
        if (get_rec_state($usr['usr_ID'])) {
            stopRecorder($usr['usr_ID']);
        }
        
        // IDs -> pctID|evtID
        //
        $IDs = explode('|',$axValue);
        startRecorder($IDs[0],$IDs[1],$usr['usr_ID']);
        
        $pctdata = pct_get_data($IDs[0]);        
        $return =  "pct_name = '" . $pctdata['pct_name'] ."'; ";
        
        $knddata = knd_get_data($pctdata['pct_kndID']);        
        $return .=  "knd_name = '" . $knddata['knd_name'] ."'; ";
                
        $evtdata = evt_get_data($IDs[1]);
        $return .= "evt_name = '" . $evtdata['evt_name'] ."'; ";

        echo $return;
        // TODO return false if error
    break;

    // ==================
    // = stop recording =
    // ==================
    case 'stop':
        stopRecorder($usr['usr_ID']);
        echo 1;
    break;

    // =========================================
    // = Erase timesheet entry via quickdelete =
    // =========================================
    case 'quickdelete':
        zef_delete_record($id);
        echo 1;
    break;

    // ===================================================
    // = Load timesheet data (zef) from DB and return it =
    // ===================================================
    case 'reload_zef':
        $arr_zef = get_arr_zef($usr['usr_ID'],$in,$out,1);
        if (count($arr_zef)>0) {
            $tpl->assign('arr_zef', $arr_zef);
        } else {
            $tpl->assign('arr_zef', 0);
        }
        $tpl->assign('total', intervallApos(get_zef_time($usr['usr_ID'],$in,$out)));
        $tpl->display("zef.tpl");
    break;

    // ==================================================================================================
    // = load customer table (knd) from DB - returned table includes time summary for current timespace =
    // ==================================================================================================
    case 'reload_knd':
        $arr_knd = get_arr_knd_with_time($usr['usr_grp'],$usr['usr_ID'],$in,$out);
        if (count($arr_knd)>0) {
            $tpl->assign('arr_knd', $arr_knd);
        } else {
            $tpl->assign('arr_knd', 0);
        }
        $tpl->display("knd.tpl");
    break;

    // =================================================================================================
    // = load project table (pct) from DB - returned table includes time summary for current timespace =
    // =================================================================================================
    case 'reload_pct':
        $arr_pct = get_arr_pct_with_time($usr['usr_grp'],$usr['usr_ID'],$in,$out);
        if (count($arr_pct)>0) {
            $tpl->assign('arr_pct', $arr_pct);
        } else {
            $tpl->assign('arr_pct', 0);
        }
        $tpl->display("pct.tpl");
    break;

    // ================================================================================================
    // = load events table (evt) from DB - returned table includes time summary for current timespace =
    // ================================================================================================
    case 'reload_evt':
        $arr_evt = get_arr_evt_with_time($usr['usr_grp'],$usr['usr_ID'],$in,$out);
        if (count($arr_evt)>0) {
            $tpl->assign('arr_evt', $arr_evt);
        } else {
            $tpl->assign('arr_evt', 0);
        }
        $tpl->display("evt.tpl");
    break;
    
    
    // =========================
    // = add / edit zef record =
    // =========================
    case 'add_edit_record':   
    	$data['pct_ID']          = $_REQUEST['pct_ID'];
    	$data['evt_ID']          = $_REQUEST['evt_ID'];
    	$data['zlocation']       = $_REQUEST['zlocation'];
    	$data['trackingnr']      = $_REQUEST['trackingnr'];
    	$data['comment']         = $_REQUEST['comment'];
    	$data['comment_type']    = $_REQUEST['comment_type'];
    	$data['erase']           = $_REQUEST['erase'];
    	
        if ($data['erase']) {
    	    // delete checkbox set ?
    	    // then the record is simply dropped and processing stops at this point
            zef_delete_record($id);
            break;
        }
    	                                                                        logfile(implode(" : ",$data));
    	
    	// check if the posted time values are possible
        $setTimeValue = 0; // 0 means the values are incorrect. now we check if this is true ...
        
        $edit_day       = expand_date_shortcut($_REQUEST['edit_day']);
        $edit_in_time   = expand_time_shortcut($_REQUEST['edit_in_time']);
        $edit_out_time  = expand_time_shortcut($_REQUEST['edit_out_time']);
                                                                                // logfile("edit_in: ".$edit_in);
                                                                                // logfile("edit_out: ".$edit_out);
                                                                                // logfile("edit_in_time: ".$edit_in_time);
                                                                                // logfile("edit_out_time: ".$edit_out_time);
        $new_in  = "${edit_day}-${edit_in_time}";
        $new_out = "${edit_day}-${edit_out_time}";
                                                                                // logfile("new_in: ".$new_in);
                                                                                // logfile("new_out: ".$new_out);        
        
        if (check_time_format($new_in) && check_time_format($new_out)) {
            // if this is TRUE the values PASSED the test! 
            $setTimeValue = 1;   
        }
        
        $new_time = convert_time_strings($new_in,$new_out);
        
        logfile("new_time: " .serialize($new_time));
        
        // if the difference between in and out value is zero or below this can't be correct ...
        
        // TIME WRONG - NEW ENTRY
        
        
        if (!$new_time['diff'] && !$id) {
        // if (!$id) {
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
            
            // the form deliverd correct time data.
            // now we look if the timespan possibly crosses midnight
                                                                                
            $records = explode_record($new_time['in'],$new_time['out']);
            $data['in']   = $records[0]['in'];
            $data['out']  = $records[0]['out'];
            $data['diff'] = $records[0]['diff'];
        
            // now that we know the timespan of the day this entry started
            // we put it into the array which then will be written to the DB
                
            if ($id) { // TIME RIGHT - NEW OR EDIT ?

                // TIME RIGHT - EDIT ENTRY
                logfile("zef_edit_record: " .$id);
                check_zef_data($id,$data);
            
            } else {
                
                // TIME RIGHT - NEW ENTRY
                logfile("zef_create_record");
                zef_create_record($usr['usr_ID'],$data);
                
            }
            
            // Now finally we check if there is time left for a following day
            if (count($records)>1) {
                $this_record['zef_pctID'] = $pct_ID;
                $this_record['zef_evtID'] = $evt_ID;
                save_further_records($usr['usr_ID'],$this_record,$records);
            }
            
        }
        
    break;

    

/*
    // =============================
    // = Temporary Customer Filter =
    // =============================
    case 'filter':
    mysql_query(sprintf("UPDATE `%susr` SET `filter` = '%d' WHERE `usr_ID` = '%d';",$kga['server_prefix'],$_REQUEST['id'],$usr['usr_ID']));
    // this is connected to a hidden feature and can be activated in the file vars.php inside the includes dir
    break;
*/

}

?>
