<?php
echo '<script type="text/javascript" charset="utf-8">current=50;</script>';

(isset($_REQUEST['hostname']))?$mysqlhost = $_REQUEST['hostname']:$mysqlhost="localhost";
(isset($_REQUEST['username']))?$mysqluser = $_REQUEST['username']:$mysqluser = "";
(isset($_REQUEST['password']))?$mysqlpwd  = $_REQUEST['password']:$mysqlpwd  = "";

if ($mysqlhost=="") $mysqlhost="localhost";

if ($_REQUEST['lang']=="en") {

$echo=<<<EOD
    <h2>Database Server</h2>
    Please enter the account data of your database:<br/>
    (Caution: If you are not using an SSL-connection your data is going to be transmitted unencrypted!)<br/><br/>
    
    <table border="0" cellspacing="0" cellpadding="5">
        <tr>
            <td>Host:<br/><input id="host" type="text" value="$mysqlhost"/>   </td>
            <td>User:<br/><input id="user" type="text" value="$mysqluser"/>   </td>
            <td>Password:<br/><input id="pass" type="password" value="$mysqlpwd"/></td>
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
            <td>Host:<br/><input id="host" type="text" value="$mysqlhost"/>    </td>
            <td>Benutzer:<br/><input id="user" type="text" value="$mysqluser"/></td>
            <td>Passwort:<br/><input id="pass" type="password" value="$mysqlpwd"/> </td>
        </tr>
    </table>
    <span id='caution'></span><br /><br />
    <button onClick="step_back(); return false;">Zurück</button>
    <button onClick="host_proceed(); return false;" class="proceed">Fortfahren</button>
EOD;

}

echo $echo;
?>