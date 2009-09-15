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




// @Oleg: ich brauche bitte ein Array:
// [kndID][pctID, pctID, pctID, pctID, pctID....]
// [kndID][pctID, pctID, pctID, pctID, pctID....]
// [kndID][pctID, pctID, pctID, pctID, pctID....]
// ....
// Format der Ausgabe bitte so:
// js_arr_knd = "[],[],[23,45,43,56],[65,44,2],[],[44,32,4]";
// wobei dann die erste Klammer natürlich immer leer wäre.
// in javascript würde variable[2][0] dann 23 ausspucken

// ahh - ich brauche nochwas ...
// an position 0 je kunde soll bitte das letzte gelogte projekt
// stehen. also: 
// js_arr_knd = "[],[*0*],[*43*,45,43,56],[*2*,65,44,2],[*0*],[*4*,49,32,4]";
// (sternchen natürlich weglassen ;)

// wäre natürlich extrem cremig wenn das ganze einfach eine
// function im core wäre da ich das ja im processor auch nochmal
// brauche...

// $tpl->assign('jsArrKndPct', knd_pct_arr());
//$tpl->assign('jsArrKndPct', "");




// @Oleg: hier bitte so:
// [pctID][kndID, kndID, kndID, kndID, kndID....]
// (js_arr_pct)
//
// hier brauche ich dann an pos 0 die letzte gelogte
// tätigkeit des projekts




// @Oleg: und hier bitte einfach nur
// 
// [evtID, evtID, evtID, evtID, evtID....]
// (js_arr_evt )

// ==========================
// = display timesheet area =
// ==========================
$total = intervallApos(get_zef_time($in,$out,array($kga['usr']['usr_ID']),null,null));
$arr_zef = get_arr_zef($in,$out,array($kga['usr']['usr_ID']),null,null,1);
if (count($arr_zef)>0) {
    $tpl->assign('arr_zef', $arr_zef);
} else {
    $tpl->assign('arr_zef', 0);
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


$tpl->assign('zef_display', $tpl->fetch("zef.tpl"));

$tpl->assign('buzzerAction', "startRecord()");
$tpl->assign('browser', get_agent());

// select for projects
      $sel = makeSelectBox("pct",$kga['usr']['usr_grp']);
      $tpl->assign('sel_pct_names', $sel[0]);
      $tpl->assign('sel_pct_IDs',   $sel[1]);

// select for events
      $sel = makeSelectBox("evt",$kga['usr']['usr_grp']);
      $tpl->assign('sel_evt_names', $sel[0]);
      $tpl->assign('sel_evt_IDs',   $sel[1]);

// preselected
      $tpl->assign('lastProject', $kga['conf']['lastProject']);
      $tpl->assign('lastEvent',   $kga['conf']['lastEvent']);


      $tpl->display('main.tpl');

?>