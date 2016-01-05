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
 * @param $activity
 * @return bool true if $activity is in the array
 * @author AA
 */
function array_activity_exists($arrays, $activity)
{
    $index = 0;
    foreach ($arrays as $array) {
        if (in_array($activity, $array)) {
            return $index;
        }
        $index++;
    }
    return -1;
}

/**
 * @param $value
 * @param $precision
 * @return float
 */
function RoundValue($value, $precision)
{
    // suppress division by zero error
    if ($precision == 0.0) {
        $precision = 1.0;
    }

    return floor($value / $precision + 0.5) * $precision;
}

// insert KSPI
$isCoreProcessor = 0;
$user = checkUser();
$timeframe = get_timeframe();
$in = $timeframe[0];
$out = $timeframe[1];

require_once('private_func.php');

if (count($_REQUEST['projectID']) == 0) {
    echo '<script language="javascript">alert("' . $kga['lang']['ext_invoice']['noProject'] . '")</script>';
    return;
}

$invoiceArray = invoice_get_data($in, $out, $_REQUEST['projectID'], $_REQUEST['filter_cleared'], isset($_REQUEST['short']));

if (count($invoiceArray) == 0) {
    echo '<script language="javascript">alert("' . $kga['lang']['ext_invoice']['noData'] . '")</script>';
    return;
}

// ----------------------- FETCH ALL KIND OF DATA WE NEED WITHIN THE INVOICE TEMPLATES -----------------------

$date = time();
$month = $kga['lang']['months'][date("n", $out) - 1];
$year = date("Y", $out);
$projectObjects = array();
foreach ($_REQUEST['projectID'] as $projectID) {
    $projectObjects[] = $database->project_get_data($projectID);
}
$customer = $database->customer_get_data($projectObjects[0]['customerID']);
$customerName = html_entity_decode($customer['name']);
$beginDate = $in;
$endDate = $out;
$invoiceID = $customer['name'] . "-" . date("y", $in) . "-" . date("m", $in);
$today = time();
$dueDate = mktime(0, 0, 0, date("m") + 1, date("d"), date("Y"));


$round = 0;
// do we have to round the time ?
if (isset($_REQUEST['round'])) {
    $round = $_REQUEST['roundValue'];
    $time_index = 0;
    $amount = count($invoiceArray);

    while ($time_index < $amount) {
        if ($invoiceArray[$time_index]['type'] == 'timeSheet') {
            $rounded = RoundValue($invoiceArray[$time_index]['hour'], $round / 10);

            // Write a logfile entry for each value that is rounded.
            Logger::logfile("Round " . $invoiceArray[$time_index]['hour'] . " to " . $rounded . " with " . $round);

            if ($invoiceArray[$time_index]['hour'] == 0) {
                // make sure we do not raise a "divison by zero" - there might be entries with the zero seconds
                $rate = 0;
            } else {
                $rate = RoundValue($invoiceArray[$time_index]['amount'] / $invoiceArray[$time_index]['hour'], 0.05);
            }

            $invoiceArray[$time_index]['hour'] = $rounded;
            $invoiceArray[$time_index]['amount'] = $invoiceArray[$time_index]['hour'] * $rate;
        }
        $time_index++;
    }
}
// calculate invoice sums
$ttltime = 0;
$rawTotalTime = 0;
$total = 0;
while (list($id, $fd) = each($invoiceArray)) {
    $total += $invoiceArray[$id]['amount'];
    $ttltime += $invoiceArray[$id]['hour'];
}
$fttltime = Format::formatDuration($ttltime * 3600);

$vat_rate = $customer['vat'];
if (!is_numeric($vat_rate)) {
    $vat_rate = $kga['conf']['defaultVat'];
}

$vat = $vat_rate * $total / 100;
$gtotal = $total + $vat;

$baseFolder = dirname(__FILE__) . "/invoices/";
$tplFilename = $_REQUEST['ivform_file'];

if (strpos($tplFilename, '/') !== false) {
    // prevent directory traversal
    header("HTTP/1.0 400 Bad Request");
    die;
}

// ---------------------------------------------------------------------------

// totally unneccessary
unset($customer['password']);
unset($customer['passwordResetHash']);

$model = new Kimai_Invoice_PrintModel();
$model->setEntries($invoiceArray);
$model->setAmount($total);
$model->setVatRate($vat_rate);
$model->setTotal($gtotal);
$model->setVat($vat);
$model->setCustomer($customer);
$model->setProjects($projectObjects);
$model->setInvoiceId($invoiceID);

$model->setBeginDate($beginDate);
$model->setEndDate($endDate);
$model->setInvoiceDate(time());
$model->setDateFormat($kga['conf']['date_format_2']);
$model->setCurrencySign($kga['conf']['currency_sign']);
$model->setCurrencyName($kga['conf']['currency_name']);
$model->setDueDate(mktime(0, 0, 0, date("m") + 1, date("d"), date("Y")));

// ---------------------------------------------------------------------------
$renderers = array(
    'odt' => new Kimai_Invoice_OdtRenderer(),
    'html' => new Kimai_Invoice_HtmlRenderer(),
    'pdf' => new Kimai_Invoice_HtmlToPdfRenderer()
);

/* @var $renderer Kimai_Invoice_AbstractRenderer */
foreach ($renderers as $rendererType => $renderer) {
    $renderer->setTemplateDir($baseFolder);
    $renderer->setTemplateFile($tplFilename);
    $renderer->setTemporaryDirectory(APPLICATION_PATH . '/temporary');
    if ($renderer->canRender()) {
        $renderer->setModel($model);
        $renderer->render();
        return;
    }
}

// no renderer could be found
die('Template does not exist or is incompatible: ' . $baseFolder . $tplFilename);
