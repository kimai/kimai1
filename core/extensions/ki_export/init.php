<?php

// ==================================
// = implementing standard includes =
// ==================================
include('../../includes/basics.php');

$usr = checkUser();

// ============================================
// = initialize currently displayed timespace =
// ============================================
$timespace = get_timespace();
$in = $timespace[0];
$out = $timespace[1];

// set smarty config
require_once('../../libraries/smarty/Smarty.class.php');
$tpl = new Smarty();
$tpl->template_dir = 'templates/';
$tpl->compile_dir  = 'compile/';

$tpl->assign('kga', $kga);

$tpl->display('panel.tpl');


// ==========================
// = display timesheet area =
// ==========================
$total = intervallApos(get_zef_time($in,$out,array($kga['usr']['usr_ID']),null,null));

$arr_zef = get_arr_zef($in,$out,array($kga['usr']['usr_ID']),null,null,1);

if (count($arr_zef)>0) {
    $tpl->assign('arr_data', $arr_zef);
} else {
    $tpl->assign('arr_data', 0);
}
$tpl->assign('total', $total);

$ann = get_arr_time_usr($in,$out,array($kga['usr']['usr_ID']));
$ann_new = intervallApos($ann);
$tpl->assign('usr_ann',$ann_new);

$ann = get_arr_time_knd($in,$out,array($kga['usr']['usr_ID']));
$ann_new = intervallApos($ann);
$tpl->assign('knd_ann',$ann_new);

$ann = get_arr_time_pct($in,$out,array($kga['usr']['usr_ID']));
$ann_new = intervallApos($ann);
$tpl->assign('pct_ann',$ann_new);

$ann = get_arr_time_evt($in,$out,array($kga['usr']['usr_ID']));
$ann_new = intervallApos($ann);
$tpl->assign('evt_ann',$ann_new);


$tpl->assign('table_display', $tpl->fetch("table.tpl"));

$tpl->display('main.tpl');

?>