<?php

// =====================
// = INVOICE PROCESSOR =
// =====================

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require("../../includes/kspi.php");

// ==================
// = handle request =
// ==================
switch ($axAction) {

    // =====================================
    // = Reload the timespan and return it =
    // =====================================
    case 'reload_timespan':
        
        $timespace = get_timespace();
        $tpl->assign('in', $timespace[0]);
        $tpl->assign('out', $timespace[1]);

        $tpl->display("timespan.tpl");
    break;

}

?>