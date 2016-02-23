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


if (!defined('WEBROOT')) {
    define('WEBROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
}

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(WEBROOT . 'libraries/'),
            get_include_path()
        )
    )
);

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

$view = new Zend_View();
$view->setBasePath(WEBROOT . 'templates');

if (!isset($_REQUEST['err'])) {
    $_REQUEST['err'] = '';
}

switch ($_REQUEST['err']) {

  // TODO - can we make sure $kga exists?
  case 'db':
      $headline = $kga['lang']['errors'][0]['hdl'];
      $message  = $kga['lang']['errors'][0]['txt'];
  break;
    
  default:
      $headline = "Unknown Error";
      $message = "No error information was specified.";
  break;
}

$view->assign('headline', $headline);
$view->assign('message', $message);

echo $view->render('misc/error.php');
