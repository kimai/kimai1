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


// ==================================
// = implementing standard includes =
// ==================================
include('../../includes/basics.php');

$dir_templates = "templates/";
$datasrc = "config.ini";
$settings = parse_ini_file($datasrc);
$dir_ext = $settings['EXTENSION_DIR'];

$user = checkUser();

$view = new Zend_View();
$view->setBasePath(WEBROOT . 'extensions/' . $dir_ext . '/' . $dir_templates);

$view->kga = $kga;

// get list of projects for select box
$view->customers = makeSelectBox("customer",$kga['user']['groups']); 

$tmpCustomers = array_keys($view->customers);
$projects = $database->get_projects_by_customer($tmpCustomers[0], $kga['user']['groups']);
$view->projects = array();
foreach ($projects as $project) {
  $view->projects[$project['projectID']] = $project['name'];
}

// Select values for Round Time option
$roundingOptions = array(
  '1' => '0.1h',
  '2.5' =>'0.25h',
  '5' => '0.5h',
  '10' => '1.0h'
);
$view->roundingOptions = $roundingOptions;

// Get Invoice Template FileNames

$invoice_template_files = Array(); 
$handle = opendir('invoices/');
while (false!== ($file = readdir($handle))) { 
    if (stripos($file, '.') !== 0) {
        $invoice_template_files[$file] = $file;
    }
}
closedir($handle);
asort($invoice_template_files);
$view->sel_form_files = $invoice_template_files;

// Retrieve start & stop times
$timeframe = get_timeframe();
$view->in = $timeframe[0];
$view->out = $timeframe[1];

$view->timespan_display = $view->render("timespan.php");

echo $view->render('main.php');
