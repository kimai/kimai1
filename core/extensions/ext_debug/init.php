<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2009 Kimai-Development-Team
 *
 * Kimai is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; Version 3, 29 June 2007
 *
 * Kimai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimai; If not, see <http://www.gnu.org/licenses/>.
 */

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