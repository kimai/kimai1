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

/*
// ==========================
// = display timesheet area =
// ==========================
if (isset($kga['customer']))
  $total = intervallApos(get_zef_time($in,$out,null,array($kga['customer']['knd_ID']),null));
else
  $total = intervallApos(get_zef_time($in,$out,array($kga['usr']['usr_ID']),null,null));

if (isset($kga['customer']))
  $arr_zef = get_arr_zef($in,$out,null,array($kga['customer']['knd_ID']),null,1);
else
  $arr_zef = get_arr_zef($in,$out,array($kga['usr']['usr_ID']),null,null,1);
if (count($arr_zef)>0) {
    $tpl->assign('arr_zef', $arr_zef);
} else {
    $tpl->assign('arr_zef', 0);
}
$tpl->assign('total', $total);

if (isset($kga['customer']))
  $ann = get_arr_time_usr($in,$out,null,array($kga['customer']['knd_ID']));
else
  $ann = get_arr_time_usr($in,$out,array($kga['usr']['usr_ID']));
$ann_new = intervallApos($ann);
$tpl->assign('usr_ann',$ann_new);

if (isset($kga['customer']))
  $ann = get_arr_time_knd($in,$out,null,array($kga['customer']['knd_ID']));
else
  $ann = get_arr_time_knd($in,$out,array($kga['usr']['usr_ID']));
$ann_new = intervallApos($ann);
$tpl->assign('knd_ann',$ann_new);

if (isset($kga['customer']))
  $ann = get_arr_time_pct($in,$out,null,array($kga['customer']['knd_ID']));
else
  $ann = get_arr_time_pct($in,$out,array($kga['usr']['usr_ID']));
$ann_new = intervallApos($ann);
$tpl->assign('pct_ann',$ann_new);

if (isset($kga['customer']))
  $ann = get_arr_time_evt($in,$out,null,array($kga['customer']['knd_ID']));
else
  $ann = get_arr_time_evt($in,$out,array($kga['usr']['usr_ID']));
$ann_new = intervallApos($ann);
$tpl->assign('evt_ann',$ann_new);


$tpl->assign('zef_display', $tpl->fetch("zef.tpl"));

$tpl->assign('buzzerAction', "startRecord()");
$tpl->assign('browser', get_agent());

// select for projects
if (isset($kga['customer'])) {
      $tpl->assign('sel_pct_names', array());
      $tpl->assign('sel_pct_IDs',   array());
}
else {
      $sel = makeSelectBox("pct",$kga['usr']['usr_grp']);
      $tpl->assign('sel_pct_names', $sel[0]);
      $tpl->assign('sel_pct_IDs',   $sel[1]);
}

// select for events
if (isset($kga['customer'])) {
      $tpl->assign('sel_evt_names', array());
      $tpl->assign('sel_evt_IDs',   array());
}
else {
      $sel = makeSelectBox("evt",$kga['usr']['usr_grp']);
      $tpl->assign('sel_evt_names', $sel[0]);
      $tpl->assign('sel_evt_IDs',   $sel[1]);
}

// preselected
      $tpl->assign('lastProject', $kga['conf']['lastProject']);
      $tpl->assign('lastEvent',   $kga['conf']['lastEvent']);

      $tpl->display('main.tpl');

*/


?>