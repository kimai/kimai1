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
 
function exec_query($query) {
    global $conn, $pdo_conn, $errors, $db_layer;
    if ($db_layer == "pdo") {
        
        if (is_object($pdo_conn)) {
            $pdo_query = $pdo_conn->prepare($query);
            $success = $pdo_query->execute(array());
        }
        
    } else {
        
        if (is_object($conn)) {
            $success = $conn->Query($query);
        }
    }
    logfile($query,$success);
    if (!$success) $errors++;
} 

if (!isset($_REQUEST['accept'])) {
    header("Location: ../index.php?disagreedGPL=1");
    exit;
}

include('../includes/basics.php');
$db_layer = $kga['server_conn'];
if ($db_layer == '') $db_layer = $_REQUEST['db_layer'];

require(sprintf("../language/%s.php",$kga['language']));

$randomAdminID = random_number(9);

logfile("-- begin install ----------------------------------");

// if any of the queries fails, this will be true
$errors=false;

$query =
"CREATE TABLE `" . $kga['server_prefix'] . "usr` (
  `usr_ID` int(10) NOT NULL,
  `usr_name` varchar(160) NOT NULL,
  `usr_alias` varchar(10),
  `usr_grp` int(5) NOT NULL default '1',
  `usr_sts` tinyint(1) NOT NULL default '2',
  `usr_trash` tinyint(1) NOT NULL default '0',
  `usr_active` tinyint(1) NOT NULL default '1',
  `usr_mail` varchar(160) NOT NULL,
  `pw` varchar(254) NOT NULL,
  `ban` int(1) NOT NULL default '0',
  `banTime` int(7) NOT NULL default '0',
  `secure` varchar(60) NOT NULL default '0',
  `rowlimit` int(3) NOT NULL,
  `skin` varchar(20) NOT NULL,
  `lastProject` int(10) NOT NULL default '1',
  `lastEvent` int(10) NOT NULL default '1',
  `lastRecord` int(10) NOT NULL default '0',
  `filter` int(10) NOT NULL default '0',
  `filter_knd` int(10) NOT NULL default '0',
  `filter_pct` int(10) NOT NULL default '0',
  `filter_evt` int(10) NOT NULL default '0',
  `view_knd` int(10) NOT NULL default '0',
  `view_pct` int(10) NOT NULL default '0',
  `view_evt` int(10) NOT NULL default '0',
  `zef_anzahl` int(10) NOT NULL default '0',
  `timespace_in` varchar(60) NOT NULL default '0',
  `timespace_out` varchar(60) NOT NULL default '0',
  `autoselection` tinyint(1) NOT NULL default '1',
  `quickdelete` tinyint(1) NOT NULL default '0',
  `allvisible` tinyint(1) NOT NULL default '1',
  `lang` varchar(6) NOT NULL,
  `flip_pct_display` tinyint(1) NOT NULL DEFAULT '0',
  `pct_comment_flag` TINYINT(1) NOT NULL DEFAULT '0',
  `showIDs` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`usr_name`)
);";
exec_query($query);

$query=
"CREATE TABLE `" . $kga['server_prefix'] . "evt` (
  `evt_ID` int(10) NOT NULL auto_increment,
  `evt_name` varchar(255) NOT NULL,
  `evt_comment` TEXT NOT NULL,
  `evt_visible` TINYINT(1) NOT NULL DEFAULT '1',
  `evt_filter` TINYINT(1) NOT NULL DEFAULT '0',
  `evt_logo` VARCHAR( 80 ),
  `evt_trash` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`evt_ID`)
) AUTO_INCREMENT=1;";
exec_query($query);

$query=
"CREATE TABLE `" . $kga['server_prefix'] . "grp` (
  `grp_ID` int(10) NOT NULL auto_increment,
  `grp_name` varchar(160) NOT NULL,
  `grp_trash` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`grp_ID`)
) AUTO_INCREMENT=1;";
exec_query($query);

// leader/group cross-table (leaders n:m groups)
$query="CREATE TABLE `" . $kga['server_prefix'] . "ldr` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `grp_ID` int(10) NOT NULL, `grp_leader` int(10) NOT NULL);";
exec_query($query);

// group/customer cross-table (groups n:m customers)
$query="CREATE TABLE `" . $kga['server_prefix'] . "grp_knd` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `grp_ID` INT NOT NULL, `knd_ID` INT NOT NULL);";
exec_query($query);

// group/project cross-table (groups n:m projects)
$query="CREATE TABLE `" . $kga['server_prefix'] . "grp_pct` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `grp_ID` INT NOT NULL, `pct_ID` INT NOT NULL);";
exec_query($query);

// group/event cross-table (groups n:m events)
$query="CREATE TABLE `" . $kga['server_prefix'] . "grp_evt` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `grp_ID` INT NOT NULL, `evt_ID` INT NOT NULL);";
exec_query($query);

$query=
"CREATE TABLE `" . $kga['server_prefix'] . "knd` (
  `knd_ID` int(10) NOT NULL auto_increment,
  `knd_name` varchar(255) NOT NULL,
  `knd_comment` TEXT NOT NULL,
  `knd_visible` TINYINT(1) NOT NULL DEFAULT '1',
  `knd_filter` TINYINT(1) NOT NULL DEFAULT '0',
  `knd_company` varchar(255) NOT NULL,
  `knd_street` varchar(255) NOT NULL,
  `knd_zipcode` varchar(255) NOT NULL,
  `knd_city` varchar(255) NOT NULL,
  `knd_tel` varchar(255) NOT NULL,
  `knd_fax` varchar(255) NOT NULL,
  `knd_mobile` varchar(255) NOT NULL,
  `knd_mail` varchar(255) NOT NULL,
  `knd_homepage` varchar(255) NOT NULL,
  `knd_logo` VARCHAR( 80 ),
  `knd_trash` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`knd_ID`)
) AUTO_INCREMENT=1;";
exec_query($query);

$query=
"CREATE TABLE `" . $kga['server_prefix'] . "pct` (
  `pct_ID` int(10) NOT NULL auto_increment,
  `pct_kndID` int(3) NOT NULL,
  `pct_name` varchar(255) NOT NULL,
  `pct_comment` TEXT NOT NULL,
  `pct_visible` TINYINT(1) NOT NULL DEFAULT '1',
  `pct_filter` TINYINT(1) NOT NULL DEFAULT '0',
  `pct_logo` VARCHAR( 80 ),
  `pct_trash` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`pct_ID`)
) AUTO_INCREMENT=1;";
exec_query($query);

$query=
"CREATE TABLE `" . $kga['server_prefix'] . "zef` (
  `zef_ID` int(10) NOT NULL auto_increment,
  `zef_in` int(10) NOT NULL default '0',
  `zef_out` int(10) NOT NULL default '0',
  `zef_time` int(6) NOT NULL default '0',
  `zef_usrID` int(10) NOT NULL,
  `zef_pctID` int(10) NOT NULL,
  `zef_evtID` int(10) NOT NULL,
  `zef_comment` TEXT NOT NULL,
  `zef_comment_type` TINYINT(1) NOT NULL DEFAULT '0',
  `zef_cleared` TINYINT(1) NOT NULL DEFAULT '0',
  `zef_location` VARCHAR(50),
  `zef_trackingnr` int(20),
  `zef_rate` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`zef_ID`)
) AUTO_INCREMENT=1;";
exec_query($query);

$query=
"CREATE TABLE `" . $kga['server_prefix'] . "var` (
  `var` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`var`)
);";
exec_query($query);

$query=
"CREATE TABLE `" . $kga['server_prefix'] . "rates` (
  `user_id` int(10) DEFAULT NULL,
  `project_id` int(10) DEFAULT NULL,
  `event_id` int(10) DEFAULT NULL,
  `rate` decimal(10,2) NOT NULL
);";
exec_query($query);



// GROUPS
$defaultgrp=$kga['lang']['defaultgrp'];
$query="INSERT INTO `" . $kga['server_prefix'] . "grp` (`grp_name`) VALUES ('admin');";
exec_query($query);



// MISC
$query="INSERT INTO `" . $kga['server_prefix'] . "evt` (`evt_ID`, `evt_name`, `evt_comment`) VALUES (1, '".$kga['lang']['testEVT']."', '');";
exec_query($query);

$query="INSERT INTO `" . $kga['server_prefix'] . "knd` (`knd_ID`, `knd_name`, `knd_comment`, `knd_company`, `knd_street`, `knd_zipcode`, `knd_city`, `knd_tel`, `knd_fax`, `knd_mobile`, `knd_mail`, `knd_homepage`, `knd_logo`) VALUES (1, '".$kga['lang']['testKND']."', '', '', '', '', '', '', '', '', '', '', '');";
exec_query($query);

$query="INSERT INTO `" . $kga['server_prefix'] . "pct` (`pct_ID`, `pct_kndID`, `pct_name`, `pct_comment`) VALUES (1, 1, '".$kga['lang']['testPCT']."', '');";
exec_query($query);

$query="INSERT INTO `" . $kga['server_prefix'] . "usr` (`usr_ID`,`usr_name`,`usr_mail`,`pw`,`usr_sts`, `rowlimit`, `skin`,`lang`) VALUES ('$randomAdminID','admin','admin@yourwebspace.de','changeme','0', 100, 'standard','');";
exec_query($query);

$query="INSERT INTO `" . $kga['server_prefix'] . "ldr` (`grp_ID`,`grp_leader`) VALUES ('1','$randomAdminID');";
exec_query($query);



// CROSS TABLES
$query="INSERT INTO `" . $kga['server_prefix'] . "grp_evt` (`grp_ID`, `evt_ID`) VALUES (1, 1);";
exec_query($query);

$query="INSERT INTO `" . $kga['server_prefix'] . "grp_knd` (`grp_ID`, `knd_ID`) VALUES (1, 1);";
exec_query($query);

$query="INSERT INTO `" . $kga['server_prefix'] . "grp_pct` (`grp_ID`, `pct_ID`) VALUES (1, 1);";
exec_query($query);



// VARS
$query="INSERT INTO `" . $kga['server_prefix'] . "var` (`var`, `value`) VALUES ('version', '" . $kga['version'] . "');";
exec_query($query);

$query="INSERT INTO `" . $kga['server_prefix'] . "var` (`var`, `value`) VALUES ('login', '1');";
exec_query($query);

$query="INSERT INTO `" . $kga['server_prefix'] . "var` (`var`, `value`) VALUES ('kimail', 'kimai@yourwebspace.com');";
exec_query($query);

$query="INSERT INTO `" . $kga['server_prefix'] . "var` (`var`, `value`) VALUES ('adminmail', 'admin@yourwebspace.com');";
exec_query($query);

$query="INSERT INTO `" . $kga['server_prefix'] . "var` (`var`, `value`) VALUES ('loginTries', '3');";
exec_query($query);

$query="INSERT INTO `" . $kga['server_prefix'] . "var` (`var`, `value`) VALUES ('loginBanTime', '900');";
exec_query($query);

$query="INSERT INTO `" . $kga['server_prefix'] . "var` (`var`, `value`) VALUES ('lastdbbackup', '0');";
exec_query($query);

$query="INSERT INTO `" . $kga['server_prefix'] . "var` (`var`, `value`) VALUES ('charset', 'utf-8');";
exec_query($query);

$query="INSERT INTO `" . $kga['server_prefix'] . "var` (`var`, `value`) VALUES ('revision', '" . $kga['revision'] . "');";
exec_query($query);



// init timespace for admin user to current month
$mon = date("n"); $day = date("j"); $Y = date("Y");
save_timespace(mktime(0,0,0,$mon,1,$Y),mktime(23,59,59,$mon,lastday($month=$mon,$year=$Y),$Y),$randomAdminID);



if ($errors) {
    require_once('../libraries/smarty/Smarty.class.php');
    $tpl = new Smarty();
    $tpl->template_dir = '../templates/';
    $tpl->compile_dir  = '../compile/';
    $tpl->display('misc/error.tpl');
    logfile("-- showing install error --------------------------");
} else {
    logfile("-- installation finished without error ------------");
    header("Location: ../index.php");
}
?>