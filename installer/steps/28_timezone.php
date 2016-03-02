<script type="text/javascript" charset="utf-8">current = 28;</script>
<?php
require "../../includes/func.php";

function getTimezoneInputField()
{
    $html = '';
    $serverZone = '';
    try {
        $serverZone = @date_default_timezone_get();
        if (empty($serverZone)) {
            $serverZone = 'UTC';
        }

        $allZones = timezoneList();

        if (!empty($allZones)) {
            foreach ($allZones as $name) {
                if ($name == $serverZone) {
                    $html .= '<option selected="selected">' . $name . '</option>';
                } else {
                    $html .= '<option>'.$name.'</option>';
                }
            }
        }
    } catch (Exception $ex) {
        $html = '';
    }

    if (!empty($html)) {
        return '<select id="timezone">' . $html . '</select>';
    }

    // sometimes fetching the list of timezones seems to fail
    // see https://github.com/kimai/kimai/issues/579
    return '<input type="text" value="' . $serverZone . '" id="timezone">';
}

if ($_REQUEST['lang'] == "en") {
    ?>
    <h2>Timezone</h2>
    Select your timezone. This will be used as the default for new users and customers.<br/>
    Users can change their timezone through their preferences, customers can't. This setting can be changed using the
    <i>Admin Panel</i>.
    <br/><br/>
    <?php echo getTimezoneInputField(); ?>
    <br/><br/>
    <button onclick="step_back(); return false;">Back</button>
    <button onclick="timezone_proceed(); return false;" class="proceed">Proceed</button>
    <?php
} else {
    ?>
    <h2>Zeitzone</h2>
    Wählen Sie ihre Zeitzone aus. Diese wird als Standard für neue Benutzer und Kunden verwendet.<br/>
    Benutzer können später ihre eigene Zeitzone auswählen, Kunden jedoch nicht. Die Einstellung kann später im <i>Admin
        Panel</i> geändert werden.
    <br/><br/>
    <?php echo getTimezoneInputField(); ?>
    <br/><br/>
    <button onclick="step_back(); return false;">Zurück</button>
    <button onclick="timezone_proceed(); return false;" class="proceed">Fortfahren</button>
    <?php
}
