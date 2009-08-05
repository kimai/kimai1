<?php
echo '<script type="text/javascript" charset="utf-8">current=45;</script>';

try {
    $drivers = @PDO::getAvailableDrivers();
    $pdo_present = true;
} catch(Exception $e) {
    $pdo_present = false;
}
        
if (!$pdo_present) {
    
    if ($_REQUEST['lang']=="en") {
        echo<<<EOD
            <h2 style='color:red;'>PDO is not available!</h2>
            In order to connect to any database Kimai needs PDO (PHP Data Objects).<br/> 
            Unfortunately, PDO is not installed on this server.<br/><br/>
            <strong style='color:red;'>Installation can not proceed.</strong>
EOD;
    } else {
        echo<<<EOD
            <h2 style='color:red;'>PDO nicht verfügbar!</h2>
            Damit Kimai sich mit einer Datenbank verbinden kann muss PDO (PHP Data Objects)<br/> 
            auf dem System installiert sein. Leider ist das auf diesem Server nicht der Fall.<br/><br/>
            <strong style='color:red;'>Die Installation kann leider nicht fortgesetzt werden.</strong>
EOD;
    }
    
} else {
    
    if ($_REQUEST['lang']=="en") {
        echo "<h2>Database Connection via PDO</h2>";
        
        echo "Next step is to connect to the database.<br/>Please select the database-type you want to use:<br/><br/>";

        echo "Driver: <select id='con_type'>";

        foreach ($drivers as $driver) {
            if ($driver == "mysql") {
                $selected=" selected='selected'";
            } else {
                $selected="";
            }
            echo "<option value='$driver'$selected>$driver</option>";
        }

        echo "</select>";
        echo "<br /><br /><button onClick='step_back(); return false;'>Back</button> <button onClick='pdo_proceed(); return false;'>Proceed</button>";
        
        
    } else {

        echo "<h2>Datenbank Verbindung via PDO</h2>";
        
        echo "Im nächsten Schritt verbinden wir uns mit der Datenbank.<br/>Bitte wählen Sie aus welchen Datenbanktyp Sie benutzen möchten:<br/><br/>";

        echo "Datenbanktreiber: <select id='con_type'>";

        foreach ($drivers as $driver) {
            if ($driver == "mysql") {
                $selected=" selected='selected'";
            } else {
                $selected="";
            }
            echo "<option value='$driver'$selected>$driver</option>";
        }

        echo "</select>";
        echo "<br /><br /><button onClick='step_back(); return false;'>Zurück</button> <button onClick='pdo_proceed(); return false;'>Fortsetzen</button>";

    }
    
}

?>
