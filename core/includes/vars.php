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
 * The Kimai Global Array ($kga) is initialized here. It is used throught
 * all functions, processors, etc.
 */

$kga = array();

require(dirname(__FILE__).'/version.php');

// ------------------------------------------------------------------------------------------------------------------------------------------------



//$kga['show_sensible_data'] = 1;       // turn this on to display sensible data in the debug/developer extension
                                        // CAUTION - THINK TWICE IF YOU REALLY WANNA DO THIS AND DON'T FORGET TO TURN IT OFF IN A PRODUCTION ENVIRONMENT!!!
                                        // DON'T BLAME US - YOU HAVE BEEN WARNED!

$kga['logfile_lines']      = 100;       // number of lines shown from the logfile in debug extension. Set to "@" to display the entire file (might freeze your browser...)
$kga['delete_logfile']     = 1;         // can the logfile be cleaned via debug_ext?

$kga['utf8']               = 0;         // set to 1 if utf-8 CONVERSION (!) is needed - this is not always the case,
                                        // depends on server settings

$kga['calender_start']     = "0";       // here you can set a custom start day for the date-picker.
                                        // if this is not set the day of the users first day in the system will be taken
                                        // Format: ... = "DD/MM/YYYY";


// ------------------------------------------------------------------------------------------------------------------------------------------------
// write vars from autoconf.php into kga
$kga['server_prefix']   = $server_prefix;
$kga['server_hostname'] = $server_hostname;
$kga['server_database'] = $server_database;
$kga['server_username'] = $server_username;
$kga['server_password'] = $server_password;
$kga['server_type']     = "";
$kga['server_conn']     = 'mysql';
$kga['language']        = isset($language)      ? $language            : 'en';
$kga['password_salt']   = isset($password_salt) ? $password_salt       : '';
$kga['authenticator']   = isset($authenticator) ? trim($authenticator) : 'kimai';
$kga['defaultTimezone'] = $defaultTimezone;

$cleanup = array(
    'server_prefix', 'server_hostname', 'server_database', 'server_username', 'server_password',
    'server_type', 'server_conn', 'language', 'password_salt', 'authenticator', 'defaultTimezone'
);

date_default_timezone_set($defaultTimezone);

foreach($cleanup as $varName) {
    if (isset($$varName)) {
        unset($$varName);
    }
}