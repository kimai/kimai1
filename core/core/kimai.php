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

$user = $database->checkUser();

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

// ============================================
// = load the config =
// ============================================
include('Config.php');


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
else if (isset($kga['user']))
    $dp_start = date("d/m/Y",$database->getjointime($kga['user']['userID']));    
    

$dp_today = date("d/m/Y",time());

$tpl->assign('dp_start', $dp_start);
$tpl->assign('dp_today', $dp_today);

if (isset($kga['customer']))
  $tpl->assign('total', Format::formatDuration($database->get_duration($in,$out,null,array($kga['customer']['customerID']))));
else
  $tpl->assign('total', Format::formatDuration($database->get_duration($in,$out,$kga['user']['userID'])));

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

$tpl->assign('currentRecording', -1);

if (isset($kga['user'])) {
  $currentRecordings = $database->get_current_recordings($kga['user']['userID']);
  if (count($currentRecordings) > 0)
    $tpl->assign('currentRecording', $currentRecordings[0]);
}

$tpl->assign('lang_checkUsername', $kga['lang']['checkUsername']);
$tpl->assign('lang_checkGroupname', $kga['lang']['checkGroupname']);
$tpl->assign('lang_checkStatusname', $kga['lang']['checkStatusname']);


$customerData = array('customerID'=>false,'name'=>'');
$projectData = array('projectID'=>false,'name'=>'');
$activityData = array('activityID'=>false,'name'=>'');

if (!isset($kga['customer'])) {
  //$lastTimeSheetRecord = $database->timeSheet_get_data(false);
  $lastProject = $database->project_get_data($kga['user']['lastProject']);
  $lastActivity = $database->activity_get_data($kga['user']['lastActivity']);
  if (!$lastProject['trash']) {
    $projectData = $lastProject;
    $customerData = $database->customer_get_data($lastProject['customerID']);
  }
  if (!$lastActivity['trash'])
    $activityData = $lastActivity;    
}
$tpl->assign('customerData', $customerData);
$tpl->assign('projectData', $projectData);
$tpl->assign('activityData', $activityData);

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
  $users = array();
else
  $users = $database->get_arr_watchable_users($kga['user']);
if (count($users)>0) {
    $tpl->assign('users', $users);
} else {
    $tpl->assign('users', '0');
}
$tpl->assign('user_display', $tpl->fetch("lists/users.tpl"));

// ==========================
// = display customer table =
// ========================
if (isset($kga['customer']))
  $customers = array(array(
      'customerID'=>$kga['customer']['customerID'],
      'name'=>$kga['customer']['name'],
      'visible'=>$kga['customer']['visible']));
else
  $customers = $database->get_arr_customers($kga['user']['groups']);
if (count($customers)>0) {
    $tpl->assign('customers', $customers);
} else {
    $tpl->assign('customers', '0');
}
$tpl->assign('customer_display', $tpl->fetch("lists/customers.tpl"));

// =========================
// = display project table =
// =========================
if (isset($kga['customer']))
  $projects = $database->get_arr_projects_by_customer($kga['customer']['customerID']);
else
  $projects = $database->get_arr_projects($kga['user']['groups']);
if (count($projects)>0) {
    $tpl->assign('projects', $projects);
} else {
    $tpl->assign('projects', '0');
}
$tpl->assign('project_display', $tpl->fetch("lists/projects.tpl"));

// ========================
// = display activity table =
// ========================
if (isset($kga['customer']))
  $activities = $database->get_arr_activities_by_customer($kga['customer']['customerID']);
else if ($projectData['projectID'])
  $activities = $database->get_arr_activities_by_project($projectData['projectID'],$kga['user']['groups']);
else
  $activities = $database->get_arr_activities($kga['user']['groups']);
if (count($activities)>0) {
    $tpl->assign('activities', $activities);
} else {
    $tpl->assign('activities', '0');
}
$tpl->assign('activity_display', $tpl->fetch("lists/activities.tpl"));

if (isset($kga['user']))
  $tpl->assign('showInstallWarning',$kga['user']['status']==0 && file_exists(WEBROOT.'installer'));
else
  $tpl->assign('showInstallWarning',false);



// ========================
// = BUILD HOOK FUNCTIONS =
// ========================


$tpl->assign('hook_timeframe_changed',      $extensions->timeframeChangedHooks());
$tpl->assign('hook_buzzer_record',   $extensions->buzzerRecordHooks());
$tpl->assign('hook_buzzer_stopped',   $extensions->buzzerStopHooks());
$tpl->assign('hook_users_changed',   $extensions->usersChangedHooks());
$tpl->assign('hook_customers_changed',   $extensions->customersChangedHooks());
$tpl->assign('hook_projects_changed',   $extensions->projectsChangedHooks());
$tpl->assign('hook_activities_changed',   $extensions->activitiesChangedHooks());
$tpl->assign('hook_filter',   $extensions->filterHooks());
$tpl->assign('hook_resize',   $extensions->resizeHooks());
$tpl->assign('timeoutlist',   $extensions->timeoutList());

$tpl->display('core/main.tpl');

?>
