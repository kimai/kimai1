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

// =============================
// = Smarty (initialize class) =
// ============================= 
require_once('../libraries/smarty/Smarty.class.php');
$tpl = new Smarty();
$tpl->template_dir = '../templates/';
$tpl->compile_dir  = '../compile/';

// prevent IE from caching the response
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// ==================================
// = implementing standard includes =
// ==================================
include('../includes/basics.php');

$usr = $database->checkUser();

// Jedes neue update schreibt seine Versionsnummer in die Datenbank.
// Beim nächsten Update kommt dann in der Datei /includes/var.php die neue V-Nr. mit.
// der updater.php weiss dann welche Aenderungen an der Datenbank vorgenommen werden muessen. 
checkDBversion("..");

$extensions = new Extensions($kga, WEBROOT.'/extensions/');
$extensions->loadConfigurations();

// ============================================
// = initialize currently displayed timespace =
// ============================================
$timespace = get_timespace();
$in = $timespace[0];
$out = $timespace[1];

// ===============================================
// = get time for the probably running stopwatch =
// ===============================================
$current_timer = array();
if (isset($kga['customer'])) {
  $current_timer['all']  = 0;
  $current_timer['hour'] = 0;
  $current_timer['min']  = 0;
  $current_timer['sec']  = 0;
}
else
  $current_timer = $database->get_current_timer();

// =======================================
// = Display date and time in the header =
// =======================================
$wd       = $kga['lang']['weekdays_short'][date("w",time())];

$dp_start = 0;
if ($kga['calender_start']!="")
    $dp_start = $kga['calender_start'];
else if (isset($kga['usr']))
    $dp_start = date("d/m/Y",$database->getjointime($kga['usr']['usr_ID']));    
    

$dp_today = date("d/m/Y",time());

$tpl->assign('dp_start', $dp_start);
$tpl->assign('dp_today', $dp_today);

if (isset($kga['customer']))
  $tpl->assign('total', Format::formatDuration($database->get_zef_time($in,$out,null,array($kga['customer']['knd_ID']))));
else
  $tpl->assign('total', Format::formatDuration($database->get_zef_time($in,$out,$kga['usr']['usr_ID'])));

// ===========================
// = DatePicker localization =
// ===========================
$localized_DatePicker ="";

$tpl->assign('weekdays_array', sprintf("['%s','%s','%s','%s','%s','%s','%s']\n" 
,$kga['lang']['weekdays'][0],$kga['lang']['weekdays'][1],$kga['lang']['weekdays'][2],$kga['lang']['weekdays'][3],$kga['lang']['weekdays'][4],$kga['lang']['weekdays'][5],$kga['lang']['weekdays'][6]));

$tpl->assign('weekdays_short_array', sprintf("['%s','%s','%s','%s','%s','%s','%s']\n" 
,$kga['lang']['weekdays_short'][0],$kga['lang']['weekdays_short'][1],$kga['lang']['weekdays_short'][2],$kga['lang']['weekdays_short'][3],$kga['lang']['weekdays_short'][4],$kga['lang']['weekdays_short'][5],$kga['lang']['weekdays_short'][6]));

$tpl->assign('months_array', sprintf("['%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s']\n",
$kga['lang']['months'][0],$kga['lang']['months'][1],$kga['lang']['months'][2],$kga['lang']['months'][3],$kga['lang']['months'][4],$kga['lang']['months'][5],$kga['lang']['months'][6],$kga['lang']['months'][7],$kga['lang']['months'][8],$kga['lang']['months'][9],$kga['lang']['months'][10],$kga['lang']['months'][11]));

$tpl->assign('months_short_array', sprintf("['%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s']", $kga['lang']['months_short'][0],$kga['lang']['months_short'][1],$kga['lang']['months_short'][2],$kga['lang']['months_short'][3],$kga['lang']['months_short'][4],$kga['lang']['months_short'][5],$kga['lang']['months_short'][6],$kga['lang']['months_short'][7],$kga['lang']['months_short'][8],$kga['lang']['months_short'][9],$kga['lang']['months_short'][10],$kga['lang']['months_short'][11]));



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
                       
$tpl->assign('extensions', $extensions->extensionsTabData());
$tpl->assign('css_extension_files', $extensions->cssExtensionFiles());
$tpl->assign('js_extension_files', $extensions->jsExtensionFiles());

if (isset($kga['usr']))
  $tpl->assign('recstate', $database->get_rec_state($kga['usr']['usr_ID']));
else
  $tpl->assign('recstate', 0);

$tpl->assign('lang_checkUsername', $kga['lang']['checkUsername']);
$tpl->assign('lang_checkGroupname', $kga['lang']['checkGroupname']);


$knd_data = array('knd_ID'=>false,'knd_name'=>'');
$pct_data = array('pct_ID'=>false,'pct_name'=>'');
$evt_data = array('evt_ID'=>false,'evt_name'=>'');

if (!isset($kga['customer'])) {
  //$lastZefRecord = $database->zef_get_data(false);
  $last_pct = $database->pct_get_data($kga['usr']['lastProject']);
  $last_evt = $database->evt_get_data($kga['usr']['lastEvent']);
  if (!$last_pct['pct_trash']) {
    $pct_data = $last_pct;
    $knd_data = $database->knd_get_data($last_pct['pct_kndID']);
  }
  if (!$last_evt['evt_trash'])
    $evt_data = $last_evt;    
}
$tpl->assign('knd_data', $knd_data);
$tpl->assign('pct_data', $pct_data);
$tpl->assign('evt_data', $evt_data);

// =========================================
// = INCLUDE EXTENSION PHP FILE            =
// =========================================
foreach ($extensions->phpIncludeFiles() as $includeFile) {
  require_once($includeFile);
}

// =======================
// = display user table =
// =======================
if (isset($kga['customer']))
  $arr_usr = array();
else
  $arr_usr = $database->get_arr_watchable_users($kga['usr']);
if (count($arr_usr)>0) {
    $tpl->assign('arr_usr', $arr_usr);
} else {
    $tpl->assign('arr_usr', '0');
}
$tpl->assign('usr_display', $tpl->fetch("lists/usr.tpl"));

// ==========================
// = display customer table =
// ========================
if (isset($kga['customer']))
  $arr_knd = array(array(
      'knd_ID'=>$kga['customer']['knd_ID'],
      'knd_name'=>$kga['customer']['knd_name'],
      'knd_visible'=>$kga['customer']['knd_visible']));
else
  $arr_knd = $database->get_arr_knd($kga['usr']['groups']);
if (count($arr_knd)>0) {
    $tpl->assign('arr_knd', $arr_knd);
} else {
    $tpl->assign('arr_knd', '0');
}
$tpl->assign('knd_display', $tpl->fetch("lists/knd.tpl"));

// =========================
// = display project table =
// =========================
if (isset($kga['customer']))
  $arr_pct = $database->get_arr_pct_by_knd($kga['customer']['knd_ID']);
else
  $arr_pct = $database->get_arr_pct($kga['usr']['groups']);
if (count($arr_pct)>0) {
    $tpl->assign('arr_pct', $arr_pct);
} else {
    $tpl->assign('arr_pct', '0');
}
$tpl->assign('pct_display', $tpl->fetch("lists/pct.tpl"));

// ========================
// = display events table =
// ========================
if (isset($kga['customer']))
  $arr_evt = $database->get_arr_evt_by_knd($kga['customer']['knd_ID']);
else if ($pct_data['pct_ID'])
  $arr_evt = $database->get_arr_evt_by_pct($pct_data['pct_ID'],$kga['usr']['groups']);
else
  $arr_evt = $database->get_arr_evt($kga['usr']['groups']);
if (count($arr_evt)>0) {
    $tpl->assign('arr_evt', $arr_evt);
} else {
    $tpl->assign('arr_evt', '0');
}
$tpl->assign('evt_display', $tpl->fetch("lists/evt.tpl"));

if (isset($kga['usr']))
  $tpl->assign('showInstallWarning',$kga['usr']['usr_sts']==0 && file_exists(WEBROOT.'installer'));
else
  $tpl->assign('showInstallWarning',false);



// ========================
// = BUILD HOOK FUNCTIONS =
// ========================


$tpl->assign('hook_tss',      $extensions->tssHooks());
$tpl->assign('hook_bzzRec',   $extensions->recHooks());
$tpl->assign('hook_bzzStp',   $extensions->stpHooks());
$tpl->assign('hook_chgUsr',   $extensions->chuHooks());
$tpl->assign('hook_chgKnd',   $extensions->chkHooks());
$tpl->assign('hook_chgPct',   $extensions->chpHooks());
$tpl->assign('hook_chgEvt',   $extensions->cheHooks());
$tpl->assign('hook_filter',   $extensions->lftHooks());
$tpl->assign('hook_resize',   $extensions->rszHooks());
$tpl->assign('timeoutlist',   $extensions->timeoutList());

$tpl->display('core/main.tpl');

?>
