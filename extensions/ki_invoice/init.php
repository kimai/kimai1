<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team since 2006
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

include '../../includes/basics.php';

$user = checkUser();

$view = new Kimai_View();
$view->addBasePath(__DIR__ . '/templates/');

// get list of projects for select box
if (isset($kga['customer'])) {
    $view->assign('customers', array($kga['customer']['customerID'] => $kga['customer']['name']));
} else {
    $view->assign('customers', makeSelectBox("customer", $kga['user']['groups']));
}

$tmpCustomers = array_keys($view->customers);
$projects = $database->get_projects_by_customer($tmpCustomers[0], $kga['user']['groups']);

$tmpProjects = array();
foreach ($projects as $project) {
    $tmpProjects[$project['projectID']] = $project['name'];
}
$view->assign('projects', $tmpProjects);

// Select values for Round Time option
$roundingOptions = array(
    '0' => '',
    '1' => '0.1h',
    '2.5' => '0.25h',
    '5' => '0.5h',
    '10' => '1.0h'
);
$view->assign('roundingOptions', $roundingOptions);

// Extract all Invoice Templates in groups
$invoice_template_files = array();
$allInvoices = glob('invoices/*');
foreach($allInvoices as $tplFile)
{
    $extension = 'HTML';
    $tplInfo = pathinfo($tplFile);
    if (!is_dir($tplFile)) {
        $extension = strtoupper($tplInfo['extension']);
    }
    $filename = str_replace('_', ' ', $tplInfo['filename']);
    $invoice_template_files[$extension][$tplInfo['basename']] = ucfirst($filename);
}

$view->assign('invoice_templates', $invoice_template_files);

// Retrieve start & stop times
$timeframe = get_timeframe();
$view->assign('start_day', date($kga['date_format'][3], $timeframe[0]));
$view->assign('end_day', date($kga['date_format'][3], $timeframe[1]));

echo $view->render('main.php');
