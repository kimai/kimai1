<?php
echo '<script type="text/javascript" charset="utf-8">current=60;</script>';

error_reporting(E_ERROR | E_PARSE);

$db_error = false;

$hostname    = $_REQUEST['hostname'];
$username    = $_REQUEST['username'];
$password    = $_REQUEST['password'];
$server_type = $_REQUEST['db_type'];

$lang = $_REQUEST['lang'];

$pdo_dsn = $server_type . ':host=' . $hostname;

try {
    $pdo_conn = new PDO($pdo_dsn, $username, $password);
} catch (PDOException $pdo_ex) {
    error_log('PDO CONNECTION FAILED: ' . $pdo_ex->getMessage());
    $db_error = true;
}


$return = "";

if ($db_error == false) {

    $db_connection = "";      

    $pdo_query = $pdo_conn->prepare("SHOW DATABASES;");
    $pdo_query->execute(array());
    $i = 0;

    while ($row = $pdo_query->fetch()) {
        if ($row[0] != "information_schema") {
            $db_connection[$i] = $row[0];
            $i++;
        }
    }
    
    if ($i == 0) {
        $db_connection = 1;
    }
     
}


// $databases_can_be_created = 1;      // flag ob die rechte zum DB anlegen bestehen


if (is_array($db_connection)) {
    
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



        
            // can new database be created?
            $create_query = $pdo_conn->prepare("CREATE DATABASE kimai_permission_check;");
            $drop_query = $pdo_conn->prepare("DROP DATABASE kimai_permission_check;");
            if ($create_query->execute()) {
              $databases_can_be_created = 1;
              $drop_query->execute();
            } else {
              $databases_can_be_created = 0;
            }

    if ($databases_can_be_created) {
      ($lang=="de")?$return.="Neue Datenbank anlegen: (der angegebene DB-Nutzer muss die entspr. Rechte besitzen!)<br/><input id='db_create' type='text' value=''/> <strong id='db_create_label'></strong><br/><br/>"
		    :$return.="Create a blank database: (the db-user you entered must have appropriate rights!)<br/><input id='db_create' type='text' value=''/> <strong id='db_create_label'></strong><br/><br/>";
    }

    $return.="<input id='db_create' type='hidden' value=''/>";


    ($lang=="de")?$return.="Möchten Sie einen Tabellen-Prefix vergeben?<br/>(Wenn Sie nicht wissen was das ist, lassen Sie einfach 'kimai_' stehen...)<br/><input id='prefix' type='text' value='kimai_'/><br/><br/>"
                 :$return.="Would you like to assign a table-prefix?<br/>(If you don't know what this is - leave it as 'kimai_'...)<br/><input id='prefix' type='text' value='kimai_'/><br/><br/>";


    ($lang=="de")?$return.="<button onClick='step_back(); return false;'>Zurück</button> <button onClick='db_proceed(); return false;' class='proceed'>Fortfahren</button>"
                 :$return.="<button onClick='step_back(); return false;'>Go back</button> <button onClick='db_proceed(); return false;' class='proceed'>Proceed</button>";



} else {
    
    if ($db_connection) {
        ($lang=="de")?$return.="Keine Datenbanken vorhanden!<br/><br/>"
                     :$return.="No databases to choose from!<br/><br/>";
    } else {
        ($lang=="de")?$return.="Datenbank hat Zugriff verweigert. Gehen Sie zurück.<br/><br/><button onClick='step_back(); return false;'>Zurück</button>"
                     :$return.="The database refused access. Please go back.<br/><br/><button onClick='step_back(); return false;'>Go back</button>";
        echo $return;
    }
    
}



echo $return;


?>