<?php
  // Include Basics
  include('../../includes/basics.php');

  $usr = checkUser();
  // =========================================
  // = Get the currently displayed timespace =
  // =========================================
  $timespace = get_timespace();
  $in = $timespace[0];
  $out = $timespace[1];

  // Set smarty config.
  require_once(WEBROOT.'libraries/smarty/Smarty.class.php');
  $tpl = new Smarty();
  $tpl->template_dir = 'templates/';
  $tpl->compile_dir  = 'compile/';

  $tpl->display('index.tpl');
?>