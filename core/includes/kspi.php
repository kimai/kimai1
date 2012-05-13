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

/**
 * The Kimai Standard Processor Initialization.
 * This is used by all processor.php files. General setup stuff is done here.
 */

/**
 * ==================================================================
 * Bootstrap Zend
 * ==================================================================
 *
 * - Ensure library/ is on include_path
 * - Register Autoloader
 */
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(APPLICATION_PATH . '/libraries/'),
        )
    )
);

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

// ==================================
// = implementing standard includes =
// ==================================
require("basics.php");

if (!$isCoreProcessor) {
  $datasrc = "config.ini";
  $settings = parse_ini_file($datasrc);
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


// ============================================================================================
// = assigning language and config variables / they are needed in all following smarty output =
// ============================================================================================
$user = checkUser();

$tpl->assign('kga',$kga);

$commentTypes   = array($kga['lang']['ctype0'],$kga['lang']['ctype1'],$kga['lang']['ctype2']);

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
// = initialize currently displayed timeframe =
// ============================================
$timeframe = get_timeframe();
$in = $timeframe[0];
$out = $timeframe[1];

if (isset($_REQUEST['first_day']))
  $in  = (int)$_REQUEST['first_day'];
if (isset($_REQUEST['last_day']))
  $out = mktime(23,59,59,date("n",$_REQUEST['last_day']),date("j",$_REQUEST['last_day']),date("Y",$_REQUEST['last_day']));

if ($axAction != "reloadLogfile") {
    Logger::logfile("KSPI axAction (".(array_key_exists('customer',$kga)?$kga['customer']['name']:$kga['user']['name'])."): " . $axAction);
}


// prevent IE from caching the response
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

?>