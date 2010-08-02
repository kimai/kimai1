<?php
/**
 * Basic initialization takes place here.
 * From loading the configuration to connecting to the database this all is done
 * here.
 * 
 * What does NOT happen here is including the database dependant functions.
 */


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