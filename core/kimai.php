<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
 * (c) Kimai-Development-Team since 2006
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

require_once '../includes/basics.php';

$database = Kimai_Registry::getDatabase();

$view = new Zend_View();
$view->setBasePath(WEBROOT . 'templates');

// prevent IE from caching the response
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// ==================================
// = implementing standard includes =
// ==================================

$user = checkUser();

// Jedes neue update schreibt seine Versionsnummer in die Datenbank.
// Beim nächsten Update kommt dann in der Datei /includes/var.php die neue V-Nr. mit.
// der updater.php weiss dann welche Aenderungen an der Datenbank vorgenommen werden muessen.
checkDBversion('..');

$extensions = new Kimai_Extensions($kga, WEBROOT . 'extensions/');
$extensions->loadConfigurations();

// ============================================
// = initialize currently displayed timeframe =
// ============================================
$timeframe = get_timeframe();
$in = $timeframe[0];
$out = $timeframe[1];

// ===============================================
// = get time for the probably running stopwatch =
// ===============================================
$current_timer = [];
if (isset($kga['customer'])) {
    $current_timer['all'] = 0;
    $current_timer['hour'] = 0;
    $current_timer['min'] = 0;
    $current_timer['sec'] = 0;
} else {
    $current_timer = $database->get_current_timer();
}

// =======================================
// = Display date and time in the header =
// =======================================
$wd = $kga['lang']['weekdays_short'][date("w", time())];

$dp_start = 0;
if ($kga['calender_start'] != "") {
    $dp_start = $kga['calender_start'];
} else if (isset($kga['user'])) {
    $dp_start = date("d/m/Y", $database->getjointime($kga['user']['userID']));
}

$dp_today = date("d/m/Y", time());

$view->assign('dp_start', $dp_start);
$view->assign('dp_today', $dp_today);

if (isset($kga['customer'])) {
    $view->assign('total', Kimai_Format::formatDuration($database->get_duration($in, $out, null, [$kga['customer']['customerID']])));
} else {
    $view->assign('total', Kimai_Format::formatDuration($database->get_duration($in, $out, $kga['user']['userID'])));
}

// ===========================
// = DatePicker localization =
// ===========================
$localized_DatePicker = '';

$view->assign('weekdays_array', sprintf(
    "['%s','%s','%s','%s','%s','%s','%s']\n",
    $kga['lang']['weekdays'][0],
    $kga['lang']['weekdays'][1],
    $kga['lang']['weekdays'][2],
    $kga['lang']['weekdays'][3],
    $kga['lang']['weekdays'][4],
    $kga['lang']['weekdays'][5],
    $kga['lang']['weekdays'][6]
));

$view->assign('weekdays_short_array', sprintf(
    "['%s','%s','%s','%s','%s','%s','%s']\n",
    $kga['lang']['weekdays_short'][0],
    $kga['lang']['weekdays_short'][1],
    $kga['lang']['weekdays_short'][2],
    $kga['lang']['weekdays_short'][3],
    $kga['lang']['weekdays_short'][4],
    $kga['lang']['weekdays_short'][5],
    $kga['lang']['weekdays_short'][6]
));

$view->assign('months_array', sprintf(
    "['%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s']\n",
    $kga['lang']['months'][0],
    $kga['lang']['months'][1],
    $kga['lang']['months'][2],
    $kga['lang']['months'][3],
    $kga['lang']['months'][4],
    $kga['lang']['months'][5],
    $kga['lang']['months'][6],
    $kga['lang']['months'][7],
    $kga['lang']['months'][8],
    $kga['lang']['months'][9],
    $kga['lang']['months'][10],
    $kga['lang']['months'][11]
));

$view->assign('months_short_array', sprintf(
    "['%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s']",
    $kga['lang']['months_short'][0],
    $kga['lang']['months_short'][1],
    $kga['lang']['months_short'][2],
    $kga['lang']['months_short'][3],
    $kga['lang']['months_short'][4],
    $kga['lang']['months_short'][5],
    $kga['lang']['months_short'][6],
    $kga['lang']['months_short'][7],
    $kga['lang']['months_short'][8],
    $kga['lang']['months_short'][9],
    $kga['lang']['months_short'][10],
    $kga['lang']['months_short'][11]
));

// assign view placeholders
$view->assign('current_timer_hour', $current_timer['hour']);
$view->assign('current_timer_min', $current_timer['min']);
$view->assign('current_timer_sec', $current_timer['sec']);
$view->assign('current_timer_start', $current_timer['all'] ? $current_timer['all'] : time());
$view->assign('current_time', time());

$view->assign('timeframe_in', $in);
$view->assign('timeframe_out', $out);

$view->assign('kga', $kga);

$view->assign('extensions', $extensions->extensionsTabData());
$view->assign('css_extension_files', $extensions->cssExtensionFiles());
$view->assign('js_extension_files', $extensions->jsExtensionFiles());

$view->assign('currentRecording', -1);

if (isset($kga['user'])) {
    $view->assign('user', Kimai_Registry::getUser());
    $currentRecordings = $database->get_current_recordings($kga['user']['userID']);
    if (count($currentRecordings) > 0) {
        $view->assign('currentRecording', $currentRecordings[0]);
    }
}

$view->assign('openAfterRecorded', $kga->getSettings()->isShowAfterRecorded());
$view->assign('lang_checkUsername', $kga['lang']['checkUsername']);
$view->assign('lang_checkGroupname', $kga['lang']['checkGroupname']);
$view->assign('lang_checkStatusname', $kga['lang']['checkStatusname']);
$view->assign('lang_checkGlobalRoleName', $kga['lang']['checkGlobalRoleName']);
$view->assign('lang_checkMembershipRoleName', $kga['lang']['checkMembershipRoleName']);

$customerData = ['customerID' => false, 'name' => ''];
$projectData = ['projectID' => false, 'name' => ''];
$activityData = ['activityID' => false, 'name' => ''];

if (!isset($kga['customer'])) {
    //$lastTimeSheetRecord = $database->timeSheet_get_data(false);
    $lastProject = $database->project_get_data($kga['user']['lastProject']);
    $lastActivity = $database->activity_get_data($kga['user']['lastActivity']);
    if (!$lastProject['trash']) {
        $projectData = $lastProject;
        $customerData = $database->customer_get_data($lastProject['customerID']);
    }
    if (!$lastActivity['trash']) {
        $activityData = $lastActivity;
    }
}
$view->assign('customerData', $customerData);
$view->assign('projectData', $projectData);
$view->assign('activityData', $activityData);

// =========================================
// = INCLUDE EXTENSION PHP FILE            =
// =========================================
foreach ($extensions->phpIncludeFiles() as $includeFile) {
    require_once $includeFile;
}

// =======================
// = display user table =
// =======================
if (isset($kga['customer'])) {
    $view->assign('users', $database->get_customer_watchable_users($kga['customer']));
} else {
    $view->assign('users', $database->get_user_watchable_users($kga['user']));
}
$view->assign('user_display', $view->render('lists/users.php'));

// ==========================
// = display customer table =
// ========================
if (isset($kga['customer'])) {
    $view->assign('customers', [
        [
            'customerID' => $kga['customer']['customerID'],
            'name' => $kga['customer']['name'],
            'visible' => $kga['customer']['visible']
        ]
    ]);
} else {
    $view->assign('customers', $database->get_customers($kga['user']['groups']));
}

$view->assign('show_customer_add_button', isset($kga['user']) && coreObjectActionAllowed('customer', 'add'));
$view->assign('show_customer_edit_button', isset($kga['user']) && coreObjectActionAllowed('customer', 'edit'));

$view->assign('customer_display', $view->render("lists/customers.php"));

// =========================
// = display project table =
// =========================
if (isset($kga['customer'])) {
    $view->assign('projects', $database->get_projects_by_customer($kga['customer']['customerID']));
} else {
    $view->assign('projects', $database->get_projects($kga['user']['groups']));
}

$view->assign('show_project_add_button', isset($kga['user']) && coreObjectActionAllowed('project', 'add'));
$view->assign('show_project_edit_button', isset($kga['user']) && coreObjectActionAllowed('project', 'edit'));

$view->assign('project_display', $view->render('lists/projects.php'));

// ========================
// = display activity table =
// ========================
if (isset($kga['customer'])) {
    $view->assign('activities', $database->get_activities_by_customer($kga['customer']['customerID']));
} elseif ($projectData['projectID']) {
    $view->assign('activities', $database->get_activities_by_project($projectData['projectID'], $kga['user']['groups']));
} else {
    $view->assign('activities', $database->get_activities($kga['user']['groups']));
}

$view->assign('show_activity_add_button', isset($kga['user']) && coreObjectActionAllowed('activity', 'add'));
$view->assign('show_activity_edit_button', isset($kga['user']) && coreObjectActionAllowed('activity', 'edit'));

$view->assign('activity_display', $view->render("lists/activities.php"));

if (isset($kga['user'])) {
    $view->assign('showInstallWarning', file_exists(WEBROOT . 'installer'));
} else {
    $view->assign('showInstallWarning', false);
}

// BUILD HOOK FUNCTIONS
$view->assign('hook_timeframe_changed', $extensions->timeframeChangedHooks());
$view->assign('hook_buzzer_record', $extensions->buzzerRecordHooks());
$view->assign('hook_buzzer_stopped', $extensions->buzzerStopHooks());
$view->assign('hook_users_changed', $extensions->usersChangedHooks());
$view->assign('hook_customers_changed', $extensions->customersChangedHooks());
$view->assign('hook_projects_changed', $extensions->projectsChangedHooks());
$view->assign('hook_activities_changed', $extensions->activitiesChangedHooks());
$view->assign('hook_filter', $extensions->filterHooks());
$view->assign('hook_resize', $extensions->resizeHooks());
$view->assign('timeoutlist', $extensions->timeoutList());

echo $view->render('core/main.php');
