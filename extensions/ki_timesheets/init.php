<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
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

// ==================================
// = implementing standard includes =
// ==================================
include('../../includes/basics.php');

$database = Kimai_Registry::getDatabase();

$dir_templates = 'templates/';
$datasrc = 'config.ini';
$settings = parse_ini_file($datasrc);
$dir_ext = $settings['EXTENSION_DIR'];

$user = checkUser();

// ============================================
// = initialize currently displayed timeframe =
// ============================================
$timeframe = get_timeframe();
$in = $timeframe[0];
$out = $timeframe[1];

$view = new Zend_View();
$view->setBasePath(WEBROOT . 'extensions/' . $dir_ext . '/' . $dir_templates);
$view->addHelperPath(WEBROOT . 'templates/helpers', 'Zend_View_Helper');

$view->assign('kga', $kga);

// prevent IE from caching the response
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Get the total time displayed in the table.
if (isset($kga['customer'])) {
    $total = Kimai_Format::formatDuration($database->get_duration($in, $out, null, [$kga['customer']['customerID']], null));
} else {
    $total = Kimai_Format::formatDuration($database->get_duration($in, $out, [$kga['user']['userID']], null, null));
}
$view->assign('total', $total);

// Get the array of timesheet entries.
if (isset($kga['customer'])) {
    $timeSheetEntries = $database->get_timeSheet($in, $out, null, [$kga['customer']['customerID']], null, 1);
    $view->assign('latest_running_entry', null);
} else {
    $timeSheetEntries = $database->get_timeSheet($in, $out, [$kga['user']['userID']], null, null, 1);
    $view->assign('latest_running_entry', $database->get_latest_running_entry());
}

if (count($timeSheetEntries) > 0) {
    $view->assign('timeSheetEntries', $timeSheetEntries);
} else {
    $view->assign('timeSheetEntries', 0);
}

// Get the annotations for the user sub list.
if (isset($kga['customer'])) {
    $ann = $database->get_time_users($in, $out, null, [$kga['customer']['customerID']]);
} else {
    $ann = $database->get_time_users($in, $out, [$kga['user']['userID']]);
}
Kimai_Format::formatAnnotations($ann);
$view->assign('user_annotations', $ann);

// Get the annotations for the customer sub list.
if (isset($kga['customer'])) {
    $ann = $database->get_time_customers($in, $out, null, [$kga['customer']['customerID']]);
} else {
    $ann = $database->get_time_customers($in, $out, [$kga['user']['userID']]);
}
Kimai_Format::formatAnnotations($ann);
$view->assign('customer_annotations', $ann);

// Get the annotations for the project sub list.
if (isset($kga['customer'])) {
    $ann = $database->get_time_projects($in, $out, null, [$kga['customer']['customerID']]);
} else {
    $ann = $database->get_time_projects($in, $out, [$kga['user']['userID']]);
}
Kimai_Format::formatAnnotations($ann);
$view->assign('project_annotations', $ann);

// Get the annotations for the activity sub list.
if (isset($kga['customer'])) {
    $ann = $database->get_time_activities($in, $out, null, [$kga['customer']['customerID']]);
} else {
    $ann = $database->get_time_activities($in, $out, [$kga['user']['userID']]);
}
Kimai_Format::formatAnnotations($ann);
$view->assign('activity_annotations', $ann);

$view->assign('hideComments', true);
$view->assign('showOverlapLines', false);
$view->assign('showTrackingNumber', false);

$showBillability = false;
$inlineEditingOfDescriptions = false;
if (isset($kga['user'])) {
    $view->assign('hideComments', !$kga->getSettings()->isShowComments());
    $view->assign('showOverlapLines', $kga->getSettings()->isShowOverlapLines());
    $view->assign('showTrackingNumber', $kga->isTrackingNumberEnabled() && $kga->getSettings()->isShowTrackingNumber());
    $showBillability = $kga->getSettings()->isShowBillability();
    $inlineEditingOfDescriptions = $kga->getSettings()->isInlineEditingOfDescriptionsSet();
}

$view->assign('showBillability', $showBillability);
$view->assign('inlineEditingOfDescriptions', $inlineEditingOfDescriptions);
$view->assign('showRates', isset($kga['user']) && $database->global_role_allows($kga['user']['globalRoleID'], 'ki_timesheets-showRates'));

$defaults = [
    'table_time_format' => $kga->getTableTimeFormat()
];
$prefs = $database->user_get_preferences_by_prefix('ui.');
$view->assign('prefs', array_merge($defaults, $prefs));
$view->assign('timeSheet_display', $view->render("timeSheet.php"));

$view->assign('buzzerAction', "startRecord()");

// select for projects
if (isset($kga['customer'])) {
    $view->assign('projects', []);
} else {
    $sel = makeSelectBox("project", $kga['user']['groups']);
    $view->assign('projects', $sel);
}

// select for activities
if (isset($kga['customer'])) {
    $view->assign('activities', []);
} else {
    $sel = makeSelectBox("activity", $kga['user']['groups']);
    $view->assign('activities', $sel);
}

echo $view->render('main.php');
