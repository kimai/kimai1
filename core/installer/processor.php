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
 * Handle all AJAX calls from the installer.
 */


// from php documentation at http://www.php.net/manual/de/function.ini-get.php
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}


// stolen somewhere ... please forgive me - i don't know who wrote this .... O-o
function getpass() {
    $newpass = "";
    $laenge=10;
    $string="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    mt_srand((double)microtime()*1000000);

    for ($i=1; $i <= $laenge; $i++) {
        $newpass .= substr($string, mt_rand(0,strlen($string)-1), 1);
    }

    return $newpass;
}

$axAction = strip_tags($_REQUEST['axAction']);

$javascript="";
$errors=0;

switch ($axAction) {


    /**
     * Check for the requirements of Kimai:
     *  - PHP major version >= 5
     *  - MySQL extension available
     *  - memory limit should be at least 20 MB for reliable PDF export
     */
    case "checkRequirements":
       if (version_compare(PHP_VERSION, '5.2') < 0) {
           $errors++;
           $javascript .= "$('div.sp_phpversion').addClass('fail');";
       }

       if (!extension_loaded('mysql')) {
           $errors++;
           $javascript .= "$('div.sp_mysql').addClass('fail');";
       }

       if (get_magic_quotes_gpc() == 1 || get_magic_quotes_runtime() == 1) {
           $errors++;
           $javascript .= "$('div.sp_magicquotes').addClass('fail');";
       }

       if (return_bytes(ini_get('memory_limit')) < 20000000) {
           $javascript .= "$('div.sp_memory').addClass('fail');";
       }


        if (empty($javascript)) {
            $javascript = "$('#installsteps button.sp-button').hide();";
        }

        if (!$errors) {
            $javascript .= "$('#installsteps button.proceed').show();";
        }

        $javascript .= "resetRequirementsIndicators();".$javascript;
        echo $javascript;

      break;

    /**
     * Check access rights to autoconf.php, the logfile, the temporary folder
     * and all compile folders (also for the extensions).
     */
    case "checkRights":
        if (!$fp = @fopen("../includes/autoconf.php", "w")) {
            $errors++;
            $javascript .= "$('span.ch_autoconf').addClass('fail');";
        }

        if (!$fp = @fopen("../temporary/logfile.txt", "w")) {
            $errors++;
            $javascript .= "$('span.ch_logfile').addClass('fail');";
        }

        $filename = "%%" . getpass();

        if (!$fp = @fopen("../compile/".$filename ."_testfile.txt", "w")) {
            $errors++;
            $javascript .= "$('span.ch_compile').addClass('fail');";
        }
        if (!$fp = @fopen("../extensions/ki_timesheets/compile/".$filename ."_testfile.txt", "w")) {
            $errors++;
            $javascript .= "$('span.ch_compile_tsext').addClass('fail');";
        }

        if (!$fp = @fopen("../extensions/ki_adminpanel/compile/".$filename ."_testfile.txt", "w")) {
            $errors++;
            $javascript .= "$('span.ch_compile_apext').addClass('fail');";
        }

        if (!$fp = @fopen("../extensions/ki_expenses/compile/".$filename ."_testfile.txt", "w")) {
            $errors++;
            $javascript .= "$('span.ch_compile_epext').addClass('fail');";
        }

        if (!$fp = @fopen("../extensions/ki_export/compile/".$filename ."_testfile.txt", "w")) {
            $errors++;
            $javascript .= "$('span.ch_compile_xpext').addClass('fail');";
        }

        if (!$fp = @fopen("../extensions/ki_budget/compile/".$filename ."_testfile.txt", "w")) {
            $errors++;
            $javascript .= "$('span.ch_compile_bgtext').addClass('fail');";
        }

         if (!$fp = @fopen("../extensions/ki_invoice/compile/".$filename ."_testfile.txt", "w")) {
             $errors++;
             $javascript .= "$('span.ch_compile_ivext').addClass('fail');";
         }




        if (!$fp = @fopen("../temporary/".$filename ."_testfile.txt", "w")) {
            $errors++;
            $javascript .= "$('span.ch_temporary').addClass('fail');";
        }

        if ($errors) {
            $javascript .= "$('span.ch_correctit').fadeIn(500);";
        } else {
            $javascript = "$('#installsteps button.cp-button').hide();$('#installsteps button.proceed').show();";
        }

        echo $javascript;
    break;


////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Create the autoconf.php file.
     */
    case ("write_config"):
    include("../includes/func.php");
     // special characters " and $ are escaped
    $database    = addcslashes($_REQUEST['database'],'"$');
    $hostname    = addcslashes($_REQUEST['hostname'],'"$');
    $username    = addcslashes($_REQUEST['username'],'"$');
    $password    = addcslashes($_REQUEST['password'],'"$');
    $db_layer    = $_REQUEST['db_layer'];
    $db_type     = $_REQUEST['db_type'];
    $prefix      = addcslashes($_REQUEST['prefix'],'"$');
    $lang        = $_REQUEST['lang'];
    $salt        = createPassword(20);

    write_config_file($database,$hostname,$username,$password,$db_layer,$db_type,$prefix,$lang,$salt);

    break;
    
    /**
     * Create the database.
     */
    case ("make_database");

    error_log("make database ###################");

        $database     = $_REQUEST['database'];
        $hostname     = $_REQUEST['hostname'];
        $username     = $_REQUEST['username'];
        $password     = $_REQUEST['password'];
        $server_type  = $_REQUEST['db_type'];
        $db_layer     = $_REQUEST['db_layer'];

        $db_error = false;
        $result = false;

        if ($db_layer == "pdo") {

                $pdo_dsn = $server_type . ':host=' . $hostname;
                try {
                    $pdo_conn = @new PDO($pdo_dsn, $username, $password);
                    $pdo_query = $pdo_conn->prepare("CREATE DATABASE `" . $database . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
                    $result = $pdo_query->execute();
                } catch (PDOException $pdo_ex) {
                    error_log('PDO CONNECTION FAILED: ' . $pdo_ex->getMessage());
                    $db_error = true;
                }

        } else {

                $con = mysql_connect($hostname,$username,$password);
                $query = "CREATE DATABASE `" . $database . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
                $result = mysql_query($query);

        }


        if ($result != false) {
            echo "1"; // <-- hat geklappt
        } else {
            echo "0"; // <-- schief gegangen
        }

    break;

}

?>