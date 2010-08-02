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
 * along with Kimai; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 */

ob_start();

// =============================
// = Smarty (initialize class) =
// =============================
require_once('libraries/smarty/Smarty.class.php');
$tpl = new Smarty();
$tpl->template_dir = 'templates/misc/';
$tpl->compile_dir  = 'compile/';

if(file_exists('includes/autoconf.php')){
    require('includes/autoconf.php');
    require('includes/vars.php');
    include(sprintf("language/%s.php",$kga['language']));
}else{
    die("no config file!");
}

//switch ($_REQUEST['err']) {
    
//    default:
        $headline = $kga['lang']['errors'][0]['hdl'];
        $message  = $kga['lang']['errors'][0]['txt'];
//    break;
//}

$tpl->assign('headline', $headline);
$tpl->assign('message', $message);

// if the language-file could not be loaded this is a sure sign that something is wrong with the config...
if (!is_array($kga['language'])) {
    $tpl->assign('headline', "Fatal Error!");
    $tpl->assign('message', "No config-file found or it doesn't contain any data. Make sure your autoconf.php contains access-data for the database.<br/><br/>Die Konfigurations-Datei konnte nicht gefunden werden oder ist leer.");
}

$tpl->display('error.tpl');

ob_end_flush();

?>