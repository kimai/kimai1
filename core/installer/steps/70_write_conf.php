<?php 
echo '<script type="text/javascript" charset="utf-8">current=70;</script>';

$database = $_REQUEST['database'];
$prefix = $_REQUEST['prefix'];
if ( strlen($database) > 64 || !preg_match('/^[a-zA-Z0-9_-]+$/',$database) ||
     strlen($prefix)   > 64 || !preg_match('/^[a-zA-Z0-9_-]+$/',$prefix) ) {
echo '<script type="text/javascript" charset="utf-8">step_back();</script>';
}

if ($_REQUEST['lang']=="en") {

echo<<<EOD
    <h2>Write Config-File</h2>
    
    Now all data has been collected. By clicking OK you will install Kimai as follows:
    
    <br/>
    
    <table>
        <tr><td>Use database:</td> <td class="use_db"></td></tr> 
        <tr><td>On host:</td> <td class="use_host"></td></tr> 
        <tr><td>With table-prefix:</td> <td class="use_prefix"></td></tr>
    </table>
    
    <br/ >
    
    <div class="logindata">
        You can login afterwards with the following account-data:<br/>                
        <strong>User:</strong> admin<br/>
        <strong>Password:</strong> changeme<br/>
        Don't forget to change the password immediately!
    </div>
    <button onClick="step_back(); return false;">Back</button>
    <button onClick="install(); return false;" class="proceed">Install</button>
EOD;

} else {

echo<<<EOD
    <h2>Config-Datei Schreiben</h2>
    
    Die Daten sind jetzt komplett. Kimai kann jetzt mit folgender Datanbank-Verbindung installiert werden:
    
    <br/ >
    
    <table>
        <tr><td>Datenbank:</td> <td class="use_db"></td></tr> 
        <tr><td>Auf Server:</td> <td class="use_host"></td></tr> 
        <tr><td>Datenbank-Präfix:</td> <td class="use_prefix"></td></tr>
    </table>
    
    <br/ >
    
    <div class="logindata">
        Sie können sich anschließend mit<br/>                
        <strong>Nutzer:</strong> admin<br/>
        <strong>Passwort:</strong> changeme<br/>
        anmelden. Vergessen Sie nicht das Admin-Passwort anschließend sofort zu ändern!
    </div>
    <button onClick="step_back(); return false;">Zurück</button>
    <button onClick="install(); return false;" class="proceed">Installieren</button>
EOD;

}

?>