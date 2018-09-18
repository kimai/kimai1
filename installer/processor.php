<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
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
 * Handle all AJAX calls from the installer.
 */

defined('WEBROOT') || define('WEBROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

require_once WEBROOT . 'libraries/autoload.php';

// from php documentation at http://www.php.net/manual/de/function.ini-get.php
function return_bytes($val)
{
    $val = trim($val);
    $last = strtolower($val[strlen($val) - 1]);
    if ($last) {
        $val = substr($val, 0, strlen($val) - 1);
    }
    switch ($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

// stolen somewhere ... please forgive me - i don't know who wrote this .... O-o
function getpass()
{
    $newpass = "";
    $laenge = 10;
    $string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    mt_srand((double)microtime() * 1000000);

    for ($i = 1; $i <= $laenge; $i++) {
        $newpass .= substr($string, mt_rand(0, strlen($string) - 1), 1);
    }

    return $newpass;
}

$axAction = strip_tags($_REQUEST['axAction']);

$javascript = '';
$errors = 0;

switch ($axAction) {

    /**
     * Check for the requirements of Kimai:
     *  - PHP major version >= 5.5
     *  - MySQLi extension available
     *  - iconv extension available
     *  - memory limit should be at least 20 MB for reliable PDF export
     */
    case 'checkRequirements':
        if (version_compare(PHP_VERSION, '5.5') < 0) {
            $errors++;
            $javascript .= "$('div.sp_phpversion').addClass('fail');";
        }

        if (!extension_loaded('mysqli')) {
            $errors++;
            $javascript .= "$('div.sp_mysql').addClass('fail');";
        }

        if (!extension_loaded('iconv')) {
            $errors++;
            $javascript .= "$('div.sp_iconv').addClass('fail');";
        }

        if (!class_exists('DOMDocument') || !extension_loaded('dom')) {
            $errors++;
            $javascript .= "$('div.sp_dom').addClass('fail');";
        }
        if (!class_exists('ZipArchive') || !extension_loaded('zip')) {
            $errors++;
            $javascript .= "$('div.sp_zip').addClass('fail');";
        }

        if (return_bytes(ini_get('memory_limit')) < 20000000) {
            $javascript .= "$('div.sp_memory').addClass('fail');";
        }

        if (empty($javascript)) {
            $javascript = "$('#installsteps button.sp-button').hide();";
        }

        if (!$errors) {
            $javascript .= "$('#installsteps button.proceed').show();";
        }

        $javascript .= "resetRequirementsIndicators();" . $javascript;
        echo $javascript;

        break;

    /**
     * Check access rights to autoconf.php, the logfile and the temporary folder.
     */
    case 'checkRights':
        if ((file_exists("../includes/autoconf.php") && !is_writeable("../includes/autoconf.php")) || !is_writeable("../includes/")) {
            $errors++;
            $javascript .= "$('span.ch_autoconf').addClass('fail');";
        }

        if ((file_exists("../temporary/logfile.txt") && !is_writeable("../temporary/logfile.txt")) || !is_writeable("../temporary/")) {
            $errors++;
            $javascript .= "$('span.ch_logfile').addClass('fail');";
        }

        if (!is_writeable("../temporary/")) {
            $errors++;
            $javascript .= "$('span.ch_temporary').addClass('fail');";
        }

        if ($errors) {
            $javascript .= "$('span.ch_correctit').fadeIn(500);";
        } else {
            $javascript = "$('#installsteps button.cp-button').hide();$('#installsteps button.proceed').show();";
        }

        echo $javascript;
        break;

    /**
     * Create the autoconf.php file.
     */
    case 'write_config':
        include '../includes/func.php';
        // special characters " and $ are escaped
        $database = $_REQUEST['database'];
        $hostname = $_REQUEST['hostname'];
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];
        $charset = 'utf8';
        $prefix = addcslashes($_REQUEST['prefix'], '"$');
        $lang = $_REQUEST['lang'];
        $salt = createPassword(20);
        $timezone = $_REQUEST['timezone'];

        $kimaiConfig = new Kimai_Config([
            'server_prefix' => $prefix,
            'server_hostname' => $hostname,
            'server_database' => $database,
            'server_username' => $username,
            'server_password' => $password,
            'server_charset' => $charset,
            'defaultTimezone' => $timezone,
            'password_salt' => $salt
        ]);
        Kimai_Registry::setConfig($kimaiConfig);

        write_config_file($database, $hostname, $username, $password, $charset, $prefix, $lang, $salt, $timezone);

        break;

    /**
     * Create the database.
     */
    case 'make_database':
        $databaseName = $_REQUEST['database'];
        $hostname = $_REQUEST['hostname'];
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];

        $db_error = false;
        $result = false;
        $config = new Kimai_Config([]);

        $database = new Kimai_Database_Mysql($config, false);
        $database->connect($hostname, null, $username, $password, true);
        $conn = $database->getConnectionHandler();

        $query = "CREATE DATABASE `" . $databaseName . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci";
        $result = $conn->Query($query);

        if ($result !== false) {
            echo "1"; // ok
        } else {
            echo "0"; // error
        }
        break;
}
