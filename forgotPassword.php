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

/**
 * Show an login window or process the login request. On succes the user
 * will be redirected to core/kimai.php.
 */

if (!isset($_REQUEST['a'])) {
    $_REQUEST['a'] = '';
}

if (!isset($_REQUEST['name']) || is_array($_REQUEST['name'])) {
    $name = '';
} else {
    $name = $_REQUEST['name'];
}

if (!isset($_REQUEST['key']) || is_array($_REQUEST['key'])) {
    $key = 'nokey'; // will never match since hash values are either NULL or 32 characters
} else {
    $key = $_REQUEST['key'];
}

require 'includes/basics.php';

$database = Kimai_Registry::getDatabase();

$view = new Zend_View();
$view->setBasePath(WEBROOT . 'templates');

$authPlugin = Kimai_Registry::getAuthenticator();

$view->assign('kga', $kga);

// current database setup correct?
checkDBversion(".");

// processing login and displaying either login screen or errors
$name = htmlspecialchars(trim($name));
$is_customer = $database->is_customer_name($name);
if ($is_customer) {
    $id = $database->customer_nameToID($name);
    $customer = $database->customer_get_data($id);
    $keyCorrect = $key === $customer['passwordResetHash'];
} else {
    $id = $database->user_name2id($name);
    $user = $database->user_get_data($id);
    $keyCorrect = $key === $user['passwordResetHash'];
}

switch ($_REQUEST['a'])
{
    case "request":
        Kimai_Logger::logfile("password reset: " . $name . ($is_customer ? " as customer" : " as user"));
        break;

    // Show password reset page
    default:
        $view->assign('devtimespan', '2006-' . date('y'));
        $view->assign('keyCorrect', $keyCorrect);
        $view->assign('requestData', [
            'key' => $key,
            'name' => $name
        ]);

        echo $view->render('login/forgotPassword.php');
        break;
}
