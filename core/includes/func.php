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
 * Functions defined here are not directly accessing the database.
 */

/**
 * Kill the current users session by redirecting him to the logout page.
 */
function kickUser() {
    die("<script type='text/javascript'>window.location.href = '../index.php?a=logout';</script>");
}

/**
 * returns array of subdirectorys - needed for skin selector
 *
 * @param string $dir Directory to list subdirectorys from
 * @return array
 * @author unknown
 */
function ls($dir){
    $arr_subfolders = array();
    $i=0;
    $handle = opendir($dir);
        while (false !== ($readdir = readdir($handle))) {
            if ($readdir != '.' && $readdir != '..' && substr($readdir,0,1) != '.'  && is_dir("${dir}/${readdir}") ) {
                $arr_subfolders[$i] = $readdir;
                $i++;
            }
        }
    return isset($arr_subfolders)?$arr_subfolders:false;
    closedir($handle);
}

/**
 * Get a list of available time zones. This is directly taken from PHP.
 * 
 * @return array of timezone names
 */
function timezoneList() {
  return DateTimeZone::listIdentifiers();
}


/**
 * Returns array for smarty's html_options funtion.
 *
 * <pre>
 * returns:
 * [0] -> pct/evt names
 * [1] -> values as IDs
 * </pre>
 *
 * @param string either 'pct', 'evt', 'knd', 'grp'
 * @return array
 * @author th, sl, kp
 */
function makeSelectBox($subject,$groups,$selection=null){

    global $kga, $database;

    $sel = array();
    $sel[0] = array();
    $sel[1] = array();

    switch ($subject) {
        case 'pct':
            $arr_pct = $database->get_arr_pct($groups);
            $i=0;
            foreach ($arr_pct as $pct) {
                if ($pct['pct_visible']) {
                    if ($kga['conf']['flip_pct_display']) {
                        $sel[0][$i] = $pct['knd_name'] . ": " . $pct['pct_name'];
                        if ($kga['conf']['pct_comment_flag']) {
                            $sel[0][$i] .= "(" . $pct['pct_comment'] .")" ;
                        }
                    } else {
                        $sel[0][$i] = $pct['pct_name'] . " (" . $pct['knd_name'] . ")";
                        if ($kga['conf']['pct_comment_flag']) {
                            $sel[0][$i] .=  "(" . $pct['pct_comment'] .")";
                        }
                    }
                    $sel[1][$i] = $pct['pct_ID'];
                    $i++;
                }
            }
            break;

        case 'evt':
            $arr_evt = $database->get_arr_evt($groups);
            $i=0;
            foreach ($arr_evt as $evt) {
                if ($evt['evt_visible']) {
                    $sel[0][$i] = $evt['evt_name'];
                    $sel[1][$i] = $evt['evt_ID'];
                    $i++;
                }
            }
            break;

        case 'knd':
            $arr_knd = $database->get_arr_knd($groups);
            $i=0;
            $selectionFound = false;
            if(is_array($arr_knd)) {
	            foreach ($arr_knd as $knd) {
	                if ($knd['knd_visible']) {
	                    $sel[0][$i] = $knd['knd_name'];
	                    $sel[1][$i] = $knd['knd_ID'];
	                    $i++;
	                    if ($selection == $knd['knd_ID'])
	                      $selectionFound = true;
	                }
	            }
            }
            if ($selection != null && !$selectionFound) {
              $data = $database->knd_get_data($selection);
              $sel[0][$i] = $data['knd_name'];
              $sel[1][$i] = $data['knd_ID'];
            }
            break;

        case 'grp':
            $arr_grp = $database->get_arr_grp();
            $i=0;
            foreach ($arr_grp as $grp) {
                if (!$grp['grp_trash']) {
                    $sel[0][$i] = $grp['grp_name'];
                    $sel[1][$i] = $grp['grp_ID'];
                    $i++;
                }
            }
            break;

        default:
            // TODO leave default options empty ???
            break;
    }

    return $sel;

}


/**
 * returns a random code with given length
 *
 * @global integer $length length of the code
 * @return array
 * @author th
 */
function random_code($length) {
    $code = "";
    $string="ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz0123456789";
    mt_srand((double)microtime()*1000000);
    for ($i=1; $i <= $length; $i++) {
        $code .= substr($string, mt_rand(0,strlen($string)-1), 1);
    }
    return $code;
}

/**
 * returns a random number with X digits
 *
 * @global integer $length digit count of number
 * @return array
 * @author th
 */
function random_number($length) {
    $number = "";
    $string="0123456789";
    mt_srand((double)microtime()*1000000);
    for ($i=1; $i <= $length; $i++) {
        $number .= substr($string, mt_rand(0,strlen($string)-1), 1);
    }
    return $number;
}

/**
 * checks if the database structure needs to be updated for new Kimai version.
 * if yes the function redirects to /admin/updater.php
 *
 * @param string $path path to admin dir relative to the document that calls this function (usually "." or "..")
 * @global array $kga kimai-global-array
 * @return array
 * @author th
 */
function checkDBversion($path) {
    global $kga, $database;

    // check for versions before 0.7.13r96
    $installedVersion = $database->get_DBversion();
    $checkVersion = $installedVersion[0];
    $checkVersion = "$checkVersion";
    if ($checkVersion != $kga['version']) {
        header("Location: $path/updater.php");
        exit;
    }

    // the check for revision is much simpler ...
    if ( (int)$installedVersion[1] < (int)$kga['revision']) {
        header("Location: $path/updater.php");
        exit;
    }
}

function convert_time_strings($in,$out) {

    $explode_in  = explode("-",$in);
    $explode_out = explode("-",$out);

    $date_in  = explode(".",$explode_in[0]);
    $date_out = explode(".",$explode_out[0]);

    $time_in  = explode(":",$explode_in[1]);
    $time_out = explode(":",$explode_out[1]);

    $time['in']   = mktime($time_in[0], $time_in[1], $time_in[2], $date_in[1], $date_in[0], $date_in[2]);
    $time['out']  = mktime($time_out[0],$time_out[1],$time_out[2],$date_out[1],$date_out[0],$date_out[2]);
    $time['diff'] = (int)$time['out']-(int)$time['in'];

    return $time;
}

/**
 * read a cookie or return a default value, if cookie is not set
 *
 * @param string $cookie_name
 * @param mixed $default the value, which will be returned, when the cookie is not set
 * @return mixed
 *
 * @author rvock
 */
function get_cookie($cookie_name, $default=null) {
    return isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : $default;
}


/**
 * check if there are 0s (erroneuos entries) in the zef data while editing and prevent overwriting old data in this case
 *
 * @param int $id the id of the entry to be edited
 * @param array $zef_data
 * @return boolean the return value of the actual editing function
 *
 * @author Oleg
 */
function check_zef_data($id, $zef_data) {
  global $database;
  
  $zef_final_data['zef_usrID']        = $zef_data['zef_usrID'];
  $zef_final_data['zef_pctID']        = $zef_data['pct_ID'];
  $zef_final_data['zef_evtID']        = $zef_data['evt_ID'];
  $zef_final_data['zef_location']     = $zef_data['zlocation'];
  $zef_final_data['zef_trackingnr']   = $zef_data['trackingnr'];
  $zef_final_data['zef_description']  = $zef_data['description'];
  $zef_final_data['zef_comment']      = $zef_data['comment'];
  $zef_final_data['zef_comment_type'] = $zef_data['comment_type'];
  $zef_final_data['zef_rate']         = $zef_data['rate'];
  $zef_final_data['zef_budget']       = $zef_data['budget'];
  $zef_final_data['zef_approved']     = $zef_data['approved'];
  $zef_final_data['zef_status']       = $zef_data['status'];
  $zef_final_data['zef_billable']     = $zef_data['billable'];
  $zef_final_data['zef_description']  = $zef_data['description'];
  $zef_final_data['zef_cleared']      = $zef_data['cleared'];

  if (($zef_data['in'] != 0) || ($zef_data['out'] != 0) || ($zef_data['diff'] != 0)) {
    $zef_final_data['zef_in']           = $zef_data['in'];
    $zef_final_data['zef_out']          = $zef_data['out'];
    $zef_final_data['zef_time']         = $zef_data['diff'];
  }

  return $database->zef_edit_record($id,$zef_final_data);

}


// based on http://wiki.jumba.com.au/wiki/PHP_Generate_random_password
function createPassword($length) {
        $chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $i = 0;
        $password = "";
        while ($i <= $length) {
                $password .= $chars{mt_rand(0,strlen($chars)-1)};
                $i++;
        }
        return $password;
}

function write_config_file($database,$hostname,$username,$password,$db_layer,$db_type,$prefix,$lang,$salt) {
  $file=fopen(realpath(dirname(__FILE__)).'/autoconf.php','w');
  if (!$file) return false;

$config=<<<EOD
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

// This file was automatically generated by the installer

\$server_hostname = "$hostname";
\$server_database = "$database";
\$server_username = "$username";
\$server_password = "$password";
\$server_conn     = "$db_layer";
\$server_type     = "$db_type";
\$server_prefix   = "$prefix";
\$language        = "$lang";
\$password_salt   = "$salt";

?>
EOD;

  fputs($file, $config);
  fclose($file);
  return true;
}




/**
 * get in and out unix seconds of specific user
 *
 * <pre>
 * returns:
 * [0] -> in
 * [1] -> out
 * </pre>
 *
 * @param string $user ID of user
 * @global array $kga kimai-global-array
 * @return array
 * @author th
 */

// checked

function get_timespace() {
    global $kga, $conn;

    $timespace = array(null,null);
    
    if (isset($kga['usr'])) {

        $timespace[0] = $kga['usr']['timespace_in'];
        $timespace[1] = $kga['usr']['timespace_out'];

    }

    /* database has no entries? */
    $mon = date("n"); $day = date("j"); $Y = date("Y");
    if (!$timespace[0]) {
        $timespace[0] = mktime(0,0,0,$mon,1,$Y);
    }
    if (!$timespace[1]) {
        $timespace[1] = mktime(23,59,59,$mon,$day,$Y);
    }
    
    return $timespace;
}

function endsWith($haystack,$needle) {
  return strcmp(substr($haystack, strlen($haystack)-strlen($needle)),$needle)===0;
}


?>