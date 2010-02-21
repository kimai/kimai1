
<script type="text/javascript" charset="utf-8">current=25;</script>

<?php
if ($_REQUEST['lang']=="en") {
?>

<h2>System Requirements</h2>
    The following conditions must be met:<br/>

<div class="sp_phpversion fail">at least PHP Major version 5</div>
<div class="sp_mysql">Extension for <b>MySQL</b> or <b>PDO with MySQL</b> has to be installed.</div>

<br/><br/>
For PDF export the following requirement must be met:<br/>
<div class="sp_memory">Allowed memory usage should be at least 20MB.</div>
<div class="note gray">See memory_limit in php.ini file.</div>

<br/><br/>

<button class="sp-button" onClick='check_system_requirements();'>Check requirements now</button>
<button onClick="system_requirements_proceed(); return false;" class="invisible proceed">Proceed</button>

<?php
}
else {
?>

<h2>Systemanforderungen</h2>
    Die folgenden Punkte m&uuml;ssen erf&uuml;llt sein:<br/>

<div class="sp_phpversion fail">mindestens PHP Hauptversion 5</div>
<div class="sp_mysql">Die Erweiterung f&uuml;r <b>MySQL</b> oder <b>PDO inkl. MySQL</b> muss installiert sein.</div>

<br/><br/>
Damit der PDF Export zuverl&auml;ssig funktioniert m&uuml;ssen folgende Punkte erf&uuml;llt sein:<br/>
<div class="sp_memory">Das Skript muss mind. 20MB an Speicher nutzen k&ouml;nnen.</div>
<div class="note gray">Siehe memory_limit in der php.ini Datei.</div>

<br/><br/>

<button class="sp-button" onClick='check_system_requirements();'>Anforderungen jetzt pr&uuml;fen</button>
<button onClick="system_requirements_proceed(); return false;" class="invisible proceed">Weiter</button>

<?php
}
?>

<script type="text/javascript" charset="utf-8">check_system_requirements();</script>