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

// ==================================
// = implementing standard includes =
// ==================================
include('../../includes/basics.php');

$user = $database->checkUser();

// ============================================
// = initialize currently displayed timeframe =
// ============================================
$timeframe = get_timeframe();
$in = $timeframe[0];
$out = $timeframe[1];

// set smarty config
require_once('../../libraries/smarty/Smarty.class.php');
$tpl = new Smarty();
$tpl->template_dir = 'templates/';
$tpl->compile_dir  = 'compile/';

$tpl->assign('kga', $kga);

// prevent IE from caching the response
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


// Get the total time displayed in the table.
if (isset($kga['customer']))
  $total = Format::formatDuration($database->get_duration($in,$out,null,array($kga['customer']['customerID']),null));
else
  $total = Format::formatDuration($database->get_duration($in,$out,array($kga['user']['userID']),null,null));
$tpl->assign('total', $total);

// Get the array of timesheet entries.
if (isset($kga['customer']))
  $timeSheetEntries = $database->get_timeSheet($in,$out,null,array($kga['customer']['customerID']),null,1);
else
  $timeSheetEntries = $database->get_timeSheet($in,$out,array($kga['user']['userID']),null,null,1);
if (count($timeSheetEntries)>0) {
    $tpl->assign('timeSheetEntries', $timeSheetEntries);
} else {
    $tpl->assign('timeSheetEntries', 0);
}

// Get the annotations for the user sub list.
if (isset($kga['customer']))
  $ann = $database->get_time_users($in,$out,null,array($kga['customer']['customerID']));
else
  $ann = $database->get_time_users($in,$out,array($kga['user']['userID']));
Format::formatAnnotations($ann);
$tpl->assign('user_annotations',$ann);

// Get the annotations for the customer sub list.
if (isset($kga['customer']))
  $ann = $database->get_time_customers($in,$out,null,array($kga['customer']['customerID']));
else
  $ann = $database->get_time_customers($in,$out,array($kga['user']['userID']));
Format::formatAnnotations($ann);
$tpl->assign('customer_annotations',$ann);

// Get the annotations for the project sub list.
if (isset($kga['customer']))
  $ann = $database->get_time_projects($in,$out,null,array($kga['customer']['customerID']));
else
  $ann = $database->get_time_projects($in,$out,array($kga['user']['userID']));
Format::formatAnnotations($ann);
$tpl->assign('project_annotations',$ann);

// Get the annotations for the task sub list.
if (isset($kga['customer']))
  $ann = $database->get_time_activities($in,$out,null,array($kga['customer']['customerID']));
else
  $ann = $database->get_time_activities($in,$out,array($kga['user']['userID']));
Format::formatAnnotations($ann);
$tpl->assign('activity_annotations',$ann);

if (isset($kga['user']))
  $tpl->assign('hideComments',$database->user_get_preference('ui.showCommentsByDefault')!=1);
else
  $tpl->assign('hideComments',true);

if (isset($kga['user']))
  $tpl->assign('showOverlapLines',$database->user_get_preference('ui.hideOverlapLines')!=1);
else
  $tpl->assign('showOverlapLines',false);

$tpl->assign('timeSheet_display', $tpl->fetch("timeSheet.tpl"));

$tpl->assign('buzzerAction', "startRecord()");

// select for projects
if (isset($kga['customer'])) {
  $tpl->assign('projects', array());
}
else {
  $sel = makeSelectBox("project",$kga['user']['groups']);
  $tpl->assign('projects', $sel);
}

// select for activities
if (isset($kga['customer'])) {
  $tpl->assign('activities', array());
}
else {
  $sel = makeSelectBox("activity",$kga['user']['groups']);
  $tpl->assign('activities', $sel);
}

$tpl->display('main.tpl');

?>