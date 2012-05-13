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

$user = checkUser();

// set smarty config
require_once('../../libraries/smarty/Smarty.class.php');
$tpl = new Smarty();
$tpl->template_dir = 'templates/';
$tpl->compile_dir  = 'compile/';

$tpl->assign('kga', $kga);

// get list of projects for select box
$sel = makeSelectBox("project",$kga['user']['groups']);  
$tpl->assign('projects', $sel);

// Select values for Round Time option
$roundingOptions = array(
  1 => '0.1h',
  2.5 =>'0.25h',
  5 => '0.5h',
  10 => '1.0h'
);
$tpl->assign('roundingOptions', $roundingOptions);

// Get Invoice Template FileNames

$invoice_template_files = Array(); 
$handle = opendir('templates/'); 
while (false!== ($file = readdir($handle))) { 
 if ($file!= "." && $file!= ".." &&!is_dir($file)) { 
 $namearr = explode('.',$file); 
 if ($namearr[count($namearr)-1] == 'odt') $invoice_template_files[] = $file; 
 if ($namearr[count($namearr)-1] == 'ods') $invoice_template_files[] = $file;
 } 
} 
closedir($handle);
sort($invoice_template_files);
$tpl->assign('sel_form_files', $invoice_template_files);



// Retrieve start & stop times
$timeframe = get_timeframe();
$tpl->assign('in', $timeframe[0]);
$tpl->assign('out', $timeframe[1]);

$tpl->assign('timespan_display', $tpl->fetch("timespan.tpl"));

$tpl->display('main.tpl');

?>
