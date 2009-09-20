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

// ==================
// = CORE PROCESSOR =
// ==================

// insert KSPI - Kimai Standard Processor Initialization ;)
$isCoreProcessor = 1;
$dir_templates = "templates/core/";
require("../includes/kspi.php");

switch ($axAction) {
    
    case 'logfile':
        logfile("JavaScript: " . $axValue);
    break;


    // ================================
    // = write user preferences to DB =
    // ================================
    case 'editPrefs':
    
        $usr_data['skin']               = $_REQUEST['skin'];
        $usr_data['autoselection']      = $_REQUEST['autoselection'];
        $usr_data['quickdelete']        = $_REQUEST['quickdelete'];
        $usr_data['rowlimit']           = $_REQUEST['rowlimit'];
        $usr_data['lang']               = $_REQUEST['lang'];
        $usr_data['allvisible']         = $_REQUEST['allvisible'];
        $usr_data['flip_pct_display']   = $_REQUEST['flip_pct_display'];
        $usr_data['pct_comment_flag']   = $_REQUEST['pct_comment_flag'];
        $usr_data['showIDs']            = $_REQUEST['showIDs'];
        
        // if password field is empty => password unchanged (not overwritten with "")
        if ($_REQUEST['pw'] != "") {
        	$usr_data['pw'] = crypt($_REQUEST['pw'], $kga['cryptmethod']);
        }
        // $usr_data['pw']                 = $_REQUEST['pw'];
        
        usr_edit($kga['usr']['usr_ID'], $usr_data);
    break;
    
    // ==============================
    // = write send timespace to DB =
    // ==============================
    case 'setTimespace':
    
        $timespace = explode('|',$axValue);
         
        $timespace_in  = explode('-',$timespace[0]);
        $timespace_in  = (int)mktime(0,0,0,$timespace_in[0],$timespace_in[1],$timespace_in[2]);
        if ($timespace_in < 950000000) $timespace_in = $in;
        
        $timespace_out = explode('-',$timespace[1]);
        $timespace_out = (int)mktime(23,59,59,$timespace_out[0],$timespace_out[1],$timespace_out[2]);
        if ($timespace_out < 950000000) $timespace_out = $out;
        
        $timespace_warning = save_timespace($timespace_in,$timespace_out,$kga['usr']['usr_ID']);

        $tpl->assign('timespace_warning', $timespace_warning);
        $tpl->assign('timespace_in',  $timespace_in);
        $tpl->assign('timespace_out', $timespace_out);
        
        // =======================================
        // = Display date and time in the header =
        // =======================================
        $wd       = $kga['lang']['weekdays_short'][date("w",time())];
        $today    = date($kga['date_format'][0],time());
        if ($kga['calender_start']=="")
          $dp_start = date("d/m/Y",getjointime($kga['usr']['usr_ID']));    
        else
          $dp_start = $kga['calender_start'];
        $pd_today = date("d/m/Y",time());
        $nextday  = $kga['lang']['weekdays_short'][date("w",time()+86400)] . ". " . date($kga['date_format'][0],time()+86400);

        $tpl->assign('today_display', "$wd. $today");
        $tpl->assign('dp_start', $dp_start);
        $tpl->assign('dp_today', $pd_today);
        $tpl->assign('nextday', $nextday);
        $tpl->assign('total', intervallApos(get_zef_time($kga['usr']['usr_ID'],$timespace_in,$timespace_out)));
        $tpl->assign('hook_tss_inDisplay',1);
        $tpl->display("display.tpl");
    break;

    // ====================
    // = record new event =
    // ====================
    case 'startRecord':
        if (get_rec_state($kga['usr']['usr_ID'])) {
            stopRecorder();
        }
    
        $IDs = explode('|',$axValue);
        startRecorder($IDs[0],$IDs[1],$id);
        echo 1;
    break;
    
    // ====================
    // = record new event =
    // ====================
    case 'stopRecord':
        stopRecorder();
        echo 1;
    break;

    // =================================
    // = load user table (usr) from DB =
    // =================================
    case 'reload_usr':
        $arr_usr = get_arr_watchable_users($kga['usr']['usr_grp'],$kga['usr']['usr_ID'],$in,$out);
        if (count($arr_usr)>0) {
            $tpl->assign('arr_usr', $arr_usr);
        } else {
            $tpl->assign('arr_usr', 0);
        }
        $tpl->display("../lists/usr.tpl");
    break;

    // =====================================
    // = load customer table (knd) from DB =
    // =====================================
    case 'reload_knd':
        $arr_knd = get_arr_knd_with_time($kga['usr']['usr_grp'],$kga['usr']['usr_ID'],$in,$out);
        if (count($arr_knd)>0) {
            $tpl->assign('arr_knd', $arr_knd);
        } else {
            $tpl->assign('arr_knd', 0);
        }
        $tpl->display("../lists/knd.tpl");
    break;

    // ====================================
    // = load project table (pct) from DB =
    // ====================================
    case 'reload_pct':
        $arr_pct = get_arr_pct_with_time($kga['usr']['usr_grp'],$kga['usr']['usr_ID'],$in,$out);
        if (count($arr_pct)>0) {
            $tpl->assign('arr_pct', $arr_pct);
        } else {
            $tpl->assign('arr_pct', 0);
        }
        $tpl->display("../lists/pct.tpl");
    break;

    // ===================================
    // = load events table (evt) from DB =
    // ===================================
    case 'reload_evt':
        $arr_evt = get_arr_evt_with_time($kga['usr']['usr_grp'],$kga['usr']['usr_ID'],$in,$out);
        if (count($arr_evt)>0) {
            $tpl->assign('arr_evt', $arr_evt);
        } else {
            $tpl->assign('arr_evt', 0);
        }
        $tpl->display("../lists/evt.tpl");
    break;


    // ============================================
    // = adding new customers, projects or events =
    // ============================================
    // core function because it is used at least by AP *and* ts_ext!
    
    case 'add_edit_KndPctEvt':
    
        if($kga['usr']['usr_sts']==2) die(); // only admins and grpleaders can do this ...
    	
        switch($axValue) {
            case "knd":
            	$data['knd_name']     = $_REQUEST['knd_name'];
            	$data['knd_comment']  = $_REQUEST['knd_comment'];
            	$data['knd_company']  = $_REQUEST['knd_company'];
            	$data['knd_street']   = $_REQUEST['knd_street'];
            	$data['knd_zipcode']  = $_REQUEST['knd_zipcode'];
            	$data['knd_city']     = $_REQUEST['knd_city'];
            	$data['knd_tel']      = $_REQUEST['knd_tel'];
            	$data['knd_fax']      = $_REQUEST['knd_fax'];
            	$data['knd_mobile']   = $_REQUEST['knd_mobile'];
            	$data['knd_mail']     = $_REQUEST['knd_mail'];
            	$data['knd_homepage'] = $_REQUEST['knd_homepage'];
            	$data['knd_visible']  = $_REQUEST['knd_visible'];
            	$data['knd_filter']   = $_REQUEST['knd_filter'];
            	$data['knd_logo']     = $_REQUEST['knd_logo'];
            	
                // logfile("knd_create (" .$kga['usr']['usr_name'] ."): " . $data['knd_name']);
            	
            	if (!$id) {
                    $id = knd_create($data);
            	} else {
            	    knd_edit($id, $data);
            	}
                $grp_array = $_REQUEST['knd_grp'];
                assign_knd2grps($id, $grp_array);
            break;
            
            case "pct":
                $data['pct_name']         = $_REQUEST['pct_name'];
                $data['pct_kndID']        = $_REQUEST['pct_kndID'];
                $data['pct_comment']      = $_REQUEST['pct_comment'];
                $data['pct_visible']      = $_REQUEST['pct_visible'];
                $data['pct_filter']       = $_REQUEST['pct_filter'];
                $data['pct_logo']         = $_REQUEST['pct_logo'];
                $data['pct_default_rate'] = $_REQUEST['pct_default_rate'];
                $data['pct_my_rate']      = $_REQUEST['pct_my_rate'];
                
                // logfile("pct_create (" .$kga['usr']['usr_name'] ."): " . $data['pct_name']);
                
                if (!$id) {
                    $id = pct_create($data);
            	} else {
            	    pct_edit($id, $data);
            	}
                $grp_array = $_REQUEST['pct_grp'];
                assign_pct2grps($id, $grp_array);
            break;
            
            case "evt":
                $data['evt_name']         = $_REQUEST['evt_name'];
                $data['evt_comment']      = $_REQUEST['evt_comment'];
                $data['evt_visible']      = $_REQUEST['evt_visible'];
                $data['evt_filter']       = $_REQUEST['evt_filter'];
                $data['evt_logo']         = $_REQUEST['evt_logo'];
                $data['evt_default_rate'] = $_REQUEST['evt_default_rate'];
                $data['evt_my_rate']      = $_REQUEST['evt_my_rate'];
                
                // logfile("evt_create (" .$kga['usr']['usr_name'] ."): " . $data['evt_name']);
                
                if (!$id) {
                    $id = evt_create($data);
            	} else {
            	    evt_edit($id, $data);
            	}
                $grp_array = $_REQUEST['evt_grp'];
                assign_evt2grps($id, $grp_array);
            break;
        }
    break;

}










// ----------------------------------------------------------------------------------------------------------------------------------
// outdated CODE FROM 0.7 ....

/*


    // =============================
    // = Temporary Customer Filter =
    // =============================
    case 'filter':
    // mysql_query(sprintf("UPDATE `%susr` SET `filter` = '%d' WHERE `usr_ID` = '%d';",$kga['server_prefix'],$_REQUEST['id'],$kga['usr']['usr_ID']));
    // this is connected to a hidden feature and can be activated in the file vars.php inside the includes dir
    
    	$usr_data['filter'] = $_REQUEST['id'];
    	usr_edit($kga['usr']['usr_ID'], $usr_data);
    
    break;
    



// following 3 cases are returning the interactive dropdowns beside the buzzer
	
/// OBSOLETE !! /// /// /// /// /// /// /// /// /// /// /// /// /// /// /// /// /// /// /// /// /// 


	    case 'giveMeTheCustomerList':
	        $arr_knd = get_arr_knd($kga['usr']['usr_grp']);
	        if (count($arr_knd)>0) {
	            $tpl->assign('arr_knd', $arr_knd);
	        } else { // no customers - no dropdown ...
	            $tpl->assign('arr_knd', 0);
	        }


	        $arr_pct = get_arr_first_pct_of_knd();

	        // // we have at least one project
	        // if (count($arr_pct)>0) {
	        //     
	        //     //       if there are projects defined we generate an MD array with records like this:
	        //     //
	        //     //       [knd_ID]
	        //     //          L_____ [pct_ID]       ID of 1st pct of knd (or the only one)
	        //     //          L_____ [pct_name]     name of that project
	        //     //          L_____ [lonely]       boolean (is true when the customer has only one project)
	        //     //
	        //     //
	        //     
	        //     $arr_first_pct = array();
	        //     
	        //     foreach ($arr_pct as $record) {
	        //         $arr_first_pct[] = $record['pct_kndID'];
	        //     }
	        //     
	        // 
	        // } else { // no projects at all - we have a problem...
	        //     $tpl->assign('pct_arr', 0);
	        // }        


	        #$tpl->assign('arr_first_pct',$arr_first_pct);
	        $tpl->assign('arr_pct',$arr_pct);


	        $tpl->assign('lines',count($arr_knd));
	        $tpl->display("select_knd.tpl");
	    break;

	    case 'giveMeTheProjectList':
	        $id = (int)strip_tags($_REQUEST['id']);
	        $arr_pct = get_arr_pct_by_knd($kga['usr']['usr_grp'],$id);
	        if (count($arr_pct)>0) {
	            $tpl->assign('arr_pct', $arr_pct);
	        } else {
	            $tpl->assign('arr_pct', 0);
	        }
	        $tpl->assign('lines',count($arr_pct));
	        $tpl->display("select_pct.tpl");
	    break;

	    case 'giveMeTheEventList':
	        $arr_evt = get_arr_evt($kga['usr']['usr_grp']);
	        if (count($arr_evt)>0) {
	            $tpl->assign('arr_evt', $arr_evt);
	        } else {
	            $tpl->assign('arr_evt', 0);
	        }
	        $tpl->assign('lines',count($arr_evt));
	        $tpl->display("select_evt.tpl");
	    break;
*/

?>
