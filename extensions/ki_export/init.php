<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
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

include '../../includes/basics.php';
require "private_func.php";

$user = checkUser();

// ============================================
// = initialize currently displayed timeframe =
// ============================================
$timeframe = get_timeframe();
$in = $timeframe[0];
$out = $timeframe[1];

$view = new Kimai_View();
$view->addBasePath(__DIR__ . '/templates/');

// prevent IE from caching the response
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$timeformat = 'H:M';
$dateformat = $kga['date_format'][1];
$view->assign('timeformat', $timeformat);
$view->assign('dateformat', $dateformat);

echo $view->render('panel.php');

$view->assign('timeformat', preg_replace('/([A-Za-z])/', '%$1', $timeformat));

$users = null;
$customers = null;

if (isset($kga['customer'])) {
    $customers = array($kga['customer']['customerID']);
} else {
    $users = array($kga['user']['userID']);
}

// Get the total amount of time shown in the table.
$total = Kimai_Format::formatDuration($database->get_duration($in, $out, $users, $customers, null));
$view->assign('total', $total);
$view->assign('exportData', export_get_data($in, $out, $users, $customers));

// Get the annotations for the user sub list.
$userAnnotations = export_get_user_annotations($in, $out, $users, $customers);
Kimai_Format::formatAnnotations($userAnnotations);
$view->assign('user_annotations', $userAnnotations);

// Get the annotations for the customer sub list.
$customerAnnotations = export_get_customer_annotations($in, $out, $users, $customers);
Kimai_Format::formatAnnotations($customerAnnotations);
$view->assign('customer_annotations', $customerAnnotations);

// Get the annotations for the project sub list.
$projectAnnotations = export_get_project_annotations($in, $out, $users, $customers);
Kimai_Format::formatAnnotations($projectAnnotations);
$view->assign('project_annotations', $projectAnnotations);

// Get the annotations for the activity sub list.
$activityAnnotations = export_get_activity_annotations($in, $out, $users, $customers);
Kimai_Format::formatAnnotations($activityAnnotations);
$view->assign('activity_annotations', $activityAnnotations);

// Get the columns the user had disabled last time.
if (isset($kga['user'])) {
    $view->assign('disabled_columns', export_get_disabled_headers($kga['user']['userID']));
}

$view->assign('table_display', $view->render("table.php"));

echo $view->render('main.php');
