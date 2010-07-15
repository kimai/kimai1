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

if(file_exists(realpath(dirname(__FILE__).'/conf.php')))
	require_once(realpath(dirname(__FILE__).'/conf.php'));

require('autoconf.php');
if (!$server_hostname) die("Error: Something is wrong with the file 'includes/conf.php' or 'includes/autoconf.php'!");
require('vars.php');

require('func.php');
require("connect_".$kga['server_conn'].".php");

$vars = var_get_data();
if (!empty($vars)) {
  $kga['currency_name']          = $vars['currency_name'];
  $kga['currency_sign']          = $vars['currency_sign'];
  $kga['show_sensible_data']     = $vars['show_sensible_data'];
  $kga['show_update_warn']       = $vars['show_update_warn'];
  $kga['check_at_startup']       = $vars['check_at_startup'];
  $kga['show_daySeperatorLines'] = $vars['show_daySeperatorLines'];
  $kga['show_gabBreaks']         = $vars['show_gabBreaks'];
  $kga['show_RecordAgain']       = $vars['show_RecordAgain'];
  $kga['show_TrackingNr']        = $vars['show_TrackingNr'];
  $kga['date_format'][0]         = $vars['date_format_0'];
  $kga['date_format'][1]         = $vars['date_format_1'];
  $kga['date_format'][2]         = $vars['date_format_2'];
  if ($vars['language'] != '')
    $kga['language']             = $vars['language'];
}
?>