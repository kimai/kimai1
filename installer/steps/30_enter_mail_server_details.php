<script type="text/javascript" charset="utf-8">current = 30;</script>
<?php
require "../../includes/func.php";

$mail_transport = isset($_REQUEST['mail_transport']) ? $_REQUEST['mail_transport'] : "sendmail";
$smtp_name = isset($_REQUEST['smtp_name']) ? $_REQUEST['smtp_name'] : "smtp.example.org";
$smtp_host = isset($_REQUEST['smtp_host']) ? $_REQUEST['smtp_host'] : "127.0.0.1";
$smtp_port = isset($_REQUEST['smtp_port']) ? $_REQUEST['smtp_port'] : "25";
$smtp_user = isset($_REQUEST['smtp_user']) ? $_REQUEST['smtp_user'] : "";
$smtp_pass = isset($_REQUEST['smtp_pass']) ? $_REQUEST['smtp_pass'] : "";
$smtp_ssl = isset($_REQUEST['smtp_ssl']) ? $_REQUEST['smtp_ssl'] : "";
$smtp_auth = isset($_REQUEST['smtp_auth']) ? $_REQUEST['smtp_auth'] : "login";

function get_list($id, $selected, $list) {
    $html = '';

    foreach ($list as $item) {
        if ($item == $selected) {
            $html .= '<option selected="selected">' . $item . '</option>';
        }
        else {
            $html .= '<option>'.$item.'</option>';
        }
    }

    if (!empty($html)) {
//        return '<select name="' . $id . '" id="' . $id .'" >' . $html . '</select>';
        return '<select name="' . $id . '" id="' . $id .'" onchange="mail_transport_select();" >' . $html . '</select>';
    }

    return '<input type="text" value="' . $selected . '" id="timezone">';
}

if ($_REQUEST['lang'] == "en") {
    ?>
    <h2>Mail Server</h2>
    Please enter the email server information:<br/>
    (Caution: If you are not using an SSL-connection your data is going to be transmitted unencrypted!)<br/><br/>
    
    <table border="0" cellspacing="0" cellpadding="5">
        <tr>
            <td>Mail Transport Type:<br/> <?php echo get_list("mail_transport", $mail_transport, array('sendmail', 'smtp', 'file')); ?>
            </td>
        </tr>
        <tr class="smtp">
            <td>Name:<br/><input id="smtp_name" type="text" value="<?php echo $smtp_name; ?>"/>   </td>
        </tr>
        <tr class="smtp">
            <td>Mail Server:<br/><input id="smtp_host" type="text" value="<?php echo $smtp_host; ?>"/>   </td>
            <td>Port:<br/><input id="smtp_port" type="text" value="<?php echo $smtp_port; ?>"/>   </td>
        </tr>
        <tr class="smtp">
            <td>Auth Method:<br/> <?php echo get_list("smtp_auth", $smtp_auth, array('', 'login', 'plain', 'crammd5')); ?>
            </td>
        </tr class="smtp">
        <tr class="smtp">
            <td>User:<br/><input id="smtp_user" type="text" value="<?php echo $smtp_user; ?>"/>   </td>
            <td>Password:<br/><input id="smtp_pass" type="password" value="<?php echo $smtp_pass; ?>"/>   </td>
        </tr>
        <tr class="smtp">
            <td>SSL:<br/> <?php echo get_list("smtp_ssl", $smtp_ssl, array('', 'ssl', 'tls')); ?>
            </td>
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
            <td>Transportart:<br/><?php echo get_list("mail_transport", $mail_transport, array('sendmail', 'smtp', 'file')); ?>
            </td>
        </tr>
        <tr class="smtp">
            <td>Beschreibung:<br/><input id="smtp_name" type="text" value="<?php echo $smtp_name; ?>"/>   </td>
        </tr>
        <tr class="smtp">
            <td>Host:<br/><input id="smtp_host" type="text" value="<?php echo $smtp_host; ?>"/>   </td>
            <td>TCP-Port:<br/><input id="smtp_port" type="text" value="<?php echo $smtp_port; ?>"/>   </td>
        </tr>
        <tr class="smtp">
            <td>Authentifizierung:<br/> <?php echo get_list("smtp_auth", $smtp_auth, array('', 'login', 'plain', 'crammd5')); ?>
            </td>
        </tr>
        <tr class="smtp">
            <td>Benutzer:<br/><input id="smtp_user" type="text" value="<?php echo $smtp_user; ?>"/>   </td>
            <td>Passwort:<br/><input id="smtp_pass" type="password" value="<?php echo $smtp_pass; ?>"/>   </td>
        </tr>
        <tr class="smtp">
            <td>SSL:<br/> <?php echo get_list("smtp_ssl", $smtp_ssl, array('', 'ssl', 'tls')); ?>
            </td>
        </tr>
    </table>
    <span id='caution'></span><br /><br />
    <button onclick="step_back(); return false;">Zurück</button>
    <button onclick="mail_proceed(); return false;" class="proceed">Fortfahren</button>
    <?php
} ?>
