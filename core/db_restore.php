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
 * This file allows the user to create and restore backups. The backups are
 * kept within the database, so they aren't true backups but more like
 * snapshots.
 */ 

require('includes/basics.php');

$version_temp  = get_DBversion();
$versionDB  = $version_temp[0];
$revisionDB = $version_temp[1];

$p = $kga['server_prefix'];

/**
 * Execute an sql query in the database. The correct database connection
 * will be chosen and the query will be logged with the success status.
 * 
 * @param $query query to execute as string
 */
function exec_query($query) {
    global $conn, $pdo_conn, $kga, $errors, $executed_queries;
    
    $success = false;
   
    if ($kga['server_conn'] == "pdo") {
            if (is_object($pdo_conn)) {
                $pdo_query = $pdo_conn->prepare($query);
                $success = $pdo_query->execute(array());
        }
        else
          $errorInfo = "No connection object.";
    } else {
        if (is_object($conn)) {
            $success = $conn->Query($query);
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

if (isset($_REQUEST['submit'])) 
{
	if ($_REQUEST['submit'] == $kga['lang']['backup'][8]) 
	{
      /**
       * Create a backup.
       */
      
      logfile("-- begin backup -----------------------------------");
	    $backup_stamp = time();  
	    $query = ("SHOW TABLES;");
      
      if ($kga['server_conn'] == "pdo") {
              if (is_object($pdo_conn)) {
                  $pdo_query = $pdo_conn->prepare($query);
                  $success = $pdo_query->execute(array());
            $tables = $pdo_query->fetchAll();
              }
      } else {
          if (is_object($conn)) {
              $success = $conn->Query($query);
        $tables = $conn->RecordsArray();
          }
      }
	    $prefix_length = strlen($p);
	
	    foreach($tables as $row) {
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
        if (strlen(strstr($row[0],"preferences"))>0) { $primaryKey = "userID`,`var";      }
				
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
		header("location: db_restore.php");
	}

	if ($_REQUEST['submit'] == $kga['lang']['backup'][3]) 
	{
      /**
       * Delete backups.
       */
		$dates = $_REQUEST['dates'];

		$query = ("SHOW TABLES;");
      
      if ($kga['server_conn'] == "pdo") {
              if (is_object($pdo_conn)) {
                  $pdo_query = $pdo_conn->prepare($query);
                  $success = $pdo_query->execute(array());
            $tables = $pdo_query->fetchAll();
              }
      } else {
          if (is_object($conn)) {
              $success = $conn->Query($query);
        $tables = $conn->RecordsArray();
          }
      }

		foreach ($tables as $row)
		{
			if ((substr($row[0], 0, 10) == "kimai_bak_"))
			{
				if ( in_array(substr($row[0], 10, 10),$dates) )
				{
					$arr2[] = "DROP TABLE `".$row[0]."`;";	
				}
			}
		}
		if ($kga['server_conn'] == "pdo") 
		{
		        if (is_object($pdo_conn)) 
			{
			

			 $query="";
			foreach($arr2 AS $row)
			{
				$query .= $row;
			}
			    $pdo_query = $pdo_conn->prepare($query);
		            $success = $pdo_query->execute(array());
		        }
		} 
		else 
		{
		    if (is_object($conn)) 
			{
			foreach($arr2 AS $row)
			{
				$success = $conn->Query($row);
				if (!$success)
					break;
			
			}
		    }
		}
		header("location: db_restore.php");
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
  <meta name="robots" value="noindex,nofollow" />
	<title>Kimai Backup Restore Utility</title>
	<style type="text/css" media="screen">
		body {
		    background: #46E715 url('grfx/ki_twitter_bg.jpg') no-repeat;
		    font-family: sans-serif;
		    color:#333;
		}
		div.main {
		    margin-left:420px;
		}
		div.warn {
			padding:5px;
			background-image: url('skins/standard/grfx/floaterborder.png');
			color:red;
			font-weight:bold;
			text-align:center;
			border-top:2px solid red;
			border-bottom:2px solid red;
		}
		p.label_checkbox input {
			float: left;
		}
		p.label_checkbox label {
			display: block;
			float: left;
			margin-left: 10px;
			width: 300px;
		}
		p.label_checkbox {
			clear:left;
			height:.6em;
		}
		p.radio {
			display: block;
			float: left;
		}
		h1.message {
			border:3px solid white;
			padding:10px;
			background-image: url('skins/standard/grfx/floaterborder.png');
			margin-right:20px;
		}
		h1.fail {
			border:3px solid red;
			padding:10px;
			background-image: url('skins/standard/grfx/floaterborder.png');
			color:red;
			margin-right:20px;
		}
		p.submit {
			margin-top:25px;
		}
		p.caution {
			font-size:80%;
			color:#136C00;
			width:300px;
		}
	</style>
</head>
<body>


<div class="warn"><?=$kga['lang']['backup'][0]?></div>
<div class="main">
<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// restore

if (isset($_REQUEST['submit'])) 
{
	if (($_REQUEST['submit'] == $kga['lang']['backup'][2]) && (isset($_REQUEST['dates']))) 
	{
		$dates = $_REQUEST['dates'];

		if (count($dates)>1) 
		{
			echo "<h1 class='fail'>".$kga['lang']['backup'][5]."</h1>";
		}
		else
		{
			$query = ("SHOW TABLES;");
			
			if ($kga['server_conn'] == "pdo") {
			        if (is_object($pdo_conn)) {
			            $pdo_query = $pdo_conn->prepare($query);
			            $success = $pdo_query->execute(array());
				    $tables = $pdo_query->fetchAll();
			        }
			} else {
			    if (is_object($conn)) {
			        $success = $conn->Query($query);
				$tables = $conn->RecordsArray();
			    }
			}

			$arr = array();
			$arr2 = array();

			foreach ($tables as $row)
			{
				if ( (substr($row[0], 0, 10) == "kimai_bak_"))
				{
					if ( in_array(substr($row[0], 10, 10),$dates) )
					{
						$table = $row[0];
						$arr[]=$table;
						$arr2[]=substr($row[0], 21, 100);
					}
				}
			}
			
##################
			// Bis rev 733 gab es in tabelle ldr keinen Primary Key ...
			
			$query = "SELECT value FROM kimai_bak_" . $dates[0] . "_kimai_var WHERE var = 'revision' LIMIT 0,1;";
			if ($kga['server_conn'] == "pdo") {
			        if (is_object($pdo_conn)) {
			            $pdo_query = $pdo_conn->prepare($query);
			            $success = $pdo_query->execute(array());
				    $revision = $pdo_query->fetch(PDO::FETCH_ASSOC);
			        }
			} else {
			    if (is_object($conn)) {
			        $success = $conn->Query($query);
				$revision = $conn->RowArray(0,MYSQL_ASSOC);
			    }
			}
			$revision = $revision['value'];
##################

			$i=0;
			foreach($arr2 AS $newTable)
			{
				
				$primaryKey = "";

				if (strlen(strstr($newTable,"evt"))>0) { $primaryKey = "evt_ID";   }
				if (strlen(strstr($newTable,"grp"))>0) { $primaryKey = "grp_ID";   }
				if (strlen(strstr($newTable,"knd"))>0) { $primaryKey = "knd_ID";   }
				if (strlen(strstr($newTable,"pct"))>0) { $primaryKey = "pct_ID";   }
				if (strlen(strstr($newTable,"zef"))>0) { $primaryKey = "zef_ID";   }
				if (strlen(strstr($newTable,"usr"))>0) { $primaryKey = "usr_name"; }
				if (strlen(strstr($newTable,"var"))>0) { $primaryKey = "var";      }
				if ( (strlen(strstr($newTable,"ldr"))>0) 
					|| (strlen(strstr($newTable,"grp_evt"))>0) 
          || (strlen(strstr($newTable,"pct_evt"))>0) 
					|| (strlen(strstr($newTable,"grp_knd"))>0) 
					|| (strlen(strstr($newTable,"grp_pct"))>0)) 
				{ 
					$primaryKey = "uid";
				}
        if (strlen(strstr($newTable,"preferences"))>0) { $primaryKey = "userID`,`var";      }
								
				if ($primaryKey!="") {
					$primaryKey = " (PRIMARY KEY (`" .$primaryKey. "`))";
				}
				
				if (    ((int)$revision < 733)    &&    (strlen(strstr($newTable,"ldr"))>0)    ) { 
					$primaryKey = "";
				}
				
				exec_query("DROP TABLE `".$arr2[$i]."`;\n");
				
					exec_query("CREATE TABLE " . $newTable . $primaryKey . " SELECT * FROM " .  $arr[$i] . ";\n");
				$i++;
			}
			
			exec_query("ALTER TABLE `kimai_evt`     CHANGE `evt_ID` `evt_ID` INT( 10 ) NOT NULL AUTO_INCREMENT");
			exec_query("ALTER TABLE `kimai_knd`     CHANGE `knd_ID` `knd_ID` INT( 10 ) NOT NULL AUTO_INCREMENT");
			exec_query("ALTER TABLE `kimai_pct`     CHANGE `pct_ID` `pct_ID` INT( 10 ) NOT NULL AUTO_INCREMENT");
			exec_query("ALTER TABLE `kimai_zef`     CHANGE `zef_ID` `zef_ID` INT( 10 ) NOT NULL AUTO_INCREMENT");
			exec_query("ALTER TABLE `kimai_exp`     CHANGE `exp_ID` `exp_ID` INT( 10 ) NOT NULL AUTO_INCREMENT");
			exec_query("ALTER TABLE `kimai_grp`     CHANGE `grp_ID` `grp_ID` INT( 10 ) NOT NULL AUTO_INCREMENT");
			exec_query("ALTER TABLE `kimai_ldr`     CHANGE `uid`    `uid`    INT( 11 ) NOT NULL AUTO_INCREMENT");
			exec_query("ALTER TABLE `kimai_grp_pct` CHANGE `uid`    `uid`    INT( 11 ) NOT NULL AUTO_INCREMENT");
			exec_query("ALTER TABLE `kimai_grp_knd` CHANGE `uid`    `uid`    INT( 11 ) NOT NULL AUTO_INCREMENT");
			exec_query("ALTER TABLE `kimai_grp_evt` CHANGE `uid`    `uid`    INT( 11 ) NOT NULL AUTO_INCREMENT");
			
			// echo $restorequery;

			
		
			$date = @date ("d. M Y, H:i:s", $dates[0]);
			echo "<h1 class='message'>" .$kga['lang']['backup'][6]. " ".$date."<br>" . $kga['lang']['backup'][7] ."</h1>";
		}
	}
}

echo "<h1>" . $kga['lang']['backup'][1] . "</h1>";

$query = ("SHOW TABLES;");
                       
$result_backup=@mysql_query($query); 

$arr = array();
$arr2 = array();

while ($row = mysql_fetch_array($result_backup))
{
	if ( (substr($row[0], 0, 10) == "kimai_bak_"))
	{
		$time = substr($row[0], 10, 10);
		$arr[]=$time;
	}
}

$neues_array = array_unique ($arr);

echo '<form method="post" accept-charset="utf-8">';
	
foreach($neues_array AS $date)
{
$value = @date ("d. M Y - H:i:s", $date);

if ( @date("dMY", $date) == @date("dMY", time()) )
{
	$label = $kga['lang']['heute'] . @date (" - H:i:s", $date);
}
else
{
	$label = $value; 
}
echo<<<EOD
<p class="label_checkbox">
<input type="checkbox" id="$value " name="dates[]" value="$date">
<label for="$value">$label</label>
</p>
EOD;
}

?>

<p class="submit">
<input type="submit" name="submit" value="<?php echo $kga['lang']['backup'][2]; ?>"> <!-- restore -->
<input type="submit" name="submit" value="<?php echo $kga['lang']['backup'][3]; ?>"> <!-- delete -->
<input type="submit" name="submit" value="<?php echo $kga['lang']['backup'][8]; ?>"> <!-- backup -->
</p>

</form>
<a href="index.php">Login</a>
<p class="caution"><?php echo $kga['lang']['backup'][9]; ?></p>
</div>

</body>
</html>