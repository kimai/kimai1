<?php
echo '<script type="text/javascript" charset="utf-8">current=60;</script>';

error_reporting(E_ERROR | E_PARSE);

$mysqlhost = $_REQUEST['hostname'];
$mysqluser = $_REQUEST['username'];
$mysqlpwd  = $_REQUEST['password'];
$lang      = $_REQUEST['lang'];

if ($_REQUEST['hostname']=="") $mysqlhost ="localhost";
if ($_REQUEST['username']=="") $mysqluser = "???";

$return = "";

$con = mysql_connect($mysqlhost,$mysqluser,$mysqlpwd); 

if ($con) {
            
            // read existing databases
            $result = mysql_query("SHOW DATABASES");
            $db_connection = array(); 
            while ( $row = mysql_fetch_row($result) ) {
                if (($row[0] != "information_schema")&&($row[0] != "mysql")&&($row[0] != "test")) {
                    $db_connection[] = $row[0];
                }
            }

            // if there are databases build selectbox
            if (count($db_connection)) {
    
                ($lang=="de")?$return.="Bitte wählen Sie eine Datenbank:"
                             :$return.="Please choose a database:";
                          
                ($lang=="de")?$choose="Bitte wählen"
                             :$choose="Please select";
    
                $return .="<br/><select id='db_names'>";
                $return .="<option value='0'>$choose</option>";
    
                foreach ($db_connection as $db_name) {
                    $return .="<option value='$db_name'>$db_name</option>";
                }
    
                $return .="</select> <strong id='db_select_label'></strong><br/><br/>";
        
            } else {
                ($lang=="de")?$return.="Keine Datenbank(en) vorhanden.<br/><br/>"
                             :$return.="No databases to choose from.<br/><br/>";
            }
        
            // can new database be created?
            if (mysql_query("CREATE DATABASE kimai_permission_check",$con)) {
              $databases_can_be_created = 1;
              mysql_query("DROP DATABASE kimai_permission_check",$con);
            } else {
              $databases_can_be_created = 0;
            }
    
            if ($databases_can_be_created) {
                ($lang=="de")?$return.="Neue Datenbank anlegen:<br/><input id='db_create' type='text' value=''/> <strong id='db_create_label'></strong><br/><br/>"
                             :$return.="Create a blank database:<br/><input id='db_create' type='text' value=''/> <strong id='db_create_label'></strong><br/><br/>";
            } 

            ($lang=="de")?$return.="Möchten Sie einen Tabellen-Prefix vergeben?<br/>(Wenn Sie nicht wissen was das ist, lassen Sie einfach 'kimai_' stehen...)<br/><input id='prefix' type='text' value='kimai_'/><br/><br/>"
                         :$return.="Would you like to assign a table-prefix?<br/>(If you don't know what this is - leave it as 'kimai_'...)<br/><input id='prefix' type='text' value='kimai_'/><br/><br/>";

            ($lang=="de")?$return.="<button onClick=\"step_back(); return false;\">Zurück</button> <button onClick='db_proceed(); return false;' class='proceed'>Fortfahren</button>"
                         :$return.="<button onClick=\"step_back(); return false;\">Back</button> <button onClick='db_proceed(); return false;' class='proceed'>Proceed</button>";

    
} else {

        ($lang=="de")?$return.="Datenbank hat Zugriff verweigert. Gehen Sie bitte zurück.<br /><button onClick=\"step_back(); return false;\">Zurück</button>"
                     :$return.="The database refused access. Please go back.<br /><button onClick=\"step_back(); return false;\">Back</button>";
    
}



echo $return;

mysql_close($con);


?>