<?php
	// Include Basics
	include('../../includes/basics.php');

require("private_func.php");
	
	$usr = checkUser();
	// ============================================
	// = initialize currently displayed timespace =
	// ============================================
	$timespace = get_timespace();
	$in = $timespace[0];
	$out = $timespace[1];
	
	// set smarty config
	require_once(WEBROOT.'libraries/smarty/Smarty.class.php');
	$tpl = new Smarty();
	$tpl->template_dir = 'templates/';
	$tpl->compile_dir  = 'compile/';
    // $tpl->cache_dir    = 'smarty/cache';
    // $tpl->config_dir   = 'smarty/configs';


  if (isset($kga['customer']))
    $arr_pct = get_arr_pct_by_knd("all",$kga['customer']['knd_ID']);
  else
    $arr_pct = get_arr_pct($kga['usr']['usr_grp']);

  if (count($arr_pct)>0) {
      $arr_plotdata = budget_plot_data($arr_pct);
      $tpl->assign('arr_plotdata', $arr_plotdata);
      $tpl->assign('arr_pct', $arr_pct);
  } else {
      $tpl->assign('arr_pct', 0);
  }

  $events = get_arr_evt("all");
  $chartColors = array("#4bb2c5", "#EAA228", "#c5b47f", "#579575", "#839557", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc");
  $tpl->assign('chartColors',json_encode($chartColors));
  $legend = array();

  $legend[] = array('color'=>$chartColors[0], 'name' => 'Auslagen');

  for ($i = 0;$i<count($events);$i++) {
    $legend[] = array('color'=>$chartColors[($i+1)%(count($chartColors)-1)], 'name' => $events[$i]['evt_name']);
  }

  $tpl->assign('arr_legend',$legend);
        
  $tpl->display('index.tpl');


?>