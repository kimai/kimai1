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
 * Basic initialization takes place here.
 * From loading the configuration to connecting to the database this all is done
 * here.
 */

defined('WEBROOT') ||
    define('WEBROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

defined('APPLICATION_PATH') ||
    define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(APPLICATION_PATH . '/libraries/'),
        )
    )
);

ini_set('display_errors', '0');

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Kimai');

if (file_exists(WEBROOT.'includes/autoconf.php'))
  require(WEBROOT.'includes/autoconf.php');
else {
  header('location:installer/index.php');
  exit;
}

require(WEBROOT.'includes/vars.php');
require(WEBROOT.'includes/classes/format.class.php');
require(WEBROOT.'includes/classes/logger.class.php');
require(WEBROOT.'includes/classes/translations.class.php');
require(WEBROOT.'includes/classes/rounding.class.php');
require(WEBROOT.'includes/func.php');

// ==================================================================================
// = check for additional database(s) and set $kga['server_database'] accordingly   =
// = $kga['server_database'] stays untouched if there is no entry in the            =
// = $server_ext_database array (for more info see /includes/vars.php)              =
// ==================================================================================
if (isset($_REQUEST['database'])) {
    if ($_REQUEST['database']==true) {

        $dbnr = $_REQUEST['database'] - 1;

        $kga['server_database'] = $server_ext_database[$dbnr];

            if ($server_ext_username[$dbnr] != '') {
                $kga['server_username'] = $server_ext_username[$dbnr];
            }
            if ($server_ext_password[$dbnr] != '') {
                $kga['server_password'] = $server_ext_password[$dbnr];
            }
            if ($server_ext_prefix[$dbnr] != '') {
                $kga['server_prefix'] = $server_ext_prefix[$dbnr];
            }
    }
} else {
    if (isset($_COOKIE['kimai_db']) && $_COOKIE['kimai_db'] == true) {

        $dbnr = $_COOKIE['kimai_db'] - 1;

        $kga['server_database'] = $server_ext_database[$dbnr];

            if ($server_ext_username[$dbnr] != '') {
                $kga['server_username'] = $server_ext_username[$dbnr];
            }
            if ($server_ext_password[$dbnr] != '') {
                $kga['server_password'] = $server_ext_password[$dbnr];
            }
            if ($server_ext_prefix[$dbnr] != '') {
                $kga['server_prefix'] = $server_ext_prefix[$dbnr];
            }
    }
}

$database = new Kimai_Database_Mysql($kga);
$database->connect($kga['server_hostname'],$kga['server_database'],$kga['server_username'],$kga['server_password'],$kga['utf8'],$kga['server_type'] );
if (!$database->isConnected()) { die('Kimai could not connect to database. Check your autoconf.php.'); }
Kimai_Registry::setDatabase($database);

global $translations;
$translations = new Translations($kga);
if ($kga['language'] != 'en') {
  $translations->load($kga['language']);
}
Kimai_Registry::setTranslations($translations);

$vars = $database->configuration_get_data();
if (!empty($vars)) {
  $kga['currency_name']          = $vars['currency_name'];
  $kga['currency_sign']          = $vars['currency_sign'];
  $kga['show_sensible_data']     = $vars['show_sensible_data'];
  $kga['show_update_warn']       = $vars['show_update_warn'];
  $kga['check_at_startup']       = $vars['check_at_startup'];
  $kga['show_daySeperatorLines'] = $vars['show_daySeperatorLines'];
  $kga['show_gabBreaks']         = $vars['show_gabBreaks'];
  $kga['show_RecordAgain']       = $vars['show_RecordAgain'];
  $kga['show_TrackingNr']        = $vars['show_TrackingNr'];
  $kga['date_format'][0]         = $vars['date_format_0'];
  $kga['date_format'][1]         = $vars['date_format_1'];
  $kga['date_format'][2]         = $vars['date_format_2'];
  if ($vars['language'] != '')
    $kga['language']             = $vars['language'];
  else if ($kga['language'] == '')
    $kga['language'] = 'en';
}
