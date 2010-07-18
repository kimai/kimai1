<?php
echo '<script type="text/javascript" charset="utf-8">current=30;</script>';
$pdo_available = extension_loaded('PDO') && extension_loaded('pdo_mysql');

if ($_REQUEST['lang']=="en") {
  $header = 'Select Database-Layer';
  if ($pdo_available)
    $infoText = 'If you\'re unsure, try to install the MySQL version.';
  else
    $infoText = 'PDO is not available as the modules are not loaded.';

} else {
  $header = 'Datenbank-Verbindungsart auswählen';
  if ($pdo_available)
    $infoText = 'Wenn Sie unsicher sind, versuchen Sie die MySQL-Version.';
  else
    $infoText = 'PDO ist nicht verfügbar da die Module dafür nicht aktiviert sind.';
}

echo "<h2>$header</h2>
$infoText";

echo '
    <form id="layer" >
        <div id="layer_sel">
        <input type="image" class="layer" id="mysql" value="" onClick="layer_selected(\'mysql\'); return false;"/>';

if ($pdo_available)
  echo '
        <input type="image" class="layer" id="pdo"   value="" onClick="layer_selected(\'pdo\'); return false;"/>';
else
  echo '
        <input type="image" class="layer" id="pdo_disabled"  value="" onClick="$(this).blur();return false;"/>';

echo '
        </div>
    </form>

    <div style="clear:both;"></div>';


?>