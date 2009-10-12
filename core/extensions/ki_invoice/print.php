<?php

include_once('../../includes/basics.php');

// libs TinyButStrong
include_once('TinyButStrong/tinyButStrong.class.php');
include_once('TinyButStrong/tinyDoc.class.php');

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

function RoundValue( $value, $prec ) {
   $precision = $prec;
    
   // suppress division by zero errror
   if ($precision == 0.0) {
      $precision = 1.0;
   }
  
   return floor($value / $precision + 0.5)*$precision;
}

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";

$usr = checkUser();

$timespace = get_timespace();
$in = $timespace[0];
$out = $timespace[1];


$timeArray = get_arr_zef($in,$out,null,null,array($_REQUEST['pct_ID']),1);
 
$order  = '05123456';
$date  = date("Y-m-d");
$month  = $kga['lang']['months'][date("n",$out)-1];
$year = date("Y", $out );

if (count($timeArray) > 0) {
	
	$kndArray = get_entry_knd($timeArray[0]['knd_name']);
	// data customer
	$customerName = $timeArray[0]['knd_name'];
	$customerStreet = $kndArray['knd_street'];
	$customerTown = $kndArray['knd_zipcode'].' '.$kndArray['knd_city'];
}
else {
   echo "<script language=\"javascript\">alert(\"In der Ausgewählten Zeitspanne sind keine Einträge!\")</script>";
   return;
}
    
// MERGE SORT
$time_index = 0;
$invoiceArray = array();

while ($time_index < count($timeArray)) {
	
    $wage  = $timeArray[$time_index]['wage'];
    $time  = $timeArray[$time_index]['zef_time']/3600;
    $event = $timeArray[$time_index]['evt_name'];
    
   // do we have to create a short form?
   if ( $_REQUEST['short'] ) {
   	
      $index = array_event_exists($invoiceArray,$event);
      if ( $index >= 0 ) {
         $totalTime = $invoiceArray[$index]['hour'];
         $totalAmount = $invoiceArray[$index]['amount'];
         $invoiceArray[$index] = array('desc'=>$event, 'hour' => $totalTime+$time, "amount" => $totalAmount+$rate);
	  }
	  else {
   	     $invoiceArray[] = array('desc'=>$event, 'hour'=>$time, 'amount'=>$wage );
	  }
   }
   else {
      $invoiceArray[] = array('desc'=>$event, 'hour'=>$time, 'amount'=>$wage );
   }
   $time_index++;   
}

logfile( "ROUND 12.3 ". RoundValue(12.3,0.5) . " - 12.7 ". RoundValue(12.7,0.5) );

$round = 0;
// do we have to round the time ?
if ( $_REQUEST['round'] ) {
   $round = $_REQUEST['pct_round'];
   $time_index = 0;
   
   while ($time_index < count($invoiceArray)) {

 logfile( "Round ".  $invoiceArray[$time_index]['hour'] . " to " . RoundValue( $invoiceArray[$time_index]['hour'], $round/10). " with ".  $round );
 
      $rate = RoundValue($invoiceArray[$time_index]['amount']/$invoiceArray[$time_index]['hour'],0.05);
      $invoiceArray[$time_index]['hour'] = RoundValue( $invoiceArray[$time_index]['hour'], $round/10);
      $invoiceArray[$time_index]['amount'] = $invoiceArray[$time_index]['hour']*$rate;
      $time_index++;
   }
   
}

// calculate invoice sum
$gtotal = 0;
while (list($id, $fd) = each($invoiceArray)) {
  $gtotal += $invoiceArray[$id]['amount'];
}

$vat_rate = 7.6;
$vat = $vat_rate*$gtotal/100;
$total = $gtotal-$vat;

// create the document
$doc = new tinyDoc();
$doc->setZipMethod('shell');
$doc->setZipBinary('zip');
$doc->setUnzipBinary('unzip');
$doc->setProcessDir('./tmp');
  
if ( $_REQUEST['vat'] ) {
   $doc->createFrom('templates/invoiceVAT.odt');   
}
else  {
   $doc->createFrom('templates/invoice.odt');
}
$doc->loadXml('content.xml');
  
$doc->mergeXmlBlock('row', $invoiceArray);
  
$doc->saveXml();
$doc->close();

// send and remove the document
$doc->sendResponse();
$doc->remove();

?>