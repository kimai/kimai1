<?php

include_once('../../includes/basics.php');

// libs TinyButStrong
include_once('TinyButStrong/tinyButStrong.class.php');
include_once('TinyButStrong/tinyDoc.class.php');

include_once('private_db_layer_'.$kga['server_conn'].'.php');

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
/* $timeArray now contains: zef_ID, zef_in, zef_out, zef_time, zef_rate, zef_pctID, 
	zef_evtID, zef_usrID, pct_ID, knd_name, pct_kndID, evt_name, pct_comment, 
	pct_name, zef_location, zef_trackingnr, zef_comment, zef_comment_type, 
	usr_name, usr_alias, zef_cleared
*/

$date  = date("m-d-Y");
$month  = $kga['lang']['months'][date("n",$out)-1];
$year = date("Y", $out );

if (count($timeArray) > 0) {
	
	$kndArray = get_entry_knd($timeArray[0]['knd_name']);
// customer data
	$project = html_entity_decode($timeArray[0]['pct_name']);
	$customerName = html_entity_decode($timeArray[0]['knd_name']); 
	$companyName = $kndArray['knd_company']; 
	$customerStreet = $kndArray['knd_street'];
	$customerCity = $kndArray['knd_city'];
	$customerZip = $kndArray['knd_zipcode'];
	$customerComment = $kndArray['knd_comment'];
	$customerPhone = $kndArray['knd_tel'];
	$customerFax = $kndArray['knd_fax'];
	$customerMobile = $kndArray['knd_mobile'];
	$customerEmail = $kndArray['knd_mail'];
	$customerContact = $kndArray['knd_homepage']; //I'm using the "homepage" field to store client contact name
	$beginDate = date("F j, Y", $in);
	$endDate = date("F j, Y", $out);
	$invoiceID = $customerName. "-" . date("y", $in). "-" . date("m", $in);
	$today = date("F j, Y");
	$dueDate = date("F j, Y", mktime(0, 0, 0, date("m") + 1, date("d"),   date("Y")));

	
}
else {
   echo '<script language="javascript">alert("'.$kga['lang']['ext_invoice']['noData'].'")</script>';
   return;
}
    
// MERGE SORT
$time_index = 0;
$invoiceArray = array();

while ($time_index < count($timeArray)) {
	
	$wage  = $timeArray[$time_index]['wage'];
	$time  = $timeArray[$time_index]['zef_time']/3600;
	$event = html_entity_decode($timeArray[$time_index]['evt_name']);
	$comment = $timeArray[$time_index]['zef_comment'];
	$evtdt = date("m/d/Y", $timeArray[$time_index]['zef_in']);
    
   // do we have to create a short form?
   if ( $_REQUEST['short'] ) {
   	
      $index = array_event_exists($invoiceArray,$event);
      if ( $index >= 0 ) {
         $totalTime = $invoiceArray[$index]['hour'];
         $totalAmount = $invoiceArray[$index]['amount'];
         $invoiceArray[$index] = array('desc'=>$event, 'hour' => $totalTime+$time, "amount" => $totalAmount+$wage, 'date'=>$evtdt, 'comment'=>$comment);
	  }
	  else {
   	     $invoiceArray[] = array('desc'=>$event, 'hour'=>$time, 'amount'=>$wage, 'date'=>$evtdt, 'comment'=>$comment);
	  }
   }
   else {
      $invoiceArray[] = array('desc'=>$event, 'hour'=>$time, 'amount'=>$wage, 'date'=>$evtdt, 'comment'=>$comment);
   }
   $time_index++;   
}

$round = 0;
// do we have to round the time ?
if ( $_REQUEST['round'] ) {
   $round = $_REQUEST['pct_round'];
   $time_index = 0;
   
   while ($time_index < count($invoiceArray)) {

// Write a logfile entry for each value that is rounded. 
 logfile( "Round ".  $invoiceArray[$time_index]['hour'] . " to " . RoundValue( $invoiceArray[$time_index]['hour'], $round/10). " with ".  $round );
 
      $rate = RoundValue($invoiceArray[$time_index]['amount']/$invoiceArray[$time_index]['hour'],0.05);
      $invoiceArray[$time_index]['hour'] = RoundValue( $invoiceArray[$time_index]['hour'], $round/10);
      $invoiceArray[$time_index]['amount'] = $invoiceArray[$time_index]['hour']*$rate;
      $time_index++;
   }
   
}


// calculate invoice sums
$ttltime = 0;
$gtotal = 0;
while (list($id, $fd) = each($invoiceArray)) {
  $gtotal += $invoiceArray[$id]['amount'];
  $ttltime += $invoiceArray[$id]['hour'];
}

$vat_rate = 7.6;
$vat = $vat_rate*$gtotal/100;
$total = $gtotal-$vat;

// create the document
$doc = new tinyDoc();

// use zip extension if available
if (class_exists('ZipArchive')) {
  $doc->setZipMethod('ziparchive');
}
else {
  $doc->setZipMethod('shell');
  try {
    $doc->setZipBinary('zip');
    $doc->setUnzipBinary('unzip');
  }
  catch (tinyDocException $e) {
    $doc->setZipMethod('pclzip');
  }
}

$doc->setProcessDir('./tmp');

//This is where the template is selected

$templateform = "templates/" . $_REQUEST['ivform_file'];
$doc->createFrom($templateform);


$doc->loadXml('content.xml');
  
$doc->mergeXmlBlock('row', $invoiceArray);
  
$doc->saveXml();
$doc->close();

// send and remove the document
$doc->sendResponse();
$doc->remove();

?>
