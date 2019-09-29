<?php
defined('WEBROOT') || define('WEBROOT', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR);
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(__FILE__)) . '/../'));

set_include_path(
    implode(
        PATH_SEPARATOR,
        [
            realpath(APPLICATION_PATH . '/libraries/zendframework/zendframework1/library/'),
        ]
    )
);

require_once WEBROOT . 'libraries/autoload.php';

echo '<script type="text/javascript">current=60;</script>';

$hostname = isset($_REQUEST['hostname']) ? $_REQUEST['hostname'] : 'localhost';
$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';
$lang = isset($_REQUEST['lang']) ? $_REQUEST['lang'] : 'en';
$database = isset($_REQUEST['database']) ? $_REQUEST['database'] : null;
$create_database = isset($_REQUEST['create_database']) ? $_REQUEST['create_database'] : '';
$prefix = isset($_REQUEST['prefix']) ? $_REQUEST['prefix'] : 'kimai_';

$con = new MySQL(true, $database, $hostname, $username, $password);

// we could not connect to the database, show error and leave the script
if (! $con) {
    if ($lang == 'de') {
        echo 'Datenbank hat Zugriff verweigert. Gehen Sie bitte zurück.<br /><button onclick="step_back(); return false;">Zurück</button>';
    } else {
        echo 'The database refused access. Please go back.<br /><button onclick="step_back(); return false;">Back</button>';
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
$result = $con->Query('SHOW GRANTS');
while ($row = $con->RowArray(null, MYSQLI_NUM)) {
    if (strpos($row[0], 'SHOW DATABASES') !== false) {
        $showDatabasesAllowed = true;
    } elseif (strpos($row[0], 'CREATE,') !== false) {
        $createDatabaseAllowed = true;
    } elseif (strpos($row[0], 'ALL PRIVILEGES') !== false) {
        $showDatabasesAllowed = true;
        $createDatabaseAllowed = true;
    }
}

$useDatabases = [];

if ($showDatabasesAllowed) {
    try {
        $con->MoveFirst();
        $result = $con->Query('SHOW DATABASES');
        while ($row = $con->RowArray(-2, MYSQLI_NUM)) {
            if (($row[0] != 'information_schema') && ($row[0] != 'mysql')) {
                $useDatabases[] = $row[0];
            }
        }
    } catch (Exception $ex) {
        // we cannot always detect if we can read read databases, see https://github.com/kimai/kimai/issues/492
        // no need for error handling, user will see an input field for the database name
    }
}

if (count($useDatabases) == 0) {
    if ($lang == 'de') {
        echo 'Keine Datenbank(en) vorhanden oder keine Berechtigung um Datenbanken aufzulisten. Name der zu verwendenden Datenbank:<br/>';
    } elseif ($lang == 'bg') {
        echo 'Не е намерена база данни или правата на потребителя са ограничени. Име на базата данни:<br/>';
    } else {
        echo 'No database(s) found or no permission to list databases. Name of the database to use:<br/>';
    }

    echo '<input type="text" id="db_names" value="' . htmlspecialchars($database) . '"/>';

    if (($database !== '' && $create_database === '') && ! $con->IsConnected()) {
        $errors = true;
        if ($lang == 'de') {
            echo '<strong id="db_select_label" class="arrow">Diese Datenbank konnte nicht geöffnet werden.</strong>';
        } elseif ($lang == 'bg') {
            echo '<strong id="db_select_label" class="arrow">Няма достъп до базата данни.</strong>';
        } else {
            echo '<strong id="db_select_label" class="arrow">Unable to open that database.</strong>';
        }
    } else {
        echo '<strong id="db_select_label"></strong>';
    }
    echo '<br/><br/>';

} else {
    // if there are databases build selectbox
    if ($lang == 'de') {
        echo 'Bitte wählen Sie eine Datenbank:';
    } elseif ($lang == 'bg') {
        echo 'Моля изберете база данни:';
    } else {
        echo 'Please choose a database:';
    }

    echo '<br/><select id="db_names">';
    echo '<option value=""></option>';

    foreach ($useDatabases as $db_name) {
        if ($database == $db_name) {
            echo '<option selected="selected" value="' . htmlspecialchars($db_name) . '">' . htmlspecialchars($db_name) . '</option>';
        } else {
            echo '<option value="' . htmlspecialchars($db_name) . '">' . htmlspecialchars($db_name) . '</option>';
        }
    }

    echo '</select> <strong id="db_select_label"></strong><br/><br/>';
}


if ($createDatabaseAllowed) {
    if ($database === '' && $create_database !== '') {
        if (! preg_match('/^[a-zA-Z0-9_]+$/', $create_database)) {
            if ($lang == 'de') { $databaseErrorMessage = 'Nur Buchstaben, Zahlen und Unterstriche.'; }
            if ($lang == 'bg') { $databaseErrorMessage = 'Само латински букви, цифри и долно тире(_).'; }
            if ($lang == 'en') { $databaseErrorMessage = 'Only letters, numbers and underscores.'; }
        } elseif (strlen($create_database) > 64) {
            if ($lang == 'de') { $databaseErrorMessage = 'Maximal 64 Zeichen.' ; }
            if ($lang == 'bg') { $databaseErrorMessage = 'Най-много 64 знака.'; }
            if ($lang == 'en') { $databaseErrorMessage = 'At most 64 characters.'; }
        } elseif ($con->SelectDatabase($create_database)) {
            if ($lang == 'de') { $databaseErrorMessage = 'Datenbank existiert bereits.' ; }
            if ($lang == 'bg') { $databaseErrorMessage = 'Базата данни вече съществува..'; }
            if ($lang == 'en') { $databaseErrorMessage = 'Database already exists.'; }
        }
    }

    if ($lang == 'de') {
        echo 'Neue Datenbank anlegen: (der angegebene DB-Nutzer muss die entsprechenden Rechte besitzen!)<br/><input id="db_create" type="text" value="' . $create_database . '"/>';
    } elseif ($lang == 'bg') {
        echo 'Създай празна база данни: (потребителят трябва да има нужните права за това!)<br/><input id="db_create" type="text" value="' . $create_database . '"/>';
    } else {
        echo 'Create a blank database: (the db-user you entered must have appropriate rights!)<br/><input id="db_create" type="text" value="' . $create_database . '"/>';
    }

    if (isset($databaseErrorMessage)) {
        $errors = true;
        echo '<strong id="db_create_label" class="arrow">' . $databaseErrorMessage . '</strong><br/><br/>';
    } else {
        echo '<strong id="db_create_label"></strong><br/><br/>';
    }
} else {
    echo '<input id="db_create" type="hidden" value=""/>';
}

if ($database !== '' && $create_database !== '') {
    $errors = true;
    if ($lang == 'de') {
        echo '<strong class="fail">Wählen sie entweder eine Datenbank aus oder geben sie eine neue an, aber nicht beides.</strong><br/><br/>';
    } elseif ($lang == 'bg') {
        echo '<strong class="fail">Или изберете база данни или задайте ново име, но не и двете наведнъж.</strong><br/><br/>';
    } else {
        echo '<strong class="fail">Either choose a database or give a new one, but not both.</strong><br/><br/>';
    }
}

// Table prefix
if ($prefix != 'kimai' && strlen($prefix) > 0 && ! preg_match('/^[a-zA-Z0-9_]+$/', $prefix)) {
    $errors = true;
    if ($lang == 'de') { $prefixErrorMessage = 'Nur Buchstaben, Zahlen und Unterstriche.' ; }
    if ($lang == 'bg') { $prefixErrorMessage = 'Само латински букви, цифри и долно тире(_).'; }
    if ($lang == 'en') { $prefixErrorMessage = 'Only letters, numbers and underscores.'; }
}
if ($prefix != 'kimai' && strlen($prefix) > 64) {
    $errors = true;
    if ($lang == 'de') { $prefixErrorMessage = 'Maximal 64 Zeichen.' ; }
    if ($lang == 'bg') { $prefixErrorMessage = 'Най-много 64 знака.'; }
    if ($lang == 'en') { $prefixErrorMessage = 'At most 64 characters.'; }
}

if ($lang == 'de') {
    echo 'Möchten Sie einen Tabellen-Prefix vergeben?<br/>(Wenn Sie nicht wissen was das ist, lassen Sie einfach "kimai_" stehen)<br/><input id="prefix" type="text" value="' . htmlspecialchars($prefix) . '"/>';
} elseif ($lang == 'bg') {
    echo 'Бихте ли искали да сложите представка на табелите?<br/>(Ако не знаете какво означава това, оставете "kimai_")<br/><input id="prefix" type="text" value="' . htmlspecialchars($prefix) . '"/>';
} else {
    echo 'Would you like to assign a table-prefix?<br/>(If you don\'t know what this is - leave it as "kimai_")<br/><input id="prefix" type="text" value="' . htmlspecialchars($prefix) . '"/>';
}

if (isset($prefixErrorMessage)) {
    echo '<strong id="prefix_label" class="arrow">' . $prefixErrorMessage . '</strong><br/><br/>';
} else {
    echo '<strong id="prefix_label"></strong><br/><br/>';
}

echo '<br/><br/>';

if ($lang == 'de') {
    echo '<button onclick="step_back(); return false;">Zurück</button> <button onclick="db_check(); return false;" class="proceed">Fortfahren</button>';
} elseif ($lang == 'bg') {
    echo '<button onclick="step_back(); return false;">Назад</button> <button onclick="db_check(); return false;" class="proceed">Напред</button>';
} else {
    echo '<button onclick="step_back(); return false;">Back</button> <button onclick="db_check(); return false;" class="proceed">Proceed</button>';
}

if (($database === '' && $create_database === '') || $errors || ! isset($_REQUEST['redirect'])) {
    echo ob_get_clean();
} else {
    echo '<script type="text/javascript">db_proceed();</script>';
}

$con->Close();
