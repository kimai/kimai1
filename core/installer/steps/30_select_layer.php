<?php
$echo = '<script type="text/javascript" charset="utf-8">current=30;</script>';

if ($_REQUEST['lang']=="en") {
echo<<<EOD
<h2>Select Database-Layer</h2>
<!--If you're unsure try to install the MySQL version.-->
MySQL-DB-Layer is currently not available!
EOD;
} else {
echo<<<EOD
<h2>Datenbank-Verbindungsart auswählen</h2>
<!--Wenn Sie unsicher sind versuchen Sie die MySQL-Version.-->
Der MySQL-DB-Layer ist in dieser Version nicht lauffähig!
EOD;
}

echo<<<EOD
    <form id="layer" >
        <div id="layer_sel">
        <!--<input type="image" class="layer" id="mysql" value="" onClick="layer_selected('mysql'); return false;"/> -->
        <input type="image" class="layer" id="mysql" value="" onClick="return false;"/>
        <input type="image" class="layer" id="pdo"   value="" onClick="layer_selected('pdo'); return false;"/>  
        </div>
    </form>
    
    <div style="clear:both;"></div>
    
EOD;


/*
if ($_REQUEST['lang']=="en") {
echo<<<EOD
<button onClick="step_back(); return false;" class="">Back</button>
EOD;
} else {
echo<<<EOD
<button onClick="step_back(); return false;" class="">Zurück</button>
EOD;
}
*/

?>