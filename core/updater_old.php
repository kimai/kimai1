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

require('includes/basics.php');

if (!$kga['revision']) die("Database update failed. (Revision not defined!)");

if ($kga['conf']['lang'] == "") {
    $language = $kga['language'];
} else {
    $language = $kga['conf']['lang'];
}
require_once( "language/${language}.php" );

$version_temp  = get_DBversion();
$versionDB  = $version_temp[0];
$revisionDB = $version_temp[1];
unset($version_temp);

$version_e   = explode(".",$kga['version']);
$versionDB_e = explode(".",$versionDB);

if (!isset($_GET['ok']) && $kga['show_update_warn']) {
    require_once('libraries/smarty/Smarty.class.php');
    $tpl = new Smarty();
    $tpl->template_dir = 'templates/';
    $tpl->compile_dir = 'compile/';
    $tpl->assign('kga',$kga);
    $tpl->display('admin/updater.tpl');
    exit;
} else {
    logfile("-- begin update -----------------------------------");
}


// Backup Tables

logfile("-- begin backup -----------------------------------");

$backup_stamp = time(); // as an individual backup label the timestamp should be enough for now...
                        // by using this type of label we can also exactly identify when it was done
                        // may be shown by a recovering script in human-readable format
                        
$query = ("SHOW TABLES;"); // shows also views - but we can just drop them because they are easy to recover without a backup...
                           // but what is CREATE TABLE doing when you try to create a table from a view?
                           
$result_backup=@mysql_query($query); logfile($query,$result_backup);
$prefix_length = strlen($kga['server_prefix']);

while ($row = mysql_fetch_array($result_backup)) {

    // if (substr($row[0], 0, $prefix_length) == $kga['server_prefix']) {
	if ((substr($row[0], 0, $prefix_length) == $kga['server_prefix']) && (substr($row[0], 0, 10) != "kimai_bak_")) {
		
		$query_copy = "CREATE TABLE kimai_bak_" . $backup_stamp . "_" . $row[0] . " SELECT * FROM " . $row[0] . ";";
		$success = @mysql_query($query_copy);
		logfile($query_copy,$success);
	}
}

logfile("-- backup finished -----------------------------------");


// =============================================
// = Step 1: update DB in versions under 0.5.2 =
// =============================================
if ($versionDB=="0.5.1") {
    
    logfile("-- update from 0.5.1");

    $query=sprintf("ALTER TABLE `%szef` ADD `zef_comment` TEXT NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%sevt` ADD `evt_comment` TEXT NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%sknd` ADD `knd_comment` TEXT NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%spct` ADD `pct_comment` TEXT NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%szef` DROP `zef_kndID`;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

    $query= 
    sprintf("CREATE TABLE `%svar` ( 
    `var` varchar(255) collate utf8_unicode_ci NOT NULL, 
    `value` varchar(255) collate utf8_unicode_ci NOT NULL 
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

    $query=sprintf("INSERT INTO `%svar` (`var`,`value`) VALUES ('version','%s');",$kga['server_prefix'],$kga['version']);
    $success=@mysql_query($query); logfile($query,$success);
}

// ===============================
// = update DB for version 0.6.0 =
// ===============================
if ( (int)$versionDB_e[1] < 6 ) {
    
    logfile("-- update from 0.6.0");

    $query=sprintf("ALTER TABLE `%szef` DROP `zef_kndID`;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%susr` CHANGE `usr_ID` `usr_ID` INT(10) NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%susr` ADD `pw` VARCHAR(255) NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%susr` ADD `ban` INT(1) NOT NULL DEFAULT '0';",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%susr` ADD `banTime` INT(7) NOT NULL DEFAULT '0';",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%susr` ADD `secure` VARCHAR(30) NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%susr` DROP PRIMARY KEY , ADD PRIMARY KEY ( `usr_name` );",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("INSERT INTO `%susr` (`usr_ID`,`usr_name`,`pw`) VALUES ('%d','admin','admin');",$kga['server_prefix'],random_number(9));
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%sconf` CHANGE `conf_usrID` `conf_usrID` INT(10) NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%svar` ADD PRIMARY KEY (`var`);",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("INSERT INTO `%svar` (`var`,`value`) VALUES ('login','1');",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("INSERT INTO `%svar` (`var`,`value`) VALUES ('kimail','kimai@yourwebspace.com');",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("INSERT INTO `%svar` (`var`,`value`) VALUES ('adminmail','admin@yourwebspace.com');",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("INSERT INTO `%svar` (`var`,`value`) VALUES ('loginTries','3');",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("INSERT INTO `%svar` (`var`,`value`) VALUES ('loginBanTime','900');",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("UPDATE `%szef` SET `zef_usrID` = '$randomAdminID' WHERE `zef_ID` > 0 ;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("UPDATE `%susr` SET `usr_ID` = '$randomAdminID' WHERE `usr_name` = 'admin' ;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("UPDATE `%sconf` SET `conf_usrID` = '$randomAdminID' WHERE `conf_usrID` = '0' ;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    $versionDB_e[0] = 0;
    $versionDB_e[1] = 6;
    $versionDB_e[2] = 0;
}

// ===============================
// = update DB for version 0.7.x =
// ===============================

if ( (int)$versionDB_e[1] < 7 ) {
    
logfile("-- update to 0.7.5");
    
    $query=sprintf("ALTER TABLE `%susr` ADD `usr_grp` INT(5) NOT NULL DEFAULT '1' AFTER `usr_name`;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%susr` ADD `usr_sts` TINYINT(1) NOT NULL DEFAULT '2' AFTER `usr_grp`;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%susr` ADD `usr_trash` TINYINT(1) NOT NULL DEFAULT '0' AFTER `usr_sts`;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%susr` ADD `usr_active` TINYINT(1) NOT NULL DEFAULT '1' AFTER `usr_trash`;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%susr` ADD `usr_mail` varchar(255) NOT NULL DEFAULT '' AFTER `usr_active`;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%sknd` ADD `knd_company` varchar(255) NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%sknd` ADD `knd_street` varchar(255) NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%sknd` ADD `knd_zipcode` varchar(255) NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%sknd` ADD `knd_city` varchar(255) NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%sknd` ADD `knd_tel` varchar(255) NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%sknd` ADD `knd_fax` varchar(255) NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%sknd` ADD `knd_mobile` varchar(255) NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%sknd` ADD `knd_mail` varchar(255) NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("ALTER TABLE `%sknd` ADD `knd_homepage` varchar(255) NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
  
logfile("-- update to 0.7.1");
    
    $query=sprintf("ALTER TABLE `%sconf` ADD `timespace_in` varchar(30) NOT NULL DEFAULT '0';",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    $query=sprintf("ALTER TABLE `%sconf` ADD `timespace_out` varchar(30) NOT NULL DEFAULT '0';",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    $query=sprintf("ALTER TABLE `%sconf` ADD `autoselection` tinyint(1) NOT NULL DEFAULT '1';",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
logfile("-- update to 0.7.5");
    
    $query=sprintf("ALTER TABLE `%sgrp` ADD `grp_leader` int(10) NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    // admin leader in gruppen eintragen
    
    // sind mehr als ein user in der usr tabelle?
    if (count(get_arr_usr())>1) {
        // wenn ja - heißt einer admin?
        if (usr_name2id('admin')) {
            // wenn ja - id rausfinden und für später merken
            $adminusr = usr_name2id('admin');
        } else {
            // wenn keiner admin heißt muss hier ausgelesen werden welcher user als erster status 0 hat
            // also der erste admin im alphabeth
            $data  = mysql_query(sprintf("SELECT usr_name FROM %susr WHERE usr_sts = '0' LIMIT 1;",$kga['server_prefix']));
            $row = mysql_fetch_assoc($data);
            mysql_free_result($data);
            // id rausfinden und für später merken
            $adminusr = usr_name2id($row['usr_name']);
        }
    }
    
    $query=sprintf("UPDATE `%sgrp` SET `grp_leader` = '$adminusr' WHERE `grp_leader` = '' OR `grp_leader` = 0 OR `grp_leader` = 1;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    $query=sprintf("CREATE TABLE `%sldr` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `grp_ID` int(10) NOT NULL, `grp_leader` int(10) NOT NULL);",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    // put existing group relations into new cross table
    $query=sprintf("SELECT `grp_ID`, `grp_leader` FROM %sgrp", $kga['server_prefix']);
    $result = @mysql_query($query);
    	
    while ($current_row = mysql_fetch_array($result, MYSQL_ASSOC)) {	
    	$query=sprintf("INSERT INTO %sldr (`grp_ID`, `grp_leader`) VALUES (%d, %d)",$kga['server_prefix'], $current_row['grp_ID'], $current_row['grp_leader']);
    	$success=@mysql_query($query);
		logfile($query,$success);
			
    	if (mysql_error() != "") {
    		logfile("An error has occured in query: $query");
    		logfile("Error text: " . mysql_error() . "");
    		logfile("UPDATE ABORTED!");
    		die("Database update failed.");
    	}
    }
    
    $query=sprintf("ALTER TABLE `%sgrp` DROP `grp_leader`;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    $query=sprintf("ALTER TABLE `%sgrp` CHANGE `grp_ID` `grp_ID` INT( 10 ) NOT NULL AUTO_INCREMENT;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    $query=sprintf("ALTER TABLE `%sgrp` ADD `grp_trash` TINYINT(1) NOT NULL DEFAULT '0';",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
logfile("-- group relations bugfix -------------------------");

    // ist mehr als eine gruppe vorhanden?

    $data  = mysql_query(sprintf("SELECT COUNT(*) AS count FROM %sgrp;",$kga['server_prefix']));
    $row = mysql_fetch_assoc($data);
    $count_groups = $row['count'];
    mysql_free_result($data);
    logfile("CountGroups:$count_groups");

    if ($count_groups > 1 ) {

        // name von gruppe 1 lesen
        $group1_name = get_grp(1);
        $group1_name = $group1_name['grp_name'];
        
        if ($group1_name != 'admin') {
            
            // neuer letzter eintrag wird jetzige gruppe 1
            //  -> gruppe hinzufügen, 1er wert rein
            $query=sprintf("INSERT INTO `%sgrp` (`grp_name`) VALUES ('%s');",$kga['server_prefix'],$group1_name);
            $success=@mysql_query($query); logfile($query,$success);

            // und mysql_insert_id() ist die neue id der alten 1er gruppe
            $new_id = mysql_insert_id();
            logfile("NewID:$new_id");

            // update aller 1er knd evt pct gruppen records auf mysql_insert_id()
            $query=sprintf("UPDATE `%sknd` SET `knd_grpID` = '$new_id' WHERE `knd_grpID` = '1';",$kga['server_prefix']);
            $success=@mysql_query($query); logfile($query,$success);
            $query=sprintf("UPDATE `%spct` SET `pct_grpID` = '$new_id' WHERE `pct_grpID` = '1';",$kga['server_prefix']);
            $success=@mysql_query($query); logfile($query,$success);
            $query=sprintf("UPDATE `%sevt` SET `evt_grpID` = '$new_id' WHERE `evt_grpID` = '1';",$kga['server_prefix']);
            $success=@mysql_query($query); logfile($query,$success);

            // löschen von gruppe 0
            $query=sprintf("DELETE FROM `%sgrp` WHERE `grp_ID` = '0';",$kga['server_prefix']);
            $success=@mysql_query($query); logfile($query,$success);

            // admin gruppe 1 anlegen
            $query=sprintf("UPDATE `%sgrp` SET `grp_name` = 'admin' WHERE `grp_ID` = '1';",$kga['server_prefix']);
            $success=@mysql_query($query); logfile($query,$success);
        }
    
    }

logfile("---------------------------------------------------");

    
    $query=sprintf("UPDATE `%sknd` SET `knd_grpID` = '1' WHERE `knd_grpID` = '0';",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    $query=sprintf("UPDATE `%spct` SET `pct_grpID` = '1' WHERE `pct_grpID` = '0';",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    $query=sprintf("UPDATE `%sevt` SET `evt_grpID` = '1' WHERE `evt_grpID` = '0';",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
        
    $query=sprintf("UPDATE `%susr` SET `usr_grp` = '1' WHERE `usr_grp` = '0';",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
  
    $versionDB_e[0] = 0;
    $versionDB_e[1] = 7;
    $versionDB_e[2] = 5;
}

if ( ((int)$versionDB_e[1] == 7 && (int)$versionDB_e[2] < 6) ) {

logfile("-- update to 0.7.6");

    $query=sprintf("ALTER TABLE `%sconf` ADD `quickdelete` TINYINT(1) NOT NULL DEFAULT '0' AFTER `autoselection`;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    $query=sprintf("ALTER TABLE `%szef` ADD `zef_comment_type` TINYINT(1) NOT NULL DEFAULT '0' AFTER `zef_comment`;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    $query=sprintf("ALTER TABLE `%sconf` ADD `lang` VARCHAR(3) NOT NULL;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

    $query=sprintf("INSERT INTO `%svar` (`var`,`value`) VALUES ('charset','utf-8');",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

    $versionDB_e[0] = 0;
    $versionDB_e[1] = 7;
    $versionDB_e[2] = 6;
}

if ( ((int)$versionDB_e[1] == 7 && (int)$versionDB_e[2] < 7) ) {

logfile("-- update to 0.7.7");

    $query=sprintf("ALTER TABLE `%sconf` ADD `lastRecord` VARCHAR( 10 ) NOT NULL DEFAULT '0' AFTER `lastEvent`;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    $versionDB_e[0] = 0;
    $versionDB_e[1] = 7;
    $versionDB_e[2] = 7;
}

if ( ((int)$versionDB_e[1] == 7 && (int)$versionDB_e[2] < 8) ) {

logfile("-- update to 0.7.8 -- KF Sonderversion");
    
    // feld filter wird im KF Provisorium benoetigt
    $query=sprintf("ALTER TABLE `%sconf` ADD `filter` VARCHAR( 10 ) NOT NULL DEFAULT '0' AFTER `lastRecord`;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

    $versionDB_e[0] = 0;
    $versionDB_e[1] = 7;
    $versionDB_e[2] = 8;
}

if ( ((int)$versionDB_e[1] == 7 && (int)$versionDB_e[2] < 9) ) {

logfile("-- update to 0.7.9");

// filter and view features coming in some later version ...
    
    $query=sprintf("ALTER TABLE `%sconf` ADD `filter_knd` VARCHAR( 10 ) NOT NULL DEFAULT '0'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);                                    
                                                                                              
    $query=sprintf("ALTER TABLE `%sconf` ADD `filter_pct` VARCHAR( 10 ) NOT NULL DEFAULT '0'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);                                    
                                                                                              
    $query=sprintf("ALTER TABLE `%sconf` ADD `filter_evt` VARCHAR( 10 ) NOT NULL DEFAULT '0'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);                                    
                                                                                              
    $query=sprintf("ALTER TABLE `%sconf` ADD `view_knd` VARCHAR( 10 ) NOT NULL DEFAULT '0'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);                                    
                                                                                              
    $query=sprintf("ALTER TABLE `%sconf` ADD `view_pct` VARCHAR( 10 ) NOT NULL DEFAULT '0'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);                                    
                                                                                              
    $query=sprintf("ALTER TABLE `%sconf` ADD `view_evt` VARCHAR( 10 ) NOT NULL DEFAULT '0'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

    $versionDB_e[0] = 0;
    $versionDB_e[1] = 7;
    $versionDB_e[2] = 9;
}

if ( ((int)$versionDB_e[1] == 7 && (int)$versionDB_e[2] < 12) ) {

logfile("-- update to 0.7.12");

    $query=sprintf("ALTER TABLE `%sevt` ADD `evt_visible` TINYINT NOT NULL DEFAULT '1'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);                                    

    $query=sprintf("ALTER TABLE `%sknd` ADD `knd_visible` TINYINT NOT NULL DEFAULT '1'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);                                    

    $query=sprintf("ALTER TABLE `%spct` ADD `pct_visible` TINYINT NOT NULL DEFAULT '1'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);                                    

    $query=sprintf("ALTER TABLE `%sevt` ADD `evt_filter` TINYINT NOT NULL DEFAULT '0'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);                                    

    $query=sprintf("ALTER TABLE `%sknd` ADD `knd_filter` TINYINT NOT NULL DEFAULT '0'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);                                    

    $query=sprintf("ALTER TABLE `%spct` ADD `pct_filter` TINYINT NOT NULL DEFAULT '0'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

    $versionDB_e[0] = 0;
    $versionDB_e[1] = 7;
    $versionDB_e[2] = 12;
}

if ( ((int)$versionDB_e[1] == 7 && (int)$versionDB_e[2] < 13)) {

logfile("-- update to 0.7.13r78");

    //Create Views
    $query=sprintf("DROP VIEW IF EXISTS `%sget_usr_count_in_grp`;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success); 

    $query=sprintf("CREATE VIEW `%sget_usr_count_in_grp` AS select count(0) AS `COUNT`, `%susr`.`usr_grp` AS `usr_grp` from `%susr` where (`%susr`.`usr_trash` = _utf8'0') group by `%susr`.`usr_grp`;",$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success); 

    $query=sprintf("DROP VIEW IF EXISTS `%sget_arr_grp`;",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success); 

    $query=sprintf("CREATE VIEW `%sget_arr_grp` AS select `%sgrp`.`grp_ID` AS `grp_ID`,`%sgrp`.`grp_name` AS `grp_name`,`%sgrp`.`grp_leader` AS `grp_leader`,`%sgrp`.`grp_trash` AS `grp_trash`,`%susr`.`usr_name` AS `leader_name`,if(isnull(`%sget_usr_count_in_grp`.`COUNT`),0, `%sget_usr_count_in_grp`.`COUNT`) AS `count_users` from ((`%sgrp` left join `%susr` on((`%sgrp`.`grp_leader` = `%susr`.`usr_ID`))) left join `%sget_usr_count_in_grp` on((`%sgrp`.`grp_ID` = `%sget_usr_count_in_grp`.`usr_grp`))) where (`%sgrp`.`grp_trash` <> 1) order by `%sgrp`.`grp_name`;",$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success); 
    
    $query=sprintf("INSERT INTO `%svar` (`var`, `value`) VALUES ('revision', '78');",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
                                            
    $versionDB_e[0] = 0;
    $versionDB_e[1] = 7;
    $versionDB_e[2] = 13;
}

if ((int)$revisionDB < 96) {

    logfile("-- update to 0.7.13r96");

    $query=sprintf("ALTER TABLE `%sconf` ADD `allvisible` TINYINT(1) NOT NULL DEFAULT '1'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

// a proper installed database throws errors from here. don't worry - no problem.

    $query=sprintf("ALTER TABLE `%sevt` CHANGE `visible` `evt_visible` TINYINT(1) NOT NULL DEFAULT '1'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

    $query=sprintf("ALTER TABLE `%sknd` CHANGE `visible` `knd_visible` TINYINT(1) NOT NULL DEFAULT '1'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

    $query=sprintf("ALTER TABLE `%spct` CHANGE `visible` `pct_visible` TINYINT(1) NOT NULL DEFAULT '1'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

    $query=sprintf("ALTER TABLE `%sevt` CHANGE `filter` `evt_filter` TINYINT(1) NOT NULL DEFAULT '0'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

    $query=sprintf("ALTER TABLE `%sknd` CHANGE `filter` `knd_filter` TINYINT(1) NOT NULL DEFAULT '0'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

    $query=sprintf("ALTER TABLE `%spct` CHANGE `filter` `pct_filter` TINYINT(1) NOT NULL DEFAULT '0'",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

    $versionDB_e[0] = 0;
    $versionDB_e[1] = 7;
    $versionDB_e[2] = 13;
}


if ((int)$revisionDB < 141) {

    logfile("-- update to 0.7.13r141");

    $query=sprintf("ALTER TABLE `%sconf` ADD `flip_pct_display` tinyint(1) NOT NULL DEFAULT '0';",$kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

    $versionDB_e[0] = 0;
    $versionDB_e[1] = 7;
    $versionDB_e[2] = 13;
}


if ((int)$revisionDB < 221) {

    logfile("-- update to 0.8");

    // drop views
    $query=sprintf("DROP VIEW IF EXISTS %sget_arr_grp, %sget_usr_count_in_grp",$kga['server_prefix'],$kga['server_prefix']);
    @mysql_query($query); logfile($query,$success);	

    // Set news group name length
    $query=sprintf("ALTER TABLE `%sgrp` CHANGE `grp_name` `grp_name` VARCHAR( 160 )", $kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    // Merge usr and conf tables  
    $query=sprintf("CREATE TABLE IF NOT EXISTS `%susr_tmp` (
  `usr_ID` int(10) NOT NULL,
  `usr_name` varchar(160) NOT NULL,
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
  `recordingstate` tinyint(1) NOT NULL default '1',
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
  `flip_pct_display` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`usr_name`))", $kga['server_prefix']); 
   	$success = @mysql_query($query); logfile($query,$success);
   	
    if (mysql_error() == "") {
    
    	$query=sprintf("SELECT * FROM `%susr` JOIN `%sconf` ON `%susr`.usr_ID = `%sconf`.conf_usrID",$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix'],$kga['server_prefix']);
    	$result = @mysql_query($query); // logfile($query,".",$result);
    	
    	while ($result_array = @mysql_fetch_array($result, MYSQL_ASSOC)) {
        	
    		$query=sprintf("INSERT INTO %susr_tmp (
    		`usr_ID`,
    		`usr_name`,
    		`usr_grp`,
    		`usr_sts`,
    		`usr_trash`,
    		`usr_active`,
    		`usr_mail`,
    		`pw`,
    		`ban`,
    		`banTime`,
    		`secure`,
    		`rowlimit`,
    		`skin`,
    		`lastProject`,
    		`lastEvent`,
    		`lastRecord`,
    		`filter`,
    		`filter_knd`,
    		`filter_pct`,
    		`filter_evt`,
    		`view_knd`,
    		`view_pct`,
    		`view_evt`,
    		`zef_anzahl`,
    		`timespace_in`,
    		`timespace_out`,
    		`autoselection`,
    		`quickdelete`,
    		`allvisible`,
    		`lang`,
    		`flip_pct_display`
    		) VALUES (
    		" . $result_array['usr_ID'] . ",
    		'" . $result_array['usr_name'] . "',
    		" . $result_array['usr_grp'] . ",
    		" . $result_array['usr_sts'] . ",
    		" . $result_array['usr_trash'] . ",
    		" . $result_array['usr_active'] . ",
    		'" . $result_array['usr_mail'] . "',
    		'" . $result_array['pw'] . "',
    		" . $result_array['ban'] . ",
    		" . $result_array['banTime'] . ",
    		'" . $result_array['secure'] . "',
    		" . $result_array['rowlimit'] . ",
    		'" . $result_array['skin'] . "',
    		" . $result_array['lastProject'] . ",
    		" . $result_array['lastEvent'] . ",
    		" . $result_array['lastRecord'] . ",
    		" . $result_array['filter'] . ",
    		" . $result_array['filter_knd'] . ",
    		" . $result_array['filter_pct'] . ",
    		" . $result_array['filter_evt'] . ",
    		" . $result_array['view_knd'] . ",
    		" . $result_array['view_pct'] . ",
    		" . $result_array['view_evt'] . ",
    		" . $result_array['zef_anzahl'] . ",
    		'" . $result_array['timespace_in'] . "',
    		'" . $result_array['timespace_out'] . "',
    		" . $result_array['autoselection'] . ",
    		" . $result_array['quickdelete'] . ",
    		" . $result_array['allvisible'] . ",
    		'" . $result_array['lang'] . "',
    		'" . $result_array['flip_pct_display'] . "'   		
    		)", $kga['server_prefix']);
    		$success = @mysql_query($query); logfile($query,$success);
    		if (mysql_error() != "") {
    			logfile("An error has occured in query: $query");
    			logfile("Error text: " . mysql_error() . "");
    			logfile("UPDATE ABORTED!");
    			die("Database update failed.");
    		}
    	}
    	
    	
	   	$query=sprintf("DROP TABLE `%susr`", $kga['server_prefix']);
    	$success=@mysql_query($query); logfile($query,$success);
    
    	$query=sprintf("DROP TABLE `%sconf`", $kga['server_prefix']);
    	$success=@mysql_query($query); logfile($query,$success);
    
    	$query=sprintf("RENAME TABLE `%susr_tmp` TO `%susr`", $kga['server_prefix'], $kga['server_prefix']);
    	$success=@mysql_query($query); logfile($query,$success);
	
    }

    // in some older installations (which updated from versions earlier than 0.7.5) there are
    // wrong field names in the knd table. we just rename them without checking for errors ...
    $query=sprintf("ALTER TABLE `%sknd` CHANGE `knd_telephon` `knd_tel` VARCHAR( 255 )", $kga['server_prefix']); @mysql_query($query);
    $query=sprintf("ALTER TABLE `%sknd` CHANGE `knd_mobilphon` `knd_mobile` VARCHAR( 255 )", $kga['server_prefix']); @mysql_query($query);

    // Add field for icon/logo filename to customer, project and task table
   	$query=sprintf("ALTER TABLE `%sknd` ADD `knd_logo` VARCHAR( 80 )", $kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

   	$query=sprintf("ALTER TABLE `%spct` ADD `pct_logo` VARCHAR( 80 )", $kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
   	$query=sprintf("ALTER TABLE `%sevt` ADD `evt_logo` VARCHAR( 80 )", $kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    // Add trash field for customer, project and task tables
   	$query=sprintf("ALTER TABLE `%sknd` ADD `knd_trash` TINYINT( 1 ) NOT NULL DEFAULT '0'", $kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);

   	$query=sprintf("ALTER TABLE `%spct` ADD `pct_trash` TINYINT( 1 ) NOT NULL DEFAULT '0'", $kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    $query=sprintf("ALTER TABLE `%sevt` ADD `evt_trash` TINYINT( 1 ) NOT NULL DEFAULT '0'", $kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    
    $query=sprintf("ALTER TABLE `%szef` ADD `zef_cleared` TINYINT( 1 ) NOT NULL DEFAULT '0'", $kga['server_prefix']);
    $success=@mysql_query($query); logfile($query,$success);
    	    	
    
    // put the existing group-customer-relations into the new table
	$query=sprintf("CREATE TABLE `%sgrp_knd` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `grp_ID` INT NOT NULL, `knd_ID` INT NOT NULL);",$kga['server_prefix']);
	$success=@mysql_query($query);
	logfile($query,$success);    	
    	
    $query=sprintf("SELECT `knd_ID`, `knd_grpID` FROM %sknd", $kga['server_prefix']);
    $result = @mysql_query($query);
    	
    while ($current_row = mysql_fetch_array($result, MYSQL_ASSOC)) {	
    	$query=sprintf("INSERT INTO %sgrp_knd (`grp_ID`, `knd_ID`) VALUES (%d, %d)",$kga['server_prefix'], $current_row['knd_grpID'], $current_row['knd_ID']);
    	$success=@mysql_query($query);
		logfile($query,$success);
			
    	if (mysql_error() != "") {
    		logfile("An error has occured in query: $query");
    		logfile("Error text: " . mysql_error() . "");
    		logfile("UPDATE ABORTED!");
    		die("Database update failed.");
    	}
    }
    	
    // put the existing group-project-relations into the new table
	$query=sprintf("CREATE TABLE `%sgrp_pct` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `grp_ID` INT NOT NULL, `pct_ID` INT NOT NULL);",$kga['server_prefix']);
	$success=@mysql_query($query);
	logfile($query,$success);    	
    	
    $query=sprintf("SELECT `pct_ID`, `pct_grpID` FROM %spct", $kga['server_prefix']);
    $result = @mysql_query($query);
    	
    while ($current_row = mysql_fetch_array($result, MYSQL_ASSOC)) {	
    	$query=sprintf("INSERT INTO %sgrp_pct (`grp_ID`, `pct_ID`) VALUES (%d, %d)",$kga['server_prefix'], $current_row['pct_grpID'], $current_row['pct_ID']);
    	$success=@mysql_query($query);
		logfile($query,$success);
		
        if (mysql_error() != "") {
    		logfile("An error has occured in query: $query");
    		logfile("Error text: " . mysql_error() . "");
    		logfile("UPDATE ABORTED!");
    		die("Database update failed.");
    	}
    }
    	
    // put the existing group-event-relations into the new table
	$query=sprintf("CREATE TABLE `%sgrp_evt` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `grp_ID` INT NOT NULL, `evt_ID` INT NOT NULL);",$kga['server_prefix']);
	$success=@mysql_query($query);
	logfile($query,$success);    	
    
    $query=sprintf("SELECT `evt_ID`, `evt_grpID` FROM %sevt", $kga['server_prefix']);
    $result = @mysql_query($query);
    	
    while ($current_row = mysql_fetch_array($result, MYSQL_ASSOC)) {	
    	$query=sprintf("INSERT INTO %sgrp_evt (`grp_ID`, `evt_ID`) VALUES (%d, %d)",$kga['server_prefix'], $current_row['evt_grpID'], $current_row['evt_ID']);
    	$success=@mysql_query($query);
		logfile($query,$success);
			
  		if (mysql_error() != "") {
   		 	logfile("An error has occured in query: $query");
   		 	logfile("Error text: " . mysql_error() . "");
   		 	logfile("UPDATE ABORTED!");
    		die("Database update failed.");
    	}
    }
    
    
    // delete old single-group fields in knd, pct and evt
    $query=sprintf("ALTER TABLE %sknd DROP `knd_grpID`", $kga['server_prefix']);
    $success=@mysql_query($query); 
    logfile($query,$success);
    if (mysql_error() != "") {
    	logfile("An error has occured in query: $query");
    	logfile("Error text: " . mysql_error() . "");
    	logfile("UPDATE ABORTED!");
    	die("Database update failed.");
    }
    
    $query=sprintf("ALTER TABLE %spct DROP `pct_grpID`", $kga['server_prefix']);
    $success=@mysql_query($query); 
    logfile($query,$success);
    if (mysql_error() != "") {
    	logfile("An error has occured in query: $query");
    	logfile("Error text: " . mysql_error() . "");
    	logfile("UPDATE ABORTED!");
    	die("Database update failed.");
    }
    
    $query=sprintf("ALTER TABLE %sevt DROP `evt_grpID`", $kga['server_prefix']);
    $success=@mysql_query($query); 
    logfile($query,$success);
    if (mysql_error() != "") {
    	logfile("An error has occured in query: $query");
    	logfile("Error text: " . mysql_error() . "");
    	logfile("UPDATE ABORTED!");
    	die("Database update failed.");
    }
    
    
    $versionDB_e[0] = 0;
    $versionDB_e[1] = 8;
    $versionDB_e[2] = 0;
}



//////// ---------------------------------------------------------------------------------------------------
//////// ---------------------------------------------------------------------------------------------------
//////// ---------------------------------------------------------------------------------------------------
//////// ---------------------------------------------------------------------------------------------------



if ((int)$revisionDB < 001) {

    logfile("-- update to 0.8.1");


// table usr
    // drop "lastProject"
    // drop "lastEvent"
    // drop "lastRecord"
    // drop "recordingstate"
    // add "showIDs"


    $versionDB_e[0] = 0;
    $versionDB_e[1] = 8;
    $versionDB_e[2] = 1;
}





//////// ---------------------------------------------------------------------------------------------------
//////// ---------------------------------------------------------------------------------------------------
//////// ---------------------------------------------------------------------------------------------------
//////// ---------------------------------------------------------------------------------------------------



// ==============================================================================
// = if DB version differs from installment version -> update DB version number =
// ==============================================================================
if ( (int)$revisionDB != (int)$kga['revision']) { 
    $query=sprintf("UPDATE `%svar` SET value = '%s' WHERE var = 'version';",$kga['server_prefix'],$kga['version']);
    $success=@mysql_query($query); logfile($query,$success);
    $query=sprintf("UPDATE `%svar` SET value = '%d' WHERE var = 'revision';",$kga['server_prefix'],$kga['revision']);
    $success=@mysql_query($query); logfile($query,$success);
}

logfile("-- update finished --------------------------------");
header("Location: index.php");
// header/location isn't working here - no clue why ...
echo "<script type=\"text/javascript\">window.location.href = \"index.php\";</script>";
?>
