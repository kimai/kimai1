<?php
echo '<script type="text/javascript" charset="utf-8">current=50;</script>';

$hostname = isset($_REQUEST['hostname'])?$_REQUEST['hostname']:"localhost";
$username = isset($_REQUEST['username'])?$_REQUEST['username']:"";
$password = isset($_REQUEST['password'])?$_REQUEST['password']:"";

if ($hostname=="") $hostname="localhost";

if ($_REQUEST['lang']=="en") {

$echo=<<<EOD
    <h2>Database Server</h2>
    Please enter the account data of your database:<br/>
    (Caution: If you are not using an SSL-connection your data is going to be transmitted unencrypted!)<br/><br/>
    
    <table border="0" cellspacing="0" cellpadding="5">
        <tr>
            <td>Host:<br/><input id="host" type="text" value="$hostname"/>   </td>
            <td>User:<br/><input id="user" type="text" value="$username"/>   </td>
            <td>Password:<br/><input id="pass" type="password" value="$password"/></td>
        </tr>
    </table>
    <span id='caution'></span><br />
    <button onClick="step_back(); return false;">Back</button>
    <button onClick="host_proceed(); return false;" class="proceed">Proceed</button>
EOD;

} else {

$echo=<<<EOD
    <h2>Datenbank Server</h2>
    Bitte geben Sie nun die Zugangsdaten der Datenbank ein:<br/>
    (Achtung: Wenn Ihre Installation nicht SSL-geschützt ist werden diese Informationen unverschlüsselt gesendet!)<br/><br/>

    <table border="0" cellspacing="0" cellpadding="5">
        <tr>
            <td>Host:<br/><input id="host" type="text" value="$hostname"/>    </td>
            <td>Benutzer:<br/><input id="user" type="text" value="$username"/></td>
            <td>Passwort:<br/><input id="pass" type="password" value="$password"/> </td>
        </tr>
    </table>
    <span id='caution'></span><br /><br />
    <button onClick="step_back(); return false;">Zurück</button>
    <button onClick="host_proceed(); return false;" class="proceed">Fortfahren</button>
EOD;

}

echo $echo;
?>