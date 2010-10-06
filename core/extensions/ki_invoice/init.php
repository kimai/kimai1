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


// ==================================
// = implementing standard includes =
// ==================================
include('../../includes/basics.php');

// libs TinyButStrong
include_once('TinyButStrong/tinyButStrong.class.php');
include_once('TinyButStrong/tinyDoc.class.php');

$usr = checkUser();

// set smarty config
require_once('../../libraries/smarty/Smarty.class.php');
$tpl = new Smarty();
$tpl->template_dir = 'templates/';
$tpl->compile_dir  = 'compile/';

$tpl->assign('kga', $kga);

// get list of projects for select box
$sel = makeSelectBox("pct",$kga['usr']['usr_grp']);  
$tpl->assign('sel_pct_names', $sel[0]);
$tpl->assign('sel_pct_IDs',   $sel[1]);

// Select values for Round Time option
$tpl->assign('sel_round_names', array('0.1h', '0.25h', '0.5h', '1.0h') );
$tpl->assign('sel_round_IDs',   array(1, 2.5, 5, 10) );

// Get Invoice Template FileNames

$iv_tmp_arr = Array(); 
$handle = opendir('templates/'); 
while (false!== ($file = readdir($handle))) { 
 if ($file!= "." && $file!= ".." &&!is_dir($file)) { 
 $namearr = explode('.',$file); 
 if ($namearr[count($namearr)-1] == 'odt') $iv_tmp_arr[] = $file; 
 if ($namearr[count($namearr)-1] == 'ods') $iv_tmp_arr[] = $file;
 } 
} 
closedir($handle);
sort($iv_tmp_arr);
$tpl->assign('sel_form_files', $iv_tmp_arr);



// Retrieve start & stop times
$timespace = get_timespace();
$tpl->assign('in', $timespace[0]);
$tpl->assign('out', $timespace[1]);

$tpl->assign('timespan_display', $tpl->fetch("timespan.tpl"));

$tpl->display('main.tpl');

?>
