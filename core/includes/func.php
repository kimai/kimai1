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
 * Check if a user is logged in or kick them.
 */
function checkUser()
{
    $database = Kimai_Registry::getDatabase();

    if (isset($_COOKIE['kimai_user']) && isset($_COOKIE['kimai_key']) && $_COOKIE['kimai_user'] != "0" && $_COOKIE['kimai_key'] != "0") {
      $kimai_user = addslashes($_COOKIE['kimai_user']);
      $kimai_key = addslashes($_COOKIE['kimai_key']);

      if ($database->get_seq($kimai_user) != $kimai_key) {
          kickUser();
      } else {
          $user = $database->checkUserInternal($kimai_user);
          Kimai_Registry::setUser(new Kimai_User($user));
          return $user;
      }
    }
    kickUser();
}

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
 * [0] -> project/activity names
 * [1] -> values as IDs
 * </pre>
 *
 * @param string either 'project', 'activity', 'customer', 'group'
 * @return array
 * @author th, sl, kp
 */
function makeSelectBox($subject,$groups,$selection=null, $includeDeleted = false){

    global $kga, $database;

    $sel = array();

    switch ($subject) {
        case 'project':
            $projects = $database->get_projects($groups);
            foreach ($projects as $project) {
                if ($project['visible']) {
                    if ($kga['conf']['flip_project_display']) {
                        $projectName = $project['customerName'] . ": " . $project['name'];
                        if ($kga['conf']['project_comment_flag']) {
                            $projectName .= "(" . $project['comment'] .")" ;
                        }
                    } else {
                        $projectName = $project['name'] . " (" . $project['customerName'] . ")";
                        if ($kga['conf']['project_comment_flag']) {
                            $projectName .=  "(" . $project['comment'] .")";
                        }
                    }
                    $sel[$project['projectID']] = $projectName;
                }
            }
            break;

        case 'activity':
            $activities = $database->get_activities($groups);
            foreach ($activities as $activity) {
                if ($activity['visible']) {
                    $sel[$activity['activityID']] = $activity['name'];
                }
            }
            break;

        case 'customer':
            $customers = $database->get_customers($groups);
            $selectionFound = false;
            if(is_array($customers)) {
	            foreach ($customers as $customer) {
	                if ($customer['visible']) {
	                    $sel[$customer['customerID']] = $customer['name'];
	                    if ($selection == $customer['customerID'])
	                      $selectionFound = true;
	                }
	            }
            }
            if ($selection != null && !$selectionFound) {
              $data = $database->customer_get_data($selection);
              $sel[$data['customerID']] = $data['name'];
            }
            break;

        case 'group':
            $groups = $database->get_groups();
            foreach ($groups as $group) {
                if ($includeDeleted || !$group['trash']) {
                    $sel[$group['groupID']] = $group['name'];
                }
            }
            break;

        case 'user':
            $users = $database->get_watchable_users($kga['user']);

            foreach ($users as $user) {
              if ($includeDeleted || !$user['trash']) {
                $sel[$user['userID']] = $user['name'];
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

    if ($checkVersion == "0.5.1" && count($database->get_users()) == 0) {
      // fresh install
      header("Location: $path/installer");
      exit;
    }

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

function write_config_file($database,$hostname,$username,$password,$db_layer,$db_type,$prefix,$lang,$salt,$timezone) {
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
\$defaultTimezone = "$timezone";

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

function get_timeframe() {
    global $kga, $conn;

    $timeframe = array(null,null);
    
    if (isset($kga['user'])) {

        $timeframe[0] = $kga['user']['timeframeBegin'];
        $timeframe[1] = $kga['user']['timeframeEnd'];

    }

    /* database has no entries? */
    $mon = date("n"); $day = date("j"); $Y = date("Y");
    if (!$timeframe[0]) {
        $timeframe[0] = mktime(0,0,0,$mon,1,$Y);
    }
    if (!$timeframe[1]) {
        $timeframe[1] = mktime(23,59,59,$mon,$day,$Y);
    }
    
    return $timeframe;
}

function endsWith($haystack,$needle) {
  return strcmp(substr($haystack, strlen($haystack)-strlen($needle)),$needle)===0;
}

/**
 * Returns the boolean value as integer, submitted via checkbox.
 *
 * @param $name
 * @return int
 */
function getRequestBool($name)
{
    if (isset($_REQUEST[$name])) {
        if (strtolower($_REQUEST[$name]) == 'on') {
            return 1;
        }

        $temp = intval($_REQUEST[$name]);
        if ($temp == 1 || $temp == 0) {
            return $temp;
        }

        return 1;
    }

    return 0;
}