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
 * Show an login window or process the login request. On success the user
 * will be redirected to core/kimai.php.
 */

if (!isset($_REQUEST['a'])) {
    $_REQUEST['a'] = '';
}

if (!isset($_POST['name']) || is_array($_POST['name'])) {
    $name = '';
} else {
    $name = $_POST['name'];
}

if (!isset($_POST['password']) || is_array($_POST['password'])) {
    $password = '';
} else {
    $password = $_POST['password'];
}

ob_start();

// =====================
// = standard includes =
// =====================
require_once 'includes/basics.php';

$view = new Zend_View();
$view->setBasePath(WEBROOT . '/templates');

// =========================
// = authentication method =
// =========================
$authClass = 'Kimai_Auth_' . ucfirst($kga['authenticator']);
if (!class_exists($authClass)) {
    $authClass = 'Kimai_Auth_' . ucfirst($kga['authenticator']);
}
/* @var Kimai_Auth_Abstract $authPlugin */
$authPlugin = new $authClass($database, $kga);

$view->assign('kga', $kga);

// ===================================
// = current database setup correct? =
// ===================================
checkDBversion('.');

// ==========================
// = installation required? =
// ==========================
$users = $database->get_users();
if (count($users) == 0) {
    $view->assign('devtimespan', '2006-' . date('y'));
    if (isset($_REQUEST['disagreedGPL'])) {
        $view->assign('disagreedGPL', 1);
    } else {
        $view->assign('disagreedGPL', 0);
    }
    echo $view->render('install/welcome.php');
    ob_end_flush();
    exit;
}

// =========================
// = User requested logout =
// =========================
$justLoggedOut = false;
if ($_REQUEST['a'] == 'logout') {
    setcookie('kimai_key', '0');
    setcookie('kimai_user', '0');
    $justLoggedOut = true;
}

// ===========================
// = User already logged in? =
// ===========================
if (isset($_COOKIE['kimai_user']) && isset($_COOKIE['kimai_key']) && $_COOKIE['kimai_user'] != '0' && $_COOKIE['kimai_key'] != '0' && !$_REQUEST['a'] == 'logout') {
    if ($database->get_seq($_COOKIE['kimai_user']) == $_COOKIE['kimai_key']) {
        header('Location: core/kimai.php');
        exit;
    }
}

// ======================================
// = if possible try an automatic login =
// ======================================
if (!$justLoggedOut && $authPlugin->autoLoginPossible() && $authPlugin->performAutoLogin($userId)) {
    if ($userId === false) {
        $userId = $database->user_create(array(
            'name' => $name,
            'globalRoleID' => $kga['user']['globalRoleID'],
            'active' => 1
        ));
        $database->setGroupMemberships($userId, array($authPlugin->getDefaultGroups()));
    }
    $userData = $database->user_get_data($userId);

    $loginKey = random_code(30);
    setcookie('kimai_key', $loginKey);
    setcookie('kimai_user', $userData['name']);

    $database->user_loginSetKey($userId, $loginKey);

    header('Location: core/kimai.php');
}

// =================================================================
// = processing login and displaying either login screen or errors =
// =================================================================

switch ($_REQUEST['a']) {

    case 'checklogin':
        $is_customer = $database->is_customer_name($name);

        Kimai_Logger::logfile('login: ' . $name . ($is_customer ? ' as customer' : ' as user'));

        if ($is_customer) {
            // perform login of customer
            $passCrypt = encode_password($password);
            $customerId = $database->customer_nameToID($name);
            $data = $database->customer_get_data($customerId);

            // TODO: add BAN support
            if ($data['password'] == $passCrypt && $name != '' && $passCrypt != '') {
                $loginKey = random_code(30);
                setcookie('kimai_key', $loginKey);
                setcookie('kimai_user', 'customer_' . $name);
                $database->customer_loginSetKey($customerId, $loginKey);
                header('Location: core/kimai.php');
            } else {
                setcookie('kimai_key', '0');
                setcookie('kimai_user', '0');
                $view->assign('headline', $kga['lang']['accessDenied']);
                $view->assign('message', $kga['lang']['wrongPass']);
                $view->assign('refresh', '<meta http-equiv="refresh" content="5;URL=index.php">');
                echo $view->render('misc/error.php');
            }
        } else {
            // perform login of user
            if ($authPlugin->authenticate($name, $password, $userId)) {
                if ($userId === false) {
                    $userId = $database->user_create(array(
                        'name' => $name,
                        'globalRoleID' => $authPlugin->getDefaultGlobalRole(),
                        'active' => 1
                    ));
                    $database->setGroupMemberships($userId, array($authPlugin->getDefaultGroups()));
                }

                $userData = $database->user_get_data($userId);

                // global configuration must be present from now on
                $database->get_global_config();

                if (!isset($kga['conf']) || !isset($kga['conf']['loginTries']) || ($userData['ban'] < ($kga['conf']['loginTries']) || (time() - $userData['banTime']) > $kga['conf']['loginBanTime'])) {

                    // login tries not used up OR bantime is over => grant access

                    $loginKey = random_code(30);
                    setcookie('kimai_key', $loginKey);
                    setcookie('kimai_user', $userData['name']);

                    $database->user_loginSetKey($userId, $loginKey);

                    header('Location: core/kimai.php');
                } else {
                    // login attempt even though logintries are used up and bantime is not over => deny
                    setcookie('kimai_key', '0');
                    setcookie('kimai_user', '0');
                    $database->loginUpdateBan($userId);

                    $view->assign('headline', $kga['lang']['banned']);
                    $view->assign('message', $kga['lang']['tooManyLogins']);
                    $view->assign('refresh', '<meta http-equiv="refresh" content="5;URL=index.php">');
                    echo $view->render('misc/error.php');
                }
            } else {
                // wrong username/password => deny
                setcookie('kimai_key', '0');
                setcookie('kimai_user', '0');
                if ($userId !== false) {
                    $database->loginUpdateBan($userId, true);
                }

                $view->assign('headline', $kga['lang']['accessDenied']);
                $view->assign('message', $kga['lang']['wrongPass']);
                $view->assign('refresh', '<meta http-equiv="refresh" content="5;URL=index.php">');
                echo $view->render('misc/error.php');
            }
        }
        break;
    
    default:
        // Show login panel
        $view->assign('devtimespan', '2006-' . date('y'));
        echo $view->render('login/panel.php');
}

ob_end_flush();
