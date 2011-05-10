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
 * This script performs updates of the database from any version
 * to the current version. 
 */ 
 
require('includes/basics.php');


if (!$kga['revision']) die("Database update failed. (Revision not defined!)");

$version_temp  = get_DBversion();
$versionDB  = $version_temp[0];
$revisionDB = $version_temp[1];
unset($version_temp);
 
if (!isset($_REQUEST['a']) && $kga['show_update_warn'] == 1) { 

$RUsure = $kga['lang']['updater'][0];

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="robots" value="noindex,nofollow" />
	<title>Kimai Update</title>
	<style type="text/css" media="screen">
	   body {
	       background: #46E715 url('grfx/ki_twitter_bg.jpg') no-repeat;
	       font-family: sans-serif;
           color:#333;
       }
       div {
           background-image: url('skins/standard/grfx/floaterborder.png');
           position: absolute;
           top: 50%;
           left: 50%;
           width:500px;
           height:250px;
           margin-left:-250px;
           margin-top:-125px;
           border:6px solid white;
           padding:10px;
       }
	
	   #dbrecover {
	   }
	
	</style>
</head>
<body>
	<div  align="center">
	     <FORM action="" method="post">
		     <img src="grfx/caution.png" width="70" height="63" alt="Caution"><br />
		     <h1>UPDATE</h1>
		     <?=$RUsure?>
		     <br /><br />
		     <INPUT type="hidden" name="a" value="1">
		     <INPUT type="submit" value="START UPDATE">
     
	     </FORM>
		 <a href="db_restore.php" id="dbrecover">Database Backup Recover Utility</a>
	</div>
</body>
</html>

<?php

} else if ((int)$revisionDB < 1219 && !isset($_REQUEST['timezone'])) {
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="robots" value="noindex,nofollow" />
  <title>Kimai Update</title>
  <style type="text/css" media="screen">
     body {
         background: #46E715 url('grfx/ki_twitter_bg.jpg') no-repeat;
         font-family: sans-serif;
           color:#333;
       }
       div {
           background-image: url('skins/standard/grfx/floaterborder.png');
           position: absolute;
           top: 50%;
           left: 50%;
           width:500px;
           height:250px;
           margin-left:-250px;
           margin-top:-125px;
           border:6px solid white;
           padding:10px;
       }
  
     #dbrecover {
     }
  
  </style>
</head>
<body>
  <div  align="center">
       <FORM action="" method="post">
         <h1> <?=$kga['lang']['timezone']?></h1>
         <?=$kga['lang']['updater']['timezone'] ?>
         <br/><br/>
         <select name="timezone">
          <?php
          $serverZone = @date_default_timezone_get();

          foreach (timezoneList() as $name) {
            if ($name == $serverZone)
              echo "<option selected=\"selected\">$name</option>";
            else
              echo "<option>$name</option>";
          }
          ?>
          </select>
         <br /><br />
         <INPUT type="hidden" name="a" value="1">
         <INPUT type="submit" value="START UPDATE">
     
       </FORM>
  </div>
</body>
</html>

<?php
} else {     
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Kimai Update <?php echo $kga['version'] . "." . $kga['revision']; ?></title>
	<style type="text/css" media="screen">
	   html {
	       font-family:sans-serif;
	       font-size:80%;
	   }
	   .red { background-color:#f00; color:#fff; font-weight:bold; }
	   .green { background-color:#0f0; }
	   .orange { background-color:#1FA100; }
	   .machtnix { color:#1FA100; }
	   .error_info { color:#888; }
	   .abst { padding:10px;margin-bottom:10px;font-weight:bold;}
	   table { padding:2px;}
       td { 
           border-top: 1px solid #eee;
           border-bottom: 1px dotted black;
           padding:5px 0;
          }
       .success { 
           border: 4px solid #0f0;
           padding:10px;
           width:300px;
           margin-bottom:10px;
          }
       .fail { 
           border: 4px solid #f00;
           padding:10px;
           width:300px;
           margin-bottom:10px;
          }
	   .red, .green, .orange {
	       width:30px; 
	       text-align:center;
	   }
	   #queries {
	       background-color:#0f0;
	       color: white;
	       font-weight:bold;
	       padding:10px;
	       margin-bottom:20px;
	   }
    #important_message {
      background-color:red;
          color: white;
          font-weight:bold;
          padding:10px;
          margin-bottom:20px;
          display:none;
    }
    .important_block_head {
      background-color:red;
          color: white;
          font-weight:bold;
          padding:10px;
    }
	   a {
	       color:#0f0;
	       text-decoration:none;
	       padding:5px;
	       border: 1px dotted gray;
	   }
	   
	   a:hover {
	       color:white;
	       background-color:#0f0;
	       border:1px solid black;
	   }
	
	#logo {
		width:135px;
		height:52px;
		position:absolute;
		top:10px;
		right:10px;
		background-image:url('grfx/logo.png');
	}
	#restore {
		display:block;
		margin-bottom:15px;
		width:100px;
	}
	</style>
    <script src="libraries/jQuery/jquery-1.4.2.min.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
<h1>Kimai Auto Updater v<?php echo $kga['version'] . "." . $kga['revision']; ?></h1>
<div id="logo">&nbsp;</div>
<div id="link">&nbsp;</div>
<a href="db_restore.php" id="restore" title="db_restore">Database Utility</a>
<div id="queries"></div>
<div id="important_message"></div>
<table>
    <tr>
        <td colspan='2'>
            <strong><?php echo $kga['lang']['updater'][10]; ?></strong>
        </td>
    </tr>
    <tr>
        <td>
			<?php echo $kga['lang']['updater'][20]; ?>
        </td>
        <td class='green'>
            &nbsp;&nbsp;
        </td>
    </tr>
    <tr>
        <td>
			<?php echo $kga['lang']['updater'][30]; ?>
        </td>
        <td class='orange'>
            &nbsp;&nbsp;
        </td>
    </tr>
    <tr>
        <td>
			<?php echo $kga['lang']['updater'][40]; ?>
        </td>
        <td class='red'>
            !
        </td>
    </tr>
</table>

<br />
<br />

<table cellspacing='0' cellpadding='2'>
<?php


/**
 * Execute an sql query in the database. The correct database connection
 * will be chosen and the query will be logged with the success status.
 * 
 * As third parameter an alternative query can be passed, which should be
 * displayed instead of the executed query. This prevents leakage of
 * confidential information like password salts. The logfile will still
 * contain the executed query.
 * 
 * @param $query query to execute as string
 * @param $errorProcessing true if it's an error when the query fails.
 */
function exec_query($query,$errorProcessing=false,$displayQuery=null) {
    global $conn, $pdo_conn, $kga, $errors, $executed_queries;
    
    $executed_queries++;
    
    echo "<tr>";

    if ($kga['server_conn'] == "pdo") {
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
    
    if ($kga['server_conn'] == "pdo") {
        if (is_object($pdo_conn)) {
            $err = $pdo_query->errorInfo();
            $err = serialize($err);
        }
    } else {
        if (is_object($conn)) {
            $err = $conn->Error();
        }
    }
    
    $query = htmlspecialchars($query);
    $displayQuery = htmlspecialchars($displayQuery);

    echo "<td>".($displayQuery==null?$query:$displayQuery) ."<br/>";
    echo "<span class='error_info'>" . $err . "</span>";
    echo "</td>";
    
    if ($success) {
        echo "<td class='green'>&nbsp;&nbsp;</td>"; 
    } else {
        if ($errorProcessing) {
            echo "<td class='red'>!</td>";
            $errors++;
        } else {
            echo "<td class='orange'>&nbsp;&nbsp;</td>";
        }
    }

    if (!$success) {

        logfile("An error has occured in query: $query");

        if ($kga['server_conn'] == "pdo") {
            if (is_object($pdo_conn)) {
                $err = $pdo_query->errorInfo();
                $err = serialize($err);
            }
        } else {
            if (is_object($conn)) {
                $err = $conn->Error();
            }
        }

        logfile("Error text: $err");
    }
    
}


$version_e   = explode(".",$kga['version']);
$versionDB_e = explode(".",$versionDB);

$errors = 0;
$executed_queries = 0;

logfile("-- begin update -----------------------------------");

$p = $kga['server_prefix'];

if ((int)$revisionDB < $kga['revision']) {
    /**
     * Perform an backup (or snapshot) of the current tables.
     */
    logfile("-- begin backup -----------------------------------");

    $backup_stamp = time();    // as an individual backup label the timestamp should be enough for now...
                               // by using this type of label we can also exactly identify when it was done
                               // may be shown by a recovering script in human-readable format
                        
    $query = ("SHOW TABLES;");

                           
    $result_backup=@mysql_query($query); 
    logfile($query,$result_backup);
    $prefix_length = strlen($p);
    
    echo "</table>";
    
    echo "<strong>".$kga['lang']['updater'][50]."</strong>";
    echo "<table style='width:100%'>";

    while ($row = mysql_fetch_array($result_backup)) {
    	if ((substr($row[0], 0, $prefix_length) == $p) && (substr($row[0], 0, 10) != "kimai_bak_")) {
	
			$primaryKey = "";
			
			if (strlen(strstr($row[0],"evt"))>0) { $primaryKey = "evt_ID";}
			if (strlen(strstr($row[0],"grp"))>0) { $primaryKey = "grp_ID";}
			if (strlen(strstr($row[0],"knd"))>0) { $primaryKey = "knd_ID";}
			if (strlen(strstr($row[0],"pct"))>0) { $primaryKey = "pct_ID";}
			if (strlen(strstr($row[0],"zef"))>0) { $primaryKey = "zef_ID";}
			if (strlen(strstr($row[0],"usr"))>0) { $primaryKey = "usr_name";}
			if (strlen(strstr($row[0],"var"))>0) { $primaryKey = "var";}
			if ( (strlen(strstr($row[0],"ldr"))>0) 
				|| (strlen(strstr($row[0],"grp_evt"))>0) 
				|| (strlen(strstr($row[0],"pct_evt"))>0) 
				|| (strlen(strstr($row[0],"grp_knd"))>0) 
				|| (strlen(strstr($row[0],"grp_pct"))>0)) 
			{ 
				$primaryKey = "uid";
			}
			
			if ( ((int)$revisionDB < 733) && (strlen(strstr($row[0],"ldr"))>0) ) { $primaryKey = ""; }
			
			if ($primaryKey!="") {
				$primaryKey = " (PRIMARY KEY (`" .$primaryKey. "`))";
			}

	    	$query = "CREATE TABLE kimai_bak_" . $backup_stamp . "_" . $row[0] . $primaryKey . " SELECT * FROM " . $row[0] . ";";				
				
    		exec_query($query,1);
    		if ($errors) die($kga['lang']['updater'][60]);
    	}
    }

    logfile("-- backup finished -----------------------------------");
    
    echo "</table><br /><br />";
    echo "<strong>".$kga['lang']['updater'][70]."</strong></br>";
    echo "<table style='width:100%'>";
}
//////// ---------------------------------------------------------------------------------------------------
//////// ---------------------------------------------------------------------------------------------------


if ( ((int)$versionDB_e[1] == 7 && (int)$versionDB_e[2] < 12) ) {
logfile("-- update to 0.7.12");
    exec_query("ALTER TABLE `${p}evt` ADD `evt_visible` TINYINT NOT NULL DEFAULT '1'",1);                                    
    exec_query("ALTER TABLE `${p}knd` ADD `knd_visible` TINYINT NOT NULL DEFAULT '1'",1);
    exec_query("ALTER TABLE `${p}pct` ADD `pct_visible` TINYINT NOT NULL DEFAULT '1'",1);                               
    exec_query("ALTER TABLE `${p}evt` ADD `evt_filter` TINYINT NOT NULL DEFAULT '0'",1);                               
    exec_query("ALTER TABLE `${p}knd` ADD `knd_filter` TINYINT NOT NULL DEFAULT '0'",1);                               
    exec_query("ALTER TABLE `${p}pct` ADD `pct_filter` TINYINT NOT NULL DEFAULT '0'",1);    
    exec_query("INSERT INTO ${p}var (`var`, `value`) VALUES ('revision','0')",1);
}

if ((int)$revisionDB < 96) {
logfile("-- update to 0.7.13r96");
    exec_query("ALTER TABLE `${p}conf` ADD `allvisible` TINYINT(1) NOT NULL DEFAULT '1'",1);
    // a proper installed database throws errors from here. don't worry - no problem. We ignore those ...
    exec_query("ALTER TABLE `${p}evt` CHANGE `visible` `evt_visible` TINYINT(1) NOT NULL DEFAULT '1'",0);
    exec_query("ALTER TABLE `${p}knd` CHANGE `visible` `knd_visible` TINYINT(1) NOT NULL DEFAULT '1'",0);
    exec_query("ALTER TABLE `${p}pct` CHANGE `visible` `pct_visible` TINYINT(1) NOT NULL DEFAULT '1'",0);
    exec_query("ALTER TABLE `${p}evt` CHANGE `filter` `evt_filter` TINYINT(1) NOT NULL DEFAULT '0'",0);
    exec_query("ALTER TABLE `${p}knd` CHANGE `filter` `knd_filter` TINYINT(1) NOT NULL DEFAULT '0'",0);
    exec_query("ALTER TABLE `${p}pct` CHANGE `filter` `pct_filter` TINYINT(1) NOT NULL DEFAULT '0'",0);
}

if ((int)$revisionDB < 141) {
logfile("-- update to 0.7.13r141");
    $query="ALTER TABLE `${p}conf` ADD `flip_pct_display` tinyint(1) NOT NULL DEFAULT '0'";
    exec_query($query,1);
}

if ((int)$revisionDB < 221) {
logfile("-- update to 0.8");
    // drop views
    exec_query("DROP VIEW IF EXISTS ${p}get_arr_grp, ${p}get_usr_count_in_grp",0);	
    // Set news group name length
    exec_query("ALTER TABLE `${p}grp` CHANGE `grp_name` `grp_name` VARCHAR(160)",1);
    
    // Merge usr and conf tables  
    $query="CREATE TABLE IF NOT EXISTS `${p}usr_tmp` (
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
            `flip_pct_display` tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`usr_name`))"; 
   	exec_query($query,1);
   	
//////// ---------------------------------------------------------------------------------------------------

    	$query="SELECT * FROM `${p}usr` JOIN `${p}conf` ON `${p}usr`.usr_ID = `${p}conf`.conf_usrID";
    	
    	if ($kga['server_conn'] == "pdo") {
    	    
    	    if (is_object($pdo_conn)) {
    	    
                $pdo_query = $pdo_conn->prepare($query);
                $success = $pdo_query->execute(array());
                $executed_queries++;

            	while ($result_array = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
            	    echo "<tr>";
                    $query=<<<EOD
INSERT INTO ${p}usr_tmp (
`usr_ID`,`usr_name`,`usr_grp`,`usr_sts`,`usr_trash`,`usr_active`,`usr_mail`,`pw`,`ban`,`banTime`,
`secure`,`rowlimit`,`skin`,`lastProject`,`lastEvent`,`lastRecord`,`filter`,`filter_knd`,`filter_pct`,`filter_evt`,
`view_knd`,`view_pct`,`view_evt`,`zef_anzahl`,`timespace_in`,`timespace_out`,`autoselection`,`quickdelete`,`allvisible`,`lang`,
`flip_pct_display`) VALUES (
  $result_array[usr_ID],'$result_array[usr_name]',$result_array[usr_grp],$result_array[usr_sts],$result_array[usr_trash],$result_array[usr_active],'$result_array[usr_mail]','$result_array[pw]',$result_array[ban],$result_array[banTime],
  '$result_array[secure]',$result_array[rowlimit],'$result_array[skin]',$result_array[lastProject],$result_array[lastEvent],$result_array[lastRecord],$result_array[filter],$result_array[filter_knd],$result_array[filter_pct],$result_array[filter_evt],
  $result_array[view_knd],$result_array[view_pct],$result_array[view_evt],$result_array[zef_anzahl],'$result_array[timespace_in]','$result_array[timespace_out]',$result_array[autoselection],$result_array[quickdelete],$result_array[allvisible],'$result_array[lang]',
  '$result_array[flip_pct_display]');
EOD;
                    $d_query = $pdo_conn->prepare($query);
                    $success = $d_query->execute(array());
                    $executed_queries++;
                
                    $err = $d_query->errorInfo();
                    $err = serialize($err);
                
                    echo "<td>".$query ."<br/>";
                    echo "<span class='error_info'>" . $err . "</span>";
                    echo "</td>";
                
                    if ($success) {
                        echo "<td class='green'>&nbsp;&nbsp;</td>"; 
                    } else {
                            echo "<td class='red'>!</td>";
                    }
                
                    echo "</tr>";
                }
        	}

        } else {
            
            if (is_object($conn)) {
            
                $success = $conn->Query($query);
                $executed_queries++;

                $arr = array();  
                $rows = $conn->RecordsArray(MYSQL_ASSOC);
            	foreach($rows as $row) {
            	        echo "<tr>";
                    $query=<<<EOD
INSERT INTO ${p}usr_tmp (
`usr_ID`,`usr_name`,`usr_grp`,`usr_sts`,`usr_trash`,`usr_active`,`usr_mail`,`pw`,`ban`,`banTime`,
`secure`,`rowlimit`,`skin`,`lastProject`,`lastEvent`,`lastRecord`,`filter`,`filter_knd`,`filter_pct`,`filter_evt`,
`view_knd`,`view_pct`,`view_evt`,`zef_anzahl`,`timespace_in`,`timespace_out`,`autoselection`,`quickdelete`,`allvisible`,`lang`,
`flip_pct_display`) VALUES (
  $row[usr_ID],'$row[usr_name]',$row[usr_grp],$row[usr_sts],$row[usr_trash],$row[usr_active],'$row[usr_mail]','$row[pw]',$row[ban],$row[banTime],
  '$row[secure]',$row[rowlimit],'$row[skin]',$row[lastProject],$row[lastEvent],$row[lastRecord],$row[filter],$row[filter_knd],$row[filter_pct],$row[filter_evt],
  $row[view_knd],$row[view_pct],$row[view_evt],$row[zef_anzahl],'$row[timespace_in]','$row[timespace_out]',$row[autoselection],$row[quickdelete],$row[allvisible],'$row[lang]',
  '$row[flip_pct_display]');
EOD;
            	    $success = $conn->Query($query);
            	    $executed_queries++;
                    echo "<td>".$query ."<br/>";
                    echo "<span class='error_info'>" . $conn->Error() . "</span>";
                    echo "</td>";
                
                    if ($success) {
                        echo "<td class='green'>&nbsp;&nbsp;</td>"; 
                    } else {
                            echo "<td class='red'>!</td>";
                    }
                
                    echo "</tr>";
                }
            }
        }

//////// ---------------------------------------------------------------------------------------------------
    	
	exec_query("DROP TABLE `${p}usr`",1);
	exec_query("DROP TABLE `${p}conf`",1);
	exec_query("RENAME TABLE `${p}usr_tmp` TO `${p}usr`",1);
    	
    exec_query("ALTER TABLE `${p}knd` CHANGE `knd_telephon` `knd_tel` VARCHAR(255)",0);
    exec_query("ALTER TABLE `${p}knd` CHANGE `knd_mobilphon` `knd_mobile` VARCHAR(255)",0);

    // Add field for icon/logo filename to customer, project and task table
    exec_query("ALTER TABLE `${p}knd` ADD `knd_logo` VARCHAR(80)",1);
    
    exec_query("ALTER TABLE `${p}pct` ADD `pct_logo` VARCHAR(80)",1);
    exec_query("ALTER TABLE `${p}evt` ADD `evt_logo` VARCHAR(80)",1);
    
    // Add trash field for customer, project and task tables
    exec_query("ALTER TABLE `${p}knd` ADD `knd_trash` TINYINT(1) NOT NULL DEFAULT '0'",1);
    
    exec_query("ALTER TABLE `${p}pct` ADD `pct_trash` TINYINT(1) NOT NULL DEFAULT '0'",1);
    exec_query("ALTER TABLE `${p}evt` ADD `evt_trash` TINYINT(1) NOT NULL DEFAULT '0'",1);
    exec_query("ALTER TABLE `${p}zef` ADD `zef_cleared` TINYINT(1) NOT NULL DEFAULT '0'",1);
    
    
//////// ---------------------------------------------------------------------------------------------------    
    
    // put the existing group-customer-relations into the new table
    exec_query("CREATE TABLE `${p}grp_knd` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `grp_ID` INT NOT NULL, `knd_ID` INT NOT NULL)",0);
    
//////// ---------------------------------------------------------------------------------------------------

    	 $query="SELECT `knd_ID`, `knd_grpID` FROM ${p}knd";

    	if ($kga['server_conn'] == "pdo") {
    	    
    	    if (is_object($pdo_conn)) {
    	    
                $pdo_query = $pdo_conn->prepare($query);
                $success = $pdo_query->execute(array());
                $executed_queries++;

            	while ($result_array = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
            	    echo "<tr>";
        	    
            	    $query="INSERT INTO ${p}grp_knd (`grp_ID`, `knd_ID`) VALUES (".$result_array[knd_grpID].", ".$result_array[knd_ID].")";
        	    
                    $d_query = $pdo_conn->prepare($query);
                    $success = $d_query->execute(array());
                    $executed_queries++;

                    $err = $d_query->errorInfo();
                    $err = serialize($err);

                    echo "<td>".$query ."<br/>";
                    echo "<span class='error_info'>" . $err . "</span>";
                    echo "</td>";

                    if ($success) {
                        echo "<td class='green'>&nbsp;&nbsp;</td>"; 
                    } else {
                            echo "<td class='red'>!</td>";
                    }

                    echo "</tr>";
            	}
            }	    
    	    
        } else {
            
            if (is_object($conn)) {
                
                $success = $conn->Query($query);
                $executed_queries++;

                $arr = array();  
                $rows = $conn->RecordsArray(MYSQL_ASSOC);
            	foreach($rows as $row) {
            	        echo "<tr>";
                    $query="INSERT INTO ${p}grp_knd (`grp_ID`, `knd_ID`) VALUES (".$row[knd_grpID].", ".$row[knd_ID].")";
            	    $success = $conn->Query($query);
            	    $executed_queries++;
                    echo "<td>".$query ."<br/>";
                    echo "<span class='error_info'>" . $conn->Error() . "</span>";
                    echo "</td>";

                    if ($success) {
                        echo "<td class='green'>&nbsp;&nbsp;</td>"; 
                    } else {
                            echo "<td class='red'>!</td>";
                    }
                        echo "</tr>";

                        echo $conn->Error();
                }
            }
        }

//////// ---------------------------------------------------------------------------------------------------
    	
    // put the existing group-project-relations into the new table
	exec_query("CREATE TABLE `${p}grp_pct` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `grp_ID` INT NOT NULL, `pct_ID` INT NOT NULL)");   	
    	
//////// ---------------------------------------------------------------------------------------------------    	
    
    	$query="SELECT `pct_ID`, `pct_grpID` FROM ${p}pct";

    	if ($kga['server_conn'] == "pdo") {
    	    
    	    if (is_object($pdo_conn)) {

                $pdo_query = $pdo_conn->prepare($query);
                $success = $pdo_query->execute(array());
                $executed_queries++;

            	while ($result_array = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
            	    echo "<tr>";

            	    $query="INSERT INTO ${p}grp_pct (`grp_ID`, `pct_ID`) VALUES (".$result_array[pct_grpID].", ".$result_array[pct_ID].")";

                    $d_query = $pdo_conn->prepare($query);
                    $success = $d_query->execute(array());
                    $executed_queries++;

                    $err = $d_query->errorInfo();
                    $err = serialize($err);

                    echo "<td>".$query ."<br/>";
                    echo "<span class='error_info'>" . $err . "</span>";
                    echo "</td>";

                    if ($success) {
                        echo "<td class='green'>&nbsp;&nbsp;</td>"; 
                    } else {
                        echo "<td class='red'>!</td>";
                    }
                    echo "</tr>";
            	}
            }
        	
        } else {
            
            if (is_object($conn)) {
                
                $success = $conn->Query($query);
                $executed_queries++;

                $arr = array();  
                $rows = $conn->RecordsArray(MYSQL_ASSOC);
            	foreach($rows as $row) {
            	        echo "<tr>";
                    $query="INSERT INTO ${p}grp_pct (`grp_ID`, `pct_ID`) VALUES (".$row[pct_grpID].", ".$row[pct_ID].")";
            	    $success = $conn->Query($query);
            	    $executed_queries++;
                    echo "<td>".$query ."<br/>";
                    echo "<span class='error_info'>" . $conn->Error() . "</span>";
                    echo "</td>";
                
                    if ($success) {
                        echo "<td class='green'>&nbsp;&nbsp;</td>"; 
                    } else {
                            echo "<td class='red'>!</td>";
                    }
                    echo "</tr>";
                }
            }
        }
    
//////// ---------------------------------------------------------------------------------------------------    
    	
    // put the existing group-event-relations into the new table
	exec_query("CREATE TABLE `${p}grp_evt` (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `grp_ID` INT NOT NULL, `evt_ID` INT NOT NULL)");   	

//////// ---------------------------------------------------------------------------------------------------


    	$query="SELECT `evt_ID`, `evt_grpID` FROM ${p}evt";

    	if ($kga['server_conn'] == "pdo") {
    	    
    	    if (is_object($pdo_conn)) {
    	    
                $pdo_query = $pdo_conn->prepare($query);
                $success = $pdo_query->execute(array());
                $executed_queries++;

            	while ($result_array = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
            	    echo "<tr>";

            	    $query="INSERT INTO ${p}grp_evt (`grp_ID`, `evt_ID`) VALUES (".$result_array[evt_grpID].", ".$result_array[evt_ID].")";

                    $d_query = $pdo_conn->prepare($query);
                    $success = $d_query->execute(array());
                    $executed_queries++;

                    $err = $d_query->errorInfo();
                    $err = serialize($err);

                    echo "<td>".$query ."<br/>";
                    echo "<span class='error_info'>" . $err . "</span>";
                    echo "</td>";

                    if ($success) {
                        echo "<td class='green'>&nbsp;&nbsp;</td>"; 
                    } else {
                            echo "<td class='red'>!</td>";
                    }

                    echo "</tr>";
            	}
            }

        } else {
            
            if (is_object($conn)) {
            
                $success = $conn->Query($query);
                $executed_queries++;

                $arr = array();  
                $rows = $conn->RecordsArray(MYSQL_ASSOC);
            	foreach($rows as $row) {
            	        echo "<tr>";
                    $query="INSERT INTO ${p}grp_evt (`grp_ID`, `evt_ID`) VALUES (".$row[evt_grpID].", ".$row[evt_ID].")";
            	    $success = $conn->Query($query);
            	    $executed_queries++;
                    echo "<td>".$query;
                    echo "</td>";

                    if ($success) {
                        echo "<td class='green'>&nbsp;&nbsp;</td>"; 
                    } else {
                        echo "<td class='red'>!</td>";
                    }
                    echo "</tr>";
                }
            }
        }

//////// ---------------------------------------------------------------------------------------------------
    
    // delete old single-group fields in knd, pct and evt
    exec_query("ALTER TABLE ${p}knd DROP `knd_grpID`");
    exec_query("ALTER TABLE ${p}pct DROP `pct_grpID`");
    exec_query("ALTER TABLE ${p}evt DROP `evt_grpID`");

}

//////// ---------------------------------------------------------------------------------------------------


if ((int)$revisionDB < 733) {

    logfile("-- update to 0.8.0a");

    exec_query("ALTER TABLE `${p}evt` CHANGE `evt_visible` `evt_visible` TINYINT(1) NOT NULL DEFAULT '1';",0);
    exec_query("ALTER TABLE `${p}knd` CHANGE `knd_visible` `knd_visible` TINYINT(1) NOT NULL DEFAULT '1';",0);
    exec_query("ALTER TABLE `${p}pct` CHANGE `pct_visible` `pct_visible` TINYINT(1) NOT NULL DEFAULT '1';",0);
    exec_query("ALTER TABLE `${p}evt` CHANGE `evt_filter` `evt_filter` TINYINT(1) NOT NULL DEFAULT '0';",0);
    exec_query("ALTER TABLE `${p}knd` CHANGE `knd_filter` `knd_filter` TINYINT(1) NOT NULL DEFAULT '0';",0);
    exec_query("ALTER TABLE `${p}pct` CHANGE `pct_filter` `pct_filter` TINYINT(1) NOT NULL DEFAULT '0';",0);

    exec_query("ALTER TABLE `${p}evt` CHANGE `evt_ID` `evt_ID` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY;",0);
    
    exec_query("ALTER TABLE `${p}grp` CHANGE `grp_ID` `grp_ID` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY;",0);
    exec_query("ALTER TABLE `${p}grp` DROP `grp_leader`;",0);
    
    exec_query("ALTER TABLE `${p}knd` CHANGE `knd_ID` `knd_ID` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY;",0);
    
    exec_query("ALTER TABLE `${p}ldr` ADD `uid` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;",0);
    
    exec_query("ALTER TABLE `${p}pct` CHANGE `pct_ID` `pct_ID` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY;",0);
    
    exec_query("ALTER TABLE `${p}usr` DROP `recordingstate`;",0);
    exec_query("ALTER TABLE `${p}var` ADD PRIMARY KEY (`var`);",0);
    
    exec_query("ALTER TABLE `${p}zef` CHANGE `zef_ID` `zef_ID` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY;",0);
     
}


if ((int)$revisionDB < 809) {
    logfile("-- update to r810");
    exec_query("ALTER TABLE `${p}usr` ADD `pct_comment_flag` TINYINT(1) NOT NULL DEFAULT '0'",1);
}

if ((int)$revisionDB < 817) {
    logfile("-- update to r817");
    exec_query("ALTER TABLE `${p}usr` ADD `showIDs` TINYINT(1) NOT NULL DEFAULT '0'",1);
}

if ((int)$revisionDB < 837) {
    logfile("-- update to r837");
    exec_query("ALTER TABLE `${p}usr` ADD `usr_alias` VARCHAR(10)",0);
    exec_query("ALTER TABLE `${p}zef` ADD `zef_location` varchar(50)",1);
}

if ((int)$revisionDB < 848) {
    logfile("-- update to r848");
    exec_query("ALTER TABLE `${p}zef` ADD `zef_trackingnr` int(20)",1);
}

if ((int)$revisionDB < 898) {
    logfile("-- update to r898");
    exec_query("CREATE TABLE `${p}rates` (
  `user_id` int(10) DEFAULT NULL,
  `project_id` int(10) DEFAULT NULL,
  `event_id` int(10) DEFAULT NULL,
  `rate` decimal(10,2) NOT NULL
);",1);
    exec_query("ALTER TABLE `${p}zef` ADD `zef_rate` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0';",1);
}

if ((int)$revisionDB < 922) {
    logfile("-- update to r922");
    exec_query("ALTER TABLE `${p}knd` ADD `knd_password` VARCHAR(255);",1);
    exec_query("ALTER TABLE `${p}knd` ADD `knd_secure` varchar(60) NOT NULL default '0';",1);
}

if ((int)$revisionDB < 935) {
    logfile("-- update to r935");
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
    logfile("-- update to r1067");

  /*
   *  Write new config file with password salt
   */
    $kga['password_salt'] = createPassword(20);
    if (write_config_file(
        $kga['server_database'],
        $kga['server_hostname'],
        $kga['server_username'],
        $kga['server_password'],
        $kga['server_conn'],
        $kga['server_type'],
        $kga['server_prefix'],
        $kga['language'],
        $kga['password_salt']))
      echo '<tr><td>'.$kga['lang']['updater'][140].'</td><td class="green">&nbsp;&nbsp;</td></tr>';   
    else
      die($kga['lang']['updater'][130]);
      


  /*
   *  Reset all passwords
   */
  $new_passwords = array();

  $result = mysql_query("SELECT * FROM ${p}usr");
  
  $users = array();
  while ($row = mysql_fetch_assoc($result))
    $users[] = $row;

  foreach ($users as $user) {
    if ($user['usr_name'] == 'admin')
      $new_password = 'changeme';
    else
      $new_password = createPassword(8);
    exec_query("UPDATE ${p}usr SET pw = '".
        md5($kga['password_salt'].$new_password.$kga['password_salt']).
        "' WHERE usr_ID = $user[usr_ID]");
    if ($result)    
      $new_passwords[$user['usr_name']] = $new_password;
  }

}

if ((int)$revisionDB < 1068) {
    logfile("-- update to r1068");
    exec_query("ALTER TABLE `${p}usr` CHANGE `autoselection` `autoselection` TINYINT( 1 ) NOT NULL default '0';");
}

if ((int)$revisionDB < 1077) {
    logfile("-- update to r1076");
    exec_query("ALTER TABLE `${p}usr` CHANGE `usr_mail` `usr_mail` varchar(160) DEFAULT ''");
    exec_query("ALTER TABLE `${p}usr` CHANGE `pw` `pw` varchar(254) NULL DEFAULT NULL");
    exec_query("ALTER TABLE `${p}usr` CHANGE `lang` `lang` varchar(6) DEFAULT ''");
    exec_query("ALTER TABLE `${p}zef` CHANGE `zef_comment` `zef_comment` TEXT NULL DEFAULT NULL");
}

if ((int)$revisionDB < 1086) {
    logfile("-- update to r1086");
    exec_query("ALTER TABLE `${p}pct` ADD `pct_budget` DECIMAL(10,2) NOT NULL DEFAULT 0.00");
}

if ((int)$revisionDB < 1088) {
    logfile("-- update to r1088");
    exec_query("ALTER TABLE `${p}usr` ADD `noFading` TINYINT(1) NOT NULL DEFAULT '0'");
}

if ((int)$revisionDB < 1089) {
    logfile("-- update to r1089");
    exec_query("ALTER TABLE `${p}usr` ADD `export_disabled_columns` INT NOT NULL DEFAULT '0'");
}

if ((int)$revisionDB < 1103) {
    logfile("-- update to r1103");
    exec_query("ALTER TABLE ${p}usr DROP `allvisible`");
}

if ((int)$revisionDB < 1112) {
    logfile("-- update to r1112");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('currency_name','Euro')");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('currency_sign','€')");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('show_sensible_data','1')");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('show_update_warn','1')");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('check_at_startup','0')");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('show_daySeperatorLines','1')");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('show_gabBreaks','0')");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('show_RecordAgain','1')");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('show_TrackingNr','1')");
}

if ((int)$revisionDB < 1113) {
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('date_format_0','%d.%m.%Y')");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('date_format_1','%d.%m.')");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('date_format_2','%d.%m.%Y')");
    exec_query("DELETE FROM ${p}var WHERE `var` = 'charset' LIMIT 1");
}

if ((int)$revisionDB < 1115) {
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('language','$kga[language]')");    
}

if ((int)$revisionDB < 1126) {
    logfile("-- update to r1126");
    exec_query("ALTER TABLE `${p}grp_evt` ADD UNIQUE (`grp_ID` ,`evt_ID`);");
    exec_query("ALTER TABLE `${p}grp_knd` ADD UNIQUE (`grp_ID` ,`knd_ID`);");
    exec_query("ALTER TABLE `${p}grp_pct` ADD UNIQUE (`grp_ID` ,`pct_ID`);");
    exec_query("ALTER TABLE `${p}ldr` ADD UNIQUE (`grp_ID` ,`grp_leader`);");
}

if ((int)$revisionDB < 1132) {
    logfile("-- update to r1132");
    if ($kga['server_conn'] == "mysql") {
       exec_query("UPDATE ${p}usr, ${p}ldr SET usr_sts = 2 WHERE usr_sts = 1");
       exec_query("UPDATE ${p}usr, ${p}ldr SET usr_sts = 1 WHERE usr_sts = 2 AND grp_leader = usr_ID");
    }
}

if ((int)$revisionDB < 1139) {
    logfile("-- update to r1139");
    exec_query("ALTER TABLE `${p}usr` ADD `user_list_hidden` INT NOT NULL DEFAULT '0'");
}

if ((int)$revisionDB < 1142) {
    logfile("-- update to r1142");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('roundPrecision','0')");
}

if ((int)$revisionDB < 1145) {
    logfile("-- update to r1145");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('currency_first','0')");
}

if ((int)$revisionDB < 1176) {
    logfile("-- update to r1176");
    exec_query("ALTER TABLE `${p}exp` ADD INDEX ( `exp_usrID` ) ");
    exec_query("ALTER TABLE `${p}exp` ADD INDEX ( `exp_pctID` ) ");
    exec_query("ALTER TABLE `${p}pct` ADD INDEX ( `pct_kndID` ) ");
    exec_query("ALTER TABLE `${p}zef` ADD INDEX ( `zef_usrID` ) ");
    exec_query("ALTER TABLE `${p}zef` ADD INDEX ( `zef_pctID` ) ");
    exec_query("ALTER TABLE `${p}zef` ADD INDEX ( `zef_evtID` ) ");
}

if ((int)$revisionDB < 1183) {
    logfile("-- update to r1183");
    exec_query("ALTER TABLE `${p}zef` CHANGE `zef_trackingnr` `zef_trackingnr` varchar(30) DEFAULT ''");
}

if ((int)$revisionDB < 1184) {
    logfile("-- update to r1184");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('decimalSeparator',',')");
}

if ((int)$revisionDB < 1185) {
    logfile("-- update to r1185");
    exec_query("CREATE TABLE ${p}pct_evt (`uid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `pct_ID` INT NOT NULL, `evt_ID` INT NOT NULL, UNIQUE (`pct_ID` ,`evt_ID`)) ;");
}

if ((int)$revisionDB < 1206) {
    logfile("-- update to r1206");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('durationWithSeconds','0')");
}

if ((int)$revisionDB < 1207) {
    logfile("-- update to r1207");
    exec_query("ALTER TABLE `${p}exp` ADD `exp_multiplier` INT NOT NULL DEFAULT '1'");

}

if ((int)$revisionDB < 1213) {
    logfile("-- update to r1213");
    exec_query("ALTER TABLE ${p}knd DROP `knd_logo`");
    exec_query("ALTER TABLE ${p}pct DROP `pct_logo`");
    exec_query("ALTER TABLE ${p}evt DROP `evt_logo`");
}

if ((int)$revisionDB < 1216) {
    logfile("-- update to r1216");
    exec_query("ALTER TABLE `${p}exp`
  ADD `exp_refundable` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'expense refundable to employee (0 = no, 1 = yes)' AFTER `exp_comment_type`;");
}

if ((int)$revisionDB < 1219) {
    $timezone = mysql_real_escape_string($_REQUEST['timezone']);
    logfile("-- update to r1219");
    exec_query("ALTER TABLE `${p}usr`
  ADD `timezone` VARCHAR( 40 ) NOT NULL DEFAULT ''");
    exec_query("UPDATE `${p}usr`
  SET `timezone` = '$timezone'");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('defaultTimezone','$timezone')");
}

if ((int)$revisionDB < 1225) {
    logfile("-- update to r1225");
    exec_query("CREATE TABLE `${p}preferences` (
  `userID` int(10) NOT NULL,
  `var` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`userID`,`var`)
  );");

  $columns = array('rowlimit','skin','autoselection','quickdelete',
    'lang','flip_pct_display','pct_comment_flag','showIDs','noFading',
    'export_disabled_columns','user_list_hidden','timezone');

  // move user configuration over to preferences table, which are still in use
  foreach ($columns as $column) {
    exec_query("INSERT INTO ${p}preferences (`userID`,`var`,`value`) SELECT `usr_ID` , \"$column\", `$column` FROM `${p}usr`");
  }


  // add unused columns and drop all in usr table
  $columns = array_merge($columns,array('zef_anzahl','filter','filter_knd','filter_pct','filter_evt','view_knd','view_pct','view_evt'));
  foreach ($columns as $column) {
    exec_query("ALTER TABLE ${p}usr DROP $column");
  }


}

if ((int)$revisionDB < 1227) {
    logfile("-- update to r1227");
    exec_query("ALTER TABLE `${p}knd`
  ADD `knd_vat` VARCHAR( 255 ) NOT NULL");
    exec_query("ALTER TABLE `${p}knd`
  ADD `knd_contact` VARCHAR( 255 ) NOT NULL");
}

if ((int)$revisionDB < 1229) {
    logfile("-- update to r1229");
    exec_query("ALTER TABLE `${p}usr` CHANGE `banTime` `banTime` int(10) NOT NULL DEFAULT 0");
}

if ((int)$revisionDB < 1236) {
    logfile("-- update to r1236");
    exec_query("ALTER TABLE `${p}pct`
  ADD `pct_internal` TINYINT( 1 ) NOT NULL DEFAULT 0");
}

if ((int)$revisionDB < 1240) {
    logfile("-- update to r1240");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('exactSums','0')");
}

if ((int)$revisionDB < 1256) {
    logfile("-- update to r1256");
    exec_query("INSERT INTO ${p}var (`var`,`value`) VALUES('defaultVat','0')");
}

if ((int)$revisionDB < 1257) {
    logfile("-- update to r1257");
    exec_query("UPDATE ${p}preferences SET var = CONCAT('ui.',var) WHERE var 
    IN ('skin', 'rowlimit', 'lang', 'autoselection', 'quickdelete', 'flip_pct_display',
    'pct_comment_flag', 'showIDs', 'noFading', 'user_list_hidden', 'hideClearedEntries')");
}

if ((int)$revisionDB < 1284) {
    logfile("-- update to r1284");
    exec_query("ALTER TABLE `${p}exp` CHANGE `exp_multiplier`
    `exp_multiplier` decimal(10,2) NOT NULL DEFAULT '1.00'");
}

if ((int)$revisionDB < 1291) {
    logfile("-- update to r1291");
    $salt = $kga['password_salt'];
    $query = "UPDATE `${p}usr` SET pw=MD5(CONCAT('${salt}',pw,'${salt}')) WHERE pw REGEXP '^[0-9a-f]{32}$' = 0 AND pw != ''";
    exec_query($query,false,str_replace($salt,'salt was stripped',$query));
}

if ((int)$revisionDB < 1305) {
    logfile("-- update to r1305");

    // update knd_name
    $result = mysql_query("SELECT knd_ID,knd_name FROM ${p}knd");
    
    while ($customer = mysql_fetch_assoc($result)) {
      $name = htmlspecialchars_decode($customer['knd_name']);

      if ($name == $customer['knd_name'])
        continue;

      exec_query("UPDATE ${p}knd SET knd_name = '".
          mysql_real_escape_string($name).
          "' WHERE knd_ID = $customer[knd_ID]");
    }

    // update pct_name
    $result = mysql_query("SELECT pct_ID,pct_name FROM ${p}pct");
    
    while ($project = mysql_fetch_assoc($result)) {
      $name = htmlspecialchars_decode($project['pct_name']);

      if ($name == $project['pct_name'])
        continue;

      exec_query("UPDATE ${p}pct SET pct_name = '".
          mysql_real_escape_string($name).
          "' WHERE pct_ID = $project[pct_ID]");
    }

    // update evt_name
    $result = mysql_query("SELECT evt_ID,evt_name FROM ${p}evt");
    
    while ($event = mysql_fetch_assoc($result)) {
      $name = htmlspecialchars_decode($event['evt_name']);

      if ($name == $event['evt_name'])
        continue;

      exec_query("UPDATE ${p}evt SET evt_name = '".
          mysql_real_escape_string($name).
          "' WHERE evt_ID = $event[evt_ID]");
    }

    // update usr_name
    $result = mysql_query("SELECT usr_ID,usr_name FROM ${p}usr");
    
    while ($user = mysql_fetch_assoc($result)) {
      $name = htmlspecialchars_decode($user['usr_name']);

      if ($name == $user['usr_name'])
        continue;

      exec_query("UPDATE ${p}usr SET usr_name = '".
          mysql_real_escape_string($name).
          "' WHERE usr_ID = $user[usr_ID]");
    }

    // update grp_name
    $result = mysql_query("SELECT grp_ID,grp_name FROM ${p}grp");
    
    while ($group = mysql_fetch_assoc($result)) {
      $name = htmlspecialchars_decode($group['grp_name']);

      if ($name == $group['grp_name'])
        continue;

      exec_query("UPDATE ${p}grp SET grp_name = '".
          mysql_real_escape_string($name).
          "' WHERE grp_ID = $group[grp_ID]");
    }
    
}



// ============================
// = update DB version number =
// ============================

if ((int)$revisionDB < $kga['revision']) {
    
    $versionDB_e[0] = 0;
    $versionDB_e[1] = 8;
    $versionDB_e[2] = 2;
    
    $query=sprintf("UPDATE `${p}var` SET value = '%s' WHERE var = 'version';", $kga['version']);
    exec_query($query,0);

    $query=sprintf("UPDATE `${p}var` SET value = '%d' WHERE var = 'revision';", $kga['revision']);
    exec_query($query,0);

}

logfile("-- update finished --------------------------------");

if ((int)$revisionDB == $kga['revision']) {
    echo "<script type=\"text/javascript\">window.location.href = \"index.php\";</script>";
} else {

    $l2 = $kga['lang']['login'];
	$l3 = $kga['lang']['updater'][90];
	
    if (!$errors) {

		$l1 = $kga['lang']['updater'][80];
		
echo<<<EOD
<script type="text/javascript">
    $("#link").append("<p><strong>$l1</strong></p>");
    $("#link").append("<h1><a href='index.php'>$l2</a></h1>");
    $("#link").addClass("success");
    $("#queries").append("$executed_queries $l3</p>");
</script>
EOD;

    } else {
	
		$l1 = $kga['lang']['updater'][100];
	
echo<<<EOD
<script type="text/javascript">
    $("#link").append("<p><strong>$l1</strong></p>");
    $("#link").append("<h1><a href='index.php'>$l2</a></h1>");
    $("#link").addClass("fail");
    $("#queries").append("$executed_queries $l3");
</script>
EOD;
    }


}


?>

</table>

<?php
if (isset($new_passwords)) {
?>
<br/><br/>
<script type="text/javascript">
    $("#important_message").append("<?php echo $kga['lang']['updater'][120];?> <br/>");
    $("#important_message").show();
</script>
<div class="important_block_head"> <?php echo $kga['lang']['updater'][110];?>: </div>
<table style="width:100%">
<tr><td><i> <?php echo $kga['lang']['username'];?> </i></td><td><i> <?php echo $kga['lang']['password'];?> </i></td></tr>
<?php
foreach ($new_passwords as $username => $password)
  echo "<tr><td>$username</td><td>$password</td></tr>";
?>
</table><br/>
<?php
}
?>


<?php echo "$executed_queries " . $kga['lang']['updater'][90]; ?>

<h1><a href='index.php'><?php echo $kga['lang']['login']; ?></a></h1>

</body></html>

<?php } // end of "do you have a backup blah" condition 
?>