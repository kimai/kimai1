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

$server_hostname = 'localhost';  // the server your database is hosted
                                 // usually this is 'localhost'

$server_database = '';           // name of your database
$server_username = '';           // guess what ;)
$server_password = '';           // guess again ...

$server_type = 'mysql';			 // the database type (needed for PDO) usually 'mysql'
								 // check the PHP PDO manual if you are using a different
								 // database
$server_conn     = "";

$server_prefix   = 'kimai_';     // optional, but if you have more tables in your database
                                 // besides kimai you should assign a prefix here

$language        = 'de';         // available:
                                 //            de = german
                                 //            en = english
                                 //            es = spanish
                                 //            fr = french
                                 //            it = italian
                                 //            nl = dutch
                                 //            pt = portuguese

//   If you need access to more than one database you can configure as much as
//   you want by assigning additional tablenames to the $server_ext_* array.
//   A display name for the default database is also needed then (assigned once to
//   $server_verbose).
//
//   Example:
//
//   $server_verbose  = 'Displayname of default Database'; <= this is for the default server!
//
//   $server_ext_verbose[0]  = 'Displayname of first additional database';
//   $server_ext_database[0] = 'Name of first additional database';
//
//   $server_ext_username[0] = ...   * you can omit these three entries if they do not differ
//   $server_ext_password[0] = ...   * from the the original username, password and prefix
//   $server_ext_prefix[0]   = ...   * which eg. might be the case if you run the server locally
//
//         ...
//
//   $server_ext_verbose[1]  = 'Displayname of second additional database';
//   $server_ext_database[1] = 'Name of second additional database';
//   $server_ext_username[1] = ...
//   $server_ext_password[1] = ...
//   $server_ext_prefix[1]   = ...
//
//   and so on....
//
//   {developer note:} server_ext vars are not needed in $kga ... see index.php ...
//
//   ---------------------------------------------------------------------------------------------
//
//   advanced settings can be specified by editing the file /includes/vars.php

if (!defined('WEBROOT')) {
    define('WEBROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
}

//   IT IS VERY IMPORTANT TO NOT HAVE ADDITIONAL WHITESPACE BEHIND THE FOLLOWING PHP-ENDING-TAG!!!
?>