<?php
// Include Basics
include('../../includes/basics.php');

require("private_func.php");

$usr = checkUser();

// set smarty config
require_once(WEBROOT.'libraries/smarty/Smarty.class.php');
$tpl = new Smarty();
$tpl->template_dir = 'templates/';
$tpl->compile_dir  = 'compile/';

// Get all project for the logged in customer or the current user.
if (isset($kga['customer']))
  $arr_pct = get_arr_pct_by_knd("all",$kga['customer']['knd_ID']);
else
  $arr_pct = get_arr_pct($kga['usr']['usr_grp']);

$usedEvents = array();

// If there are any projects create the plot data.
if (count($arr_pct)>0) {
    $arr_plotdata = budget_plot_data($arr_pct,$usedEvents);
    $tpl->assign('arr_plotdata', $arr_plotdata);
    $tpl->assign('arr_pct', $arr_pct);
} else {
    $tpl->assign('arr_pct', 0);
}

$chartColors = array("#efefef", "#4bb2c5", "#EAA228", "#c5b47f", "#579575", "#839557", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc");
$tpl->assign('chartColors',json_encode($chartColors));

// Create the keys which explain to the user which color means what.
$keys = array();
$keys[] = array('color'=>$chartColors[0], 'name' => $kga['lang']['ext_budget']['unusedBudget']);
$keys[] = array('color'=>$chartColors[1], 'name' => $kga['lang']['xp_ext']['expenses']);
for ($i = 0;$i<count($usedEvents);$i++) {
  $keys[] = array('color'=>$chartColors[($i+2)%(count($chartColors)-1)], 'name' => $usedEvents[$i]['evt_name']);
}

$tpl->assign('arr_keys',$keys);
      
$tpl->display('index.tpl');


?>