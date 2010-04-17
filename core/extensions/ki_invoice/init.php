<?php

/**
 * Check if PHP zip extension is present or
 * a shell zip program can be used.
 * Also PDO has to be used.
 * 
 * @return <code>true</code> if this extension definitley won't work,
 * 	   <code>false</code> otherwise
 */
function invoiceExtProblems() {
   global $kga;
   $problems = array();

  // create a document
  $doc = new tinyDoc();

  if (!class_exists('ZipArchive')) {
    try {
      $doc->setZipBinary('zip2');
      $doc->setUnzipBinary('unzip');
    }
    catch (tinyDocException $e) {
      $problems[] = "nozip";
    }
  }

  if ($kga['server_conn'] != 'pdo')
    $problems[] = "nopdo";
  
  return $problems;
      
}

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

$problems = invoiceExtProblems();
if (count($problems) > 0 ) {
  $tpl->assign('problems',$problems);
  $tpl->display('unusable.tpl');
  return;
}

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
 $namearr = split('\.',$file); 
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
