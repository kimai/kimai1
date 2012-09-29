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
	
$dir_templates = "templates/";
$datasrc = "config.ini";
$settings = parse_ini_file($datasrc);
$dir_ext = $settings['EXTENSION_DIR'];

	$user = checkUser();
	// ============================================
	// = initialize currently displayed timeframe =
	// ============================================
	$timeframe = get_timeframe();
	$in = $timeframe[0];
	$out = $timeframe[1];
	
$view = new Zend_View();
$view->setBasePath(WEBROOT . 'extensions/' . $dir_ext . '/' . $dir_templates);
	
// read kga --------------------------------------- 
	$output = $kga;
    // clean out sone data that is way too private to be shown in the frontend ...
    
    if (!$kga['show_sensible_data']) {
    	$output['server_hostname'] = "xxx";
    	$output['server_database'] = "xxx";
    	$output['server_username'] = "xxx";
    	$output['server_password'] = "xxx";
    	$output['user']['secure']   = "xxx";
    	$output['user']['userID']   = "xxx";
    	$output['user']['pw']       = "xxx";
    }
	
    $view->kga = $kga;
    $view->kga_display = print_r($output,true);
// /read kga -------------------------------------- 

    if ($kga['logfile_lines'] =="@") {
        $view->limitText = "(unlimited lines)";
    } else {
        $view->limitText = "(limited to " .$kga['logfile_lines'] ." lines)";
    }
   
	echo $view->render('index.php');
?>