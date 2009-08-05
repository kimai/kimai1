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
 
require('../includes/basics.php');
require(sprintf("../language/%s.php",$kga['language']));

// ------

$default_knd      = "testcustomerXY";
$default_pct      = "look see";
$default_evt      = "testing";
$default_skin     = "standard";
$default_rowlimit = 100;

// ------

if ($kga['charset_descr']) {
    $_SESSION['user'] = random_number(9);
    $user = $_SESSION['user'];
    $group= $user;
    
    @mysql_query(sprintf("INSERT INTO `%sevt` (`evt_grpID`,`evt_name`) VALUES ('%d','%s');",$kga['server_prefix'],$group,$default_evt));
    $default_evtID = mysql_insert_id();

    @mysql_query(sprintf("INSERT INTO `%sknd` (`knd_grpID`,`knd_name`) VALUES ('%d','%s');",$kga['server_prefix'],$group,$default_knd));
    $default_kndID = mysql_insert_id();

    @mysql_query(sprintf("INSERT INTO `%spct` (`pct_grpID`,`pct_kndID`,`pct_name`) VALUES ('%d',%d,'%s');",$kga['server_prefix'],$group,$default_kndID,$default_pct));
    $default_pctID = mysql_insert_id();

    @mysql_query(sprintf("INSERT INTO `%susr` (`usr_ID`,`rowlimit`,`skin`) VALUES (%s,%d,'%s');",$kga['server_prefix'],$user,$default_rowlimit,$default_skin));
}

header("Location: kimai.php");
?>