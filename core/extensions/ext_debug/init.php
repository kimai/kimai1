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
	
	// set smarty config
	require_once(WEBROOT.'libraries/smarty/Smarty.class.php');
	$tpl = new Smarty();
	$tpl->template_dir = 'templates/';
	$tpl->compile_dir  = 'compile/';
	
// read kga --------------------------------------- 
	$output = $kga;
    // clean out sone data that is way too private to be shown in the frontend ...
    
    if (!$kga['show_sensible_data']) {
    	$output['server_hostname'] = "xxx";
    	$output['server_database'] = "xxx";
    	$output['server_username'] = "xxx";
    	$output['server_password'] = "xxx";
    	$output['usr']['secure']   = "xxx";
    	$output['usr']['usr_ID']   = "xxx";
    	$output['usr']['pw']       = "xxx";
    }
	
    $kga_display = print_r($output,true);
    $tpl->assign('kga', $kga);
    $tpl->assign('kga_display', $kga_display);
    $tpl->assign('browser', get_agent());
// /read kga -------------------------------------- 

    if ($kga['logfile_lines'] =="@") {
        $tpl->assign('limitText', "(unlimited lines)");
    } else {
        $tpl->assign('limitText', "(limited to " .$kga['logfile_lines'] ." lines)");
    }
   
	$tpl->display('index.tpl');
?>