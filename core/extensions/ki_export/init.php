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

$usr = checkUser();

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
  $total = formatDuration(get_zef_time($in,$out,null,array($kga['customer']['knd_ID']),null));
else
  $total = formatDuration(get_zef_time($in,$out,array($kga['usr']['usr_ID']),null,null));

if (isset($kga['customer']))
  $arr_zef = xp_get_arr($in,$out,null,array($kga['customer']['knd_ID']));
else
  $arr_zef = xp_get_arr($in,$out,array($kga['usr']['usr_ID']));

if (count($arr_zef)>0) {
    $tpl->assign('arr_data', $arr_zef);
} else {
    $tpl->assign('arr_data', 0);
}

$tpl->assign('total', $total);

// Get the annotations for the user sub list.
if (isset($kga['customer']))
  $ann = xp_get_arr_usr($in,$out,null,array($kga['customer']['knd_ID']));
else
  $ann = xp_get_arr_usr($in,$out,array($kga['usr']['usr_ID']));
formatAnnotations($ann);
$tpl->assign('usr_ann',$ann);

// Get the annotations for the customer sub list.
if (isset($kga['customer']))
  $ann = xp_get_arr_knd($in,$out,null,array($kga['customer']['knd_ID']));
else
  $ann = xp_get_arr_knd($in,$out,array($kga['usr']['usr_ID']));
formatAnnotations($ann);
$tpl->assign('knd_ann',$ann);

// Get the annotations for the project sub list.
if (isset($kga['customer']))
  $ann = xp_get_arr_pct($in,$out,null,array($kga['customer']['knd_ID']));
else
  $ann = xp_get_arr_pct($in,$out,array($kga['usr']['usr_ID']));
formatAnnotations($ann);
$tpl->assign('pct_ann',$ann);

// Get the annotations for the task sub list.
if (isset($kga['customer']))
  $ann = xp_get_arr_evt($in,$out,null,array($kga['customer']['knd_ID']));
else
  $ann = xp_get_arr_evt($in,$out,array($kga['usr']['usr_ID']));
formatAnnotations($ann);
$tpl->assign('evt_ann',$ann);

// Get the columns the user had disabled last time.
if (isset($kga['usr']))
  $tpl->assign('disabled_columns',xp_get_disabled_headers($kga['usr']['usr_ID']));

$tpl->assign('table_display', $tpl->fetch("table.tpl"));

$tpl->display('main.tpl');

?>