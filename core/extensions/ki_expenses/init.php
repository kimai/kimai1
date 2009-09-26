<?php

// ==================================
// = implementing standard includes =
// ==================================
include('../../includes/basics.php');
include('private_db_layer_'.$kga['server_conn'].'.php');
checkUser();
// append (!) config to $kga
//get_config($usr['usr_ID']);


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

if (isset($kga['usr'])) // user logged in
  $arr_exp = get_arr_exp($in,$out,$kga['usr']['usr_ID'],null,null,1);
else // customer logged in
  $arr_exp = get_arr_exp($in,$out,null,$kga['customer']['knd_ID'],null,1);

//$arr_exp = get_arr_exp($in,$out,1);

if (count($arr_exp)>0) {
    $tpl->assign('arr_exp', $arr_exp);
} else {
    $tpl->assign('arr_exp', 0);
}
$tpl->assign('total', "");



if (isset($kga['usr'])) // user logged in
  $ann = get_arr_exp_usr($in,$out,$kga['usr']['usr_ID']);
else // customer logged in
  $ann = get_arr_exp_usr($in,$out,null,$kga['customer']['knd_ID']);
$tpl->assign('usr_ann',$ann);

// TODO: function for loops or convert it in template with new function
if (isset($kga['usr'])) // user logged in
  $ann = get_arr_exp_knd($in,$out,$kga['usr']['usr_ID']);
else // customer logged in
  $ann = get_arr_exp_knd($in,$out,null,$kga['customer']['knd_ID']);
$tpl->assign('knd_ann',$ann);

if (isset($kga['usr'])) // user logged in
  $ann = get_arr_exp_pct($in,$out,$kga['usr']['usr_ID']);
else // customer logged in
  $ann = get_arr_exp_pct($in,$out,null,$kga['customer']['knd_ID']);
$tpl->assign('pct_ann',$ann);




$tpl->assign('exp_display', $tpl->fetch("exp.tpl"));



$tpl->display('main.tpl');

?>