<?php
// Always include the Kimai Standard Processor Initialization!

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require("../../includes/kspi.php");

switch ($axAction) {
    case 'test':
        echo $kga['usr']['timespace_in'];
    break;
}

?>
