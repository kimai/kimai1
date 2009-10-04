<?php

include_once('../../includes/basics.php');

include_once('TinyButStrong/tbs_class.php');
include_once('TinyButStrong/tbsooo_class.php');

include_once('private_db_layer_pdo.php');

/**
 * returns true if event is in the arrays
 *
 * @param $arrays
 * @return true if $event is in the array
 * @author AA
 */
function array_event_exists($arrays, $event) {
   $index = 0;
   foreach ($arrays as $array) {
      if ( in_array($event,$array) ) {
          return $index;
      }
      $index++;
   }
   return -1;
}


// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";

$usr = checkUser();

$timespace = get_timespace();
$in = $timespace[0];
$out = $timespace[1];


$timeArray = get_arr_zef($in,$out,null,null,array($_REQUEST['pct_ID']),1);
 
$i_invoice  = '05123456';
$d_invoice  = date("Y-m-d");
$month  = $kga['lang']['months'][date("n",$out)-1];
$year = date("Y", $out );

if (count($timeArray) > 0) {
	
	$kndArray = get_entry_knd($timeArray[0]['knd_name']);
	// data customer
	$customerName = $timeArray[0]['knd_name'];
	$customerStreet = $kndArray['knd_street'];
	$customerTown = $kndArray['knd_zipcode'].' '.$kndArray['knd_city'];
}
    
// MERGE SORT
$time_index = 0;
$invoiceArray = array();

while ($time_index < count($timeArray)) {
	
    $rate  = $timeArray[$time_index]['wage'];
    $time  = $timeArray[$time_index]['zef_time']/3600;
    $event = $timeArray[$time_index]['evt_name'];
    
   // do we have to create a short form?
   if ( $_REQUEST['short'] ) {
   	
      $index = array_event_exists($invoiceArray,$event);
      if ( $index >= 0 ) {
         $totalTime = $invoiceArray[$index]['hour'];
         $totalAmount = $invoiceArray[$index]['amount'];
         $invoiceArray[$index] = array('key'=>$event, 'hour' => $totalTime+$time, "amount" => $totalAmount+$rate);
	  }
	  else {
   	     $invoiceArray[] = array('key'=>$event, 'hour'=>$time, 'amount'=>$rate );
	  }
   }
   else {
      $invoiceArray[] = array('key'=>$event, 'hour'=>$time, 'amount'=>$rate );
   }
   $time_index++;   
}

// calculate invoice sum
$f_total = 0;
while (list($id, $fd) = each($invoiceArray)) {
  $f_total+= $invoiceArray[$id]['amount'];
}

$vat_rate = 7.6;
$f_vat = $vat_rate*$f_total/100;
$f_exctotal = $f_total-$f_vat;

$OOo = new clsTinyButStrongOOo;

// setting the object
$OOo->SetZipBinary('zip');
$OOo->SetUnzipBinary('unzip');
$OOo->SetProcessDir('tmp/');

// create a new openoffice document from the template
if ( $_REQUEST['vat'] ) {
	$OOo->NewDocFromTpl('templates/rechnungVAT.sxw');
}
else {
	$OOo->NewDocFromTpl('templates/rechnung.sxw');
}

// merge data with openoffice file named 'content.xml'
$OOo->LoadXmlFromDoc('content.xml');
$OOo->MergeBlock('blk1',$invoiceArray) ;
$OOo->SaveXmlToDoc();

// display
header('Content-type: '.$OOo->GetMimetypeDoc());
header('Content-Length: '.filesize($OOo->GetPathnameDoc()));
$OOo->FlushDoc();
$OOo->RemoveDoc();

?>