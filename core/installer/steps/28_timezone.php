
<script type="text/javascript" charset="utf-8">current=28;</script> 


<?php
if ($_REQUEST['lang']=="en") {
?>


<h2>Timezone</h2>

Select your timezone. This will be used as the default for new users and customers.<br/>
Users can change their timezone through their preferences, customers can't. This setting can be changed using the <i>Admin Panel</i>.

<br/><br/>

<select id="timezone">>
<?php
require("../../includes/func.php");

$serverZone = @date_default_timezone_get();

foreach (timezoneList() as $name) {
  if ($name == $serverZone)
    echo "<option selected=\"selected\">$name</option>";
  else
    echo "<option>$name</option>";
}
?>
</select>

<br/><br/>

<button onClick="step_back(); return false;">Zurück</button>
<button onClick="timezone_proceed(); return false;" class="proceed">Proceed</button>


<?php
}
else {
?>


<h2>Zeitzone</h2>

Wählen Sie ihre Zeitzone aus. Diese wird als Standard für neue Benutzer und Kunden verwendet.<br/>
Benutzer können später ihre eigene Zeitzone auswählen, Kunden jedoch nicht. Die Einstellung kann später im <i>Admin Panel</i> geändert werden.

<br/><br/>

<select id="timezone">
<?php
require("../../includes/func.php");

$serverZone = @date_default_timezone_get();

foreach (timezoneList() as $name) {
  if ($name == $serverZone)
    echo "<option selected=\"selected\">$name</option>";
  else
    echo "<option>$name</option>";
}
?>
</select>

<br/><br/>

<button onClick="step_back(); return false;">Zurück</button>
<button onClick="timezone_proceed(); return false;" class="proceed">Proceed</button>


<?php
}
?>