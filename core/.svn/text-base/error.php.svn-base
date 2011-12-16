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

// =============================
// = Smarty (initialize class) =
// =============================
require_once('libraries/smarty/Smarty.class.php');
$tpl = new Smarty();
$tpl->template_dir = 'templates/misc/';
$tpl->compile_dir  = 'compile/';

if (!file_exists('includes/autoconf.php')) {
       $headline = "Fatal Error!";
       $message = "No config-file found or it doesn't contain any data. Make sure your autoconf.php contains access-data for the database.<br/><br/>Die Konfigurations-Datei konnte nicht gefunden werden oder ist leer.";
}
else {
  if (!isset($_REQUEST['err']))
    $_REQUEST['err'] = '';

  switch ($_REQUEST['err']) {

    case 'db':
        $headline = $kga['lang']['errors'][0]['hdl'];
        $message  = $kga['lang']['errors'][0]['txt'];
    break;
      
    default:
        $headline = "Unknown Error";
        $message = "No error information was specified.";
    break;
  }
}

$tpl->assign('headline', $headline);
$tpl->assign('message', $message);

$tpl->display('error.tpl');


?>