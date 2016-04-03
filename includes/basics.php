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

defined('WEBROOT') || define('WEBROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            '.',
            realpath(APPLICATION_PATH . '/libraries/'),
        )
    )
);

if (!file_exists(WEBROOT . 'includes/autoconf.php')) {
    header('Location: installer/index.php');
    exit;
}

ini_set('display_errors', '0');

require_once WEBROOT . '/libraries/autoload.php';
require_once WEBROOT . 'includes/func.php';

// The $kga (formerly Kimai Global Array) is initialized here
// It was replaced by an proxy object, but by now it is still used everywhere as array
require_once WEBROOT . 'includes/autoconf.php';
$kga = new Kimai_Config(array(
    'server_prefix' => $server_prefix,
    'server_hostname' => $server_hostname,
    'server_database' => $server_database,
    'server_username' => $server_username,
    'server_password' => $server_password,
    'defaultTimezone' => $defaultTimezone,
    'password_salt' => isset($password_salt) ? $password_salt : ''
));

include WEBROOT . 'includes/version.php';

// write vars from autoconf.php into kga
if (isset($language))       { $kga->set('language', $language); }
if (isset($authenticator))  { $kga->set('authenticator', $authenticator); }
if (isset($billable))       { $kga->set('billable', $billable); }
if (isset($skin))           { $kga->set('skin', $skin); }

date_default_timezone_set($defaultTimezone);

Kimai_Registry::setConfig($kga);

// ============ global namespace cleanup ============
// remove some variables from the global namespace, that should either be
// not accessible or which are available through the kga config object
$cleanup = array(
    'server_prefix', 'server_hostname', 'server_database', 'server_username', 'server_password',
    'language', 'password_salt', 'authenticator', 'defaultTimezone', 'billable', 'skin'
);

foreach ($cleanup as $varName) {
    if (isset($$varName)) {
        unset($$varName);
    }
}

unset($cleanup);

// ============ setup database ============
// we do not unset the $database variable
// as it is historically referenced in many places from the global namespace
$database = new Kimai_Database_Mysql($kga, true);
if (!$database->isConnected()) {
    die('Kimai could not connect to database, check autoconf.php: '.$database->getLastError());
}
Kimai_Registry::setDatabase($database);

// ============ setup authenticator ============
$auth = $kga->get('authenticator');
$authClass = 'Kimai_Auth_' . ucfirst($auth);
if (!class_exists($authClass)) {
    $authClass = 'Kimai_Auth_Kimai';
}
$authPlugin = new $authClass($database, $kga);
Kimai_Registry::setAuthenticator($authPlugin);
unset($authPlugin);

// ============ load global configurations ============
$diffs = array(); // FIXME remove me after configs are cleared up
$allConf = $database->getConfigurationData();
if (!empty($allConf))
{
    foreach ($allConf as $key => $value) {
        switch($key) {
            case 'date_format_0';
                $kga->setDateFormat(0, $value);
                break;
            case 'date_format_1';
                $kga->setDateFormat(1, $value);
                break;
            case 'date_format_2';
                $kga->setDateFormat(2, $value);
                break;
            case 'date_format_3';
                $kga->setDateFormat(3, $value);
                break;

            case 'language';
                if (!empty($value)) {
                    $kga->set($key, $value);
                }
                break;

            case 'currency_name':
            case 'currency_sign':
            case 'show_sensible_data':
            case 'show_update_warn':
            case 'check_at_startup':
            case 'show_daySeperatorLines':
            case 'show_gabBreaks':
            case 'show_RecordAgain':
            case 'show_TrackingNr':
            case 'adminmail':
            case 'revision':
            case 'version':
            case 'allowRoundDown':
                $kga->set($key, $value);
                break;

            // FIXME remove me after configs are cleared up
            default:
                $diffs[$key] = $value;
        }

        // TODO this is currently backward compatibility, we need to cleanup the config namespaces:
        // settings which can be overwritten by the user belong to => $kga->getSettings()
        // global configs, which are "owned" by admins only belong into => $kga
        /*
            ["currency_first"]
            ["decimalSeparator"]
            ["defaultStatusID"]
            ["defaultVat"]
            ["durationWithSeconds"]
            ["editLimit"]
            ["exactSums"]
            ["login"]
            ["loginBanTime"] ["loginTries"]
            ["roundPrecision"]
            ["roundTimesheetEntries"] ["roundMinutes"] ["roundSeconds"]

        */

        $kga->getSettings()->set($key, $value);
    }
}
unset($allConf);

// ============ status entries ============
$kga->setStatuses($database->getStatuses());

// ============ setup translation object ============
$service = new Kimai_Translation_Service();
Kimai_Registry::setTranslation(
    $service->load(
        $kga->getLanguage()
    )
);
unset($service);
