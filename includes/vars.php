<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
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
 * The Kimai Global Array ($kga) is initialized here. It is used throught
 * all functions, processors, etc.
 */
$kga = array();

require dirname(__FILE__) . '/version.php';

$kga['show_sensible_data'] = 0;         // set to 1 to display sensible data in the debug/developer extension
                                        // CAUTION - DON'T FORGET TO TURN IT OFF IN A PRODUCTION ENVIRONMENT!!!

$kga['logfile_lines']      = 100;       // number of lines shown from the logfile in debug extension.
                                        // Set to "@" to display the entire file (might freeze your browser...)

$kga['delete_logfile']     = 1;         // activate to be able to flush the logfile via debug extension

$kga['utf8']               = 0;         // set to 1 to activate UTF-8 CONVERSION
                                        // this is not always needed, depends on server settings

$kga['calender_start']     = "0";       // set a custom start day for the date-picker (Format: "DD/MM/YYYY")
                                        // if this is not set the day of the users first day in the system will be taken

// write vars from autoconf.php into kga
$kga['server_prefix']   = $server_prefix;
$kga['server_hostname'] = $server_hostname;
$kga['server_database'] = $server_database;
$kga['server_username'] = $server_username;
$kga['server_password'] = $server_password;
$kga['defaultTimezone'] = $defaultTimezone;
$kga['password_salt']   = isset($password_salt) ? $password_salt : '';
$kga['language']        = isset($language) ? $language : Kimai_Config::getDefault(Kimai_Config::DEFAULT_LANGUAGE);
$kga['authenticator']   = isset($authenticator) ? trim($authenticator) : Kimai_Config::getDefault(Kimai_Config::DEFAULT_AUTHENTICATOR);
$kga['billable']        = isset($billable) && is_array($billable) ? $billable : Kimai_Config::getDefault(Kimai_Config::DEFAULT_BILLABLE);
$kga['skin']            = isset($skin) ? $skin : Kimai_Config::getDefault(Kimai_Config::DEFAULT_SKIN);

$cleanup = array(
    'server_prefix', 'server_hostname', 'server_database', 'server_username', 'server_password',
    'language', 'password_salt', 'authenticator', 'defaultTimezone', 'billable', 'skin'
);

date_default_timezone_set($defaultTimezone);

foreach ($cleanup as $varName) {
    if (isset($$varName)) {
        unset($$varName);
    }
}

unset($cleanup);