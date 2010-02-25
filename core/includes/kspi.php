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
 * along with Kimai; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */


// ==============
// = Get config =
// ==============
if(file_exists(realpath(dirname(__FILE__).'/conf.php')))
	require_once(realpath(dirname(__FILE__).'/conf.php'));
require("autoconf.php");

require_once(WEBROOT."/libraries/Config.php");

if (!$isCoreProcessor) {
    $datasrc = "config.ini";
    $phpIni = new Config();
    $root =& $phpIni->parseConfig($datasrc, 'inicommented');
    $settings = $root->toArray();
    $settings = $settings['root'];
    $dir_ext = $settings['EXTENSION_DIR'];
}

// =============================
// = Smarty (initialize class) =
// =============================
require_once(WEBROOT . 'libraries/smarty/Smarty.class.php');
$tpl = new Smarty();
if ($isCoreProcessor) {
    $tpl->template_dir = WEBROOT . $dir_templates;
    $tpl->compile_dir  = WEBROOT . 'compile/';
} else {
    $tpl->template_dir = WEBROOT . 'extensions/' . $dir_ext . '/' . $dir_templates;
    $tpl->compile_dir  = WEBROOT . 'extensions/' . $dir_ext . '/' . 'compile/';
}

// ==================================
// = implementing standard includes =
// ==================================
include(WEBROOT . 'includes/basics.php');

// ============================================================================================
// = assigning language and config variables / they are needed in all following smarty output =
// ============================================================================================
$usr = checkUser();

$tpl->assign('kga',$kga);

$comment_types   = array($kga['lang']['ctype0'],$kga['lang']['ctype1'],$kga['lang']['ctype2']);

// ==================
// = security check =
// ==================
if ( isset($_REQUEST['axAction']) && !is_array($_REQUEST['axAction']) && $_REQUEST['axAction']!="") {
    $axAction = strip_tags($_REQUEST['axAction']);
} else {
    $axAction = '';
}

$axValue = isset($_REQUEST['axValue']) ? strip_tags($_REQUEST['axValue']) : '';
$id = isset($_REQUEST['id']) ? strip_tags($_REQUEST['id']) : null;


// ============================================
// = initialize currently displayed timespace =
// ============================================
// $in  = (int)mktime(0,0,0,$fromMonth,$fromDay,$fromYear);
// $out = (int)mktime(23,59,59,$toMonth,$toDay,$toYear);
$timespace = get_timespace();
$in = $timespace[0];
$out = $timespace[1];

  if (isset($_REQUEST['first_day']))
    $in  = (int)$_REQUEST['first_day'];
  if (isset($_REQUEST['last_day']))
    $out = mktime(23,59,59,date("n",$_REQUEST['last_day']),date("j",$_REQUEST['last_day']),date("Y",$_REQUEST['last_day']));

if ($axAction != "reloadLogfile") {
    logfile("KSPI axAction (".(array_key_exists('customer',$kga)?$kga['customer']['knd_name']:$kga['usr']['usr_name'])."): " . $axAction);
}

?>
