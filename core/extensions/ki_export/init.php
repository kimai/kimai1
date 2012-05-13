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

require("private_func.php");

$user = $database->checkUser();

// ============================================
// = initialize currently displayed timespace =
// ============================================
$timespace = get_timespace();
$in = $timespace[0];
$out = $timespace[1];

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

$timeformat = 'H:M';
$dateformat = 'd.m.';
$tpl->assign('timeformat',$timeformat);
$tpl->assign('dateformat',$dateformat);

$tpl->display('panel.tpl');


$tpl->assign('timeformat',preg_replace('/([A-Za-z])/','%$1',$timeformat));
$tpl->assign('dateformat',preg_replace('/([A-Za-z])/','%$1',$dateformat));

// Get the total amount of time shown in the table.
if (isset($kga['customer']))
  $total = Format::formatDuration($database->get_duration($in,$out,null,array($kga['customer']['customerID']),null));
else
  $total = Format::formatDuration($database->get_duration($in,$out,array($kga['user']['userID']),null,null));

if (isset($kga['customer']))
  $timeSheetEntries = export_get_data($in,$out,null,array($kga['customer']['customerID']));
else
  $timeSheetEntries = export_get_data($in,$out,array($kga['user']['userID']));

if (count($timeSheetEntries)>0) {
    $tpl->assign('exportData', $timeSheetEntries);
} else {
    $tpl->assign('exportData', 0);
}

$tpl->assign('total', $total);

// Get the annotations for the user sub list.
if (isset($kga['customer']))
  $ann = export_get_user_annotations($in,$out,null,array($kga['customer']['customerID']));
else
  $ann = export_get_user_annotations($in,$out,array($kga['user']['userID']));
Format::formatAnnotations($ann);
$tpl->assign('user_annotations',$ann);

// Get the annotations for the customer sub list.
if (isset($kga['customer']))
  $ann = export_get_customer_annotations($in,$out,null,array($kga['customer']['customerID']));
else
  $ann = export_get_customer_annotations($in,$out,array($kga['user']['userID']));
Format::formatAnnotations($ann);
$tpl->assign('customer_annotations',$ann);

// Get the annotations for the project sub list.
if (isset($kga['customer']))
  $ann = export_get_project_annotations($in,$out,null,array($kga['customer']['customerID']));
else
  $ann = export_get_project_annotations($in,$out,array($kga['user']['userID']));
Format::formatAnnotations($ann);
$tpl->assign('project_annotations',$ann);

// Get the annotations for the task sub list.
if (isset($kga['customer']))
  $ann = export_get_activity_annotations($in,$out,null,array($kga['customer']['customerID']));
else
  $ann = export_get_activity_annotations($in,$out,array($kga['user']['userID']));
Format::formatAnnotations($ann);
$tpl->assign('activity_annotations',$ann);

// Get the columns the user had disabled last time.
if (isset($kga['user']))
  $tpl->assign('disabled_columns',export_get_disabled_headers($kga['user']['userID']));

$tpl->assign('table_display', $tpl->fetch("table.tpl"));

$tpl->display('main.tpl');

?>