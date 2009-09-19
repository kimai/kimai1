<?php

function exec_query($query,$errorProcessing=0) {
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

if (isset($_REQUEST['action'])) {
	
	if ($_REQUEST['action']=="delete") {
	
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
		header("location: db_restore.php");
	}
}


echo<<<EOD
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title>restore</title>

	</head>
	<body>
		<pre>
EOD;


if (($_REQUEST['action']=="restore")&& isset($_REQUEST['dates'])) {
	
	$dates = $_REQUEST['dates'];
			
	if (count($dates)>1) 
	{
		echo "<h1 style='color:red'>Es kann nur *ein* Backup wiederhergestellt werden!</h1>";
	}
	else
	{
echo<<<EOD
	<h1>Restore:</h1>
EOD;

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


		echo $dropquery;
		echo $restorequery;

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

	}


}


echo<<<EOD
	<h1>Vorhandene Backups:</h1>
EOD;


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
	echo '<input type="checkbox" name="dates[]" value="'.$date.'"> ' . date ("d. M Y, H:i:s", $date)."<br>";
}

echo '<br><input type="radio" name="action" value="restore" checked="checked"> Restore<br>';
echo '<input type="radio" name="action" value="delete"> Delete<br>';


echo<<<EOD
<p><input type="submit" value="Go!"></p>
</form>
EOD;




?>


</pre>
</body>

</html>

