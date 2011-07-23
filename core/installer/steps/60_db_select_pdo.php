<?php

/**
 * Check if a database already exists by trying to use it.
 * If the database name contains a semi colon we just reject it.
 */
function database_exists($pdo_conn,$database) {
  if (strpos($database,';') !== false) return false;
  $pdo_query = $pdo_conn->query("USE $database;");
  return $pdo_query !== false;
}

echo '<script type="text/javascript" charset="utf-8">current=60;</script>';

$hostname         = isset($_REQUEST['hostname'])?        $_REQUEST['hostname']        :'localhost';
$username         = isset($_REQUEST['username'])?        $_REQUEST['username']        :'???';
$password         = isset($_REQUEST['password'])?        $_REQUEST['password']        :'';
$lang             = isset($_REQUEST['lang'])?            $_REQUEST['lang']            :'en';
$database         = isset($_REQUEST['database'])?        $_REQUEST['database']        :'';
$create_database  = isset($_REQUEST['create_database'])? $_REQUEST['create_database'] :'';
$server_type      = isset($_REQUEST['db_type'])?         $_REQUEST['db_type']         :null;
$prefix           = isset($_REQUEST['prefix'])?          $_REQUEST['prefix']          :"kimai_";

$pdo_dsn = $server_type . ':host=' . $hostname;

try {
    $pdo_conn = new PDO($pdo_dsn, $username, $password);
} catch (PDOException $pdo_ex) {
    error_log('PDO CONNECTION FAILED: ' . $pdo_ex->getMessage());
}

if (!$pdo_conn) {

  if ($lang=="de")
    echo "Datenbank hat Zugriff verweigert. Gehen Sie bitte zurück.<br /><button onClick=\"step_back(); return false;\">Zurück</button>";
  else
    echo "The database refused access. Please go back.<br /><button onClick=\"step_back(); return false;\">Back</button>";
    
}
else {

  // if there is any error we have to show this page again, otherwise redirect to the next step
  $errors = false;
  ob_start();

  // We can't get permissions since different database systems handle it differently.
  // So we just try it out.
  $showDatabasesAllowed = false;
  $createDatabaseAllowed = false;

  // list databases
  $pdo_query = $pdo_conn->query("SHOW DATABASES;");
  if ($pdo_query) {
    $showDatabasesAllowed = true;
    $db_connection = array(); 
    while ($row = $pdo_query->fetch()) {
      if ($row[0] != "information_schema")
        $db_connection[] = $row[0];
    }
  }

  // check CREATE TABLE permission

  $dropPermissionCheckTableFailed = false;

  if ( $pdo_conn->query("CREATE DATABASE kimai_permission_check;") ) {

    $createDatabaseAllowed = true;

    if ( !$pdo_conn->query("DROP DATABASE kimai_permission_check;") )
      $dropPermissionCheckTableFailed = true;

  }
  

  if (!$showDatabasesAllowed) {
    if ($lang=="de")
      echo "Kein Berechtigung um Datenbanken aufzulisten. Name der zu verwendenden Datenbank:<br/>";
    else
      echo "No permission to list databases. Name of the database to use:<br/>";

    echo '<input type="text" id="db_names" value="'.$database.'"/>';
  
    if ( ($database !== '' && $create_database === '') && !database_exists($pdo_conn,$database)) {
      $errors = true;
      if ($lang=="de")
        echo '<strong id="db_select_label" class="arrow">Diese Datenbank konnte nicht geöffnet werden.</strong>';
      else
        echo '<strong id="db_select_label" class="arrow">Unable to open that database.</strong>';
    }
    else
        echo '<strong id="db_select_label"></strong>';

    echo '<br/><br/>';

  }
  else {

    if (count($db_connection) == 0) {
      if ($lang=="de")
       echo "Keine Datenbank(en) vorhanden.<br/><br/>";
     else
       echo "No database(s) found.<br/><br/>";
    }
    else {
      // if there are databases build selectbox
    
        if ($lang=="de")
          echo "Bitte wählen Sie eine Datenbank:";
        else
          echo "Please choose a database:";

        echo "<br/><select id='db_names'>";
        echo "<option value=''></option>";

        foreach ($db_connection as $db_name) {
            if ($database == $db_name) {
              echo "<option selected='selected' value='$db_name'>$db_name</option>";
            }
            else
              echo "<option value='$db_name'>$db_name</option>";
        }

        echo "</select> <strong id='db_select_label'></strong><br/><br/>";
    }
  }
        
  if ($createDatabaseAllowed) {

      if ( $database === '' && $create_database !== '' ) {
        if ( !preg_match('/^[a-zA-Z0-9_]+$/',$create_database) )
          $databaseErrorMessage = ($lang=="de")?"Nur Buchstaben, Zahlen und Unterstriche.":"Only letters, numbers and underscores.";
        else if ( strlen($create_database) > 64 )
          $databaseErrorMessage = ($lang=="de")?"Maximal 64 Zeichen.":"At most 64 characters.";
        else if ( database_exists($pdo_conn,$create_database) )
          $databaseErrorMessage = ($lang=="de")?"Datenbank existiert bereits.":"Database already exists.";
      }
      

      if ($lang=="de")
        echo "Neue Datenbank anlegen: (der angegebene DB-Nutzer muss die entspr. Rechte besitzen!)<br/><input id='db_create' type='text' value='$create_database'/>";
      else
        echo "Create a blank database: (the db-user you entered must have appropriate rights!)<br/><input id='db_create' type='text' value='$create_database'/>";
      
      if (isset($databaseErrorMessage)) {
        $errors = true;
        echo "<strong id='db_create_label' class='arrow'>$databaseErrorMessage</strong><br/><br/>";
      }
      else
        echo "<strong id='db_create_label'></strong><br/><br/>";

      if ($dropPermissionCheckTableFailed) {
        if ($lang=="de")
          echo "Es wurde testweise die Datenbank <i>kimai_permission_check</i> erzeugt. Diese konnte leider nicht wieder gelöscht werden. Bitte entschuldigen Sie diesen Umstand.<br/><br/>";
        else
          echo "For testing the permission to create a database the database <i>kimai_permission_check</i> was created. Unfortunately it couldn't be deleted. We're sorry for that.<br/><br/>";
      }
  }
  else
    echo "<input id='db_create' type='hidden' value=''/>"; 

  if ( $database !== '' && $create_database !== '' ) {
    $errors = true;
    if ($lang == 'de')
      echo '<strong class="fail">Wählen sie entweder eine Datenbank aus oder geben sie eine Neue an, aber nicht beides.</strong><br/><br/>';
    else
      echo '<strong class="fail">Either choose a database or give a new one, but not both.</strong><br/><br/>';
  }

    // Table prefix
    if ( $prefix != 'kimai' && !preg_match('/^[a-zA-Z0-9_]+$/',$prefix) ) {
      $errors = true;
      $prefixErrorMessage = ($lang=="de")?"Nur Buchstaben, Zahlen und Unterstriche.":"Only letters, numbers and underscores.";
    }
    if ( $prefix != 'kimai' && strlen($prefix) > 64 ) {
      $errors = true;
      $prefixErrorMessage = ($lang=="de")?"Maximal 64 Zeichen.":"At most 64 characters.";
    }

    if ($lang=="de")
      echo "Möchten Sie einen Tabellen-Prefix vergeben?<br/>(Wenn Sie nicht wissen was das ist, lassen Sie einfach 'kimai_' stehen...)<br/><input id='prefix' type='text' value='$prefix'/>";
    else
      echo "Would you like to assign a table-prefix?<br/>(If you don't know what this is - leave it as 'kimai_'...)<br/><input id='prefix' type='text' value='$prefix'/>";
    
    if (isset($prefixErrorMessage))
      echo "<strong id='prefix_label' class='arrow'>$prefixErrorMessage</strong><br/><br/>";
    else
      echo "<strong id='prefix_label'></strong><br/><br/>";

    echo "<br/><br/>";

    if ($lang=="de")
      echo "<button onClick=\"step_back(); return false;\">Zurück</button> <button onClick='db_check(); return false;' class='proceed'>Fortfahren</button>";
    else
      echo "<button onClick=\"step_back(); return false;\">Back</button> <button onClick='db_check(); return false;' class='proceed'>Proceed</button>";

  if ( ($database === '' && $create_database === '') || $errors || !isset($_REQUEST['redirect']))
    echo ob_get_clean();
  else
    echo '<script type="text/javascript" charset="utf-8">db_proceed();</script>';
}


$pdo_conn = null;


?>