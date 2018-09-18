<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
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

defined('WEBROOT') || define('WEBROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);

require_once WEBROOT . 'libraries/autoload.php';

$kga = Kimai_Registry::getConfig();

$view = new Zend_View();
$view->setBasePath(WEBROOT . 'templates');

if (!isset($_REQUEST['err'])) {
    $_REQUEST['err'] = '';
}

switch ($_REQUEST['err']) {
    case 'db':
        $headline = $kga['lang']['errors'][0]['hdl'];
        $message = $kga['lang']['errors'][0]['txt'];
        break;
    default:
        $headline = 'Unknown Error';
        $message = 'No error information was specified.';
}

$view->assign('headline', $headline);
$view->assign('message', $message);

echo $view->render('misc/error.php');
