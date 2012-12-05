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

include_once('../../includes/basics.php');

/**
 * returns true if activity is in the arrays
 *
 * @param $arrays
 * @return true if $activity is in the array
 * @author AA
 */
function array_activity_exists($arrays, $activity) {
   $index = 0;
   foreach ($arrays as $array) {
      if ( in_array($activity,$array) ) {
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
$user            = checkUser();
$timeframe       = get_timeframe();
$in              = $timeframe[0];
$out             = $timeframe[1];

$timeArray = $database->get_timeSheet($in, $out, null, null, array($_REQUEST['projectID']), null,false,false,$_REQUEST['filter_cleared']);

$date  = time();
$month = $kga['lang']['months'][date("n", $out)-1];
$year  = date("Y", $out );

if (count($timeArray) > 0) {
    // customer data
    $customer        = $database->customer_get_data($timeArray[0]['customerID']);
    $projectObject   = $database->project_get_data($timeArray[0]['projectID']);

	$project         = html_entity_decode($timeArray[0]['projectName']);
	$customerName    = html_entity_decode($timeArray[0]['customerName']);
	$companyName     = $customer['company'];
	$customerStreet  = $customer['street'];
	$customerCity    = $customer['city'];
	$customerZip     = $customer['zipcode'];
	$customerComment = $customer['comment'];
	$customerPhone   = $customer['phone'];
	$customerFax     = $customer['fax'];
	$customerMobile  = $customer['mobile'];
	$customerEmail   = $customer['mail'];
	$customerContact = $customer['contact'];
	$customerURL	 = $customer['homepage'];
	$customerVat     = $customer['vat'];
	$projectComment  = $projectObject['comment'];
	$beginDate       = $in;
	$endDate         = $out;
	$invoiceID       = $customerName. "-" . date("y", $in). "-" . date("m", $in);
	$today           = time();
	$dueDate         = mktime(0, 0, 0, date("m") + 1, date("d"),   date("Y"));
} else {
    echo '<script language="javascript">alert("'.$kga['lang']['ext_invoice']['noData'].'")</script>';
    return;
}

// MERGE SORT
$time_index   = 0;
$invoiceArray = array();

while ($time_index < count($timeArray)) {
	$wage    = $timeArray[$time_index]['wage'];
	$time    = $timeArray[$time_index]['duration']/3600;
	$activity   = html_entity_decode($timeArray[$time_index]['activityName']);
	$comment = $timeArray[$time_index]['comment'];
	$description = $timeArray[$time_index]['description'];
	$activityDate   = date("m/d/Y", $timeArray[$time_index]['start']);
	$userName  = $timeArray[$time_index]['userName'];
	$userAlias = $timeArray[$time_index]['userAlias'];

   // do we have to create a short form?
   if ( isset($_REQUEST['short']) ) {

      $index = array_activity_exists($invoiceArray,$activity);
      if ( $index >= 0 ) {
         $totalTime = $invoiceArray[$index]['hour'];
         $totalAmount = $invoiceArray[$index]['amount'];
         $invoiceArray[$index] = array(
            'desc'    => $activity,
            'hour'    => $totalTime+$time,
            "amount"  => $totalAmount+$wage,
            'date'    => $activityDate,
            'description' => $description,
            'comment' => $comment
         );
	  }
	  else {
   	     $invoiceArray[] = array('desc'=>$activity, 'hour'=>$time, 'amount'=>$wage, 'date'=>$activityDate, 'description'=>$description, 'comment'=>$comment,  'username'=>'', 'useralias'=>'');
	  }
   }
   else {
      $invoiceArray[] = array('desc'=>$activity, 'hour'=>$time, 'amount'=>$wage, 'date'=>$activityDate, 'description'=>$description, 'comment'=>$comment, 'username'=>$userName, 'useralias'=>$userAlias);
   }
   $time_index++;
}

$round = 0;
// do we have to round the time ?
if (isset($_REQUEST['round'])) {
   $round      = $_REQUEST['round'];
   $time_index = 0;
   $amount     = count($invoiceArray);

    while ($time_index < $amount) {
        $rounded = RoundValue( $invoiceArray[$time_index]['hour'], $round/10);

        // Write a logfile entry for each value that is rounded.
        Logger::logfile("Round ".  $invoiceArray[$time_index]['hour'] . " to " . $rounded . " with ".  $round);

        $rate = RoundValue($invoiceArray[$time_index]['amount']/$invoiceArray[$time_index]['hour'],0.05);
        $invoiceArray[$time_index]['hour'] = $rounded;
        $invoiceArray[$time_index]['amount'] = $invoiceArray[$time_index]['hour']*$rate;
        $time_index++;
    }
}

// calculate invoice sums
$ttltime = 0;
$total  = 0;
while (list($id, $fd) = each($invoiceArray)) {
    $total  += $invoiceArray[$id]['amount'];
    $ttltime += $invoiceArray[$id]['hour'];
}

$vat_rate = $customer['vat'];
if (!is_numeric($vat_rate)) {
    $vat_rate = $kga['conf']['defaultVat'];
}

$vat   = $vat_rate*$total/100;
$gtotal = $total+$vat;

$baseFolder = dirname(__FILE__) . "/invoices/";
$tplFilename = $_REQUEST['ivform_file'];

$model = new Kimai_Invoice_PrintModel();
$model->setEntries($invoiceArray);

$renderer = null;

if(stripos($tplFilename, '.odt') !== false && is_file($baseFolder . $tplFilename))
{
    $renderer = new Kimai_Invoice_OdtRenderer();
}
else if(is_dir($baseFolder . $tplFilename) && is_file($baseFolder . $tplFilename . '/index.html'))
{
    $renderer = new Kimai_Invoice_HtmlToPdfRenderer();
}
else
{
    throw new Exception('Does not exist: ' . $baseFolder . $tplFilename);
}

$renderer->setTemplateDir($baseFolder);
$renderer->setTemplateFile($tplFilename);
$renderer->setTemporaryDirectory(APPLICATION_PATH . '/temporary');
$renderer->setModel($model);
$renderer->render();
