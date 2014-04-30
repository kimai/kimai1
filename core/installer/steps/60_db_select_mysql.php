<?php
echo '<script type="text/javascript" charset="utf-8">current=60;</script>';

$hostname         = isset($_REQUEST['hostname'])?        $_REQUEST['hostname']        :'localhost';
$username         = isset($_REQUEST['username'])?        $_REQUEST['username']        :'???';
$password         = isset($_REQUEST['password'])?        $_REQUEST['password']        :'';
$lang             = isset($_REQUEST['lang'])?            $_REQUEST['lang']            :'en';
$database         = isset($_REQUEST['database'])?        $_REQUEST['database']        :'';
$create_database  = isset($_REQUEST['create_database'])? $_REQUEST['create_database'] :'';
$server_type      = isset($_REQUEST['db_type'])?         $_REQUEST['db_type']         :null;
$prefix           = isset($_REQUEST['prefix'])?          $_REQUEST['prefix']          :"kimai_";

$con = @mysql_connect($hostname,$username,$password);

// we could not connect to the database, show error and leave the script
if (!$con) {
    if ($lang=="de") {
        echo "Datenbank hat Zugriff verweigert. Gehen Sie bitte zurück.<br /><button onClick=\"step_back(); return false;\">Zurück</button>";
    } else {
        echo "The database refused access. Please go back.<br /><button onClick=\"step_back(); return false;\">Back</button>";
    }
    return;
}

// ====================================================================================================================
// if there is any error we have to show this page again, otherwise redirect to the next step
$errors = false;
ob_start();

// get permissions
$showDatabasesAllowed = false;
$createDatabaseAllowed = false;
$result = mysql_query("SHOW GRANTS;");
while ($row = mysql_fetch_row($result)) {
    if (strpos($row[0],'SHOW DATABASES') !== false)
        $showDatabasesAllowed = true;
    else if (strpos($row[0],'CREATE,') !== false)
        $createDatabaseAllowed = true;
    else if (strpos($row[0],'ALL PRIVILEGES') !== false) {
        $showDatabasesAllowed = true;
        $createDatabaseAllowed = true;
    }
}

if (!$showDatabasesAllowed) {
    if ($lang=="de")
        echo "Kein Berechtigung um Datenbanken aufzulisten. Name der zu verwendenden Datenbank:<br/>";
    else
        echo "No permission to list databases. Name of the database to use:<br/>";

    echo '<input type="text" id="db_names" value="'.$database.'"/>';

    if ( ($database !== '' && $create_database === '') && !mysql_select_db($database)) {
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

    // read existing databases
    $result = mysql_query("SHOW DATABASES");
    $db_connection = array();
    while ( $row = mysql_fetch_row($result) ) {
        if (($row[0] != "information_schema")&&($row[0] != "mysql")) {
            $db_connection[] = $row[0];
        }
    }

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
        else if ( mysql_select_db($create_database) )
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
if ( $prefix != 'kimai' && strlen($prefix) > 0 && !preg_match('/^[a-zA-Z0-9_]+$/',$prefix) ) {
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

mysql_close($con);
