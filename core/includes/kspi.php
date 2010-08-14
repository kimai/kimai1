<?php
/**
 * The Kimai Standard Processor Initialization.
 * This is used by all processor.php files. General setup stuff is done here.
 */

// ==================================
// = implementing standard includes =
// ==================================
require("basics.php");

if (!$isCoreProcessor) {
  $datasrc = "config.ini";
  $settings = parse_ini_file($datasrc);
  $dir_ext = $settings['EXTENSION_DIR'];
}

// =============================
// = Smarty (initialize class) =
// =============================
require_once(WEBROOT . 'libraries/smarty/Smarty.class.php');
$tpl = new Smarty();
if ($isCoreProcessor) {
  $tpl->template_dir = WEBROOT . $dir_templates;
  $tpl->compile_dir  = WEBROOT . 'compile/';
} else {
  $tpl->template_dir = WEBROOT . 'extensions/' . $dir_ext . '/' . $dir_templates;
  $tpl->compile_dir  = WEBROOT . 'extensions/' . $dir_ext . '/' . 'compile/';
}


// ============================================================================================
// = assigning language and config variables / they are needed in all following smarty output =
// ============================================================================================
$usr = checkUser();

$tpl->assign('kga',$kga);

$comment_types   = array($kga['lang']['ctype0'],$kga['lang']['ctype1'],$kga['lang']['ctype2']);

// ==================
// = security check =
// ==================
if ( isset($_REQUEST['axAction']) && !is_array($_REQUEST['axAction']) && $_REQUEST['axAction']!="") {
  $axAction = strip_tags($_REQUEST['axAction']);
} else {
  $axAction = '';
}

$axValue = isset($_REQUEST['axValue']) ? strip_tags($_REQUEST['axValue']) : '';
$id = isset($_REQUEST['id']) ? strip_tags($_REQUEST['id']) : null;


// ============================================
// = initialize currently displayed timespace =
// ============================================
$timespace = get_timespace();
$in = $timespace[0];
$out = $timespace[1];

if (isset($_REQUEST['first_day']))
  $in  = (int)$_REQUEST['first_day'];
if (isset($_REQUEST['last_day']))
  $out = mktime(23,59,59,date("n",$_REQUEST['last_day']),date("j",$_REQUEST['last_day']),date("Y",$_REQUEST['last_day']));

if ($axAction != "reloadLogfile") {
    logfile("KSPI axAction (".(array_key_exists('customer',$kga)?$kga['customer']['knd_name']:$kga['usr']['usr_name'])."): " . $axAction);
}


// prevent IE from caching the response
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

?>
