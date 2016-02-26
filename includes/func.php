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
        Logger::logfile("Kicking user $kimai_user because of authentication key mismatch.");
        kickUser();
      } else {
          $user = $database->checkUserInternal($kimai_user);
          Kimai_Registry::setUser(new Kimai_User($user));
          return $user;
      }
    }

    Logger::logfile("Kicking user because of missing cookie.");
    kickUser();
}

/**
 * Kill the current users session by redirecting him to the logout page.
 */
function kickUser() {
    die("<script type='text/javascript'>window.location.href = '../index.php?a=logout';</script>");
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
            if (!$database->global_role_allows($kga['user']['globalRoleID'], 'core-group-otherGroup-view'))
              $groups = array_filter($groups, function($group) {global $kga; return array_search($group['groupID'], $kga['user']['groups']) !== false; });

            foreach ($groups as $group) {
                if ($includeDeleted || !$group['trash']) {
                    $sel[$group['groupID']] = $group['name'];
                }
            }
            break;

        case 'sameGroupUser':
            $users = $database->get_users(0,$database->getGroupMemberships($kga['user']['userID']));

            foreach ($users as $user) {
              if ($includeDeleted || !$user['trash']) {
                $sel[$user['userID']] = $user['name'];
              }
            }
            break;

        case 'allUser':
            $users = $database->get_users($kga['user']);

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

function write_config_file($database,$hostname,$username,$password,$db_layer,$db_type,$prefix,$lang,$salt,$timezone = null) {
  $database = addcslashes($database, '"$');
  $hostname = addcslashes($hostname, '"$');
  $username = addcslashes($username, '"$');
  $password = addcslashes($password, '"$');
  $timezone = addcslashes($timezone, '"$');

  $file=fopen(realpath(dirname(__FILE__)).'/autoconf.php','w');
  if (!$file) return false;
  if (empty($timezone)) { $timezone = 'date_default_timezone_get()'; } else { $timezone = '"' . $timezone . '"'; }

$config=<<<EOD
<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2013 Kimai-Development-Team
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
\$defaultTimezone = $timezone;

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

/**
 * Returns the decimal value from a request value where the number is still represented
 * in the location specific way.
 * 
 * @param $value the value from the request
 * @return parsed floating point value
 */
function getRequestDecimal($value) {
  global $kga;
  if (trim($value) == '')
    return NULL;
  else
    return (double) str_replace($kga['conf']['decimalSeparator'],'.',$value);
}

/**
 * @brief Check the permission to access an object.
 * 
 * This method is meant to check permissions for adding, editing and deleting customers,
 * projects, activities and users. The input is not checked whether it falls within those boundaries since
 * it can also work with others, if the permissions match the pattern.
 * 
 * @param $objectTypeName string name of the object type being edited (e.g. Project)
 * @param $action the action being performed (e.g. add)
 * @param $oldGroups the old groups of the object (empty array for new objects)
 * @param $newGroups the new groups of the object (same as oldGroups if nothing should be changed in group assignment)
 * @return true if the permission is granted, false otherwise
 */
function checkGroupedObjectPermission($objectTypeName, $action, $oldGroups, $newGroups) {
  global $database, $kga;

  if (!isset($kga['user'])) return false;

  $assignedOwnGroups   = array_intersect($oldGroups,$database->getGroupMemberships($kga['user']['userID']));
  $assignedOtherGroups = array_diff     ($oldGroups,$database->getGroupMemberships($kga['user']['userID']));

  if (count($assignedOtherGroups) > 0) {
    $permissionName = "core-${objectTypeName}-otherGroup-${action}";
    if (!$database->global_role_allows($kga['user']['globalRoleID'], $permissionName)) {
      Logger::logfile("missing global permission $permissionName for user " . $kga['user']['name'] . " to access $objectTypeName");
      return false;
    }
  }

  if (count($assignedOwnGroups) > 0) {
    $permissionName = "core-${objectTypeName}-${action}";
    if (!$database->checkMembershipPermission($kga['user']['userID'],$assignedOwnGroups, $permissionName)) {
      Logger::logfile("missing membership permission $permissionName of current own group(s) " . implode(", ", $assignedOwnGroups) . " for user " . $kga['user']['name'] . " to access $objectTypeName");
      return false;
    }
  }

  if (count($oldGroups) != array_intersect($oldGroups,$newGroups)) {
    // group assignment has changed

      $addToGroups = array_diff($newGroups, $oldGroups);
      $removeFromGroups = array_diff($oldGroups, $newGroups);

      $addToOtherGroups = array_diff     ($addToGroups,$database->getGroupMemberships($kga['user']['userID']));
      $addToOwnGroups   = array_intersect($addToGroups,$database->getGroupMemberships($kga['user']['userID']));
      $removeFromOtherGroups = array_diff     ($removeFromGroups,$database->getGroupMemberships($kga['user']['userID']));
      $removeFromOwnGroups   = array_intersect($removeFromGroups,$database->getGroupMemberships($kga['user']['userID']));

      $action = 'assign';
      if (count($addToOtherGroups) > 0) {
        $permissionName = "core-${objectTypeName}-otherGroup-${action}";
        if (!$database->global_role_allows($kga['user']['globalRoleID'], $permissionName)) {
          Logger::logfile("missing global permission $permissionName for user " . $kga['user']['name'] . " to access $objectTypeName");
          return false;
        }
      }

      if (count($addToOwnGroups) > 0) {
        $permissionName = "core-${objectTypeName}-${action}";
        if (!$database->checkMembershipPermission($kga['user']['userID'],$addToOwnGroups, $permissionName)) {
          Logger::logfile("missing membership permission $permissionName of new own group(s) " . implode(", ", $addToOwnGroups) . " for user " . $kga['user']['name'] . " to access $objectTypeName");
          return false;
        }
      }

      $action = 'unassign';
      if (count($removeFromOtherGroups) > 0) {
        $permissionName = "core-${objectTypeName}-otherGroup-${action}";
        if (!$database->global_role_allows($kga['user']['globalRoleID'], $permissionName)) {
          Logger::logfile("missing global permission $permissionName for user " . $kga['user']['name'] . " to access $objectTypeName");
          return false;
        }
      }

      if (count($removeFromOwnGroups) > 0) {
        $permissionName = "core-${objectTypeName}-${action}";
        if (!$database->checkMembershipPermission($kga['user']['userID'],$removeFromOwnGroups, $permissionName)) {
          Logger::logfile("missing membership permission $permissionName of old own group(s) " . implode(", ", $removeFromOwnGroups) . " for user " . $kga['user']['name'] . " to access $objectTypeName");
          return false;
        }
      }

    
  }

  return true;
}

/**
 * Check if an action on a core object is allowed either
 *   - for other groups or
 *   - for any group the current user is a member of.
 *  
 *  This is helpfull to check if an option to do the action should be presented to the user.
 *  
 * @param $objectTypeName string name of the object type being edited (e.g. Project)
 * @param $action the action being performed (e.g. add)
 * @return true if allowed, false otherwise
 */
function coreObjectActionAllowed($objectTypeName, $action) {
  global $database, $kga;

  if ($database->global_role_allows($kga['user']['globalRoleID'], "core-$objectTypeName-otherGroup-$action"))
   return true;

  if ($database->checkMembershipPermission($kga['user']['userID'], $kga['user']['groups'],"core-$objectTypeName-$action",'any'))
    return true;

  return false;
}
