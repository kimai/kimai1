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

// Require database layer functions
if (isset($kga['server_conn']))
  require("db_layer_".$kga['server_conn'].".php");


/**
 * Prepare all values of the array so it's save to put them into an sql query.
 * The conversion to utf8 is done here as well, if configured.
 * 
 * @param array $data Array which values are being prepared.
 * @return array The same array, except all values are being escaped correctly.
 */
function clean_data($data) {
    global $kga;   
    foreach ($data as $key => $value) {
        if ($key != "pw") { 
            $return[$key] = urldecode(strip_tags($data[$key]));
        $return[$key] = str_replace('"','_',$data[$key]);
        $return[$key] = str_replace("'",'_',$data[$key]);
        $return[$key] = str_replace('\\','',$data[$key]);
        } else {
            $return[$key] = $data[$key];
        }
    if ($kga['utf8']) $return[$key] = utf8_decode($return[$key]);
    }
    
    return $return;
}

/**
 * Kill the current users session by redirecting him to the logout page.
 */
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
 * @deprecated use formatDuration instead
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
 * @deprecated use formatDuration instead
 */
function intervallColon($sek) {
    return sprintf('%d:%02d:%02d', $sek / 3600, $sek / 60 % 60, $sek % 60);
}

/**
 * Format a duration given in seconds according to the global setting. Either
 * seconds are shown or not.
 * 
 * @param integer|array one value in seconds or an array of values in seconds
 * @return integer|array depending on the $sek param which contains the formatted duration
 * @author sl
 */
function formatDuration($sek) {
  global $kga;
  if (is_array($sek)) {
    // Convert all values of the array.
    $arr = array();
    foreach ($sek as $key=>$value)
    {
        $arr[$key] = formatDuration($value);
    }
    return $arr;
  }
  else {
    // Format accordingly.
    if ($kga['conf']['durationWithSeconds'] == 0)
      return sprintf('%d:%02d', $sek / 3600, $sek / 60 % 60);
    else
      return sprintf('%d:%02d:%02d', $sek / 3600, $sek / 60 % 60, $sek % 60);
  }
}

/**
 * Format a currency or an array of currencies accordingly.
 * 
 * @param integer|array one value or an array of decimal numbers
 * @return integer|array formatted string(s)
 * @author sl
 */
function formatCurrency($number,$htmlNoWrap = true) {
  global $kga;
  if (is_array($number)) {
    // Convert all values of the array.
    $arr = array();
    foreach ($number as $key=>$value)
    {
        $arr[$key] = formatCurrency($value);
    }
    return $arr;
  }
  else {
    $value = str_replace(".", $kga['conf']['decimalSeparator'], sprintf("%01.2f",$number) );
    if ($kga['conf']['currency_first'])
      $value = $kga['currency_sign']." ".$value;
    else
      $value = $value." ".$kga['currency_sign'];

    if ($htmlNoWrap)
      return "<span style=\"white-space: nowrap;\">$value</span>";
    else
      return $value;
  }
}

/**
 * Format the annotations and only include data which the user wants to see. 
 * The array which is passed to the method will be modified.
 *
 * @param $ann array the annotation array (userid => (time, costs) )
 */
function formatAnnotations(&$ann) {
  $type = usr_get_preference('ui.sublistAnnotations');
  $userIds = array_keys($ann);

  if ($type == null)
    $type = 0;

  switch ($type) {
  case 0:
    // just time
    foreach ($userIds as $userId) {
      $ann[$userId] = formatDuration($ann[$userId]['time']);
    }
    break;
  case 1:
    // just costs
    foreach ($userIds as $userId) {
      $ann[$userId] = formatCurrency($ann[$userId]['costs']);
    }
    break;
  case 2:
  default:
    // both
    foreach ($userIds as $userId) {
      $time = formatDuration($ann[$userId]['time']);
      $costs = formatCurrency($ann[$userId]['costs']);
      $ann[$userId] =  "<span style=\"white-space: nowrap;\">$time |</span>  $costs";
    }

  }
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
    $arr_files[] = "";
    $handle = opendir(WEBROOT.'/language/');
    while (false !== ($readdir = readdir($handle))) {
      if ($readdir != '.' && $readdir != '..' && substr($readdir,0,1) != '.'
        && endsWith($readdir,'.php') ) {
        $arr_files[] = str_replace(".php", "", $readdir);
      }
    }
    closedir($handle);
    sort($arr_files);
    return $arr_files;
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
function makeSelectBox($subject,$user,$selection=null){

    global $kga;

    $sel = array();
    $sel[0] = array();
    $sel[1] = array();

    switch ($subject) {
        case 'pct':
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

        case 'evt':
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

        case 'knd':
            $arr_knd = get_arr_knd($user);
            $i=0;
            $selectionFound = false;
            foreach ($arr_knd as $knd) {
                if ($knd['knd_visible']) {
                    $sel[0][$i] = $knd['knd_name'];
                    $sel[1][$i] = $knd['knd_ID'];
                    $i++;
                    if ($selection == $knd['knd_ID'])
                      $selectionFound = true;
                }
            }
            if ($selection != null && !$selectionFound) {
              $data = knd_get_data($selection);
              $sel[0][$i] = $data['knd_name'];
              $sel[1][$i] = $data['knd_ID'];
            }
            break;

        case 'grp':
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

        default:
            // TODO leave default options empty ???
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
        $arr[$i]['zeit']        = @formatDuration($arr_time[$pct['pct_ID']]);
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
    if(strpos($agent,"opera") !== false) $browser = "Opera";
    else if(strpos($agent,"msie") !== false) $browser = "msie";
    else if(strpos($agent,"Safari") !== false) $browser = "Safari";
    else if(strpos($agent,"mozilla") !== false) $browser = "Mozilla";
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
function logfile($value) {

    $value = str_replace("\n", "", $value);
    $value = str_replace("  ", " ", $value);
    $value = str_replace("  ", " ", $value);

    $logdatei=fopen(WEBROOT."temporary/logfile.txt","a");

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

    $date  = str_replace(" ","",$date);

    // empty string can't be a time value
    if (strlen($date)==0)
      return false;

    // get the parts
    $parts = preg_split("/\./",$date);

    if (count($parts) == 0 || count($parts) > 3)
      return false;

    // check day
    if (strlen($parts[0]) == 1)
      $parts[0] = "0".$parts[0];

    // check month
    if (!isset($parts[1]))
      $parts[1] = date("m");
    else if (strlen($parts[1]) == 1)
      $parts[1] = "0".$parts[1];

    // check year
    if (!isset($parts[2]))
      $parts[2] = date("Y");
    else if (strlen($parts[2]) == 2) {
      if ($parts[2] > 70)
        $parts[2] = "19".$parts[2];
      else {
        if ($parts[2] < 10)
          $parts[2] = "200".$parts[2];
        else
          $parts[2] = "20".$parts[2];
      }
    }
    
    $return = implode(".",$parts);


    if (!preg_match("/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{2,4})/",$return))  $return = false;
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

    // empty string can't be a time value
    if (strlen($time)==0)
      return false;

    // get the parts
    $parts = preg_split("/:|\./",$time);

    for ($i=0;$i<count($parts);$i++) {
      switch (strlen($parts[$i])) {
        case 0:
          return false;
        case 1:
          $parts[$i] = "0".$parts[$i];
      }
    }

    // fill unsued parts (eg. 12:00 given but 12:00:00 is needed)
    while (count($parts) < 3) {
      $parts[] = "00";
    }

    $return = implode(":",$parts);

    $regex23 = '([0-1][0-9])|(2[0-3])'; // regular expression for hours
    $regex59 = '([0-5][0-9])'; // regular expression for minutes and seconds
    if (!preg_match("/^($regex23):($regex59):($regex59)$/",$return)) $return = false;
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
    if (!preg_match("/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{2,4})-([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/",$timestring)) {
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
 * Check if the new time values are better than the old once in the array.
 *
 * @param $bestTime (called by reference)
 *                  Array containing the, until now, best time data
 * @param $newStart suggestion for a better start time
 * @param $newEnd   suggestion for a better end time
 * @param $realStart the real start time
 * @param $realEnd   the real end time
 */
function roundTimespanCheckIfBetter(&$bestTime,$newStart,$newEnd,$realStart,$realEnd) {
  $realDuration = $realEnd-$realStart;
  $newDuration = $newEnd-$newStart;

  if (abs($realDuration-$newDuration) > abs($realDuration - $bestTime['duration'])) {
    // new times are definitely worse, as the timespan is furher away from the real duration
    return;
  }

  // still, this might be closer to the real time
  if (abs($realStart-$newStart)+abs($realEnd-$newEnd) >= $bestTime['totalDeviation']) {
    // it is not
    return;
  }

  // new time is better, update array
  $bestTime['start']    = $newStart;
  $bestTime['end']      = $newEnd;
  $bestTime['duration'] = $newEnd-$newStart;
  $bestTime['totalDeviation'] = abs($realStart-$newStart)+abs($realEnd-$newEnd);
}

/**
 * Find a beginning and end time whose timespan is as close to
 * the real timepsan as possible while being a multiple of $steps (in minutes).
 *
 * e.g.: 16:07:31 - 17:15:16 is "rounded" to 16:00:00 - 17:15:00
 *       with steps set to 15
 *
 *@param $start the beginning of the timespan
 *@param $end   the end of the timespan
 *@param $steps the steps in minutes (has to divide an hour, e.g. 5 is valid while 7 is not)
 *
 */
function roundTimespan($start,$end,$steps) {
  // calculate how long a steps is (e.g. 15 second steps are 900 seconds long)
  $stepWidth=$steps*60;

  if ($steps == 0) {
    $bestTime = array();
    $bestTime['start']    = $start;
    $bestTime['end']      = $end;
    return $bestTime;
  }


  // calculate how many seconds we are over the previous full step
  $startSecondsOver = $start%$stepWidth;
  $endSecondsOver   = $end%$stepWidth;

  // calculate earlier and later times of full step width
  $earlierStart = $start-$startSecondsOver;
  $earlierEnd   = $end-$endSecondsOver;
  $laterStart   = $start+($stepWidth-$startSecondsOver);
  $laterEnd     = $end+($stepWidth-$endSecondsOver);


  // assuming the earlier start end end time are the best (likely not always true)
  $bestTime = array();
  $bestTime['start']    = $earlierStart;
  $bestTime['end']      = $earlierEnd;
  $bestTime['duration'] = $earlierEnd-$earlierStart;
  $bestTime['totalDeviation'] = abs($start-$earlierStart)+abs($end-$earlierEnd);

  // check for better start and end times
  roundTimespanCheckIfBetter($bestTime,$earlierStart,$laterEnd,$start,$end);
  roundTimespanCheckIfBetter($bestTime,$laterStart,$earlierEnd,$start,$end);
  roundTimespanCheckIfBetter($bestTime,$laterStart,$laterEnd,$start,$end);

  return $bestTime;
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