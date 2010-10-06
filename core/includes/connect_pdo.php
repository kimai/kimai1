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
 * Connect to a mysql database using the PDO extension.
 */

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
// ===========================================================================

$link = @mysql_connect($kga['server_hostname'], $kga['server_username'], $kga['server_password']);
if (!$link) {
	logfile("CONNECTION ERROR (STD MYSQL): " . mysql_error());
    header("Location: error.php");
    exit;
}
if (isset($utf8)) {
    if ($utf8) mysql_query("SET CHARACTER SET utf-8");
}
@mysql_select_db($kga['server_database']);


/* Preparing for PDO :-) */
$pdo_dsn = $kga['server_type'] . ':dbname=' . $kga['server_database'] . ';host=' . $kga['server_hostname'];
$pdo_user = $kga['server_username'];
$pdo_password = $kga['server_password'];

try {
    $pdo_conn = new PDO($pdo_dsn, $pdo_user, $pdo_password);
} catch (PDOException $pdo_ex) {
    logfile('PDO CONNECTION FAILED: ' . $pdo_ex->getMessage());
    header("Location: error.php");
}

// logfile("******************** PDO database connection established.");


?>