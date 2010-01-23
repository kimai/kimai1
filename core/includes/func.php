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
 * along with Kimai; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 */

// Require database layer functions
// require('db_layer_pdo.php');
if (isset($kga['server_conn']))
  require("db_layer_".$kga['server_conn'].".php");


function kickUser() {
    die("<script type='text/javascript'>window.location.href = '../index.php?a=logout';</script>");
}

/**
 * returns formatted time string -> h:mm
 * input: number of seconds 
 *
 * @param integer $sek seconds to extract the time from
 * @return string
 * @author th
 */
function intervallApos($sek) {
  if (is_array($sek)) {
    $arr = array();
    foreach ($sek as $key=>$value)
    {
      $arr[$key] = intervallApos($value);
    }
    return $arr;
  }
  else
    return sprintf('%d:%02d', $sek / 3600, $sek / 60 % 60);
}

/**
 * returns formatted time string -> h:mm:ss
 * input: number of seconds 
 *
 * @param integer $sek seconds to extract the time from
 * @return string
 * @author th
 */
function intervallColon($sek) {
    return sprintf('%d:%02d:%02d', $sek / 3600, $sek / 60 % 60, $sek % 60);
}

/**
 * returns hours, minutes and seconds as array
 * input: number of seconds
 *
 * @param integer $sek seconds to extract the time from
 * @return array
 * @author th
 */
function hourminsec($sek) {  
    $i['h']   = $sek / 3600 % 24;
    $i['i']   = $sek / 60 % 60; 
    $i['s']   = $sek % 60;
    return $i;
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
 * returns array of language files
 *
 * @param none
 * @return array
 * @author unknown/th
 */
function langs(){
    $arr_files = array();
    $arr_files[0] = "";
    $i=1;
    $handle = opendir(WEBROOT.'/language/');
        while (false !== ($readdir = readdir($handle))) {
            if ($readdir != '.' && $readdir != '..' && substr($readdir,0,1) != '.'  && $readdir != ("index.php") ) {
                $readdir = str_replace(".php", "", $readdir);
                $arr_files[$i] = $readdir;
                $i++;
            }
        }
    return isset($arr_files)?$arr_files:false;
    closedir($handle);
}


/**
 * returns array for smarty's html_options funtion
 *
 * <pre>
 * returns:
 * [0] -> pct/evt names
 * [1] -> values as IDs
 * </pre>
 *
 * @param string either 'pct' or 'evt'
 * @return array
 * @author th
 */
function makeSelectBox($subject,$user){
  
    global $kga;

    switch ($subject) {
        case('pct'):
            $arr_pct = get_arr_pct($user);
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
        case('evt'):
            $arr_evt = get_arr_evt($user);
            $i=0;
            foreach ($arr_evt as $evt) {
                if ($evt['evt_visible']) {
                    $sel[0][$i] = $evt['evt_name'];
                    $sel[1][$i] = $evt['evt_ID'];
                    $i++;
                }
            }
    break;
        case('knd'):
            $arr_knd = get_arr_knd($user);
            $i=0;
            foreach ($arr_knd as $knd) {
                if ($knd['knd_visible']) {
                    $sel[0][$i] = $knd['knd_name'];
                    $sel[1][$i] = $knd['knd_ID'];
                    $i++;
                }
            }
    break;
        case('grp'):
            $arr_grp = get_arr_grp();
            $i=0;
            foreach ($arr_grp as $grp) {
                if (!$grp['grp_trash']) {
                    $sel[0][$i] = $grp['grp_name'];
                    $sel[1][$i] = $grp['grp_ID'];
                    $i++;
                }
            }
    break;

    }

    return $sel;
    
}


/**
 * returns list of projects and their time summary within zef_entry timespace as array
 *
 * OLD VERSION THAT MERGES TWO QUERYS - bad bad stuff ... 
 * TODO: [tom] revise with join query!
 *
 * @param integer $group ID of group in table grp
 * @param integer $user ID of user in table usr
 * @param integer $in start time in unix seconds
 * @param integer $out end time in unix seconds
 * @global array $kga kimai-global-array
 * @return array
 * @author th
 */
function get_arr_pct_with_time($group,$user,$in,$out) {
    global $kga; 
    //TODO: [tom] Functions results with 1 query
    $arr_pcts = get_arr_pct($group);
    $arr_time = get_arr_time_pct($user,$in,$out);
    //TODO END
    $arr = array(); 
    
    $i=0;
    foreach ($arr_pcts as $pct) {
        $arr[$i]['pct_ID']      = $pct['pct_ID'];
        $arr[$i]['knd_ID']      = $pct['knd_ID'];
        $arr[$i]['pct_name']    = $pct['pct_name'];
		$arr[$i]['pct_comment'] = $pct['pct_comment'];
        $arr[$i]['knd_name']    = $pct['knd_name'];
        $arr[$i]['pct_visible'] = $pct['pct_visible'];
        $arr[$i]['zeit']        = @intervallApos($arr_time[$pct['pct_ID']]);
        $i++;
    }
    
    return $arr;
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
    global $kga;
    
    // check for versions before 0.7.13r96
    $installedVersion = get_DBversion();
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

/**
 * returns browser name
 * 
 * returns: "Opera", "msie", "Safari", "Mozilla" or "?"
 *
 * TODO: [togi] check if this still is really needed anywhere
 * Smarty has a browser value by itself!!!
 *
 *
 * @return string 
 * @author th
 */
function get_agent() {
    @$agent=$_SERVER["HTTP_USER_AGENT"];
    if(eregi("opera",$agent)) $browser = "Opera";
    else if(eregi("msie",$agent)) $browser = "msie";
    else if(eregi("Safari",$agent)) $browser = "Safari";
    else if(eregi("mozilla",$agent) && !eregi("Safari",$agent)) $browser = "Mozilla";
    else $browser = "?";
    return $browser;
}

/**
 * writes errors during install or update to the logfile stored in temporary
 * 
 * @param string $value message
 * @param string $path relative path to temporary directory
 * @param boolean $success
 * @author th
 */
// function logfile($value,$success=1) {
function logfile($value) {
    
    $value = str_replace("\n", "", $value);
    $value = str_replace("  ", " ", $value);
    $value = str_replace("  ", " ", $value);
    
    $logdatei=fopen(WEBROOT."temporary/logfile.txt","a");
    
    // $success = ($success==1) ? "success: " : "error:   ";
    // fputs($logdatei, date("d.m.Y, H:i:s",time()) .", " . $success . $value ."\n");
    fputs($logdatei, date("[d.m.Y H:i:s] ",time()) . $value ."\n");
    fclose($logdatei);
}

/**
 * preprocess shortcut for date entries
 *
 * allowed shortcut formats are shown in the dialogue for edit timesheet entries (click the "?")
 * 
 * @param string $date shortcut date
 * @return string 
 * @author th
 */
function expand_date_shortcut($date) {
    
    $date  = str_replace(" ","",strip_tags($date));

    $return = $date;
    
    $length = strlen($date);
    
    switch ($length) {
        case 1:
            $return = "0" . $date .".". date("m") .".". date("Y");
        break;        
        
        case 2:
            $return = $date .".". date("m") .".". date("Y");
        break;
        
        case 4: 
            $return = substr($date,0,2) .".". substr($date,2,2) .".". date("Y");
        break;
    
        case 6:
            $return = substr($date,0,2) .".". substr($date,2,2);
            $year=(int)substr($date,4,2);
            if ($year>70) {
				$year = "19".$year;
			} else {
				if ($year<10) {
					$year = "200".$year;	
				} else {
					$year = "20".$year;
				}
			}
            $return = "$return.$year";
        break;
    }
    
    if (!ereg("([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{2,4})",$return))  $return = false;
    return $return;
}

/**
 * preprocess shortcut for time entries
 *
 * allowed shortcut formats are shown in the dialogue for edit timesheet entries (click the "?")
 * 
 * @param string $date shortcut time
 * @return string 
 * @author th
 */
function expand_time_shortcut($time) {
    $time  = str_replace(" ","",$time);
    $time  = str_replace(":","",$time);
    $return = $time;
    
    $length = strlen($time);
    
    switch ($length) {
        case 1:
            $return = "0${time}:00:00";
        break;
        
        case 2:
            if ((int)$time<24) $return = "${time}:00:00";
            else $return = false;
        break;
        
        case 4:
            $hours = substr($time,0,2);
            $minutes = substr($time,2,2);
            if ((int)$hours<24 && (int)$minutes<60) $return = "${hours}:${minutes}:00";
            else $return = false;
        break;
        
        case 6:
            $hours = substr($time,0,2);
            $minutes = substr($time,2,2);
            $seconds = substr($time,4,2);
            if ((int)$hours<24 && (int)$minutes<60 && (int)$seconds<60) $return = "${hours}:${minutes}:${seconds}";
            else $return = false;
        break;
        
    }
    
    if (!ereg("([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})",$return)) $return = false;
    return $return;
}

/**
 * check if a parset string matches with the following time-formatting: 20.08.2008-19:00:00 
 * returns true if string is ok
 *
 * @param string $timestring
 * @return boolean 
 * @author th
 */
function check_time_format($timestring) {
    if (!ereg("([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{2,4})-([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})",$timestring)) {
        return false; // WRONG format
    } else {
        $ok = 1;
        
        $hours   = substr($timestring,11,2);
        $minutes = substr($timestring,14,2);
        $seconds = substr($timestring,17,2);
        
        if ((int)$hours>=24)  $ok=0;
        if ((int)$minutes>=60) $ok=0;
        if ((int)$seconds>=60) $ok=0;
        
        logfile("timecheck: ".$ok);
        
        $day   = substr($timestring,0,2);
        $month = substr($timestring,3,2);
        $year  = substr($timestring,6,4);        
        
        if (!checkdate( (int)$month, (int)$day, (int)$year) ) $ok=0;
        
        logfile("time/datecheck: ".$ok);
        
        if ($ok) {
            return true;
        } else {
            return false;
        }
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
 * returns last day (..30..31) of given month
 *
 * @param integer $month
 * @param integer $year
 * @return integer number of day
 *
 * @author http://lutrov.com/blog/php-last-day-of-the-month-calculation/
 */
function lastday($month = '', $year = '') {
   if (empty($month)) {
      $month = date('m');
   }
   if (empty($year)) {
      $year = date('Y');
   }
   $result = strtotime("{$year}-{$month}-01");
   $result = strtotime('-1 second', strtotime('+1 month', $result));
   return date('d', $result);
}

/**
 * lookup if either in or out timespace fields has to be highlighted red
 *
 * @param integer $in unix seconds
 * @param integer $out unix seconds
 * @return array
 *
 * @author th
 */
function timespace_warning($in,$out) {
 
    if ($in>time()) {
        $time_err['in'] = true;
    } else {
        $time_err['in'] = false;
    }

    if ($out!=0 && $out<time()) {
        $time_err['out'] = true;
    } else {
        $time_err['out'] = false;
    }
  
    if (!$time_err['out'] && !$time_err['in']) { return 0; } // both correct
    if ( $time_err['out'] && !$time_err['in']) { return 1; } // out wrong
    if (!$time_err['out'] &&  $time_err['in']) { return 2; } // in wrong
    if ( $time_err['out'] &&  $time_err['in']) { return 3; } // both wrong
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

	if (($zef_data['in'] == 0) && ($zef_data['out'] == 0) && ($zef_data['diff'] == 0)) {
	
		$zef_final_data['zef_pctID']        = $zef_data['pct_ID']; 
	    $zef_final_data['zef_evtID']        = $zef_data['evt_ID'];
	    $zef_final_data['zef_location']     = $zef_data['zlocation'];
	    $zef_final_data['zef_trackingnr']   = $zef_data['trackingnr'];
	    $zef_final_data['zef_comment']      = $zef_data['comment'];
	    $zef_final_data['zef_comment_type'] = $zef_data['comment_type'];
	    $zef_final_data['zef_rate']         = $zef_data['rate'];
      $zef_final_data['zef_cleared']      = $zef_data['cleared'];
    
	    return zef_edit_record($id,$zef_final_data);
    
	} else {

		$zef_final_data['zef_pctID']        = $zef_data['pct_ID']; 
	    $zef_final_data['zef_evtID']        = $zef_data['evt_ID'];
	    $zef_final_data['zef_location']     = $zef_data['zlocation'];
	    $zef_final_data['zef_trackingnr']   = $zef_data['trackingnr'];
	    $zef_final_data['zef_comment']      = $zef_data['comment'];
	    $zef_final_data['zef_comment_type'] = $zef_data['comment_type'];
	    $zef_final_data['zef_in']           = $zef_data['in'];
	    $zef_final_data['zef_out']          = $zef_data['out'];
	    $zef_final_data['zef_time']         = $zef_data['diff'];
	    $zef_final_data['zef_rate']         = $zef_data['rate'];
      $zef_final_data['zef_cleared']         = $zef_data['cleared'];
    
	    return zef_edit_record($id,$zef_final_data);

	}

}





// http://www.alfasky.com/?p=20
// This little function will help you truncate a string to a specified 
// length when copying data to a place where you can only store or display 
// a limited number of characters, then it will append “…” to it showing 
// that some characters were removed from the original entry.

function addEllipsis($string, $length, $end='…')
{
  if (strlen($string) > $length)
  {
    $length -=  strlen($end);  // $length =  $length - strlen($end);
    $string  = substr($string, 0, $length);
    $string .= $end;  //  $string =  $string . $end;
  }
  return $string;
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
 * along with Kimai; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
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

if (!defined('WEBROOT')) {
    define('WEBROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
}

?>
EOD;
        
  fputs($file, $config);
  fclose($file);
  return true;
}


?>