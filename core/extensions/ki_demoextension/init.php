<?php
	// Include Basics
	include('../../includes/basics.php');
	
	$usr = checkUser();
	// ============================================
	// = initialize currently displayed timespace =
	// ============================================
	$timespace = get_timespace();
	$in = $timespace[0];
	$out = $timespace[1];
	
	// append (!) config to $kga
	get_config($usr['usr_ID']);
	
	// set smarty config
	require_once(WEBROOT.'libraries/smarty/Smarty.class.php');
	$tpl = new Smarty();
	$tpl->template_dir = 'templates/';
	$tpl->compile_dir  = 'compile/';
    // $tpl->cache_dir    = 'smarty/cache';
    // $tpl->config_dir   = 'smarty/configs';
				
	$tpl->display('index.tpl');
?>