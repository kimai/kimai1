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
include('private_db_layer_'.$kga['server_conn'].'.php');
checkUser();


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

if (isset($kga['usr'])) // user logged in
  $arr_exp = get_arr_exp($in,$out,array($kga['usr']['usr_ID']),null,null,1);
else // customer logged in
  $arr_exp = get_arr_exp($in,$out,null,array($kga['customer']['knd_ID']),null,1);

if (count($arr_exp)>0) {
    $tpl->assign('arr_exp', $arr_exp);
} else {
    $tpl->assign('arr_exp', 0);
}
$tpl->assign('total', "");



if (isset($kga['usr'])) // user logged in
  $ann = get_arr_exp_usr($in,$out,array($kga['usr']['usr_ID']));
else // customer logged in
  $ann = get_arr_exp_usr($in,$out,null,array($kga['customer']['knd_ID']));
$ann = formatCurrency($ann);
$tpl->assign('usr_ann',$ann);

// TODO: function for loops or convert it in template with new function
if (isset($kga['usr'])) // user logged in
  $ann = get_arr_exp_knd($in,$out,array($kga['usr']['usr_ID']));
else // customer logged in
  $ann = get_arr_exp_knd($in,$out,null,array($kga['customer']['knd_ID']));
$ann = formatCurrency($ann);
$tpl->assign('knd_ann',$ann);

if (isset($kga['usr'])) // user logged in
  $ann = get_arr_exp_pct($in,$out,array($kga['usr']['usr_ID']));
else // customer logged in
  $ann = get_arr_exp_pct($in,$out,null,array($kga['customer']['knd_ID']));
$ann = formatCurrency($ann);
$tpl->assign('pct_ann',$ann);

$tpl->assign('exp_display', $tpl->fetch("exp.tpl"));

$tpl->display('main.tpl');

?>