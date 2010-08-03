<?php
echo '<script type="text/javascript" charset="utf-8">current=60;</script>';

error_reporting(E_ERROR | E_PARSE);

$mysqlhost = $_REQUEST['hostname'];
$mysqluser = $_REQUEST['username'];
$mysqlpwd  = $_REQUEST['password'];
$lang      = $_REQUEST['lang'];
$database  = $_REQUEST['database'];
$prefix    = isset($_REQUEST['prefix'])?$_REQUEST['prefix']:"kimai_";

$databaseInList = false;

if ($_REQUEST['hostname']=="") $mysqlhost ="localhost";
if ($_REQUEST['username']=="") $mysqluser = "???";

$return = "";

$con = mysql_connect($mysqlhost,$mysqluser,$mysqlpwd); 

if ($con) {
            
            // read existing databases
            $result = mysql_query("SHOW DATABASES");
            $db_connection = array(); 
            while ( $row = mysql_fetch_row($result) ) {
                if (($row[0] != "information_schema")&&($row[0] != "mysql")) {
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
                    if ($database == $db_name) {
                      $return .="<option selected='selected' value='$db_name'>$db_name</option>";
                      $database = '';
                      $databaseInList = true;
                    }
                    else
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

                if ( !$databaseInList && isset($_REQUEST['database']) && !preg_match('/^[a-zA-Z0-9_]+$/',$database) ) {
                  $databaseErrorMessage = ($lang=="de")?"Nur Buchstaben, Zahlen und Unterstriche.":"Only letters, numbers and underscores.";
                }
                if ( !$databaseInList && isset($_REQUEST['database']) && strlen($database) > 64 ) {
                  $databaseErrorMessage = ($lang=="de")?"Maximal 64 Zeichen.":"At most 64 characters.";
                }

                if ($lang=="de") {
                  $return.="Neue Datenbank anlegen: (der angegebene DB-Nutzer muss die entspr. Rechte besitzen!)<br/><input id='db_create' type='text' value='$database'/>";
                }
                else {
                  $return.="Create a blank database: (the db-user you entered must have appropriate rights!)<br/><input id='db_create' type='text' value='$database'/>";
                }
                if (isset($databaseErrorMessage))
                  $return .= "<strong id='db_create_label' class='arrow'>$databaseErrorMessage</strong><br/><br/>";
                else
                  $return .= "<strong id='db_create_label'></strong><br/><br/>";

            }
            else
              $return.="<input id='db_create' type='hidden' value=''/>"; 

            // Table prefix
            if ( isset($_REQUEST['prefix']) && !preg_match('/^[a-zA-Z0-9_]+$/',$prefix) ) {
              $prefixErrorMessage = ($lang=="de")?"Nur Buchstaben, Zahlen und Unterstriche.":"Only letters, numbers and underscores.";
            }
            if ( isset($_REQUEST['prefix']) && strlen($prefix) > 64 ) {
              $prefixErrorMessage = ($lang=="de")?"Maximal 64 Zeichen.":"At most 64 characters.";
            }

            if ($lang=="de") {
              $return.="Möchten Sie einen Tabellen-Prefix vergeben?<br/>(Wenn Sie nicht wissen was das ist, lassen Sie einfach 'kimai_' stehen...)<br/><input id='prefix' type='text' value='$prefix'/>";
            }
            else {
              $return.="Would you like to assign a table-prefix?<br/>(If you don't know what this is - leave it as 'kimai_'...)<br/><input id='prefix' type='text' value='$prefix'/>";
            }
            if (isset($prefixErrorMessage))
              $return .= "<strong id='prefix_label' class='arrow'>$prefixErrorMessage</strong><br/><br/>";
            else
              $return .= "<strong id='prefix_label'></strong><br/><br/>";

            $return .= "<br/><br/>";

            ($lang=="de")?$return.="<button onClick=\"step_back(); return false;\">Zurück</button> <button onClick='db_proceed(); return false;' class='proceed'>Fortfahren</button>"
                         :$return.="<button onClick=\"step_back(); return false;\">Back</button> <button onClick='db_proceed(); return false;' class='proceed'>Proceed</button>";

    
} else {

        ($lang=="de")?$return.="Datenbank hat Zugriff verweigert. Gehen Sie bitte zurück.<br /><button onClick=\"step_back(); return false;\">Zurück</button>"
                     :$return.="The database refused access. Please go back.<br /><button onClick=\"step_back(); return false;\">Back</button>";
    
}



echo $return;

mysql_close($con);


?>