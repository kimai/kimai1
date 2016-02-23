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

if (isset($_REQUEST['submit']) &&
    isset($_REQUEST['salt']) &&
    $_REQUEST['submit'] == $kga['lang']['login'] &&
    $_REQUEST['salt'] == $kga['password_salt'])
    {
      $cookieValue = sha1($kga['password_salt']);
      setcookie('db_restore_authCode', $cookieValue);
      $_COOKIE['db_restore_authCode'] = $cookieValue;
}
$authenticated = (isset($_COOKIE['db_restore_authCode']) && $_COOKIE['db_restore_authCode'] == sha1($kga['password_salt']));

/**
 * Execute an sql query in the database. The correct database connection
 * will be chosen and the query will be logged with the success status.
 *
 * @param $query query to execute as string
 */
function exec_query($query) {
    global $conn, $kga, $errors, $executed_queries;
    
    $success = $conn->Query($query);
    $errorInfo = serialize($conn->Error());

    Logger::logfile($query);
    if (!$success) {
      Logger::logfile($errorInfo);
      $errors=true;
    }
}

if (isset($_REQUEST['submit']) && $authenticated)
{
    $version_temp  = $database->get_DBversion();
    $versionDB  = $version_temp[0];
    $revisionDB = $version_temp[1];
    $p = $kga['server_prefix'];
    $conn = $database->getConnectionHandler();

  if ($_REQUEST['submit'] == $kga['lang']['backup'][8])
  {
    /**
     * Create a backup.
     */

    Logger::logfile("-- begin backup -----------------------------------");
    $backup_stamp = time();  
    $query = ("SHOW TABLES;");

    if (is_object($conn)) {
      $success = $conn->Query($query);
      $tables = $conn->RecordsArray();
    }
    $prefix_length = strlen($p);
  
    foreach($tables as $row) {
      if ((substr($row[0], 0, $prefix_length) == $p) && (substr($row[0], 0, 10) != "kimai_bak_")) {
        $backupTable = "kimai_bak_" . $backup_stamp . "_" . $row[0];
        $query = "CREATE TABLE ". $backupTable . " LIKE " . $row[0];
        exec_query($query);

        $query = "INSERT INTO " . $backupTable . " SELECT * FROM " . $row[0];
        exec_query($query);

        if ($errors) die($kga['lang']['updater'][60]);
      }
    }
    Logger::logfile("-- backup finished -----------------------------------");
    header("location: db_restore.php");
  }

  if ($_REQUEST['submit'] == $kga['lang']['backup'][3]) 
  {
    /**
     * Delete backups.
     */
    $dates = $_REQUEST['dates'];

    $query = ("SHOW TABLES;");
      
    if (is_object($conn)) {
      $success = $conn->Query($query);
      $tables = $conn->RecordsArray();
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

    if (is_object($conn)) 
    {
      foreach($arr2 AS $row)
      {
        $success = $conn->Query($row);
        if (!$success)
          break;
      }
    }
    header("location: db_restore.php");
  }
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
  <meta name="robots" content="noindex,nofollow" />
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

<?php if (!empty($kga['lang']['backup'][0])) { ?>
    <div class="warn"><?php echo $kga['lang']['backup'][0]?></div>
<?php } ?>
<div class="main">
<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// restore

if ($authenticated && isset($_REQUEST['submit']))
{
  if (($_REQUEST['submit'] == $kga['lang']['backup'][2]) && (isset($_REQUEST['dates']))) 
  {

    if (count($_REQUEST['dates'])>1) 
    {
        echo "<h1 class='fail'>".$kga['lang']['backup'][5]."</h1>";
    }
    else
    {
      $restoreDate = intval($_REQUEST['dates'][0]);
      $query = ("SHOW TABLES;");


      if (is_object($conn)) {
        $success = $conn->Query($query);
        $tables = $conn->RecordsArray();
      }

      $arr = array();
      $arr2 = array();

      foreach ($tables as $row)
      {
        if ( (substr($row[0], 0, 10) == "kimai_bak_"))
        {
          if (substr($row[0], 10, 10) == $restoreDate)
          {
            $table = $row[0];
            $arr[]=$table;
            $arr2[]=substr($row[0], 21, 100);
          }
        }
      }

      $i=0;
      foreach($arr2 AS $newTable)
      {
        $query = "DROP TABLE ". $arr2[$i];
        exec_query($query,1);

        $query = "CREATE TABLE " . $newTable . " LIKE " . $arr[$i];
        exec_query($query,1);
        $query = "INSERT INTO " . $newTable . " SELECT * FROM " . $arr[$i];
        exec_query($query,1);
        $i++;
      }

      $date = @date ("d. M Y, H:i:s", $restoreDate);
      echo "<h1 class='message'>" .$kga['lang']['backup'][6]. " ".$date."<br>" . $kga['lang']['backup'][7] ."</h1>";
    }
  }
}

echo '<form method="post" accept-charset="utf-8">';

if (!$authenticated)
{
    echo "<h1>" . $kga['lang']['backup'][10] . "</h1>";
    echo '<p class="caution">', $kga['lang']['backup'][11], '</p>';
    echo '<input type="text" name="salt"/>';
    echo '<input type="submit" name="submit" value="', $kga['lang']['login'], '"/>';
}
else
{
    echo "<h1>" . $kga['lang']['backup'][1] . "</h1>";

    $query = ("SHOW TABLES;");

    $result_backup=$database->queryAll($query);

    $arr = array();
    $arr2 = array();

    foreach ($result_backup as $row)
    {
        if ( (substr($row[0], 0, 10) == "kimai_bak_"))
        {
            $time = substr($row[0], 10, 10);
            $arr[]=$time;
        }
    }

    $neues_array = array_unique ($arr);


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
        echo <<<EOD
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
    <?php
}
?>

</form>
  <br/>
<a href="index.php">Login</a>
<p class="caution"><?php echo $kga['lang']['backup'][9]; ?></p>
</div>
</body>
</html>
