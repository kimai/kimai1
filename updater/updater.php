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
 * This script performs updates of the database from any version to the current version.
 */

require_once 'functions.php';

define('KIMAI_UPDATER_RUNNING', true);
$min_php_version = '5.5';

// check all requirements/file permissions before starting an upgrade process
if (!file_exists('../includes/autoconf.php')) {
    exitUpdater('File missing', 'Updater needs an existing kimai configuration!', 'Missing file: <b>includes/autoconf.php</b>');
}
if (!is_writable('../includes/autoconf.php')) {
    exitUpdater('Broken permissions', 'Please give write permission for file:', '<b>includes/autoconf.php</b>');
}
if (!file_exists('../temporary/logfile.txt') && !is_writable('../temporary/')) {
    exitUpdater('Broken permissions', 'Please give write permission for directory:', '<b>temporary/</b>');
}
if (file_exists('../temporary/logfile.txt') && !is_writable('../temporary/logfile.txt')) {
    exitUpdater('Broken permissions', 'Please give write permission for file:', '<b>temporary/logfile.txt</b>');
}
if (version_compare(PHP_VERSION, $min_php_version) < 0) {
    exitUpdater(
        'PHP version outdated',
        'You are using <b>PHP ' . phpversion() . '</b> but Kimai requires at least <b>PHP ' . $min_php_version . '</b>',
        'Please update your PHP installation, <br>we cannot continue the update otherwise.'
    );
}

// ================================================================================
// All requirements are met to start the update, so lets do it :-)
// ================================================================================

require_once '../includes/basics.php';

$database = Kimai_Registry::getDatabase();

if (!$kga['revision']) {
    exitUpdater('DB updated failed', 'Database update cannot be executed:', '<b>Revision not defined</b>');
}

$version_temp = $database->get_DBversion();
$versionDB = $version_temp[0];
$revisionDB = $version_temp[1];
error_log(serialize($version_temp));
unset($version_temp);

// ================================================================================
// Display starting screen before executing the Update finally
// ================================================================================
if (!isset($_REQUEST['a']) && $kga['show_update_warn'] == 1) {
    exitUpdater(
        'UPDATE',
        $kga['lang']['updater'][0] . '
            <form action="" method="post">
            <input type="hidden" name="a" value="1"><br><input type="submit" value="START UPDATE">
            </form>
        ',
        '<a href="db_restore.php" id="dbrecover">Database Backup Recover Utility</a>'
    );
}

// ================================================================================
// timezone was introduced, give the user an option to select the default one
// ================================================================================
if ((int)$revisionDB < 1219 && !isset($_REQUEST['timezone'])) {
    $timeZonesOptions = '';
    $serverZone = @date_default_timezone_get();

    foreach (timezoneList() as $name) {
        if ($name == $serverZone) {
            $timeZonesOptions .= "<option selected=\"selected\">$name</option>";
        } else {
            $timeZonesOptions .= "<option>$name</option>";
        }
    }

    $selectTimezone = '<form action="" method="post"><select name="timezone">' . $timeZonesOptions . '</select>' .
        '<br><br><input type="hidden" name="a" value="1"><input type="submit" value="START UPDATE"></form>';

    exitUpdater(
        $kga['lang']['timezone'],
        $kga['lang']['updater']['timezone'],
        $selectTimezone
    );
}

require_once 'update_header.php';

$errors = 0;
$executed_queries = 0;

Kimai_Logger::logfile('-- begin update -----------------------------------');

$p = $kga['server_prefix'];

// ================================================================================
// CREATE DATABASE BACKUP
// ================================================================================
if ((int)$revisionDB < $kga['revision']) {
    /**
     * Perform an backup (or snapshot) of the current tables.
     */
    Kimai_Logger::logfile('-- begin backup -----------------------------------');

    $backup_stamp = time(); // as an individual backup label the timestamp should be enough for now...
    // by using this type of label we can also exactly identify when it was done
    // may be shown by a recovering script in human-readable format

    $query = ('SHOW TABLES;');

    $result_backup = $database->queryAll($query);
    Kimai_Logger::logfile($query);
    $prefix_length = strlen($p);

    echo '</table>';

    echo '<strong>' . $kga['lang']['updater'][50] . '</strong>';
    echo '<table style="width:100%">';

    foreach ($result_backup as $row) {
        if ((substr($row[0], 0, $prefix_length) == $p) && (substr($row[0], 0, 10) != "kimai_bak_")) {
            $backupTable = "kimai_bak_" . $backup_stamp . "_" . $row[0];
            $query = "CREATE TABLE " . $backupTable . " LIKE " . $row[0];
            exec_query($query);

            $query = "INSERT INTO " . $backupTable . " SELECT * FROM " . $row[0];
            exec_query($query);

            if ($errors) {
                die($kga['lang']['updater'][60]);
            }
        }
    }

    Kimai_Logger::logfile('-- backup finished -----------------------------------');

    echo '</table><br /><br />';
    echo '<strong>' . $kga['lang']['updater'][70] . '</strong></br>';
    echo '<table style="width:100%">';
}

// ================================================================================
// BELOW ARE ALL REVISION UPDATES!
// ================================================================================

if ((int)$revisionDB < 221) {
    exitUpdater(
        'UPDATE NOT POSSIBLE',
        'Sorry, but we cannot update your Kimai version. Please download an older version of Kimai, which has update support for your version.',
        '<a href="https://github.com/kimai/kimai/releases/tag/0.9.3">Download Kimai 0.9.3</a>');
}

if ((int)$revisionDB < 733) {
    Kimai_Logger::logfile('-- update to r733 (0.8.0a)');

    exec_query("ALTER TABLE `${p}evt` CHANGE `evt_visible` `evt_visible` TINYINT(1) NOT NULL DEFAULT '1';", 0);
    exec_query("ALTER TABLE `${p}knd` CHANGE `knd_visible` `knd_visible` TINYINT(1) NOT NULL DEFAULT '1';", 0);
    exec_query("ALTER TABLE `${p}pct` CHANGE `pct_visible` `pct_visible` TINYINT(1) NOT NULL DEFAULT '1';", 0);
    exec_query("ALTER TABLE `${p}evt` CHANGE `evt_filter` `evt_filter` TINYINT(1) NOT NULL DEFAULT '0';", 0);
    exec_query("ALTER TABLE `${p}knd` CHANGE `knd_filter` `knd_filter` TINYINT(1) NOT NULL DEFAULT '0';", 0);
    exec_query("ALTER TABLE `${p}pct` CHANGE `pct_filter` `pct_filter` TINYINT(1) NOT NULL DEFAULT '0';", 0);

    exec_query("ALTER TABLE `${p}evt` CHANGE `evt_ID` `evt_ID` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY;", 0);

    exec_query("ALTER TABLE `${p}grp` CHANGE `grp_ID` `grp_ID` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY;", 0);
    exec_query("ALTER TABLE `${p}grp` DROP `grp_leader`;", 0);

    exec_query("ALTER TABLE `${p}knd` CHANGE `knd_ID` `knd_ID` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY;", 0);

    exec_query("ALTER TABLE `${p}ldr` ADD `uid` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;", 0);

    exec_query("ALTER TABLE `${p}pct` CHANGE `pct_ID` `pct_ID` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY;", 0);

    exec_query("ALTER TABLE `${p}usr` DROP `recordingstate`;", 0);
    exec_query("ALTER TABLE `${p}var` ADD PRIMARY KEY (`var`);", 0);

    exec_query("ALTER TABLE `${p}zef` CHANGE `zef_ID` `zef_ID` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY;", 0);
}

if ((int)$revisionDB < 809) {
    Kimai_Logger::logfile('-- update to r810');
    exec_query("ALTER TABLE `${p}usr` ADD `pct_comment_flag` TINYINT(1) NOT NULL DEFAULT '0'", 1);
}

if ((int)$revisionDB < 817) {
    Kimai_Logger::logfile('-- update to r817');
    exec_query("ALTER TABLE `${p}usr` ADD `showIDs` TINYINT(1) NOT NULL DEFAULT '0'", 1);
}

if ((int)$revisionDB < 837) {
    Kimai_Logger::logfile("-- update to r837");
    exec_query("ALTER TABLE `${p}usr` ADD `usr_alias` VARCHAR(10)", 0);
    exec_query("ALTER TABLE `${p}zef` ADD `zef_location` varchar(50)", 1);
}

if ((int)$revisionDB < 848) {
    Kimai_Logger::logfile('-- update to r848');
    exec_query("ALTER TABLE `${p}zef` ADD `zef_trackingnr` int(20)", 1);
}

if ((int)$revisionDB < 898) {
    Kimai_Logger::logfile('-- update to r898');
    exec_query("CREATE TABLE `${p}rates` (
`user_id` int(10) DEFAULT NULL,
`project_id` int(10) DEFAULT NULL,
`event_id` int(10) DEFAULT NULL,
`rate` decimal(10,2) NOT NULL
);", 1);
    exec_query("ALTER TABLE `${p}zef` ADD `zef_rate` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0';", 1);
}

if ((int)$revisionDB < 922) {
    Kimai_Logger::logfile('-- update to r922');
    exec_query("ALTER TABLE `${p}knd` ADD `knd_password` VARCHAR(255);", 1);
    exec_query("ALTER TABLE `${p}knd` ADD `knd_secure` varchar(60) NOT NULL default '0';", 1);
}

if ((int)$revisionDB < 935) {
    Kimai_Logger::logfile('-- update to r935');
    exec_query("CREATE TABLE `${p}exp` (
`exp_ID` int(10) NOT NULL AUTO_INCREMENT,
`exp_timestamp` int(10) NOT NULL DEFAULT '0',
`exp_usrID` int(10) NOT NULL,
`exp_pctID` int(10) NOT NULL,
`exp_designation` text NOT NULL,
`exp_comment` text NOT NULL,
`exp_comment_type` tinyint(1) NOT NULL DEFAULT '0',
`exp_cleared` tinyint(1) NOT NULL DEFAULT '0',
`exp_value` decimal(10,2) NOT NULL DEFAULT '0.00',
PRIMARY KEY (`exp_ID`)
) AUTO_INCREMENT=1;");
}

if ((int)$revisionDB < 1067) {
    Kimai_Logger::logfile('-- update to r1067');

    // Write new config file with password salt
    $kga['password_salt'] = createPassword(20);
    if (write_config_file(
        $kga['server_database'],
        $kga['server_hostname'],
        $kga['server_username'],
        $kga['server_password'],
        '',
        $kga['server_prefix'],
        $kga['language'],
        $kga['password_salt'],
        'Europe/Berlin')) {
        echo '<tr><td>' . $kga['lang']['updater'][140] . '</td><td class="green">&nbsp;&nbsp;</td></tr>';
    } else {
        die($kga['lang']['updater'][130]);
    }

    // Reset all passwords
    $new_passwords = [];
    $users = $database->queryAll("SELECT * FROM ${p}usr");
    foreach ($users as $user) {
        if ($user['usr_name'] == 'admin') {
            $new_password = 'changeme';
        } else {
            $new_password = createPassword(8);
        }
        exec_query("UPDATE `${p}usr` SET pw = '" .
                   encode_password($new_password) .
            "' WHERE usr_ID = $user[usr_ID]");
        if ($result) {
            $new_passwords[$user['usr_name']] = $new_password;
        }
    }
}

if ((int)$revisionDB < 1068) {
    Kimai_Logger::logfile('-- update to r1068');
    exec_query("ALTER TABLE `${p}usr` CHANGE `autoselection` `autoselection` TINYINT( 1 ) NOT NULL default '0';");
}

if ((int)$revisionDB < 1077) {
    Kimai_Logger::logfile('-- update to r1076');
    exec_query("ALTER TABLE `${p}usr` CHANGE `usr_mail` `usr_mail` varchar(160) DEFAULT ''");
    exec_query("ALTER TABLE `${p}usr` CHANGE `pw` `pw` varchar(254) NULL DEFAULT NULL");
    exec_query("ALTER TABLE `${p}usr` CHANGE `lang` `lang` varchar(6) DEFAULT ''");
    exec_query("ALTER TABLE `${p}zef` CHANGE `zef_comment` `zef_comment` TEXT NULL DEFAULT NULL");
}

if ((int)$revisionDB < 1086) {
    Kimai_Logger::logfile('-- update to r1086');
    exec_query("ALTER TABLE `${p}pct` ADD `pct_budget` DECIMAL(10,2) NOT NULL DEFAULT 0.00");
}

if ((int)$revisionDB < 1088) {
    Kimai_Logger::logfile('-- update to r1088');
    exec_query("ALTER TABLE `${p}usr` ADD `noFading` TINYINT(1) NOT NULL DEFAULT '0'");
}

if ((int)$revisionDB < 1089) {
    Kimai_Logger::logfile('-- update to r1089');
    exec_query("ALTER TABLE `${p}usr` ADD `export_disabled_columns` INT NOT NULL DEFAULT '0'");
}

if ((int)$revisionDB < 1103) {
    Kimai_Logger::logfile('-- update to r1103');
    exec_query("ALTER TABLE `${p}usr` DROP `allvisible`");
}

if ((int)$revisionDB < 1112) {
    Kimai_Logger::logfile('-- update to r1112');
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('currency_name','Euro')");
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('currency_sign','€')");
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('show_sensible_data','1')");
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('show_update_warn','1')");
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('check_at_startup','0')");
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('show_daySeperatorLines','1')");
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('show_gabBreaks','0')");
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('show_RecordAgain','1')");
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('show_TrackingNr','1')");
}

if ((int)$revisionDB < 1113) {
    Kimai_Logger::logfile('-- update to r1113');
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('date_format_0','%d.%m.%Y')");
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('date_format_1','%d.%m.')");
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('date_format_2','%d.%m.%Y')");
    exec_query("DELETE FROM `${p}var` WHERE `var` = 'charset' LIMIT 1");
}

if ((int)$revisionDB < 1115) {
    Kimai_Logger::logfile('-- update to r1115');
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('language','$kga[language]')");
}

if ((int)$revisionDB < 1126) {
    Kimai_Logger::logfile('-- update to r1126');
    exec_query("ALTER TABLE `${p}grp_evt` ADD UNIQUE (`grp_ID`, `evt_ID`);");
    exec_query("ALTER TABLE `${p}grp_knd` ADD UNIQUE (`grp_ID`, `knd_ID`);");
    exec_query("ALTER TABLE `${p}grp_pct` ADD UNIQUE (`grp_ID`, `pct_ID`);");
    exec_query("ALTER TABLE `${p}ldr` ADD UNIQUE (`grp_ID`, `grp_leader`);");
}

if ((int)$revisionDB < 1132) {
    Kimai_Logger::logfile('-- update to r1132');
    exec_query("UPDATE `${p}usr`, ${p}ldr SET usr_sts = 2 WHERE usr_sts = 1");
    exec_query("UPDATE `${p}usr`, ${p}ldr SET usr_sts = 1 WHERE usr_sts = 2 AND grp_leader = usr_ID");
}

if ((int)$revisionDB < 1139) {
    Kimai_Logger::logfile('-- update to r1139');
    exec_query("ALTER TABLE `${p}usr` ADD `user_list_hidden` INT NOT NULL DEFAULT '0'");
}

if ((int)$revisionDB < 1142) {
    Kimai_Logger::logfile('-- update to r1142');
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('roundPrecision','0')");
}

if ((int)$revisionDB < 1145) {
    Kimai_Logger::logfile('-- update to r1145');
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('currency_first','0')");
}

if ((int)$revisionDB < 1176) {
    Kimai_Logger::logfile('-- update to r1176');
    exec_query("ALTER TABLE `${p}exp` ADD INDEX ( `exp_usrID` ) ");
    exec_query("ALTER TABLE `${p}exp` ADD INDEX ( `exp_pctID` ) ");
    exec_query("ALTER TABLE `${p}pct` ADD INDEX ( `pct_kndID` ) ");
    exec_query("ALTER TABLE `${p}zef` ADD INDEX ( `zef_usrID` ) ");
    exec_query("ALTER TABLE `${p}zef` ADD INDEX ( `zef_pctID` ) ");
    exec_query("ALTER TABLE `${p}zef` ADD INDEX ( `zef_evtID` ) ");
}

if ((int)$revisionDB < 1183) {
    Kimai_Logger::logfile('-- update to r1183');
    exec_query("ALTER TABLE `${p}zef` CHANGE `zef_trackingnr` `zef_trackingnr` varchar(30) DEFAULT ''");
}

if ((int)$revisionDB < 1184) {
    Kimai_Logger::logfile('-- update to r1184');
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('decimalSeparator',',')");
}

if ((int)$revisionDB < 1185) {
    Kimai_Logger::logfile('-- update to r1185');
    exec_query("CREATE TABLE `${p}pct_evt` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `pct_ID` INT NOT NULL, `evt_ID` INT NOT NULL, UNIQUE (`pct_ID`, `evt_ID`));");
}

if ((int)$revisionDB < 1206) {
    Kimai_Logger::logfile('-- update to r1206');
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('durationWithSeconds','0')");
}

if ((int)$revisionDB < 1207) {
    Kimai_Logger::logfile('-- update to r1207');
    exec_query("ALTER TABLE `${p}exp` ADD `exp_multiplier` INT NOT NULL DEFAULT '1'");
}

if ((int)$revisionDB < 1213) {
    Kimai_Logger::logfile('-- update to r1213');
    exec_query("ALTER TABLE `${p}knd` DROP `knd_logo`");
    exec_query("ALTER TABLE `${p}pct` DROP `pct_logo`");
    exec_query("ALTER TABLE `${p}evt` DROP `evt_logo`");
}

if ((int)$revisionDB < 1216) {
    Kimai_Logger::logfile('-- update to r1216');
    exec_query("ALTER TABLE `${p}exp`
ADD `exp_refundable` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'expense refundable to employee (0 = no, 1 = yes)' AFTER `exp_comment_type`;");
}

if ((int)$revisionDB < 1219) {
    $timezone = quoteForSql($_REQUEST['timezone']);
    Kimai_Logger::logfile('-- update to r1219');
    exec_query("ALTER TABLE `${p}usr` ADD `timezone` VARCHAR( 40 ) NOT NULL DEFAULT ''");
    exec_query("UPDATE `${p}usr` SET `timezone` = $timezone");
    exec_query("INSERT INTO `${p}var` (`var`,`value`) VALUES('defaultTimezone', $timezone)");
}

if ((int)$revisionDB < 1225) {
    Kimai_Logger::logfile('-- update to r1225');
    exec_query("CREATE TABLE `${p}preferences` (
`userID` int(10) NOT NULL,
`var` varchar(255) NOT NULL,
`value` varchar(255) NOT NULL,
PRIMARY KEY (`userID`,`var`)
);");

    $columns = [
        'rowlimit', 'skin', 'autoselection', 'quickdelete',
        'lang', 'flip_pct_display', 'pct_comment_flag', 'showIDs', 'noFading',
        'export_disabled_columns', 'user_list_hidden', 'timezone'
    ];

    // move user configuration over to preferences table, which are still in use
    foreach ($columns as $column) {
        exec_query("INSERT INTO ${p}preferences (`userID`,`var`,`value`) SELECT `usr_ID` , \"$column\", `$column` FROM `${p}usr`");
    }

    // add unused columns and drop all in usr table
    $columns = array_merge($columns, ['zef_anzahl', 'filter', 'filter_knd', 'filter_pct', 'filter_evt', 'view_knd', 'view_pct', 'view_evt']);
    foreach ($columns as $column) {
        exec_query("ALTER TABLE ${p}usr DROP $column");
    }
}

if ((int)$revisionDB < 1227) {
    Kimai_Logger::logfile('-- update to r1227');
    exec_query("ALTER TABLE `${p}knd` ADD `knd_vat` VARCHAR( 255 ) NOT NULL");
    exec_query("ALTER TABLE `${p}knd` ADD `knd_contact` VARCHAR( 255 ) NOT NULL");
}

if ((int)$revisionDB < 1229) {
    Kimai_Logger::logfile('-- update to r1229');
    exec_query("ALTER TABLE `${p}usr` CHANGE `banTime` `banTime` int(10) NOT NULL DEFAULT 0");
}

if ((int)$revisionDB < 1236) {
    Kimai_Logger::logfile('-- update to r1236');
    exec_query("ALTER TABLE `${p}pct` ADD `pct_internal` TINYINT( 1 ) NOT NULL DEFAULT 0");
}

if ((int)$revisionDB < 1240) {
    Kimai_Logger::logfile('-- update to r1240');
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('exactSums','0')");
}

if ((int)$revisionDB < 1256) {
    Kimai_Logger::logfile('-- update to r1256');
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('defaultVat','0')");
}

if ((int)$revisionDB < 1257) {
    Kimai_Logger::logfile('-- update to r1257');
    exec_query("UPDATE ${p}preferences SET var = CONCAT('ui.',var) WHERE var
IN ('skin', 'rowlimit', 'lang', 'autoselection', 'quickdelete', 'flip_pct_display',
'pct_comment_flag', 'showIDs', 'noFading', 'user_list_hidden', 'hideClearedEntries')");
}

if ((int)$revisionDB < 1284) {
    Kimai_Logger::logfile('-- update to r1284');
    exec_query("ALTER TABLE `${p}exp` CHANGE `exp_multiplier`
`exp_multiplier` decimal(10,2) NOT NULL DEFAULT '1.00'");
}

if ((int)$revisionDB < 1291) {
    Kimai_Logger::logfile('-- update to r1291');
    $salt = $kga['password_salt'];
    $query = "UPDATE `${p}usr` SET pw=MD5(CONCAT('${salt}',pw,'${salt}')) WHERE pw REGEXP '^[0-9a-f]{32}$' = 0 AND pw != ''";
    exec_query($query, false, str_replace($salt, 'salt was stripped', $query));
}

if ((int)$revisionDB < 1305) {
    Kimai_Logger::logfile('-- update to r1305');

    // update knd_name
    $result = $database->queryAll("SELECT knd_ID,knd_name FROM ${p}knd");

    foreach ($result as $customer) {
        $name = htmlspecialchars_decode($customer['knd_name']);

        if ($name == $customer['knd_name']) {
            continue;
        }

        exec_query("UPDATE ${p}knd SET knd_name = " .
            quoteForSql($name) .
            " WHERE knd_ID = $customer[knd_ID]");
    }

    // update pct_name
    $result = $database->queryAll("SELECT pct_ID,pct_name FROM ${p}pct");

    foreach ($result as $project) {
        $name = htmlspecialchars_decode($project['pct_name']);

        if ($name == $project['pct_name']) {
            continue;
        }

        exec_query("UPDATE ${p}pct SET pct_name = " .
            quoteForSql($name) .
            " WHERE pct_ID = $project[pct_ID]");
    }

    // update evt_name
    $result = $database->queryAll("SELECT evt_ID,evt_name FROM ${p}evt");

    foreach ($result as $event) {
        $name = htmlspecialchars_decode($event['evt_name']);

        if ($name == $event['evt_name']) {
            continue;
        }

        exec_query("UPDATE ${p}evt SET evt_name = " .
            quoteForSql($name) . ' WHERE evt_ID = ' . $event['evt_ID']);
    }

    // update usr_name
    $result = $database->queryAll("SELECT usr_ID,usr_name FROM ${p}usr");

    foreach ($result as $user) {
        $name = htmlspecialchars_decode($user['usr_name']);

        if ($name == $user['usr_name']) {
            continue;
        }

        exec_query("UPDATE ${p}usr SET usr_name = " .
            quoteForSql($name) . ' WHERE usr_ID = ' . $user['usr_ID']);
    }

    // update grp_name
    $result = $database->queryAll("SELECT grp_ID,grp_name FROM ${p}grp");

    foreach ($result as $group) {
        $name = htmlspecialchars_decode($group['grp_name']);

        if ($name == $group['grp_name']) {
            continue;
        }

        exec_query("UPDATE ${p}grp SET grp_name = " .
            quoteForSql($name) . ' WHERE grp_ID = ' . $group['grp_ID']);
    }
}

// release of kimai 0.9.2 with r1306

if ((int)$revisionDB < 1326) {
    Kimai_Logger::logfile('-- update to r1326');
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('editLimit','0')");
}

if ((int)$revisionDB < 1327) {
    Kimai_Logger::logfile('-- update to r1327');
    $result = $database->queryAll("SELECT value FROM ${p}var WHERE var = 'defaultTimezone'");
    $timezone = quoteForSql($result[0][0]);
    exec_query("ALTER TABLE ${p}knd ADD COLUMN `knd_timezone` varchar(255) NOT NULL DEFAULT $timezone");
    exec_query("ALTER TABLE ${p}knd ALTER COLUMN `knd_timezone` DROP DEFAULT");
}

if ((int)$revisionDB < 1328) {
    Kimai_Logger::logfile('-- update to r1328');
    exec_query("DELETE FROM ${p}var WHERE var='login' LIMIT 1;");
}

if ((int)$revisionDB < 1331) {
    Kimai_Logger::logfile('-- update to r1331');
    exec_query("ALTER TABLE ${p}evt ADD COLUMN `evt_assignable` TINYINT(1) NOT NULL DEFAULT '0';");
    $result = $database->queryAll("SELECT DISTINCT evt_ID FROM ${p}pct_evt");
    foreach ($result as $row) {
        exec_query("UPDATE ${p}evt SET evt_assignable=1 WHERE evt_ID=" . $row[0]);
    }
}

if ((int)$revisionDB < 1332) {
    Kimai_Logger::logfile('-- update to r1332');
    $query =
        "CREATE TABLE `${p}fixed_rates` (
`project_id` int(10) DEFAULT NULL,
`event_id` int(10) DEFAULT NULL,
`rate` decimal(10,2) NOT NULL
);";
    exec_query($query);
    exec_query("ALTER TABLE ${p}zef ADD COLUMN `zef_fixed_rate` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0';");
}

if ((int)$revisionDB < 1333) {
    Kimai_Logger::logfile('-- update to r1333');
    $query =
        "CREATE TABLE `${p}grp_usr` (
`grp_ID` int(10) NOT NULL,
`usr_ID` int(10) NOT NULL,
PRIMARY KEY (`grp_ID`,`usr_ID`)
) AUTO_INCREMENT=1;";
    exec_query($query);

    $result = $database->queryAll("SELECT usr_ID,usr_grp FROM ${p}usr");
    foreach ($result as $row) {
        exec_query('INSERT INTO ' . $p . 'grp_usr (`grp_ID`, `usr_ID`) VALUES(' . $row['usr_grp'] . ',' . $row['usr_ID'] . ');');
    }

    exec_query("ALTER TABLE ${p}usr DROP `usr_grp`;");
}

if ((int)$revisionDB < 1347) {
    Kimai_Logger::logfile('-- update to r1347');
    exec_query("ALTER TABLE `${p}pct_evt` ADD `evt_budget` DECIMAL( 10, 2 ) NULL ,
ADD `evt_effort` DECIMAL( 10, 2 ) NULL ,
ADD `evt_approved` DECIMAL( 10, 2 ) NULL ;");

    exec_query("ALTER TABLE `${p}pct` ADD `pct_effort` DECIMAL( 10, 2 ) NULL AFTER `pct_budget` ,
ADD `pct_approved` DECIMAL( 10, 2 ) NULL AFTER `pct_effort` ");

    exec_query("ALTER TABLE `${p}zef` ADD `zef_status` SMALLINT DEFAULT 1,
ADD `zef_billable` TINYINT NULL");

    exec_query("CREATE TABLE `${p}status` (
`status_id` TINYINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`status` VARCHAR( 200 ) NOT NULL
) ENGINE = InnoDB ");

    exec_query("INSERT INTO `${p}status` (`status_id` ,`status`) VALUES ('1', 'open'), ('2', 'review'), ('3', 'closed');");

    exec_query("ALTER TABLE `${p}zef` ADD `zef_budget` DECIMAL( 10, 2 ) NULL AFTER `zef_fixed_rate` ,
ADD `zef_approved` DECIMAL( 10, 2 ) NULL AFTER `zef_budget` ;");

    exec_query("ALTER TABLE `${p}zef` ADD `zef_description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL AFTER `zef_evtID` ");

    exec_query("UPDATE ${p}zef SET zef_status = 3 WHERE zef_cleared = 1");

    exec_query("INSERT INTO `${p}var` (`var` ,`value`) VALUES ('roundTimesheetEntries', '0' );");

    exec_query("INSERT INTO `${p}var` (`var` ,`value`) VALUES ('roundMinutes', '0');");

    exec_query("INSERT INTO `${p}var` (`var` ,`value`) VALUES ('roundSeconds', '0');");

    exec_query("DELETE FROM `${p}var` WHERE `var` = 'status';");
}

if ((int)$revisionDB < 1349) {
    Kimai_Logger::logfile('-- update to r1350');
    exec_query("ALTER TABLE `${p}usr` ADD `apikey` VARCHAR( 30 ) NULL AFTER `timespace_out`");
    exec_query("ALTER TABLE `${p}usr` ADD UNIQUE (`apikey`)");
}

if ((int)$revisionDB < 1368) {
    Kimai_Logger::logfile('-- update to r1368');

    // some users don't seem to have these columns so we add them here (if they don't exist yet).
    exec_query("ALTER TABLE  `${p}evt` ADD `evt_budget`     decimal(10,2) DEFAULT NULL;", false);
    exec_query("ALTER TABLE  `${p}evt` ADD `evt_effort`     decimal(10,2) DEFAULT NULL;", false);
    exec_query("ALTER TABLE  `${p}evt` ADD `evt_approved`   decimal(10,2) DEFAULT NULL;", false);

    exec_query("ALTER TABLE `${p}evt` RENAME TO `${p}activities`,
CHANGE `evt_ID`         `activityID` int(10) NOT NULL AUTO_INCREMENT,
CHANGE `evt_name`       `name`       varchar(255) NOT NULL,
CHANGE `evt_comment`    `comment`    text NOT NULL,
CHANGE `evt_visible`    `visible`    tinyint(1) NOT NULL DEFAULT '1',
CHANGE `evt_filter`     `filter`     tinyint(1) NOT NULL DEFAULT '0',
CHANGE `evt_trash`      `trash`      tinyint(1) NOT NULL DEFAULT '0',
CHANGE `evt_assignable` `assignable` tinyint(1) NOT NULL DEFAULT '0',
CHANGE `evt_budget`     `budget`     decimal(10,2) DEFAULT NULL,
CHANGE `evt_effort`     `effort`     decimal(10,2) DEFAULT NULL,
CHANGE `evt_approved`   `approved`   decimal(10,2) DEFAULT NULL
;");

    exec_query("ALTER TABLE `${p}exp` RENAME TO `${p}expenses`,
CHANGE `exp_ID`           `expenseID`   int(10) NOT NULL AUTO_INCREMENT,
CHANGE `exp_timestamp`    `timestamp`   int(10) NOT NULL DEFAULT '0',
CHANGE `exp_usrID`        `userID`      int(10) NOT NULL,
CHANGE `exp_pctID`        `projectID`   int(10) NOT NULL,
CHANGE `exp_designation`  `designation` text NOT NULL,
CHANGE `exp_comment`      `comment`     text NOT NULL,
CHANGE `exp_comment_type` `commentType` tinyint(1) NOT NULL DEFAULT '0',
CHANGE `exp_refundable`   `refundable`  tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'expense refundable to employee (0 = no, 1 = yes)',
CHANGE `exp_cleared`      `cleared`     tinyint(1) NOT NULL DEFAULT '0',
CHANGE `exp_multiplier`   `multiplier`  decimal(10,2) NOT NULL DEFAULT '1.00',
CHANGE `exp_value`        `value`       decimal(10,2) NOT NULL DEFAULT '0.00'
;");

    exec_query("ALTER TABLE `${p}fixed_rates` RENAME TO `${p}fixedRates`,
CHANGE `project_id` `projectID`  int(10) DEFAULT NULL,
CHANGE `event_id`   `activityID` int(10) DEFAULT NULL
;");

    exec_query("ALTER TABLE `${p}grp` RENAME TO `${p}groups`,
CHANGE `grp_ID`    `groupID` int(10) NOT NULL AUTO_INCREMENT,
CHANGE `grp_name`  `name`    varchar(160) NOT NULL,
CHANGE `grp_trash` `trash`   tinyint(1) NOT NULL DEFAULT '0'
;");

    exec_query("ALTER TABLE `${p}grp_evt` DROP INDEX `grp_ID`;", false);
    exec_query("ALTER TABLE `${p}grp_evt` RENAME TO `${p}groups_activities`,
CHANGE `grp_ID` `groupID`    int(10) NOT NULL,
CHANGE `evt_ID` `activityID` int(10) NOT NULL,
DROP `uid`,
ADD PRIMARY KEY (`groupID`, `activityID`)
;");

    exec_query("ALTER TABLE `${p}grp_knd` DROP INDEX `grp_ID`;", false);
    exec_query("ALTER TABLE `${p}grp_knd` RENAME TO `${p}groups_customers`,
CHANGE `grp_ID` `groupID`    int(10) NOT NULL,
CHANGE `knd_ID` `customerID` int(10) NOT NULL,
DROP `uid`,
ADD PRIMARY KEY (`groupID`, `customerID`)
;");

    exec_query("ALTER TABLE `${p}grp_pct` DROP INDEX `grp_ID`;", false);
    exec_query("ALTER TABLE `${p}grp_pct` RENAME TO `${p}groups_projects`,
CHANGE `grp_ID` `groupID`    int(10) NOT NULL,
CHANGE `pct_ID` `projectID` int(10) NOT NULL,
DROP `uid`,
ADD PRIMARY KEY (`groupID`, `projectID`)
;");

    exec_query("ALTER TABLE `${p}grp_usr` RENAME TO `${p}groups_users`,
CHANGE `grp_ID` `groupID`    int(10) NOT NULL,
CHANGE `usr_ID` `userID` int(10) NOT NULL
;");

    exec_query("ALTER TABLE `${p}knd` RENAME TO `${p}customers`,
CHANGE `knd_ID`       `customerID` int(10) NOT NULL AUTO_INCREMENT,
CHANGE `knd_name`     `name`       varchar(255) NOT NULL,
CHANGE `knd_password` `password`   varchar(255) DEFAULT NULL,
CHANGE `knd_secure`   `secure`     varchar(60) NOT NULL DEFAULT '0',
CHANGE `knd_comment`  `comment`    text NOT NULL,
CHANGE `knd_visible`  `visible`    tinyint(1) NOT NULL DEFAULT '1',
CHANGE `knd_filter`   `filter`     tinyint(1) NOT NULL DEFAULT '0',
CHANGE `knd_company`  `company`    varchar(255) NOT NULL,
CHANGE `knd_vat`      `vat`        varchar(255) NOT NULL,
CHANGE `knd_contact`  `contact`    varchar(255) NOT NULL,
CHANGE `knd_street`   `street`     varchar(255) NOT NULL,
CHANGE `knd_zipcode`  `zipcode`    varchar(255) NOT NULL,
CHANGE `knd_city`     `city`       varchar(255) NOT NULL,
CHANGE `knd_tel`      `phone`      varchar(255) NOT NULL,
CHANGE `knd_fax`      `fax`        varchar(255) NOT NULL,
CHANGE `knd_mobile`   `mobile`     varchar(255) NOT NULL,
CHANGE `knd_mail`     `mail`       varchar(255) NOT NULL,
CHANGE `knd_homepage` `homepage`   varchar(255) NOT NULL,
CHANGE `knd_trash`    `trash`      tinyint(1) NOT NULL DEFAULT '0',
CHANGE `knd_timezone` `timezone`   varchar(255) NOT NULL
;");

    exec_query("ALTER TABLE `${p}ldr` RENAME TO `${p}groupleaders`,
CHANGE `grp_ID`     `groupID` int(10) NOT NULL,
CHANGE `grp_leader` `userID`  int(10) NOT NULL,
DROP `uid`,
ADD PRIMARY KEY (`groupID`, `userID`)
;");

    exec_query("ALTER TABLE `${p}pct` RENAME TO `${p}projects`,
CHANGE `pct_ID`       `projectID`  int(10) NOT NULL AUTO_INCREMENT,
CHANGE `pct_kndID`    `customerID` int(3) NOT NULL,
CHANGE `pct_name`     `name`       varchar(255) NOT NULL,
CHANGE `pct_comment`  `comment`    text NOT NULL,
CHANGE `pct_visible`  `visible`    tinyint(1) NOT NULL DEFAULT '1',
CHANGE `pct_filter`   `filter`     tinyint(1) NOT NULL DEFAULT '0',
CHANGE `pct_trash`    `trash`      tinyint(1) NOT NULL DEFAULT '0',
CHANGE `pct_budget`   `budget`     decimal(10,2) NOT NULL DEFAULT '0.00',
CHANGE `pct_effort`   `effort`     decimal(10,2) DEFAULT NULL,
CHANGE `pct_approved` `approved`   decimal(10,2) DEFAULT NULL,
CHANGE `pct_internal` `internal`   tinyint(1) NOT NULL DEFAULT '0'
;");

    // fix ER_WARN_DATA_TRUNCATED for evt_budget
    exec_query("UPDATE `${p}pct_evt` SET `evt_budget` = 0.00 WHERE `evt_budget` IS NULL");

    exec_query("ALTER TABLE `${p}pct_evt` RENAME TO `${p}projects_activities`,
CHANGE `pct_ID` `projectID`  int(10) NOT NULL,
CHANGE `evt_ID` `activityID` int(10) NOT NULL,
CHANGE `evt_budget`   `budget`     decimal(10,2) NOT NULL DEFAULT '0.00',
CHANGE `evt_effort`   `effort`     decimal(10,2) DEFAULT NULL,
CHANGE `evt_approved` `approved`   decimal(10,2) DEFAULT NULL,
DROP `uid`,
ADD PRIMARY KEY (`projectID`, `activityID`)
;");

    exec_query("ALTER TABLE `${p}preferences`
CHANGE `var` `option` varchar(255) NOT NULL
;");

    exec_query("ALTER TABLE `${p}rates`
CHANGE `user_id`    `userID`     int(10) DEFAULT NULL,
CHANGE `project_id` `projectID`  int(10) DEFAULT NULL,
CHANGE `event_id`   `activityID` int(10) DEFAULT NULL
;");

    exec_query("ALTER TABLE `${p}status` RENAME TO `${p}statuses`,
CHANGE `status_id` `statusID` tinyint(4) NOT NULL AUTO_INCREMENT
;");

    exec_query("ALTER TABLE `${p}usr` RENAME TO `${p}users`,
CHANGE `usr_ID`        `userID`   int(10) NOT NULL,
CHANGE `usr_name`      `name`     varchar(160) COLLATE latin1_general_ci NOT NULL,
CHANGE `usr_alias`     `alias`    varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
CHANGE `usr_sts`       `status`   tinyint(1) NOT NULL DEFAULT '2',
CHANGE `usr_trash`     `trash`    tinyint(1) NOT NULL DEFAULT '0',
CHANGE `usr_active`    `active`   tinyint(1) NOT NULL DEFAULT '1',
CHANGE `usr_mail`      `mail`     varchar(160) COLLATE latin1_general_ci NOT NULL DEFAULT '',
CHANGE `pw`            `password` varchar(254) COLLATE latin1_general_ci DEFAULT NULL,
CHANGE `ban`           `ban`      int(1) NOT NULL DEFAULT '0',
CHANGE `banTime`       `banTime`  int(10) NOT NULL DEFAULT '0',
CHANGE `secure`        `secure`   varchar(60) COLLATE latin1_general_ci NOT NULL DEFAULT '0',
CHANGE `lastEvent`     `lastActivity` int(10) NOT NULL DEFAULT '1',
CHANGE `timespace_in`  `timeframeBegin` varchar(60) COLLATE latin1_general_ci NOT NULL DEFAULT '0',
CHANGE `timespace_out` `timeframeEnd`   varchar(60) COLLATE latin1_general_ci NOT NULL DEFAULT '0',
DROP PRIMARY KEY,
ADD PRIMARY KEY (`userID`),
ADD UNIQUE KEY `name` (`name`)
;");

    exec_query("ALTER TABLE `${p}var` RENAME TO `${p}configuration`,
CHANGE `var` `option` varchar(255) NOT NULL
;");

    exec_query("UPDATE `${p}configuration` SET `option` = 'project_comment_flag' WHERE `option` = 'pct_comment_flag';");

    exec_query("ALTER TABLE `${p}zef` RENAME TO `${p}timeSheet`,
CHANGE `zef_ID`           `timeEntryID`     int(10) NOT NULL AUTO_INCREMENT,
CHANGE `zef_in`           `start`           int(10) NOT NULL DEFAULT '0',
CHANGE `zef_out`          `end`             int(10) NOT NULL DEFAULT '0',
CHANGE `zef_time`         `duration`        int(6) NOT NULL DEFAULT '0',
CHANGE `zef_usrID`        `userID`          int(10) NOT NULL,
CHANGE `zef_pctID`        `projectID`       int(10) NOT NULL,
CHANGE `zef_evtID`        `activityID`      int(10) NOT NULL,
CHANGE `zef_description`  `description`     text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
CHANGE `zef_comment`      `comment`         text COLLATE latin1_general_ci,
CHANGE `zef_comment_type` `commentType`     tinyint(1) NOT NULL DEFAULT '0',
CHANGE `zef_cleared`      `cleared`         tinyint(1) NOT NULL DEFAULT '0',
CHANGE `zef_location`     `location`        varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
CHANGE `zef_trackingnr`   `trackingNumber`  varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
CHANGE `zef_rate`         `rate`            decimal(10,2) NOT NULL DEFAULT '0.00',
CHANGE `zef_fixed_rate`   `fixedRate`       decimal(10,2) NOT NULL DEFAULT '0.00',
CHANGE `zef_budget`       `budget`          decimal(10,2) DEFAULT NULL,
CHANGE `zef_approved`     `approved`        decimal(10,2) DEFAULT NULL,
CHANGE `zef_status`       `statusID`        smallint(6) NOT NULL,
CHANGE `zef_billable`     `billable`        tinyint(4) DEFAULT NULL COMMENT 'how many percent are billable to customer'
;");
}

if ((int)$revisionDB < 1370) {
    Kimai_Logger::logfile('-- update to r1370');
    $result = $database->queryAll("SELECT `value` FROM ${p}configuration WHERE `option` = 'defaultTimezone'");
    $defaultTimezone = $result[0][0];

    $success = write_config_file(
        $kga['server_database'],
        $kga['server_hostname'],
        $kga['server_username'],
        $kga['server_password'],
        '',
        $kga['server_prefix'],
        $kga['language'],
        $kga['password_salt'],
        $defaultTimezone
    );

    if ($success) {
        $level = 'green';
        $additional = 'Timezone: ' . $defaultTimezone;
    } else {
        $level = 'red';
        $additional = 'Unable to write to file.';
    }

    printLine($level, 'Store default timezone in configuration file <i>autoconf.php</i>.', $additional);

    if ($success) {
        exec_query("DELETE FROM ${p}configuration WHERE `option` = 'defaultTimezone'");
    }
}

if ((int)$revisionDB < 1371) {
    Kimai_Logger::logfile('-- update to r1371');
    // The mentioned columns were accidentally removed by the update script. But there was no release since then.
    // Therefore this updater was fixed to to the right thing now: Keep the column and rename it correctly.
    // But there might be people using the development version. They lost their data but we have to add the columns again.
    // That's why these queries are allowed to fail. This will happen for all not using a development version.

    exec_query("ALTER TABLE `${p}activities`
DROP `budget`,
DROP `effort`,
DROP `approved`
;", false);
}

if ((int)$revisionDB < 1372) {
    Kimai_Logger::logfile('-- update to r1372');
    exec_query("ALTER TABLE `${p}users` CHANGE `alias` `alias` varchar(160);");
}

if ((int)$revisionDB < 1373) {
    Kimai_Logger::logfile('-- update to r1373');
    exec_query("ALTER TABLE `${p}activities` DROP `assignable`;");
}

if ((int)$revisionDB < 1374) {
    Kimai_Logger::logfile('-- update to r1374');
    require("../installer/installPermissions.php");

    // add membershipRoleID column, initialized with user role
    exec_query("ALTER TABLE `${p}groups_users` ADD `membershipRoleID` int(10) DEFAULT $membershipUserRoleID;");
    exec_query("ALTER TABLE `${p}groups_users` CHANGE `membershipRoleID` `membershipRoleID` int(10) NOT NULL;");

    // add globalRoleID column, initialized with user role
    exec_query("ALTER TABLE `${p}users` ADD `globalRoleID` int(10) DEFAULT $globalUserRoleID;");
    exec_query("ALTER TABLE `${p}users` CHANGE `globalRoleID` `globalRoleID` int(10) NOT NULL;");
    exec_query("UPDATE `${p}users` SET `globalRoleID` = (SELECT globalRoleID FROM `${p}globalRoles` WHERE name = 'Admin') WHERE status=0;");

    // set groupleader role
    exec_query("UPDATE `${p}groups_users` SET membershipRoleID=(SELECT membershipRoleID FROM `${p}membershipRoles` WHERE name = 'Groupleader') WHERE (groupID,userID) IN (SELECT groupID, userID FROM `${p}groupleaders`)");

    // set admin role
    exec_query("UPDATE `${p}groups_users` SET membershipRoleID=(SELECT membershipRoleID FROM `${p}membershipRoles` WHERE name = 'Admin') WHERE userID IN (SELECT userID FROM `${p}users` WHERE status=0)");
}

if ((int)$revisionDB < 1375) {
    Kimai_Logger::logfile('-- update to r1375');
    foreach (['customer', 'project', 'activity', 'group', 'user'] as $object) {
        exec_query("ALTER TABLE `${p}globalRoles` ADD `core-$object-otherGroup-view` tinyint DEFAULT 1;");
        exec_query("ALTER TABLE `${p}globalRoles` CHANGE `core-$object-otherGroup-view` `core-$object-otherGroup-view` tinyint DEFAULT 0;");
    }

    exec_query("DROP TABLE `${p}groupleaders`;");
}

if ((int)$revisionDB < 1376) {
    Kimai_Logger::logfile('-- update to r1376');
    # column already added in installer/installPermissions.php in r1374
    #exec_query("ALTER TABLE `${p}globalRoles` ADD `demo_ext-access` tinyint DEFAULT 0;", false);
    exec_query("UPDATE `${p}globalRoles` SET `demo_ext-access` = 1 WHERE `name` = 'Admin';");
}

if ((int)$revisionDB < 1377) {
    Kimai_Logger::logfile('-- update to r1377');
    exec_query("ALTER TABLE `${p}rates` ADD UNIQUE KEY(`userID`, `projectID`, `activityID`);");
}

if ((int)$revisionDB < 1378) {
    Kimai_Logger::logfile('-- update to r1378');
    exec_query("UPDATE `${p}configuration` SET `value` = '0' WHERE `option` = 'show_sensible_data';");
}

if ((int)$revisionDB < 1379) {
    Kimai_Logger::logfile('-- update to r1379');
    if (!isset($defaultTimezone) && isset($kga['defaultTimezone'])) {
        $defaultTimezone = $kga['defaultTimezone'];
    }
    if (!isset($defaultTimezone)) {
        $defaultTimezone = null;
    }

    $success = write_config_file(
        $kga['server_database'],
        $kga['server_hostname'],
        $kga['server_username'],
        $kga['server_password'],
        '',
        $kga['server_prefix'],
        $kga['language'],
        $kga['password_salt'],
        $defaultTimezone
    );

    if ($success) {
        $level = 'green';
    } else {
        $level = 'red';
    }

    printLine($level, 'Updated autoconf.php to use MYSQL configuration in <i>autoconf.php</i>.');
}

if ((int)$revisionDB < 1380) {
    Kimai_Logger::logfile('-- update to r1380');
    exec_query("INSERT INTO `${p}configuration` VALUES('allowRoundDown', '1');");
}

if ((int)$revisionDB < 1381) {
    Kimai_Logger::logfile('-- update to r1381');
    // make sure all keys are defined correctly
    # primary key since r733
    #exec_query("ALTER TABLE `${p}activities`          ADD PRIMARY KEY(`activityID`);", false);
    #exec_query("ALTER TABLE `${p}configuration`       ADD PRIMARY KEY(`option`);", false);
    #exec_query("ALTER TABLE `${p}customers`           ADD PRIMARY KEY(`customerID`);", false);
    #exec_query("ALTER TABLE `${p}expenses`            ADD PRIMARY KEY(`expenseID`);", false);
    exec_query("ALTER TABLE `${p}expenses`            ADD INDEX      (`userID`);", false);
    exec_query("ALTER TABLE `${p}expenses`            ADD INDEX      (`projectID`);", false);
    exec_query("ALTER TABLE `${p}fixedRates`          ADD UNIQUE  KEY(`projectID`, `activityID`);", false);
    #exec_query("ALTER TABLE `${p}globalRoles`         ADD PRIMARY KEY(`globalRoleID`);", false);
    #exec_query("ALTER TABLE `${p}groups`              ADD PRIMARY KEY(`groupID`);", false);
    exec_query("ALTER TABLE `${p}groups_activities`   ADD UNIQUE  KEY(`groupID`, `activityID`);", false);
    exec_query("ALTER TABLE `${p}groups_customers`    ADD UNIQUE  KEY(`groupID`, `customerID`);", false);
    exec_query("ALTER TABLE `${p}groups_projects`     ADD UNIQUE  KEY(`groupID`, `projectID`);", false);
    exec_query("ALTER TABLE `${p}groups_users`        ADD UNIQUE  KEY(`groupID`, `userID`);", false);
    #exec_query("ALTER TABLE `${p}membershipRoles`     ADD PRIMARY KEY(`membershipRoleID`);", false);
    #exec_query("ALTER TABLE `${p}preferences`         ADD PRIMARY KEY(`userID`, `option`);", false);
    #exec_query("ALTER TABLE `${p}projects`            ADD PRIMARY KEY(`projectID`);", false);
    exec_query("ALTER TABLE `${p}projects`            ADD INDEX      (`customerID`);", false);
    exec_query("ALTER TABLE `${p}projects_activities` ADD UNIQUE  KEY(`projectID`, `activityID`);", false);
    exec_query("ALTER TABLE `${p}rates`               ADD UNIQUE  KEY(`userID`, `projectID`, `activityID`);", false);
    #exec_query("ALTER TABLE `${p}statuses`            ADD PRIMARY KEY(`statusID`);", false);

    # primary key exists since r733 (renamed in r1368)
    #exec_query("ALTER TABLE `${p}timeSheet` ADD PRIMARY KEY(`timeEntryID`);", false);

    #drop keys from r1176 and create new ones
    exec_query("ALTER TABLE `${p}timeSheet` DROP INDEX zef_usrID", false);
    exec_query("ALTER TABLE `${p}timeSheet` ADD INDEX (`userID`)", false);

    exec_query("ALTER TABLE `${p}timeSheet` DROP INDEX zef_pctID", false);
    exec_query("ALTER TABLE `${p}timeSheet` ADD INDEX (`projectID`)", false);

    exec_query("ALTER TABLE `${p}timeSheet` DROP INDEX zef_evtID", false);
    exec_query("ALTER TABLE `${p}timeSheet` ADD INDEX (`activityID`)", false);

    # column has primary key since r1368
    #exec_query("ALTER TABLE `${p}users` ADD PRIMARY KEY(`userID`);", false);
    exec_query("ALTER TABLE `${p}users` ADD UNIQUE  KEY(`name`);", false);
    exec_query("ALTER TABLE `${p}users` ADD UNIQUE  KEY(`apiKey`);", false);

    exec_query("UPDATE `${p}preferences` SET `option` = 'ui.project_comment_flag' WHERE `option` = 'ui.pct_comment_flag';");
}

if ((int)$revisionDB < 1382) {
    Kimai_Logger::logfile('-- update to r1382');
    #column already added in installer/installPermissions.php in r1374
    #exec_query("ALTER TABLE `${p}membershipRoles` ADD `core-user-view` tinyint DEFAULT 0 AFTER `core-user-unassign`;", false);
    exec_query("UPDATE `${p}membershipRoles` SET `core-user-view` = 1 WHERE `name` = 'Admin';");
    exec_query("UPDATE `${p}membershipRoles` SET `core-user-view` = 1 WHERE `name` = 'Groupleader';");
}

if ((int)$revisionDB < 1383) {
    Kimai_Logger::logfile('-- update to r1383');
    exec_query("INSERT INTO `${p}configuration` VALUES('defaultStatusID', '1');");
}

if ((int)$revisionDB < 1384) {
    Kimai_Logger::logfile('-- update to r1384');
    exec_query("ALTER TABLE ${p}users ADD COLUMN `passwordResetHash` char(32) NULL DEFAULT NULL AFTER `password`");
    exec_query("ALTER TABLE ${p}customers ADD COLUMN `passwordResetHash` char(32) NULL DEFAULT NULL AFTER `password`");
}

// release of kimai 0.9.3

if ((int)$revisionDB < 1385) {
    Kimai_Logger::logfile('-- update to r1385');
    exec_query("ALTER TABLE ${p}customers CHANGE `comment` `comment` TEXT NULL;");
    exec_query("ALTER TABLE ${p}customers CHANGE `company` `company` VARCHAR(255) NULL;");
    exec_query("ALTER TABLE ${p}customers CHANGE `vat` `vat` VARCHAR(255) NULL;");
    exec_query("ALTER TABLE ${p}customers CHANGE `contact` `contact` VARCHAR(255) NULL;");
    exec_query("ALTER TABLE ${p}customers CHANGE `street` `street` VARCHAR(255) NULL");
    exec_query("ALTER TABLE ${p}customers CHANGE `zipcode` `zipcode` VARCHAR(255) NULL;");
    exec_query("ALTER TABLE ${p}customers CHANGE `city` `city` VARCHAR(255) NULL;");
    exec_query("ALTER TABLE ${p}customers CHANGE `phone` `phone` VARCHAR(255) NULL;");
    exec_query("ALTER TABLE ${p}customers CHANGE `fax` `fax` VARCHAR(255) NULL;");
    exec_query("ALTER TABLE ${p}customers CHANGE `mobile` `mobile` VARCHAR(255) NULL;");
    exec_query("ALTER TABLE ${p}customers CHANGE `mail` `mail` VARCHAR(255) NULL;");
    exec_query("ALTER TABLE ${p}customers CHANGE `homepage` `homepage` VARCHAR(255) NULL;");

    exec_query("ALTER TABLE ${p}projects CHANGE `comment` `comment` TEXT NULL;");
    exec_query("ALTER TABLE ${p}projects CHANGE `budget` `budget` DECIMAL(10,2) NULL DEFAULT '0.00';");

    exec_query("ALTER TABLE ${p}activities CHANGE `comment` `comment` TEXT NULL;");
}

if ((int)$revisionDB < 1386) {
    Kimai_Logger::logfile('-- update to r1386');
    exec_query("ALTER TABLE ${p}expenses CHANGE `comment` `comment` TEXT NULL;");
}

if ((int)$revisionDB < 1387) {
    Kimai_Logger::logfile('-- update to r1387');
    exec_query("UPDATE `${p}configuration` set `value`= 'dd.mm.yy' WHERE `option` = 'date_format_0'");
    exec_query("INSERT INTO `${p}configuration` (`option`,`value`) VALUES('date_format_3','d.m.Y')");
}

if ((int)$revisionDB < 1388) {
    Kimai_Logger::logfile('-- update to r1388');
    exec_query("DELETE FROM `${p}configuration` WHERE `option` = 'lastdbbackup'");
    exec_query("DELETE FROM `${p}configuration` WHERE `option` = 'kimail'");
}

// release of kimai 1.0.0

if ((int)$revisionDB < 1389) {
    Kimai_Logger::logfile('-- update to r1389');
    exec_query("ALTER TABLE ${p}customers ADD COLUMN `country` varchar(2) NULL DEFAULT NULL AFTER `city`");
}

// release of kimai 1.1.0

if ((int)$revisionDB < 1390) {
    Kimai_Logger::logfile('-- update to r1390');
    exec_query("DELETE FROM `${p}configuration` WHERE `option` = 'show_sensible_data'");
}

if ((int)$revisionDB < 1391) {
    Kimai_Logger::logfile('-- update to r1391');
    exec_query("INSERT INTO `${p}configuration` (`option`,`value`) VALUES('table_time_format', '%H:%M')");
}

if ((int)$revisionDB < 1392) {
    Kimai_Logger::logfile('-- update to r1392');

    $charset = '';
    if ($kga['utf8']) {
        $charset = 'utf8';
    }

    $success = write_config_file(
        $kga['server_database'],
        $kga['server_hostname'],
        $kga['server_username'],
        $kga['server_password'],
        $charset,
        $kga['server_prefix'],
        $kga['language'],
        $kga['password_salt'],
        $kga['defaultTimezone']
    );

    if ($success) {
        $level = 'green';
        $additional = 'charset: ' . $charset;
    } else {
        $level = 'red';
        $additional = 'Unable to write config file.';
    }

    printLine($level, 'Store charset in configuration file <i>autoconf.php</i>.', $additional);
}

if ((int)$revisionDB < 1393) {
    Kimai_Logger::logfile('-- update to r1393');
    exec_query("ALTER TABLE `${p}users` CHANGE `mail` `mail` VARCHAR(160) NULL");
    exec_query("ALTER TABLE `${p}timeSheet` CHANGE `fixedRate` `fixedRate` DECIMAL(10,2) NULL");
}

if ((int)$revisionDB < 1394) {
    Kimai_Logger::logfile('-- update to r1394');
    exec_query("ALTER TABLE `${p}preferences` CHANGE `option` `option` VARCHAR(190) NOT NULL");
    exec_query("ALTER TABLE `${p}configuration` CHANGE `option` `option` VARCHAR(190) NOT NULL");
}

// release of kimai 1.2.2
// release of kimai 1.3.0
// release of kimai 1.3.1

// ================================================================================
// FINALIZATION: update DB version number
// ================================================================================
if ((int)$revisionDB < $kga['revision'] && !$errors) {
    $query = sprintf("UPDATE `${p}configuration` SET value = '%s' WHERE `option` = 'version';", $kga['version']);
    exec_query($query, 0);

    $query = sprintf("UPDATE `${p}configuration` SET value = '%d' WHERE `option` = 'revision';", $kga['revision']);
    exec_query($query, 0);
}

Kimai_Logger::logfile('-- update finished --------------------------------');

require_once 'update_footer.php';
