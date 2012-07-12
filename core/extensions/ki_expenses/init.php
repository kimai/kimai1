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

if (isset($kga['user'])) // user logged in
  $expenses = get_expenses($in,$out,array($kga['user']['userID']),null,null,1);
else // customer logged in
  $expenses = get_expenses($in,$out,null,array($kga['customer']['customerID']),null,1);

if (count($expenses)>0) {
    $tpl->assign('expenses', $expenses);
} else {
    $tpl->assign('expenses', 0);
}
$tpl->assign('total', "");



if (isset($kga['user'])) // user logged in
  $ann = expenses_by_user($in,$out,array($kga['user']['userID']));
else // customer logged in
  $ann = expenses_by_user($in,$out,null,array($kga['customer']['customerID']));
$ann = Format::formatCurrency($ann);
$tpl->assign('user_annotations',$ann);

// TODO: function for loops or convert it in template with new function
if (isset($kga['user'])) // user logged in
  $ann = expenses_by_customer($in,$out,array($kga['user']['userID']));
else // customer logged in
  $ann = expenses_by_customer($in,$out,null,array($kga['customer']['customerID']));
$ann = Format::formatCurrency($ann);
$tpl->assign('customer_annotations',$ann);

if (isset($kga['user'])) // user logged in
  $ann = expenses_by_project($in,$out,array($kga['user']['userID']));
else // customer logged in
  $ann = expenses_by_project($in,$out,null,array($kga['customer']['customerID']));
$ann = Format::formatCurrency($ann);
$tpl->assign('project_annotations',$ann);

if (isset($kga['user']))
  $tpl->assign('hideComments',$database->user_get_preference('ui.showCommentsByDefault')!=1);
else
  $tpl->assign('hideComments',true);

$tpl->assign('expenses_display', $tpl->fetch("expenses.tpl"));

$tpl->display('main.tpl');

?>