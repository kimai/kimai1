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

// ==================================
// = implementing standard includes =
// ==================================
include('../../includes/basics.php');
include('private_db_layer_mysql.php');
checkUser();

$dir_templates = 'templates/';
$datasrc = "config.ini";
$settings = parse_ini_file($datasrc);
$dir_ext = $settings['EXTENSION_DIR'];

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

if (isset($kga['user'])) {
    // user logged in
    $view->assign('expenses', get_expenses($in, $out, [$kga['user']['userID']], null, null, 1));
} else {
    // customer logged in
    $view->assign('expenses', get_expenses($in, $out, null, [$kga['customer']['customerID']], null, 1));
}

$view->assign('total', Kimai_Format::formatCurrency(array_reduce($view->expenses, function ($sum, $expense) {
    return $sum + $expense['multiplier'] * $expense['value'];
}, 0)));


if (isset($kga['user'])) // user logged in
  $ann = expenses_by_user($in, $out, [$kga['user']['userID']]);
else // customer logged in
  $ann = expenses_by_user($in, $out, null, [$kga['customer']['customerID']]);
$ann = Kimai_Format::formatCurrency($ann);
$view->assign('user_annotations', $ann);

// TODO: function for loops or convert it in template with new function
if (isset($kga['user'])) // user logged in
  $ann = expenses_by_customer($in, $out, [$kga['user']['userID']]);
else // customer logged in
  $ann = expenses_by_customer($in, $out, null, [$kga['customer']['customerID']]);
$ann = Kimai_Format::formatCurrency($ann);
$view->assign('customer_annotations', $ann);

if (isset($kga['user'])) // user logged in
  $ann = expenses_by_project($in, $out, [$kga['user']['userID']]);
else // customer logged in
  $ann = expenses_by_project($in, $out, null, [$kga['customer']['customerID']]);
$ann = Kimai_Format::formatCurrency($ann);
$view->assign('project_annotations', $ann);

if (isset($kga['user'])) {
    $view->assign('hideComments', !$kga->getSettings()->isShowComments());
} else {
    $view->assign('hideComments', true);
}

$view->assign('expenses_display', $view->render("expenses.php"));

echo $view->render('main.php');
