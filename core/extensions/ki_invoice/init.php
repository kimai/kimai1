<?php

// ==================================
// = implementing standard includes =
// ==================================
include('../../includes/basics.php');

$usr = checkUser();

// select language
if (!isset($kga['conf']['lang']) || $kga['conf']['lang'] == "") {
    $language = $kga['language'];
} else {
   $language = $kga['conf']['lang'];
}

if ($language == "de") {
   require_once( "language/${language}.php" );
}
else {
   require_once( "language/en.php" );
}


// set smarty config
require_once('../../libraries/smarty/Smarty.class.php');
$tpl = new Smarty();
$tpl->template_dir = 'templates/';
$tpl->compile_dir  = 'compile/';

$tpl->assign('kga', $kga);

// select for projects
$sel = makeSelectBox("pct",$kga['usr']['usr_grp']);
$tpl->assign('sel_pct_names', $sel[0]);
$tpl->assign('sel_pct_IDs',   $sel[1]);


$timespace = get_timespace();
$tpl->assign('in', $timespace[0]);
$tpl->assign('out', $timespace[1]);

$tpl->assign('timespan_display', $tpl->fetch("timespan.tpl"));

$tpl->display('main.tpl');

?>