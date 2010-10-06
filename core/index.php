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

/**
 * Show an login window or process the login request. On succes the user
 * will be redirected to core/kimai.php.
 */

if (!isset($_REQUEST['a'])) $_REQUEST['a'] = '';

if (!isset($_POST['name']) || is_array($_POST['name'])) {
    $name = ""; 
} else { 
    $name = $_POST['name'];
}

if (!isset($_POST['password']) || is_array($_POST['password'])) {
    $password = "";
} else {
    $password = $_POST['password'];
}

if (!isset($_POST['database']) || is_array($_POST['database'])) {
    $database = ""; 
} else { 
    $database = $_POST['database'];
    setcookie ("kimai_db",$database);
}


ob_start();

// =============================
// = Smarty (initialize class) =
// =============================
require_once('libraries/smarty/Smarty.class.php');
$tpl = new Smarty();
$tpl->template_dir = 'templates/';
$tpl->compile_dir  = 'compile/';

// =====================
// = standard includes =
// =====================
require('includes/basics.php');


// =========================
// = authentication method =
// =========================
require(WEBROOT.'auth/kimai.php');
$authPlugin = new KimaiAuth();

// =====================================
// = send kimai-global-array to smarty =
// =====================================
$tpl->assign('kga', $kga);

// ==========================
// = installation required? =
// ==========================
$ready = @mysql_query(sprintf("select count(*) FROM %susr;",$kga['server_prefix']));
if (!$ready) { 
    $tpl->assign('devtimespan', '2006-'.date('y'));
    if (isset($_REQUEST['disagreedGPL'])) {
        $tpl->assign('disagreedGPL', 1);
    } else {
        $tpl->assign('disagreedGPL', 0);
    }
    $tpl->display('install/welcome.tpl');
    ob_end_flush();
    exit;
}

// ===================================
// = current database setup correct? =
// ===================================
checkDBversion(".");

// =========================
// = User requested logout =
// =========================
if ($_REQUEST['a']=="logout") {
    setcookie ("kimai_key","0"); 
    setcookie ("kimai_usr","0");    
}

// ===========================
// = User already logged in? =
// ===========================
if (isset($_COOKIE['kimai_usr']) && isset($_COOKIE['kimai_key']) && $_COOKIE['kimai_usr']!='0' && $_COOKIE['kimai_key']!='0' && !$_REQUEST['a']=="logout") {
    if (get_seq($_COOKIE['kimai_usr']) == $_COOKIE['kimai_key']) { 
        header("Location: core/kimai.php");
        exit;
    }
}

// ==============================================
// = Login active? If not redirect to interface =
// ==============================================
get_global_config();
if (!$kga['conf']['login']) {
    header("Location: core/kimai.php");
    exit;
}

// ==============================================
// = Is the client really a browser?? (or IE ;) =
// ==============================================
$tpl->assign('browser', get_agent());

// ===========================
// = Send HEADER information =
// ===========================
$tpl->display('login/header.tpl');


// ======================================
// = if possible try an automatic login =
// ======================================
if ($authPlugin->autoLoginPossible() && $authPlugin->performAutoLogin($userId)) {

  if ($userId === false) {
    $userId   = usr_create(array(
                'usr_name' => $name,
                'usr_grp' => $authPlugin->getDefaultGroupId(),
                'usr_sts' => 2,
                'usr_active' => 1
              ));
  }
  $userData = usr_get_data($userId);

  $keymai=random_code(30);        
  setcookie ("kimai_key",$keymai);
  setcookie ("kimai_usr",$userData['usr_name']);

  loginSetKey($userId,$keymai);

  header("Location: core/kimai.php");
}

// =================================================================
// = processing login and displaying either login screen or errors =
// =================================================================

switch($_REQUEST['a']){

case "checklogin":
    $name = htmlspecialchars(trim($name));
    
    $is_customer = is_customer_name($name);
    
    logfile("login: " . $name. ($is_customer?" as customer":" as user"));

    if ($is_customer) {
      // perform login of customer
      $passCrypt = md5($kga['password_salt'].$password.$kga['password_salt']);
      $result = @mysql_query(sprintf("SELECT * FROM %sknd WHERE knd_name ='%s';",$kga['server_prefix'],$name));
      $row    = @mysql_fetch_assoc($result);
      $id      = $row['knd_ID'];
      $user    = $row['knd_name'];
      $pass    = $row['knd_password'];
      // TODO: add BAN support
      if ( $name==$user && $pass==$passCrypt && $user!='' && $pass!='') { 
        $keymai=random_code(30);        
        setcookie ("kimai_key",$keymai);
        setcookie ("kimai_usr",'knd_'.$user);
        @mysql_query(sprintf("UPDATE %sknd SET knd_secure='%s' WHERE knd_name='%s';",$kga['server_prefix'],$keymai,$user));
        header("Location: core/kimai.php");
      }
      else {
        setcookie ("kimai_key","0"); setcookie ("kimai_usr","0");
        $tpl->assign('headline', $kga['lang']['accessDenied']); 
        $tpl->assign('message', $kga['lang']['wrongPass']);
        $tpl->assign('refresh', '<meta http-equiv="refresh" content="5;URL=index.php">');
        $tpl->display('misc/error.tpl');
      }
    }
    else
    {
      // perform login of user
      if ($authPlugin->authenticate($name,$password,$userId)) {
        
        if ($userId === false) {
          $userId   = usr_create(array(
                      'usr_name' => $name,
                      'usr_grp' => $authPlugin->getDefaultGroupId(),
                      'usr_sts' => 2,
                      'usr_active' => 1
                    ));
        }

        $userData = usr_get_data($userId);

        if ($userData['ban'] < ($kga['conf']['loginTries']) ||
            (time() - $userData['banTime']) > $kga['conf']['loginBanTime']) {

          // logintries not used up OR
          // bantime is over
          // => grant access

          $keymai=random_code(30);        
          setcookie ("kimai_key",$keymai);
          setcookie ("kimai_usr",$userData['usr_name']);

          loginSetKey($userId,$keymai);

          header("Location: core/kimai.php");
        } else {
          // login attempt even though logintries are used up and bantime is not over => deny
          setcookie ("kimai_key","0"); setcookie ("kimai_usr","0");
          loginUpdateBan($userId);

          $tpl->assign('headline', $kga['lang']['banned']);
          $tpl->assign('message', $kga['lang']['tooManyLogins']); 
          $tpl->assign('refresh', '<meta http-equiv="refresh" content="5;URL=index.php">');
          $tpl->display('misc/error.tpl');
        }
      }
      else {
        // wrong username/password => deny
        setcookie ("kimai_key","0"); setcookie ("kimai_usr","0");
        if ($userId !== false)
          loginUpdateBan($userId,true);

        $tpl->assign('headline', $kga['lang']['accessDenied']); 
        $tpl->assign('message', $kga['lang']['wrongPass']);
        $tpl->assign('refresh', '<meta http-equiv="refresh" content="5;URL=index.php">');
        $tpl->display('misc/error.tpl');      
      }
    }
break; 

// ============================================
// = Show login panel depending on (demo)mode =
// ============================================
default:
    // ======================================
    // = Selectbox for additional databases =
    // ======================================
    if (isset($_COOKIE['kimai_db']) && $_COOKIE['kimai_db'] == true) {
        $db_num = $_COOKIE['kimai_db'];
    } else {
        $db_num = 0;
    }
    $selectbox = "";
    if (isset($server_ext_database[0]) && $server_ext_database[0] == true) { 
        $selectbox .= "\n<select name='database'>";
        $selectbox .= "\n<option value='0'";
        if ($db_num == 0) {
            $selectbox .= " selected='selected'";
        }
        $selectbox .= sprintf(">%s</option>",$server_verbose);
        $loops = count($server_ext_database);
        for ($ext=0; $ext<$loops; $ext++) {
            $selectbox .= "\n<option value='" .($ext+1). "'";
            if ($db_num == $ext+1) {
                $selectbox .= " selected='selected'";
            }
            $selectbox .= ">".$server_ext_verbose[$ext]."</option>";
        }
        $selectbox .= "\n</select>";
    }
    $tpl->assign('selectbox', $selectbox);

    $tpl->assign('devtimespan', '2006-'.date('y'));

    $tpl->display('login/panel.tpl');
break;
}
if(isset($link))
	mysql_close($link); 
ob_end_flush();
?>
