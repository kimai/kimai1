<?php
// always put in this standard processor initialization!

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require("../../includes/kspi.php");

switch ($axAction) {
    case 'test':
        echo $kga['conf']['timespace_in'];
    break;
}

?>
