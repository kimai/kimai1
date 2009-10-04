<?php

// ================
// = TS PROCESSOR =
// ================

// ==================================
// = implementing standard includes =
// ==================================
include('../../includes/basics.php');

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require("../../includes/kspi.php");

// ==================
// = handle request =
// ==================
switch ($axAction) {

    // ===================================================
    // = Load timesheet data (zef) from DB and return it =
    // ===================================================
    case 'reload_timespan':
        
        $timespace = get_timespace();
        $tpl->assign('in', $timespace[0]);
        $tpl->assign('out', $timespace[1]);

        $tpl->display("timespan.tpl");
    break;

}

?>