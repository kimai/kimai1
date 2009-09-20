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
    global $conn, $pdo_conn, $kga, $errors, $executed_queries;
   
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
}





require('includes/basics.php');
// require(sprintf("language/%s.php",$kga['language']));

if (!isset($kga['conf']['lang']) || $kga['conf']['lang'] == "") {
    $language = $kga['language'];
} else {
    $language = $kga['conf']['lang'];
}
require_once( "language/${language}.php" );




if (isset($_REQUEST['action'])) 
{
	if ($_REQUEST['action']=="delete") 
	{
	
		$dates = $_REQUEST['dates'];

		$query = ("SHOW TABLES;");
		$result_backup=@mysql_query($query); 


		while ($row = mysql_fetch_array($result_backup))
		{
			if ((substr($row[0], 0, 10) == "kimai_bak_"))
			{
				if ( in_array(substr($row[0], 10, 10),$dates) )
				{
					$arr2[] = "DROP TABLE `".$row[0]."`;";	
				}
			}
		}

		$query="";
		foreach($arr2 AS $row)
		{
			$query .= $row;
		}

		//echo $query;

		if ($kga['server_conn'] == "pdo") 
		{
		        if (is_object($pdo_conn)) 
				{
		            $pdo_query = $pdo_conn->prepare($query);
		            $success = $pdo_query->execute(array());
		        }
		} 
		else 
		{
		    if (is_object($conn)) 
			{
		        $success = $conn->Query($query);
		    }
		}
		header("location: db_restore.php");
	}
}


echo<<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
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
		
		pre {
			font-size:80%;
		}
		
		h1.message {
			border:3px solid white;
			padding:10px;
			background-image: url('skins/standard/grfx/floaterborder.png');
		}
		
		h1.fail {
			border:3px solid red;
			padding:10px;
			background-image: url('skins/standard/grfx/floaterborder.png');
			color:red;
		}
	
	</style>

</head>
<body>
EOD;


echo '<div class="warn">'.$kga['lang']['backup'][0].'</div>';
echo '<div class="main">';



if (($_REQUEST['action']=="restore")&& isset($_REQUEST['dates'])) {
	
	$dates = $_REQUEST['dates'];
			
	if (count($dates)>1) 
	{
		echo "<h1 class='fail'>".$kga['lang']['backup'][0]."</h1>";
	}
	else
	{



//// RESTORE ///

		$query = ("SHOW TABLES;");

		$result_backup=@mysql_query($query); 

		$arr = array();
		$arr2 = array();
		$dropquery = "";
		$restorequery = "";

		while ($row = mysql_fetch_array($result_backup))
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



		$i=0;
		foreach($arr2 AS $newTable)
		{
			$dropquery .= "DROP TABLE `".$arr2[$i]."`;\n";
			$restorequery .= "CREATE TABLE " . $newTable .  " SELECT * FROM " .  $arr[$i] . ";\n";
			$i++;
		}

		// echo "<pre>";
		// echo $dropquery;
		// echo $restorequery;
		// echo "</pre>";

		if ($kga['server_conn'] == "pdo") {
		        if (is_object($pdo_conn)) {
		            $pdo_query = $pdo_conn->prepare($dropquery);
		            $success = $pdo_query->execute(array());
		        }
		} else {
		    if (is_object($conn)) {
		        $success = $conn->Query($dropquery);
		    }
		}

		if ($kga['server_conn'] == "pdo") {
		        if (is_object($pdo_conn)) {
		            $pdo_query = $pdo_conn->prepare($restorequery);
		            $success = $pdo_query->execute(array());
		        }
		} else {
		    if (is_object($conn)) {
		        $success = $conn->Query($restorequery);
		    }
		}
		
		$date = date ("d. M Y, H:i:s", $dates[0]);
		echo "<h1 class='message'>" .$kga['lang']['backup'][6]. " ".$date."<br>" . $kga['lang']['backup'][7] ."</h1>";

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
$label = date ("d. M Y, H:i:s", $date);
echo<<<EOD
<p class="label_checkbox">
<input type="checkbox" id="$label" name="dates[]" value="$date">
<label for="$label">$label</label>
</p>
EOD;
}

?>


<p class="radio"><input type="radio" name="action" value="restore" checked="checked"> <?php echo $kga['lang']['backup'][2]; ?> </p>
<p class="radio"><input type="radio" name="action" value="delete"> <?php echo $kga['lang']['backup'][3]; ?> </p>
<p style="clear:both"><input type="submit" value="<?php echo $kga['lang']['backup'][4]; ?>"></p>
</form>


</div>
</body>
</html>

