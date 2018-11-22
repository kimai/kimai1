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

/**
 * Basic initialization takes place here.
 * From loading the configuration to connecting to the database this all is done
 * here.
 */

defined('WEBROOT') || define('WEBROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

if (!file_exists(WEBROOT . 'libraries/autoload.php')) {
    die('Please run <code>composer install --no-dev</code> on the command line to install all php dependencies.');
}

set_include_path(
    implode(
        PATH_SEPARATOR,
        [
            '.',
            realpath(APPLICATION_PATH . 'libraries/'),
        ]
    )
);

if (!file_exists(WEBROOT . 'includes/autoconf.php')) {
    header('Location: installer/index.php');
    exit;
}

ini_set('display_errors', '0');

require_once WEBROOT . 'libraries/autoload.php';
require_once WEBROOT . 'includes/func.php';

// The $kga (formerly Kimai Global Array) is initialized here
// It was replaced by an proxy object, but until refactored it is still used as array in a lot of places
require_once WEBROOT . 'includes/autoconf.php';
$kga = new Kimai_Config([
    'server_prefix' => $server_prefix,
    'server_hostname' => $server_hostname,
    'server_database' => $server_database,
    'server_username' => $server_username,
    'server_password' => $server_password,
    'server_charset' => $server_charset,
    'defaultTimezone' => $defaultTimezone,
    'password_salt' => isset($password_salt) ? $password_salt : ''
]);

// will inject the version variables into the Kimai_Config object
require WEBROOT . 'includes/version.php';

// write vars from autoconf.php into kga
if (isset($language)) {
    $kga->setLanguage($language);
}
if (isset($authenticator)) {
    $kga->setAuthenticator($authenticator);
}
if (isset($billable)) {
    $kga->setBillable($billable);
}
if (isset($skin)) {
    $kga->setSkin($skin);
}

date_default_timezone_set($defaultTimezone);

Kimai_Registry::setConfig($kga);

// ============ global namespace cleanup ============
// remove some variables from the global namespace, that should either be
// not accessible or which are available through the kga config object
$cleanup = [
    'server_prefix',
    'server_hostname',
    'server_database',
    'server_username',
    'server_password',
    'server_charset',
    'language',
    'password_salt',
    'authenticator',
    'defaultTimezone',
    'billable',
    'skin'
];

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
    die('Kimai could not connect to database. Check your autoconf.php.');
}
Kimai_Registry::setDatabase($database);

// ============ setup authenticator ============
$authClass = 'Kimai_Auth_' . ucfirst($kga->getAuthenticator());
if (!class_exists($authClass)) {
    $authClass = 'Kimai_Auth_Kimai';
}
$authPlugin = new $authClass($database, $kga);
Kimai_Registry::setAuthenticator($authPlugin);
unset($authPlugin);

// ============ load global configurations ============
$database->initializeConfig($kga);

// ============ setup translation object ============
$service = new Kimai_Translation_Service();
Kimai_Registry::setTranslation(
    $service->load(
        $kga->getLanguage()
    )
);
unset($service);

$tmpDir = WEBROOT . 'temporary/';
if (!file_exists($tmpDir) || !is_dir($tmpDir) || !is_writable($tmpDir)) {
    die('Kimai needs write permissions for: temporary/');
}

$frontendOptions = ['lifetime' => 7200, 'automatic_serialization' => true];
$backendOptions = ['cache_dir' => $tmpDir];
$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
Kimai_Registry::setCache($cache);
Zend_Locale::setCache($cache);
