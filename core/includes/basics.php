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

foreach (var_get_data() as $name => $value) {
  if ($name == 'currency_name' ||
      $name == 'currency_sign' ||
      $name == 'show_sensible_data' ||
      $name == 'show_update_warn' ||
      $name == 'check_at_startup' ||
      $name == 'show_daySeperatorLines' ||
      $name == 'show_gabBreaks' ||
      $name == 'show_RecordAgain' ||
      $name == 'show_TrackingNr')
    $kga[$name] = $value;
}
?>