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
            Kimai_Logger::logfile("Kicking user $kimai_user because of authentication key mismatch.");
            kickUser();
        } else {
            $user = $database->checkUserInternal($kimai_user);
            if (!$user instanceof Kimai_User) {
                $user = new Kimai_User($user);
            }
            Kimai_Registry::setUser($user);
            return $user;
        }
    }

    Kimai_Logger::logfile("Kicking user because of missing cookie.");
    kickUser();
}

/**
 * Kill the current users session by redirecting him to the logout page.
 */
function kickUser()
{
    die("<script type='text/javascript'>window.location.href = '../index.php?a=logout';</script>");
}

/**
 * Get a list of available time zones. This is directly taken from PHP.
 *
 * @return array of timezone names
 */
function timezoneList()
{
    return DateTimeZone::listIdentifiers();
}

/**
 * Return array for rendering a select input.
 *
 * <pre>
 * returns:
 * [0] -> project/activity names
 * [1] -> values as IDs
 * </pre>
 *
 * @param string $subject one of 'project', 'activity', 'customer', 'group', 'sameGroupUser', 'user'
 * @param array $groups
 * @param string $selection
 * @param bool $includeDeleted
 * @param array $showIds an array of IDs that should be shown, no matter of their visibility
 * @return array
 */
function makeSelectBox($subject, $groups, $selection = null, $includeDeleted = false, $showIds = array())
{
    $kga = Kimai_Registry::getConfig();
    $database = Kimai_Registry::getDatabase();

    $sel = array();

    switch ($subject) {
        case 'project':
            $projects = $database->get_projects($groups);
            foreach ($projects as $project) {
                if (($project['visible'] && $project['customerVisible']) || in_array($project['projectID'], $showIds)) {
                    if ($kga->getSettings()->isFlipProjectDisplay()) {
                        $projectName = $project['customerName'] . ": " . $project['name'];
                        if ($kga->getSettings()->isShowProjectComment()) {
                            $projectName .= "(" . $project['comment'] . ")";
                        }
                    } else {
                        $projectName = $project['name'] . " (" . $project['customerName'] . ")";
                        if ($kga->getSettings()->isShowProjectComment()) {
                            $projectName .= "(" . $project['comment'] . ")";
                        }
                    }
                    $sel[$project['projectID']] = $projectName;
                }
            }
            break;

        case 'activity':
            $activities = $database->get_activities($groups);
            foreach ($activities as $activity) {
                if ($activity['visible'] || in_array($activity['activityID'], $showIds)) {
                    $sel[$activity['activityID']] = $activity['name'];
                }
            }
            break;

        case 'customer':
            $customers = $database->get_customers($groups);
            $selectionFound = false;
            if (is_array($customers)) {
                foreach ($customers as $customer) {
                    if ($customer['visible'] || in_array($customer['customerID'], $showIds)) {
                        $sel[$customer['customerID']] = $customer['name'];
                        if ($selection == $customer['customerID']) {
                            $selectionFound = true;
                        }
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
            if (!$database->global_role_allows($kga['user']['globalRoleID'], 'core-group-otherGroup-view')) {
                $groups = array_filter($groups, function ($group) {
                    $kga = Kimai_Registry::getConfig();

                    return array_search($group['groupID'], $kga['user']['groups']) !== false;
                });
            }

            foreach ($groups as $group) {
                if ($includeDeleted || !$group['trash']) {
                    $sel[$group['groupID']] = $group['name'];
                }
            }
            break;

        case 'sameGroupUser':
            $users = $database->get_users(0, $database->getGroupMemberships($kga['user']['userID']));

            foreach ($users as $user) {
                if ($includeDeleted || !$user['trash']) {
                    $sel[$user['userID']] = $user['name'];
                }
            }
            break;

        case 'allUser':
            $users = $database->get_users();

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
 * return a random code with given length
 *
 * @param int $length length of the code
 * @return string
 * @author th
 */
function random_code($length)
{
    $code = "";
    $string = "ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz0123456789";
    mt_srand((double)microtime() * 1000000);
    for ($i = 1; $i <= $length; $i++) {
        $code .= substr($string, mt_rand(0, strlen($string) - 1), 1);
    }
    return $code;
}

/**
 * return a random number with X digits
 *
 * @param integer $length digit count of number
 * @return string
 * @author th
 */
function random_number($length)
{
    $number = "";
    $string = "0123456789";
    mt_srand((double)microtime() * 1000000);
    for ($i = 1; $i <= $length; $i++) {
        $number .= substr($string, mt_rand(0, strlen($string) - 1), 1);
    }
    return $number;
}

/**
 * Checks if the database structure needs to be updated for new Kimai version and redirects accordingly.
 *
 * @param string $path path to admin dir relative to the document that calls this function (usually "." or "..")
 * @global array $kga kimai-global-array
 * @return array
 * @author th
 */
function checkDBversion($path)
{
    $database = Kimai_Registry::getDatabase();
    $config = Kimai_Registry::getConfig();

    // check for versions before 0.7.13r96
    $installedVersion = $database->get_DBversion();
    $checkVersion = $installedVersion[0];

    if ($checkVersion == "0.5.1" && count($database->get_users()) == 0) {
        // fresh install
        header("Location: $path/installer/");
        exit;
    }

    // only call updater when database changes no matter the kimai version
    if ((int)$installedVersion[1] < $config->getRevision()) {
        header("Location: $path/updater/updater.php");
        exit;
    }
}

/**
 * @param $in
 * @param $out
 * @return mixed
 */
function convert_time_strings($in, $out)
{
    
    $explode_in = explode("-", $in);
    $explode_out = explode("-", $out);

    $date_in = explode(".", $explode_in[0]);
    $date_out = explode(".", $explode_out[0]);

    $time_in = explode(":", $explode_in[1]);
    $time_out = explode(":", $explode_out[1]);

    $time['in'] = mktime($time_in[0], $time_in[1], $time_in[2], $date_in[1], $date_in[0], $date_in[2]);
    $time['out'] = mktime($time_out[0], $time_out[1], $time_out[2], $date_out[1], $date_out[0], $date_out[2]);
    $time['diff'] = (int)$time['out'] - (int)$time['in'];

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
function get_cookie($cookie_name, $default = null)
{
    return isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : $default;
}

/**
 * based on http://wiki.jumba.com.au/wiki/PHP_Generate_random_password
 *
 * @param int $length
 * @return string
 */
function createPassword($length)
{
    $chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $i = 0;
    $password = "";
    while ($i <= $length) {
        $password .= $chars{mt_rand(0, strlen($chars) - 1)};
        $i++;
    }
    return $password;
}

/**
 * @param $database
 * @param $hostname
 * @param $username
 * @param $password
 * @param $charset
 * @param $prefix
 * @param $lang
 * @param $salt
 * @param $timezone
 * @return bool
 */
function write_config_file($database, $hostname, $username, $password, $charset, $prefix, $lang, $salt, $timezone = null)
{
    $kga = Kimai_Registry::getConfig();

    $database = addcslashes($database, '"$');
    $hostname = addcslashes($hostname, '"$');
    $username = addcslashes($username, '"$');
    $password = addcslashes($password, '"$');

    $file = fopen(realpath(dirname(__FILE__)) . '/autoconf.php', 'w');
    if (!$file) {
        return false;
    }

    // fallback if timezone was not provided
    if (!empty($timezone)) {
        $timezone = addcslashes($timezone, '"$');
        $timezone = '"' . $timezone . '"';
    } else if (isset($kga['defaultTimezone'])) {
        $timezone = '"' . $kga['defaultTimezone'] . '"';
    } else {
        $timezone = 'date_default_timezone_get()';
    }

    // fetch skin from global config with "standard" fallback
    $skin = !empty($kga->getSkin()) ? $kga->getSkin() : Kimai_Config::getDefault(Kimai_Config::DEFAULT_SKIN);
    $billable = !empty($kga->getBillable()) ? var_export($kga->getBillable(), true) : var_export(Kimai_Config::getDefault(Kimai_Config::DEFAULT_BILLABLE), true);
    $authenticator = !empty($kga->getAuthenticator()) ? $kga->getAuthenticator() : Kimai_Config::getDefault(Kimai_Config::DEFAULT_AUTHENTICATOR);
    $lang = !empty($lang) ? $lang : Kimai_Config::getDefault(Kimai_Config::DEFAULT_LANGUAGE);

    $config = <<<EOD
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

// This file was automatically generated by the installer

\$server_hostname = "$hostname";
\$server_database = "$database";
\$server_username = "$username";
\$server_password = "$password";
\$server_charset = "$charset";
\$server_prefix = "$prefix";
\$language = "$lang";
\$password_salt = "$salt";
\$defaultTimezone = $timezone;
\$skin = "$skin";
\$authenticator = "$authenticator";
\$billable = $billable;

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
 * @global array $kga kimai-global-array
 * @return array
 * @author th
 */
function get_timeframe()
{
    $kga = Kimai_Registry::getConfig();

    $timeFrame = array(null, null);

    if (isset($kga['user'])) {
        $timeFrame[0] = $kga['user']['timeframeBegin'];
        $timeFrame[1] = $kga['user']['timeframeEnd'];
    }

    /* database has no entries? */
    $mon = date("n");
    $day = date("j");
    $Y = date("Y");
    if (!$timeFrame[0]) {
        $timeFrame[0] = mktime(0, 0, 0, $mon, 1, $Y);
    }
    if (!$timeFrame[1]) {
        $timeFrame[1] = mktime(23, 59, 59, $mon, $day, $Y);
    }

    return $timeFrame;
}

/**
 * Returns the boolean value as integer, submitted via checkbox.
 *
 * @param string $name
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
 * @param string $value the value from the request
 * @return double|null floating point value
 */
function getRequestDecimal($value)
{
    $kga = Kimai_Registry::getConfig();
    if (trim($value) != '') {
        return (double)str_replace($kga['conf']['decimalSeparator'], '.', $value);
    }
    return null;
}

/**
 * @brief Check the permission to access an object.
 *
 * This method is meant to check permissions for adding, editing and deleting customers,
 * projects, activities and users. The input is not checked whether it falls within those boundaries since
 * it can also work with others, if the permissions match the pattern.
 *
 * @param string $objectTypeName name of the object type being edited (e.g. Project)
 * @param string $action the action being performed (e.g. add)
 * @param array $oldGroups the old groups of the object (empty array for new objects)
 * @param array $newGroups the new groups of the object (same as oldGroups if nothing should be changed in group assignment)
 * @return boolean if the permission is granted, false otherwise
 */
function checkGroupedObjectPermission($objectTypeName, $action, $oldGroups, $newGroups)
{
    $kga = Kimai_Registry::getConfig();
    $database = Kimai_Registry::getDatabase();

    if (!isset($kga['user'])) {
        return false;
    }

    $assignedOwnGroups = array_intersect($oldGroups, $database->getGroupMemberships($kga['user']['userID']));
    $assignedOtherGroups = array_diff($oldGroups, $database->getGroupMemberships($kga['user']['userID']));

    if (count($assignedOtherGroups) > 0) {
        $permissionName = "core-${objectTypeName}-otherGroup-${action}";
        if (!$database->global_role_allows($kga['user']['globalRoleID'], $permissionName)) {
            Kimai_Logger::logfile("missing global permission $permissionName for user " . $kga['user']['name'] . " to access $objectTypeName");
            return false;
        }
    }

    if (count($assignedOwnGroups) > 0) {
        $permissionName = "core-${objectTypeName}-${action}";
        if (!$database->checkMembershipPermission($kga['user']['userID'], $assignedOwnGroups, $permissionName)) {
            Kimai_Logger::logfile("missing membership permission $permissionName of current own group(s) " . implode(", ", $assignedOwnGroups) . " for user " . $kga['user']['name'] . " to access $objectTypeName");
            return false;
        }
    }

    if (count($oldGroups) != array_intersect($oldGroups, $newGroups)) {
        // group assignment has changed

        $addToGroups = array_diff($newGroups, $oldGroups);
        $removeFromGroups = array_diff($oldGroups, $newGroups);

        $addToOtherGroups = array_diff($addToGroups, $database->getGroupMemberships($kga['user']['userID']));
        $addToOwnGroups = array_intersect($addToGroups, $database->getGroupMemberships($kga['user']['userID']));
        $removeFromOtherGroups = array_diff($removeFromGroups, $database->getGroupMemberships($kga['user']['userID']));
        $removeFromOwnGroups = array_intersect($removeFromGroups, $database->getGroupMemberships($kga['user']['userID']));

        $action = 'assign';
        if (count($addToOtherGroups) > 0) {
            $permissionName = "core-${objectTypeName}-otherGroup-${action}";
            if (!$database->global_role_allows($kga['user']['globalRoleID'], $permissionName)) {
                Kimai_Logger::logfile("missing global permission $permissionName for user " . $kga['user']['name'] . " to access $objectTypeName");
                return false;
            }
        }

        if (count($addToOwnGroups) > 0) {
            $permissionName = "core-${objectTypeName}-${action}";
            if (!$database->checkMembershipPermission($kga['user']['userID'], $addToOwnGroups, $permissionName)) {
                Kimai_Logger::logfile("missing membership permission $permissionName of new own group(s) " . implode(", ", $addToOwnGroups) . " for user " . $kga['user']['name'] . " to access $objectTypeName");
                return false;
            }
        }

        $action = 'unassign';
        if (count($removeFromOtherGroups) > 0) {
            $permissionName = "core-${objectTypeName}-otherGroup-${action}";
            if (!$database->global_role_allows($kga['user']['globalRoleID'], $permissionName)) {
                Kimai_Logger::logfile("missing global permission $permissionName for user " . $kga['user']['name'] . " to access $objectTypeName");
                return false;
            }
        }

        if (count($removeFromOwnGroups) > 0) {
            $permissionName = "core-${objectTypeName}-${action}";
            if (!$database->checkMembershipPermission($kga['user']['userID'], $removeFromOwnGroups, $permissionName)) {
                Kimai_Logger::logfile("missing membership permission $permissionName of old own group(s) " . implode(", ", $removeFromOwnGroups) . " for user " . $kga['user']['name'] . " to access $objectTypeName");
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
 *  This is helpful to check if an option to do the action should be presented to the user.
 *
 * @param string $objectTypeName name of the object type being edited (e.g. Project)
 * @param string $action the action being performed (e.g. add)
 * @return boolean if allowed, false otherwise
 */
function coreObjectActionAllowed($objectTypeName, $action)
{
    $kga = Kimai_Registry::getConfig();
    $database = Kimai_Registry::getDatabase();

    if ($database->global_role_allows($kga['user']['globalRoleID'], "core-$objectTypeName-otherGroup-$action")) {
        return true;
    }

    if ($database->checkMembershipPermission($kga['user']['userID'], $kga['user']['groups'], "core-$objectTypeName-$action", 'any')) {
        return true;
    }

    return false;
}

/**
 * Encode a password
 *
 * @param string $password the password string to encode
 * @return string the encoded password string
 */
function encode_password($password)
{
    $kga = Kimai_Registry::getConfig();

    $salt = $kga['password_salt'];
    return md5($salt . $password . $salt);
}
