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
 * Perform the installation by creating all necessary tables
 * and some basic entries.
 */
 
/**
 * Execute an sql query in the database. The correct database connection
 * will be chosen and the query will be logged with the success status.
 * 
 * @param $query query to execute as string
 */
function exec_query($query) {
    global $conn, $pdo_conn, $errors, $db_layer;
    
    $success = false;
    
    if ($db_layer == "pdo") {
        
        if (is_object($pdo_conn)) {
            $pdo_query = $pdo_conn->prepare($query);
            $success = $pdo_query->execute(array());
            $errorInfo = serialize($pdo_query->errorInfo());
        }
        else
          $errorInfo = "No connection object.";
        
    } else {
        
        if (is_object($conn)) {
            $success = $conn->Query($query);
            $errorInfo = serialize($conn->Error());
        }
        else
          $errorInfo = "No connection object.";
    }
    logfile($query);
    if (!$success) {
      logfile($errorInfo);
      $errors=true;
    }
} 

if (!isset($_REQUEST['accept'])) {
    header("Location: ../index.php?disagreedGPL=1");
    exit;
}

include('../includes/basics.php');
$db_layer = $kga['server_conn'];
if ($db_layer == '') $db_layer = $_REQUEST['db_layer'];

date_default_timezone_set($_REQUEST['timezone']);

$randomAdminID = random_number(9);

logfile("-- begin install ----------------------------------");

// if any of the queries fails, this will be true
$errors=false;

$p = $kga['server_prefix'];

$query =
"CREATE TABLE `${p}usr` (
  `usr_ID` int(10) NOT NULL,
  `usr_name` varchar(160) NOT NULL,
  `usr_alias` varchar(10),
  `usr_grp` int(5) NOT NULL default '1',
  `usr_sts` tinyint(1) NOT NULL default '2',
  `usr_trash` tinyint(1) NOT NULL default '0',
  `usr_active` tinyint(1) NOT NULL default '1',
  `usr_mail` varchar(160) NOT NULL DEFAULT '',
  `pw` varchar(254) NULL DEFAULT NULL,
  `ban` int(1) NOT NULL default '0',
  `banTime` int(10) NOT NULL default '0',
  `secure` varchar(60) NOT NULL default '0',
  `lastProject` int(10) NOT NULL default '1',
  `lastEvent` int(10) NOT NULL default '1',
  `lastRecord` int(10) NOT NULL default '0',
  `timespace_in` varchar(60) NOT NULL default '0',
  `timespace_out` varchar(60) NOT NULL default '0',
  PRIMARY KEY  (`usr_name`)
);";
exec_query($query);

$query = "CREATE TABLE `${p}preferences` (
  `userID` int(10) NOT NULL,
  `var` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`userID`,`var`)
);";
exec_query($query);

$query=
"CREATE TABLE `${p}evt` (
  `evt_ID` int(10) NOT NULL auto_increment,
  `evt_name` varchar(255) NOT NULL,
  `evt_comment` TEXT NOT NULL,
  `evt_visible` TINYINT(1) NOT NULL DEFAULT '1',
  `evt_filter` TINYINT(1) NOT NULL DEFAULT '0',
  `evt_trash` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`evt_ID`)
) AUTO_INCREMENT=1;";
exec_query($query);

$query=
"CREATE TABLE `${p}grp` (
  `grp_ID` int(10) NOT NULL auto_increment,
  `grp_name` varchar(160) NOT NULL,
  `grp_trash` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`grp_ID`)
) AUTO_INCREMENT=1;";
exec_query($query);

// leader/group cross-table (leaders n:m groups)
$query="CREATE TABLE `${p}ldr` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `grp_ID` int(10) NOT NULL, `grp_leader` int(10) NOT NULL, UNIQUE (`grp_ID` ,`grp_leader`));";
exec_query($query);

// group/customer cross-table (groups n:m customers)
$query="CREATE TABLE `${p}grp_knd` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `grp_ID` INT NOT NULL, `knd_ID` INT NOT NULL, UNIQUE (`grp_ID` ,`knd_ID`));";
exec_query($query);

// group/project cross-table (groups n:m projects)
$query="CREATE TABLE `${p}grp_pct` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `grp_ID` INT NOT NULL, `pct_ID` INT NOT NULL, UNIQUE (`grp_ID` ,`pct_ID`));";
exec_query($query);

// group/event cross-table (groups n:m events)
$query="CREATE TABLE `${p}grp_evt` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `grp_ID` INT NOT NULL, `evt_ID` INT NOT NULL, UNIQUE (`grp_ID` ,`evt_ID`)) ;";
exec_query($query);

// project/event cross-table (projects n:m events)
$query="CREATE TABLE `${p}pct_evt` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `pct_ID` INT NOT NULL, `evt_ID` INT NOT NULL, UNIQUE (`pct_ID` ,`evt_ID`)) ;";
exec_query($query);

$query=
"CREATE TABLE `${p}knd` (
  `knd_ID` int(10) NOT NULL auto_increment,
  `knd_name` varchar(255) NOT NULL,
  `knd_password` varchar(255),
  `knd_secure` varchar(60) NOT NULL default '0',
  `knd_comment` TEXT NOT NULL,
  `knd_visible` TINYINT(1) NOT NULL DEFAULT '1',
  `knd_filter` TINYINT(1) NOT NULL DEFAULT '0',
  `knd_company` varchar(255) NOT NULL,
  `knd_vat` varchar(255) NOT NULL,
  `knd_contact` varchar(255) NOT NULL,
  `knd_street` varchar(255) NOT NULL,
  `knd_zipcode` varchar(255) NOT NULL,
  `knd_city` varchar(255) NOT NULL,
  `knd_tel` varchar(255) NOT NULL,
  `knd_fax` varchar(255) NOT NULL,
  `knd_mobile` varchar(255) NOT NULL,
  `knd_mail` varchar(255) NOT NULL,
  `knd_homepage` varchar(255) NOT NULL,
  `knd_trash` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`knd_ID`)
) AUTO_INCREMENT=1;";
exec_query($query);

$query=
"CREATE TABLE `${p}pct` (
  `pct_ID` int(10) NOT NULL auto_increment,
  `pct_kndID` int(3) NOT NULL,
  `pct_name` varchar(255) NOT NULL,
  `pct_comment` TEXT NOT NULL,
  `pct_visible` TINYINT(1) NOT NULL DEFAULT '1',
  `pct_filter` TINYINT(1) NOT NULL DEFAULT '0',
  `pct_trash` TINYINT(1) NOT NULL DEFAULT '0',
  `pct_budget` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pct_internal` TINYINT( 1 ) NOT NULL DEFAULT 0,
  PRIMARY KEY  (`pct_ID`)
) AUTO_INCREMENT=1;";
exec_query($query);

exec_query("ALTER TABLE `${p}pct` ADD INDEX ( `pct_kndID` ) ");

$query=
"CREATE TABLE `${p}zef` (
  `zef_ID` int(10) NOT NULL auto_increment,
  `zef_in` int(10) NOT NULL default '0',
  `zef_out` int(10) NOT NULL default '0',
  `zef_time` int(6) NOT NULL default '0',
  `zef_usrID` int(10) NOT NULL,
  `zef_pctID` int(10) NOT NULL,
  `zef_evtID` int(10) NOT NULL,
  `zef_comment` TEXT NULL DEFAULT NULL,
  `zef_comment_type` TINYINT(1) NOT NULL DEFAULT '0',
  `zef_cleared` TINYINT(1) NOT NULL DEFAULT '0',
  `zef_location` VARCHAR(50),
  `zef_trackingnr` varchar(30),
  `zef_rate` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`zef_ID`)
) AUTO_INCREMENT=1;";
exec_query($query);

exec_query("ALTER TABLE `${p}zef` ADD INDEX ( `zef_usrID` ) ");
exec_query("ALTER TABLE `${p}zef` ADD INDEX ( `zef_pctID` ) ");
exec_query("ALTER TABLE `${p}zef` ADD INDEX ( `zef_evtID` ) ");

$query=
"CREATE TABLE `${p}var` (
  `var` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`var`)
);";
exec_query($query);

$query=
"CREATE TABLE `${p}rates` (
  `user_id` int(10) DEFAULT NULL,
  `project_id` int(10) DEFAULT NULL,
  `event_id` int(10) DEFAULT NULL,
  `rate` decimal(10,2) NOT NULL
);";
exec_query($query);

$query=
"CREATE TABLE `${p}exp` (
  `exp_ID` int(10) NOT NULL AUTO_INCREMENT,
  `exp_timestamp` int(10) NOT NULL DEFAULT '0',
  `exp_usrID` int(10) NOT NULL,
  `exp_pctID` int(10) NOT NULL,
  `exp_designation` text NOT NULL,
  `exp_comment` text NOT NULL,
  `exp_comment_type` tinyint(1) NOT NULL DEFAULT '0',
  `exp_refundable` tinyint(1) unsigned NOT NULL default '0' COMMENT 'expense refundable to employee (0 = no, 1 = yes)',
  `exp_cleared` tinyint(1) NOT NULL DEFAULT '0',
  `exp_multiplier` decimal(10,2) NOT NULL DEFAULT '1.00',
  `exp_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`exp_ID`)
) AUTO_INCREMENT=1;";
exec_query($query);

exec_query("ALTER TABLE `${p}exp` ADD INDEX ( `exp_usrID` ) ");
exec_query("ALTER TABLE `${p}exp` ADD INDEX ( `exp_pctID` ) ");



// GROUPS
$defaultgrp=$kga['lang']['defaultgrp'];
$query="INSERT INTO `${p}grp` (`grp_name`) VALUES ('admin');";
exec_query($query);



// MISC
$query="INSERT INTO `${p}evt` (`evt_ID`, `evt_name`, `evt_comment`) VALUES (1, '".$kga['lang']['testEVT']."', '');";
exec_query($query);

$query="INSERT INTO `${p}knd` (`knd_ID`, `knd_name`, `knd_comment`, `knd_company`, `knd_street`, `knd_zipcode`, `knd_city`, `knd_tel`, `knd_fax`, `knd_mobile`, `knd_mail`, `knd_homepage`, `knd_vat`) VALUES (1, '".$kga['lang']['testKND']."', '', '', '', '', '', '', '', '', '', '','');";
exec_query($query);

$query="INSERT INTO `${p}pct` (`pct_ID`, `pct_kndID`, `pct_name`, `pct_comment`) VALUES (1, 1, '".$kga['lang']['testPCT']."', '');";
exec_query($query);

$adminPassword =  md5($kga['password_salt'].'changeme'.$kga['password_salt']);
$query="INSERT INTO `${p}usr` (`usr_ID`,`usr_name`,`usr_mail`,`pw`,`usr_sts` ) VALUES ('$randomAdminID','admin','admin@yourwebspace.de','$adminPassword','0');";
exec_query($query);

$query="INSERT INTO `${p}preferences` (`userID`,`var`,`value`) VALUES ('$randomAdminID','ui.rowlimit','100'),
('$randomAdminID','ui.skin','standard'),('$randomAdminID','timezone','".mysql_real_escape_string($_REQUEST['timezone'])."');";
exec_query($query);

$query="INSERT INTO `${p}ldr` (`grp_ID`,`grp_leader`) VALUES ('1','$randomAdminID');";
exec_query($query);



// CROSS TABLES
$query="INSERT INTO `${p}grp_evt` (`grp_ID`, `evt_ID`) VALUES (1, 1);";
exec_query($query);

$query="INSERT INTO `${p}grp_knd` (`grp_ID`, `knd_ID`) VALUES (1, 1);";
exec_query($query);

$query="INSERT INTO `${p}grp_pct` (`grp_ID`, `pct_ID`) VALUES (1, 1);";
exec_query($query);



// VARS
$query="INSERT INTO `${p}var` (`var`, `value`) VALUES ('version', '" . $kga['version'] . "');";
exec_query($query);

$query="INSERT INTO `${p}var` (`var`, `value`) VALUES ('login', '1');";
exec_query($query);

$query="INSERT INTO `${p}var` (`var`, `value`) VALUES ('kimail', 'kimai@yourwebspace.com');";
exec_query($query);

$query="INSERT INTO `${p}var` (`var`, `value`) VALUES ('adminmail', 'admin@yourwebspace.com');";
exec_query($query);

$query="INSERT INTO `${p}var` (`var`, `value`) VALUES ('loginTries', '3');";
exec_query($query);

$query="INSERT INTO `${p}var` (`var`, `value`) VALUES ('loginBanTime', '900');";
exec_query($query);

$query="INSERT INTO `${p}var` (`var`, `value`) VALUES ('lastdbbackup', '0');";
exec_query($query);

$query="INSERT INTO `${p}var` (`var`, `value`) VALUES ('revision', '" . $kga['revision'] . "');";
exec_query($query);

exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('currency_name','Euro')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('currency_sign','€')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('currency_first','0')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('show_sensible_data','1')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('show_update_warn','1')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('check_at_startup','0')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('show_daySeperatorLines','1')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('show_gabBreaks','0')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('show_RecordAgain','1')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('show_TrackingNr','1')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('date_format_0','%d.%m.%Y')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('date_format_1','%d.%m.')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('date_format_2','%d.%m.%Y')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('language','$kga[language]')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('roundPrecision','0')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('decimalSeparator',',')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('durationWithSeconds','0')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('defaultTimezone','".mysql_real_escape_string($_REQUEST['timezone'])."')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('exactSums','0')");
exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('defaultVat','0')");


// init timespace for admin user to current month
$mon = date("n"); $day = date("j"); $Y = date("Y");
save_timespace(mktime(0,0,0,$mon,1,$Y),mktime(23,59,59,$mon,lastday($month=$mon,$year=$Y),$Y),$randomAdminID);



if ($errors) {
    require_once('../libraries/smarty/Smarty.class.php');
    $tpl = new Smarty();
    $tpl->template_dir = '../templates/';
    $tpl->compile_dir  = '../compile/';
    $tpl->assign('headline',$kga['lang']['errors'][1]['hdl']);
    $tpl->assign('message',$kga['lang']['errors'][1]['txt']);
    $tpl->display('misc/error.tpl');
    logfile("-- showing install error --------------------------");
} else {
    logfile("-- installation finished without error ------------");
    header("Location: ../index.php");
}
?>