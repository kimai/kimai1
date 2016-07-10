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

/**
 * Show an login window or process the login request. On succes the user
 * will be redirected to core/kimai.php.
 */

if (!isset($_REQUEST['a'])) {
    $_REQUEST['a'] = '';
}

if (!isset($_POST['name']) || is_array($_POST['name'])) {
    $name = "";
} else {
    $name = $_POST['name'];
}

require('includes/basics.php');

$view = new Zend_View();
$view->setBasePath(WEBROOT . '/templates');

$authClass = 'Kimai_Auth_' . ucfirst($kga['authenticator']);
if (!class_exists($authClass)) {
    $authClass = 'Kimai_Auth_' . ucfirst($kga['authenticator']);
}
/* @var Kimai_Auth_Kimai $authPlugin */
$authPlugin = new $authClass($database, $kga);

$view->assign('kga', $kga);

switch ($_REQUEST['a']) {

    case 'forgotPassword':
        if (!method_exists($authPlugin, 'forgotPassword')) {
            echo json_encode(array(
                'message' => $kga['lang']['passwordReset']['notSupported']
            ));
        } else {
            echo json_encode(array(
                'message' => $authPlugin->forgotPassword($name)
            ));
        }
        break;

    case 'resetPassword':
        $key = $_REQUEST['key'];
        $password = $_REQUEST['password'];
        echo json_encode($authPlugin->resetPassword($name, $password, $key));
        break;

}
