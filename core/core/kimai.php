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

// =============================
// = Smarty (initialize class) =
// ============================= 
require_once('../libraries/smarty/Smarty.class.php');
$tpl = new Smarty();
$tpl->template_dir = '../templates/';
$tpl->compile_dir  = '../compile/';

// ==================================
// = implementing standard includes =
// ==================================
include('../includes/basics.php');

checkUser();

// Jedes neue update schreibt seine Versionsnummer in die Datenbank.
// Beim nächsten Update kommt dann in der Datei /includes/var.php die neue V-Nr. mit.
// der updater.php weiss dann welche Aenderungen an der Datenbank vorgenommen werden muessen. 
checkDBversion("..");

$tpl->assign('browser', get_agent());

// =========================================
// = PARSE EXTENSION CONFIGS (ext_configs) =
// =========================================
$path = realpath(dirname(__FILE__)).'/../libraries';
require_once($path."/Config.php");

if ($handle = opendir('../extensions/')) {
    chdir("../extensions/");
    $ext_configs = array();
    
    $css_extension_files = array();
    $js_extension_files  = array();
    $extensions          = array();
    $tab_change_trigger  = array();
    $tss_hooks           = array();
    $rec_hooks           = array();
    $stp_hooks           = array();
    $chk_hooks           = array();
    $chp_hooks           = array();
    $che_hooks           = array();
    $lft_hooks           = array(); // list filter hooks
    $rsz_hooks           = array(); // resize hooks
    $timeouts            = array();
    
    while (false !== ($file = readdir($handle))) {

        if (is_dir($file) AND ($file != ".") AND ($file != "..") AND (substr($file,0) != ".") AND (substr($file,0,1) != "#")) {
            if ($subhandle = opendir("../extensions/".$file)) {
                while (false !== ($configfile = readdir($subhandle))) {
                    if($configfile == "config.ini") {
                        $datasrc = $file."/".$configfile;
                        $phpIni = new Config();
                        $root =& $phpIni->parseConfig($datasrc, 'inicommented');
                        $settings = $root->toArray();
                       	$settings = $settings['root'];
                       	
                       	// logfile("*************** ADMIN ALLOWED: " . $settings['ADMIN_ALLOWED']);
                       	// logfile("*************** GROUP LEADER ALLOWED: " . $settings['GROUP_LEADER_ALLOWED']);
                       	// logfile("*************** USER ALLOWED: " . $settings['USER_ALLOWED']);                      	
                       	// logfile("****************** user status: " . $kga['user']['usr_sts']);
                       	
                       	// Check if user has the correct rank to use this extension
                       	switch ($kga['user']['usr_sts']) {
                       		case 0:
                       		if ($settings['ADMIN_ALLOWED'] == "1") {
                       			$extensions[] = $settings;
                       		}
                       		break;
                       	
                       		case 1:
                       	    if ($settings['GROUP_LEADER_ALLOWED'] == "1") {
                       			$extensions[] = $settings;
                       		}
                       		break;
                       	
                       		case 2:
                       	    if ($settings['USER_ALLOWED'] == "1") {
                       			$extensions[] = $settings;
                       		}
                       		break;
                       	}
                       	
                       	foreach($settings as $key=>$value){
							
							// add CSS files
							if($key == 'CSS_INCLUDE_FILES'){
								if(is_array($value)){
									foreach($value as $subvalue){
										if(!in_array($subvalue, $css_extension_files)){
											$css_extension_files[] = $subvalue;
										}
									}
								} else {
									if(!in_array($value, $css_extension_files)){
										$css_extension_files[] = $value;
									}
								}
							}
							
							// add JavaScript files
							if($key == 'JS_INCLUDE_FILES') {
								if(is_array($value)){
									foreach($value as $subvalue){
										if(!in_array($subvalue, $js_extension_files)){
											$js_extension_files[] = $subvalue;
										}
									}
								} else {
									if(!in_array($value, $js_extension_files)){
										$js_extension_files[] = $value;
									}
								}
							}
							
                            // read trigger function for tab change
                            if ($key == 'TAB_CHANGE_TRIGGER') { $tab_change_trigger[] = $value; }
                                                        
                            // read hook triggers
                            if ($key == 'TIMESPACE_CHANGE_TRIGGER') { $tss_hooks[] = $value; }
                            if ($key == 'BUZZER_RECORD_TRIGGER')    { $rec_hooks[] = $value; }
                            if ($key == 'BUZZER_STOP_TRIGGER')      { $stp_hooks[] = $value; }
                            if ($key == 'CHANGE_KND_TRIGGER')       { $chk_hooks[] = $value; }
                            if ($key == 'CHANGE_PCT_TRIGGER')       { $chp_hooks[] = $value; }
                            if ($key == 'CHANGE_EVT_TRIGGER')       { $che_hooks[] = $value; }
                            if ($key == 'LIST_FILTER_TRIGGER')      { $lft_hooks[] = $value; }
                            if ($key == 'RESIZE_TRIGGER')           { $rsz_hooks[] = $value; }
                            
                            // add Timeout clearing
                            
                            if($key == 'REG_TIMEOUTS') {
                            if(is_array($value)){
                                 foreach($value as $subvalue){
                                     if(!in_array($subvalue, $timeouts)){
                                         $timeouts[] = $subvalue;
                                     }
                                 }
                             } else {
                                 if(!in_array($value, $timeouts)){
                                     $timeouts[] = $value;
                                 }
                             }
                            }
                            
                            
                        }
                    }
                }
                closedir($subhandle);
            }
        }
    }
    closedir($handle);
}

// ============================================
// = initialize currently displayed timespace =
// ============================================
$timespace = get_timespace();
$in = $timespace[0];
$out = $timespace[1];

// ===============================================
// = get time for the probably running stopwatch =
// ===============================================
$current_timer = get_current_timer();

// =======================================
// = Display date and time in the header =
// =======================================
$wd       = $kga['lang']['weekdays_short'][date("w",time())];

if ($kga['calender_start']=="") {
    $dp_start = date("d/m/Y",getjointime($kga['user']['usr_ID']));    
} else {
    $dp_start = $kga['calender_start'];    
}

$pd_today = date("d/m/Y",time());
$today    = date($kga['date_format'][0],time());
$nextday  = $kga['lang']['weekdays_short'][date("w",time()+86400)] . ". " . date($kga['date_format'][0],time()+86400);

$tpl->assign('today_display', "$wd. $today");
$tpl->assign('dp_start', $dp_start);
$tpl->assign('dp_today', $pd_today);
$tpl->assign('nextday', $nextday);
$tpl->assign('total', intervallApos(get_zef_time($kga['user']['usr_ID'],$in,$out)));

// ===========================
// = DatePicker localization =
// ===========================
$localized_DatePicker ="";

$localized_DatePicker .= sprintf("Date.dayNames = ['%s','%s','%s','%s','%s','%s','%s'];\n" 
,$kga['lang']['weekdays'][0],$kga['lang']['weekdays'][1],$kga['lang']['weekdays'][2],$kga['lang']['weekdays'][3],$kga['lang']['weekdays'][4],$kga['lang']['weekdays'][5],$kga['lang']['weekdays'][6]);

$localized_DatePicker .= sprintf("Date.abbrDayNames = ['%s','%s','%s','%s','%s','%s','%s'];\n" 
,$kga['lang']['weekdays_short'][0],$kga['lang']['weekdays_short'][1],$kga['lang']['weekdays_short'][2],$kga['lang']['weekdays_short'][3],$kga['lang']['weekdays_short'][4],$kga['lang']['weekdays_short'][5],$kga['lang']['weekdays_short'][6]);

$localized_DatePicker .= sprintf("Date.monthNames = ['%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'];\n",
$kga['lang']['months'][0],$kga['lang']['months'][1],$kga['lang']['months'][2],$kga['lang']['months'][3],$kga['lang']['months'][4],$kga['lang']['months'][5],$kga['lang']['months'][6],$kga['lang']['months'][7],$kga['lang']['months'][8],$kga['lang']['months'][9],$kga['lang']['months'][10],$kga['lang']['months'][11]);

$localized_DatePicker .= sprintf("Date.abbrMonthNames = ['%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'];", $kga['lang']['months_short'][0],$kga['lang']['months_short'][1],$kga['lang']['months_short'][2],$kga['lang']['months_short'][3],$kga['lang']['months_short'][4],$kga['lang']['months_short'][5],$kga['lang']['months_short'][6],$kga['lang']['months_short'][7],$kga['lang']['months_short'][8],$kga['lang']['months_short'][9],$kga['lang']['months_short'][10],$kga['lang']['months_short'][11]);

$tpl->assign('localized_DatePicker', $localized_DatePicker);


// ==============================
// = assign smarty placeholders =
// ==============================
$tpl->assign('current_timer_hour', $current_timer['hour']);
$tpl->assign('current_timer_min',  $current_timer['min'] );
$tpl->assign('current_timer_sec',  $current_timer['sec'] );
$tpl->assign('current_timer_start',  $current_timer['all']?$current_timer['all']:time());
$tpl->assign('current_time',time());

$tpl->assign('timespace_in', $in);
$tpl->assign('timespace_out', $out);

$tpl->assign('kga',$kga);
                       
$tpl->assign('extensions', $extensions);
$tpl->assign('css_extension_files', $css_extension_files);
$tpl->assign('js_extension_files', $js_extension_files);

$tpl->assign('timespace_warning', timespace_warning($in,$out));

$tpl->assign('recstate', get_rec_state($kga['user']['usr_ID']));

$tpl->assign('lang_checkUsername', $kga['lang']['checkUsername']);

$lastZefRecord = zef_get_data(0);
logfile($lastZefRecord['zef_pctID']);
$last_pct = pct_get_data($lastZefRecord['zef_pctID']);
if ($last_pct) {
    $tpl->assign('pct_data', $last_pct);
    $tpl->assign('knd_data', knd_get_data($last_pct['pct_kndID']));
    $tpl->assign('evt_data', evt_get_data($kga['conf']['lastEvent']));
} else {
    $knd_data['knd_ID'] = 1;
    $pct_data['pct_ID'] = 1;
    $evt_data['evt_ID'] = 1;
    $knd_data['knd_name'] = $kga['lang']['testKND'];
    $pct_data['pct_name'] = $kga['lang']['testPCT'];
    $evt_data['evt_name'] = $kga['lang']['testEVT'];
    $tpl->assign('knd_data', $knd_data);
    $tpl->assign('pct_data', $pct_data);
    $tpl->assign('evt_data', $evt_data);
}

// =========================================
// = INCLUDE EXTENSION PHP FILE            =
// =========================================
$extDir = realpath(dirname(__FILE__)).'/../extensions';
if ($handle = opendir($extDir)) {
    chdir($extDir);
    $ext_configs = array();
    while (false !== ($file = readdir($handle))) {
        if (is_dir($file) AND ($file != ".") AND ($file != "..") AND (substr($file,0) != ".") AND (substr($file,0,1) != "#")) {
            if ($subhandle = opendir($extDir . DIRECTORY_SEPARATOR . $file)) {
                while (false !== ($phpfile = readdir($subhandle))) {
                    if($phpfile == "kimai_include.php") {
                       require_once($extDir . DIRECTORY_SEPARATOR .$file. DIRECTORY_SEPARATOR .$phpfile);
                    }
                }
                closedir($subhandle);
            }
        }
    }
    closedir($handle);
}


// =======================
// = display user table =
// =======================
$arr_usr = get_arr_watchable_users($kga['user']['usr_ID']);
if (count($arr_usr)>0) {
    $tpl->assign('arr_usr', $arr_usr);
} else {
    $tpl->assign('arr_usr', '0');
}
$tpl->assign('usr_display', $tpl->fetch("lists/usr.tpl"));

// ==========================
// = display customer table =
// ==========================
$arr_knd = get_arr_knd($kga['user']['usr_grp'],$kga['user']['usr_ID'],$in,$out);
if (count($arr_knd)>0) {
    $tpl->assign('arr_knd', $arr_knd);
} else {
    $tpl->assign('arr_knd', '0');
}
$tpl->assign('knd_display', $tpl->fetch("lists/knd.tpl"));

// =========================
// = display project table =
// =========================
$arr_pct = get_arr_pct($kga['user']['usr_grp'],$kga['user']['usr_ID'],$in,$out);
if (count($arr_pct)>0) {
    $tpl->assign('arr_pct', $arr_pct);
} else {
    $tpl->assign('arr_pct', '0');
}
$tpl->assign('pct_display', $tpl->fetch("lists/pct.tpl"));

// ========================
// = display events table =
// ========================
$arr_evt = get_arr_evt($kga['user']['usr_grp'],$kga['user']['usr_ID'],$in,$out);
if (count($arr_evt)>0) {
    $tpl->assign('arr_evt', $arr_evt);
} else {
    $tpl->assign('arr_evt', '0');
}
$tpl->assign('evt_display', $tpl->fetch("lists/evt.tpl"));


// ========================
// = BUILD HOOK FUNCTIONS =
// ========================

$hook_tss ="";
if(is_array($tss_hooks)){
    foreach ($tss_hooks as $hook) { 
        $hook_tss .= $hook; 
    }
}

$hook_bzzRec="";
if(is_array($rec_hooks)){
    foreach ($rec_hooks as $hook) { 
        $hook_bzzRec .= $hook; 
    }
}

$hook_bzzStp ="";
if(is_array($stp_hooks)){
    foreach ($stp_hooks as $hook) { 
        $hook_bzzStp .= $hook; 
    }
}

$hook_chgKnd="";
if(is_array($chk_hooks)){
    foreach ($chk_hooks as $hook) { 
        $hook_chgKnd .= $hook; 
    }
}

$hook_chgPct="";
if(is_array($chp_hooks)){
    foreach ($chp_hooks as $hook) { 
        $hook_chgPct .= $hook; 
    }
}

$hook_chgEvt="";
if(is_array($che_hooks)){
    foreach ($che_hooks as $hook) { 
        $hook_chgEvt .= $hook; 
    }
}

$hook_filter="";
if(is_array($lft_hooks)){
    foreach ($lft_hooks as $hook) { 
        $hook_filter .= $hook; 
    }
}

$hook_resize="";
if(is_array($rsz_hooks)){
    foreach ($rsz_hooks as $hook) { 
        $hook_resize .= $hook; 
    }
}

$tpl->assign('hook_tss',      $hook_tss);
$tpl->assign('hook_bzzRec',   $hook_bzzRec);
$tpl->assign('hook_bzzStp',   $hook_bzzStp);
$tpl->assign('hook_chgKnd',   $hook_chgKnd);
$tpl->assign('hook_chgPct',   $hook_chgPct);
$tpl->assign('hook_chgEvt',   $hook_chgEvt);
$tpl->assign('hook_filter',   $hook_filter);
$tpl->assign('hook_resize',   $hook_resize);

$timeoutlist = "";
foreach ($timeouts as $timeout) {
    $timeoutlist .=  "kill_timeout('" . $timeout . "');" ;
}
$tpl->assign('timeoutlist', $timeoutlist);

// the TSS hook is not required at first insert of the display
$tpl->assign('hook_tss_inDisplay',0);

$tpl->display('core/main.tpl');

mysql_close();
?>
