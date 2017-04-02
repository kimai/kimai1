<script type="text/javascript" charset="utf-8">current = 30;</script>
<?php
require "../../includes/func.php";

$smtp_transport = isset($_REQUEST['smtp_transport']) ? $_REQUEST['smtp_transport'] : "sendmail";
$smtp_name = isset($_REQUEST['smtp_name']) ? $_REQUEST['smtp_name'] : "smtp.example.org";
$smtp_host = isset($_REQUEST['smtp_host']) ? $_REQUEST['smtp_host'] : "localhost";
$smtp_port = isset($_REQUEST['smtp_port']) ? $_REQUEST['smtp_port'] : "465";
$smtp_user = isset($_REQUEST['smtp_user']) ? $_REQUEST['smtp_user'] : "";
$smtp_pass = isset($_REQUEST['smtp_pass']) ? $_REQUEST['smtp_pass'] : "";
$smtp_ssl = isset($_REQUEST['smtp_ssl']) ? $_REQUEST['smtp_ssl'] : "ssl";
$smtp_auth = isset($_REQUEST['smtp_auth']) ? $_REQUEST['smtp_auth'] : "login";

function get_smtp_transport_list() {
    $html = '';
    global $smtp_transport;
    $trans = array('sendmail', 'smtp', 'file');
    foreach ($trans as $t) {
        if ($t == $smtp_transport) {
            $html .= '<option selected="selected">' . $t . '</option>';
        }
        else {
            $html .= '<option>'.$t.'</option>';
        }
    }

    if (!empty($html)) {
        return '<select name="smtp_transport" id="smtp_transport">' . $html . '</select>';
    }

    // sometimes fetching the list of timezones seems to fail
    // see https://github.com/kimai/kimai/issues/579
    return '<input type="text" value="' . $serverZone . '" id="timezone">';
}

if ($_REQUEST['lang'] == "en") {
    ?>
    <h2>Mail Server</h2>
    Please enter the email server information:<br/>
    (Caution: If you are not using an SSL-connection your data is going to be transmitted unencrypted!)<br/><br/>
    
    <table border="0" cellspacing="0" cellpadding="5">
        <tr>
            <td>Mail Transport Type:<br/> <?php echo get_smtp_transport_list(); ?>
            </td>
        </tr>
        <tr>
            <td>Description:<br/><input id="smtp_name" type="text" value="<?php echo $smtp_name; ?>"/>   </td>
        </tr>
        <tr>
            <td>Mail Server:<br/><input id="smtp_host" type="text" value="<?php echo $smtp_host; ?>"/>   </td>
            <td>Port:<br/><input id="smtp_port" type="text" value="<?php echo $smtp_port; ?>"/>   </td>
        </tr>
        <tr>
            <td>Auth Method:<br/><input id="smtp_auth" type="text" value="<?php echo $smtp_auth; ?>"/></td>
        </tr>
        <tr>
            <td>User:<br/><input id="smtp_user" type="text" value="<?php echo $smtp_user; ?>"/>   </td>
            <td>Password:<br/><input id="smtp_pass" type="password" value="<?php echo $smtp_pass; ?>"/>   </td>
        </tr>
        <tr>
            <td>SSL:<br/><input id="smtp_ssl" type="text" value="<?php echo $smtp_ssl; ?>"/>   </td>
        </tr>
    </table>
    <span id='caution'></span><br />
    <button onclick="step_back(); return false;">Back</button>
    <button onclick="mail_proceed(); return false;" class="proceed">Proceed</button>
    <?php
} else {
    ?>
    <h2>Mail-Server</h2>
    Bitte geben Sie Ihre E-Mail-Server-Details ein:<br/>
    (Achtung: Wenn Ihre Installation nicht SSL-geschützt ist werden diese Informationen unverschlüsselt gesendet!)<br/><br/>

    <table border="0" cellspacing="0" cellpadding="5">
        <tr>
            <td>Transportart:<br/><input id="smtp_transport" type="text" value="$smtp_transport"/>   </td>
        </tr>
        <tr>
            <td>Beschreibung:<br/><input id="smtp_name" type="text" value="$smtp_name"/>   </td>
        </tr>
        <tr>
            <td>Host:<br/><input id="smtp_host" type="text" value="$smtp_host"/>   </td>
            <td>TCP-Port:<br/><input id="smtp_port" type="text" value="$smtp_port"/>   </td>
        </tr>
        <tr>
            <td>Authentifizierung:<br/><input id="smtp_auth" type="text" value="$smtp_auth"/></td>
        </tr>
        <tr>
            <td>Benutzer:<br/><input id="smtp_user" type="text" value="$smtp_user"/>   </td>
            <td>Passwort:<br/><input id="smtp_pass" type="password" value="$smtp_pass"/>   </td>
        </tr>
        <tr>
            <td>SSL:<br/><input id="smtp_ssl" type="text" value="$smtp_ssl"/>   </td>
        </tr>
    </table>
    <span id='caution'></span><br /><br />
    <button onclick="step_back(); return false;">Zurück</button>
    <button onclick="mail_proceed(); return false;" class="proceed">Fortfahren</button>
    <?php
} ?>
