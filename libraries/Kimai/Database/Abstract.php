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
 * Definition of all public abstract functions that need to be implemented for accessing a database.
 * Must be subclassed by all available database drivers like MySQL.
 */
abstract class Kimai_Database_Abstract {

    protected $kga;

    /**
     * @var MySQL
     */
    protected $conn;

    /**
     * Instantiate a new database layer..
     * The provided kimai global array will be stored as a reference.
     *
     * @param $kga
     */
    public function __construct(&$kga) {
        $this->kga = &$kga;
    }

    /**
     * Return the connection handler used to connect to the database.
     * This is currently required for extensions to access the database without
     * connecting again.
     */
    public function getConnectionHandler() {
        return $this->conn;
    }

    /**
     * Prepare all values of the array so it's save to put them into an sql query.
     * The conversion to utf8 is done here as well, if configured.
     *
     * This method is public since ki_expenses private database layers use it.
     *
     * @param array $data Array which values are being prepared.
     * @return array The same array, except all values are being escaped correctly.
     */
    public function clean_data($data) {
        $return = array();
        foreach ($data as $key => $value) {
            if ($key != "pw") {
                $return[$key] = urldecode(strip_tags($data[$key]));
                $return[$key] = str_replace('"', '_', $data[$key]);
                $return[$key] = str_replace("'", '_', $data[$key]);
                $return[$key] = str_replace('\\', '', $data[$key]);
            } else {
                $return[$key] = $data[$key];
            }
        }

        return $return;
    }

    /**
     * Connect to the database.
     *
     * @param $host
     * @param $database
     * @param $username
     * @param $password
     * @param $utf8
     * @param $serverType
     * @return
     */
    public abstract function connect($host,$database,$username,$password,$utf8,$serverType);

    /**
     * @return string the tablename with the server prefix
     */
    public function getProjectTable()
    {
        return $this->kga['server_prefix'].'projects';
    }

    /**
     * @return string the tablename with the server prefix
     */
    public function getActivityTable()
    {
        return $this->kga['server_prefix'].'activities';
    }

    /**
     * @return string the tablename with the server prefix
     */
    public function getCustomerTable()
    {
        return $this->kga['server_prefix'].'customers';
    }


    public function getTimeSheetTable()
    {
        return $this->kga['server_prefix'].'timeSheet';
    }

    public function getExpenseTable() 
    {
        return $this->kga['server_prefix'].'expenses';
    }

    public function getUserTable() 
    {
        return $this->kga['server_prefix'].'users';
    }

    /**
     * Add a new customer to the database.
     *
     * @param array $data  name, address and other data of the new customer
     * @return int         the customerID of the new customer, false on failure
     */
    public abstract function customer_create($data);

    /**
     * Returns the data of a certain customer
     *
     * @param int $customerID  id of the customer
     * @return array         the customer's data (name, address etc) as array, false on failure
     */
    public abstract function customer_get_data($customerID);

    /**
     * Edits a customer by replacing his data by the new array
     *
     * @param int $customerID  id of the customer to be edited
     * @param array $data    name, address and other new data of the customer
     * @return boolean       true on success, false on failure
     */
    public abstract function customer_edit($customerID, $data);

    /**
     * Assigns a customer to 1-n groups by adding entries to the cross table
     *
     * @param int $customerID     id of the customer to which the groups will be assigned
     * @param array $groupIDs    contains one or more groupIDs
     * @return boolean            true on success, false on failure
     */
    public abstract function assign_customerToGroups($customerID, $groupIDs);

    /**
     * returns all IDs of the groups of the given customer
     *
     * @param int $customerID  id of the customer
     * @return array         contains the groupIDs of the groups or false on error
     */
    public abstract function customer_get_groupIDs($customerID);

    /**
     * deletes a customer
     *
     * @param int $customerID  id of the customer
     * @return boolean       true on success, false on failure
     */
    public abstract function customer_delete($customerID);

    /**
     * Adds a new project
     *
     * @param array $data  name, comment and other data of the new project
     * @return int         the ID of the new project, false on failure
     */
    public abstract function project_create($data);

    /**
     * Returns the data of a certain project
     *
     * @param array $projectID  ID of the project

     * @return array         the project's data (name, comment etc) as array, false on failure
     */
    public abstract function project_get_data($projectID);

    /**
     * Edits a project by replacing its data by the new array
     *
     * @param array $projectID   ID of the project to be edited
     * @param array $data     name, comment and other new data of the project
     * @return boolean        true on success, false on failure
     */
    public abstract function project_edit($projectID, $data);

    /**
     * Assigns a project to 1-n groups by adding entries to the cross table
     *
     * @param int $projectID        ID of the project to which the groups will be assigned
     * @param array $groupIDs    contains one or more groupIDs
     * @return boolean            true on success, false on failure
     */
    public abstract function assign_projectToGroups($projectID, $groupIDs);

    /**
     * returns all the groups of the given project
     *
     * @param array $projectID  ID of the project
     * @return array         contains the groupIDs of the groups or false on error
     */
    public abstract function project_get_groupIDs($projectID);

    /**
     * deletes a project
     *
     * @param array $projectID  ID of the project
     * @return boolean       true on success, false on failure
     */
    public abstract function project_delete($projectID);

    /**
     * Adds a new activity
     *
     * @param array $data   name, comment and other data of the new activity
     * @return int          the activityID of the new project, false on failure
     */
    public abstract function activity_create($data);

    /**
     * Returns the data of a certain activity
     *
     * @param array $activityID  activityID of the project
     * @return array         the activity's data (name, comment etc) as array, false on failure
     */
    public abstract function activity_get_data($activityID);

    /**
     * Edits an activity by replacing its data by the new array
     *
     * @param array $activityID  activityID of the project to be edited
     * @param array $data    name, comment and other new data of the activity
     * @return boolean       true on success, false on failure
     */
    public abstract function activity_edit($activityID, $data);

    /**
     * Assigns an activity to 1-n groups by adding entries to the cross table
     *
     * @param int $activityID         activityID of the project to which the groups will be assigned
     * @param array $groupIDs    contains one or more groupIDs
     * @return boolean            true on success, false on failure
     */
    public abstract function assign_activityToGroups($activityID, $groupIDs);

    /**
     * Assigns an activity to 1-n projects by adding entries to the cross table
     *
     * @param int $activityID         id of the activity to which projects will be assigned
     * @param array $projectIDs    contains one or more projectIDs
     * @return boolean            true on success, false on failure
     */
    public abstract function assign_activityToProjects($activityID, $projectIDs);

    /**
     * Assigns 1-n activities to a project by adding entries to the cross table
     *
     * @param int $projectID         id of the project to which activities will be assigned
     * @param array $activityIDs    contains one or more activityIDs
     * @return boolean            true on success, false on failure
     */
    public abstract function assign_projectToActivities($projectID, $activityIDs);

    /**
     * returns all the projects to which the activity was assigned
     *
     * @param int $activityID  activityID of the project
     * @return array         contains the projectIDs of the projects or false on error
     */
    public abstract function activity_get_projects($activityID);

    /**
     * returns all the activities which were assigned to a project
     *
     * @param integer $projectID  ID of the project
     * @return array         contains the activityIDs of the activities or false on error
     */
    public abstract function project_get_activities($projectID);

    /**
     * returns all the groups of the given activity
     *
     * @param array $activityID  activityID of the project
     * @return array         contains the groupIDs of the groups or false on error
     */
    public abstract function activity_get_groups($activityID);

    /**
     * deletes an activity
     *
     * @param array $activityID  activityID of the activity
     * @return boolean       true on success, false on failure
     */
    public abstract function activity_delete($activityID);

    /**
     * Assigns a group to 1-n customers by adding entries to the cross table
     * (counterpart to assign_customerToGroups)
     *
     * @param array $groupID        groupID of the group to which the customers will be assigned
     * @param array $customerIDs   contains one or more IDs of customers
     * @return boolean             true on success, false on failure
     */
    public abstract function assign_groupToCustomers($groupID, $customerIDs);

    /**
     * Assigns a group to 1-n projects by adding entries to the cross table
     * (counterpart to assign_projectToGroups)
     *
     * @param array $groupID        groupID of the group to which the projects will be assigned
     * @param array $projectIDs    contains one or more projectIDs
     * @return boolean            true on success, false on failure
     */
    public abstract function assign_groupToProjects($groupID, $projectIDs);

    /**
     * Assigns a group to 1-n activities by adding entries to the cross table
     * (counterpart to assign_activityToGroups)
     *
     * @param array $groupID        groupID of the group to which the activities will be assigned
     * @param array $activityIDs    contains one or more activityIDs
     * @return boolean            true on success, false on failure
     */
    public abstract function assign_groupToActivities($groupID, $activityIDs);

    /**
     * Adds a new user
     *
     * @param array $data  username, email, and other data of the new user
     * @return boolean     true on success, false on failure
     */
    public abstract function user_create($data);

    /**
     * Returns the data of a certain user
     *
     * @param array $userID  id of the user
     * @return array         the user's data (username, email-address, status etc) as array, false on failure
     */
    public abstract function user_get_data($userID);

    /**
     * Edits a user by replacing his data and preferences by the new array
     *
     * @param array $userID  userID of the user to be edited
     * @param array $data    username, email, and other new data of the user
     * @return boolean       true on success, false on failure
     */
    public abstract function user_edit($userID, $data);

    /**
     * deletes a user
     *
     * @param array $userID  userID of the user
     * @param boolean $moveToTrash whether to delete user or move to trash
     * @return boolean       true on success, false on failure
     */
    public abstract function user_delete($userID, $moveToTrash = false);

    /**
     * Get a preference for a user. If no user ID is given the current user is used.
     *
     * @param string  $key     name of the preference to fetch
     * @param integer $userId  (optional) id of the user to fetch the preference for
     * @return string value of the preference or null if there is no such preference
     */
    public abstract function user_get_preference($key,$userId=null);

    /**
     * Get several preferences for a user. If no user ID is given the current user is used.
     *
     * @param array   $keys    names of the preference to fetch in an array
     * @param integer $userId  (optional) id of the user to fetch the preference for
     * @return array  with keys for every found preference and the found value
     */
    public abstract function user_get_preferences(array $keys,$userId=null);

    /**
     * Get several preferences for a user which have a common prefix. The returned preferences are striped off
     * the prefix.
     * If no user ID is given the current user is used.
     *
     * @param string  $prefix   prefix all preferenc keys to fetch have in common
     * @param integer $userId  (optional) id of the user to fetch the preference for
     * @return array  with keys for every found preference and the found value
     */
    public abstract function user_get_preferences_by_prefix($prefix,$userId=null);

    /**
     * Save one or more preferences for a user. If no user ID is given the current user is used.
     * The array has to assign every preference key a value to store.
     * Example: array ( 'setting1' => 'value1', 'setting2' => 'value2');
     *
     * A prefix can be specified, which will be prepended to every preference key.
     *
     * @param array   $data   key/value pairs to store
     * @param string  $prefix prefix for all preferences
     * @param integer $userId (optional) id of another user than the current
     * @return boolean        true on success, false on failure
     */
    public abstract function user_set_preferences(array $data,$prefix='',$userId=null);

    /**
     * Adds a new group
     *
     * @param array $data  name and other data of the new group
     * @return int         the groupID of the new group, false on failure
     */
    public abstract function group_create($data);

    /**
     * Returns the data of a certain group
     *
     * @param array $groupID  groupID of the group
     * @return array         the group's data (name, etc) as array, false on failure
     */
    public abstract function group_get_data($groupID);

    /**
     * Returns the number of users in a certain group
     *
     * @param array $groupID   groupID of the group
     * @return int            the number of users in the group
     */
    public abstract function group_count_users($groupID);

    /**
     * Edits a group by replacing its data by the new array
     *
     * @param array $groupID  groupID of the group to be edited
     * @param array $data    name and other new data of the group
     * @return boolean       true on success, false on failure
     */
    public abstract function group_edit($groupID, $data);

    /**
     * Set the groups in which the user is a member in.
     * @param int $userId   id of the user
     * @param array $groups  array of the group ids to be part of
     * @return boolean       true on success, false on failure
     */
    public abstract function setGroupMemberships($userId,array $groups = null);

    /**
     * Get the groups in which the user is a member in.
     * @param int $userId   id of the user
     * @return array        list of group ids
     */
    public abstract function getGroupMemberships($userId);

    /**
     * deletes a group
     *
     * @param array $groupID  groupID of the group
     * @return boolean       true on success, false on failure
     */
    public abstract function group_delete($groupID);

    /**
     * Returns all configuration variables
     *
     * @return array       array with the vars from the var table
     */
    public abstract function configuration_get_data();

    /**
     * Edits a configuration variables by replacing the data by the new array
     *
     * @param array $data    variables array
     * @return boolean       true on success, false on failure
     */
    public abstract function configuration_edit($data);

    /**
     * Returns a list of IDs of all current recordings.
     *
     * @param integer $userID ID of user in table users
     * @return array with all IDs of current recordings. This array will be empty if there are none.
     */
    public abstract function get_current_recordings($userID);

    /**
     * Return the latest running entry with all information required for the buzzer.
     *
     * @return array with all data
     * @author sl
     */
    public abstract function get_latest_running_entry();

    /**
     * Returns the data of a certain time record
     *
     * @param array $timeSheetEntryID  timeSheetEntryID of the record
     * @return array         the record's data (time, activity id, project id etc) as array, false on failure
     */
    public abstract function timeSheet_get_data($timeSheetEntryID);

    /**
     * delete timeSheet entry
     *
     * @param integer $id -> ID of record
     */
    public abstract function timeEntry_delete($id);

    /**
     * create timeSheet entry
     *
     * @param integer $data  array with record data
     */
    public abstract function timeEntry_create($data);

    /**
     * edit timeSheet entry
     *
     * @param integer $id ID of record
     * @param array $data  array with new record data
     * @author th
     */
    public abstract function timeEntry_edit($id, Array $data);

    /**
     * saves timeframe of user in database (table conf)
     *
     * @param string $timeframeBegin unix seconds
     * @param string $timeframeEnd unix seconds
     * @param string $user ID of user
     */
    public abstract function save_timeframe($timeframeBegin,$timeframeEnd,$user);

    /**
     * returns list of projects for specific group as array
     *
     * @param array $groups ID of user in database
     * @return array
     */
    public abstract function get_projects(array $groups = null);

    /**
     * returns list of projects for specific group and specific customer as array
     *
     * @param integer $customerID customer id
     * @param array $groups list of group ids
     * @return array
     */
    public abstract function get_projects_by_customer($customerID,array $groups = null);

    /**
     *  Creates an array of clauses which can be joined together in the WHERE part
     *  of a sql query. The clauses describe whether a line should be included
     *  depending on the filters set.
     *
     *  This method also makes the values SQL-secure.
     *
     * @param array $users list of IDs of users to include
     * @param array $customers list of IDs of customers to include
     * @param array $projects list of IDs of projects to include
     * @param array $activities list of IDs of activities to include
     * @return array list of where clauses to include in the query
     *
     */
    public abstract function timeSheet_whereClausesFromFilters($users, $customers, $projects, $activities );

    /**
     * returns timesheet for specific user as multidimensional array
     *
     * @param integer $start start of timeframe in unix seconds
     * @param integer $end end of timeframe in unix seconds
     * @param array $users ID of user in table users
     * @param array $customers
     * @param array $projects
     * @param array $activities
     * @param bool $limit
     * @param bool $reverse_order
     * @param integer $filterCleared where -1 (default) means no filtering, 0 means only not cleared entries, 1 means only cleared entries
     * @param int $startRows
     * @param int $limitRows
     * @return array
     */
    public abstract function get_timeSheet($start, $end, $users = null, $customers = null, $projects = null, $activities = null,$limit = false, $reverse_order = false, $filterCleared = null, $startRows = 0, $limitRows = 0);

    /**
     * Returns a username for the given $apikey.
     *
     * @param string $apikey
     * @return string|null
     */
    public abstract function getUserByApiKey($apikey);

    /**
     * checks if user is logged on and returns user information as array
     *
     * <pre>
     * returns:
     * [userID] user ID,
     * [status] user status (rights),
     * [name] username
     * </pre>
     *
     * @param integer $kimai_user ID of user in table users
     * @return array
     */
    public abstract function checkUserInternal($kimai_user);

    /**
     * write global configuration into $this->kga including defaults for user settings.
     *
     * @return array $this->kga
     */
    public abstract function get_global_config();

    /**
     * write details of a specific user into $this->kga
     *
     * @param integer $user ID of user in table users
     * @return array $this->kga
     */
    public abstract function get_user_config($user);

    /**
     * write details of a specific customer into $this->kga
     *
     * @param integer $user ID of user in table users
     * @return array $this->kga
     */
    public abstract function get_customer_config($user);

    /**
     * checks if a customer with this name exists
     *
     * @param string $name
     * @return integer
     */
    public abstract function is_customer_name($name);

    /**
     * returns time summary of current timesheet
     *
     * @param integer $start start of timeframe in unix seconds
     * @param integer $end end of timeframe in unix seconds
     * @param array $users ID of user in table users
     * @param array $customers
     * @param array $projects
     * @param array $activities
     * @param null $filterCleared
     * @return int
     */
    public abstract function get_duration($start,$end,$users = null, $customers = null, $projects = null, $activities = null, $filterCleared = null);

    /**
     * returns list of customers in a group as array
     *
     * @param array $groups ID of group in table groups or "all" for all groups
     * @return array
     */
    public abstract function get_customers(array $groups = null);

    ## Load into Array: Activities
    public abstract function get_activities(array $groups = null);

    /**
     * Get an array of activities, which should be displayed for a specific project.
     * Those are activities which were assigned to the project or which are assigned to
     * no project.
     * Two joins can occur:
     *  The JOIN is for filtering the activities by groups.
     *  The LEFT JOIN gives each activity row the project id which it has been assigned
     *  to via the projects_activities table or NULL when there is no assignment. So we only
     *  take rows which have NULL or the project id in that column.
     *
     * @param $projectID
     * @param array $groups
     * @return
     */
    public abstract function get_activities_by_project($projectID, array $groups = null);

    /**
     * returns list of activities used with specified customer
     *
     * @param integer $customer_ID filter for only this ID of a customer
     * @return array
     */
    public abstract function get_activities_by_customer($customer_ID);

    /**
     * returns time of currently running activity recording as array
     *
     * result is meant as params for the stopwatch if the window is reloaded
     *
     * <pre>
     * returns:
     * [all] start time of entry in unix seconds (forgot why I named it this way, sorry ...)
     * [hour]
     * [min]
     * [sec]
     * </pre>
     *
     * @return array
     */
    public abstract function get_current_timer();

    /**
     * returns the version of the installed Kimai database to compare it with the package version
     *
     * @return array
     * @author th
     *
     * [0] => version number (x.x.x)
     * [1] => svn revision number
     *
     */
    public abstract function get_DBversion();

    /**
     * returns the key for the session of a specific user
     *
     * the key is both stored in the database (users table) and a cookie on the client.
     * when the keys match the user is allowed to access the Kimai GUI.
     * match test is performed via function userCheck()
     *
     * @param integer $user ID of user in table users
     * @return string
     */
    public abstract function get_seq($user);

    /**
     * returns array of all users
     * [userID] => 23103741
     * [name] => admin
     * [status] => 0
     * [mail] => 0
     * [active] => 0
     *
     * @param int $trash
     * @param array $groups list of group ids the users must be a member of
     * @return array
     */
    public abstract function get_users($trash = 0, array $groups = null);

    /**
     * returns array of all groups
     * [0]=> array(6) {
     *      ["groupID"]      =>  string(1) "1"
     *      ["userID"]  =>  string(9) "1234"
     *      ["trash"]   =>  string(1) "0"
     *      ["count_users"] =>  string(1) "2"
     * }
     * [1]=> array(6) {
     *      ["groupID"]      =>  string(1) "2"
     *      ["name"]    =>  string(4) "Test"
     *      ["userID"]  =>  string(9) "12345"
     *      ["trash"]   =>  string(1) "0"
     *      ["count_users"] =>  string(1) "1"
     *  }
     *
     * @param int $trash
     * @return array
     */
    public abstract function get_groups($trash = 0);

    /**
     * Performed when the stop buzzer is hit.
     *
     * @param integer $id id of the entry to stop
     * @return boolean
     */
    public abstract function stopRecorder($id);

    /**
     * starts timesheet record
     *
     * @param integer $projectID ID of project to record
     * @param $activityID
     * @param $user
     * @param $startTime
     * @return int id of the new entry or false on failure
     */
    public abstract function startRecorder($projectID, $activityID, $user, $startTime);

    /**
     * Just edit the project for an entry. This is used for changing the project
     * of a running entry.
     *
     * @param int $timeSheetEntryID id of the timesheet entry
     * @param int $projectID id of the project to change to
     */
    public abstract function timeEntry_edit_project($timeSheetEntryID,$projectID);

    /**
     * Just edit the activity for an entry. This is used for changing the activity
     * of a running entry.
     *
     * @param int $timeSheetEntryID id of the timesheet entry
     * @param int $activityID id of the activity to change to
     */
    public abstract function timeEntry_edit_activity($timeSheetEntryID,$activityID);


    /**
     * return ID of specific customer named 'XXX'
     *
     * @param string $name name of the customer in table customers
     * @return integer
     */
    public abstract function customer_nameToID($name);

    /**
     * return ID of specific user named 'XXX'
     *
     * @param integer $name name of user in table users
     * @return int id of the customer
     */
    public abstract function user_name2id($name);

    /**
     * return name of a user with specific ID
     *
     * @param string $id the user's userID
     * @return int
     */
    public abstract function userIDToName($id);

    /**
     * returns the date of the first timerecord of a user (when did the user join?)
     * this is needed for the datepicker
     * @param integer $userID id of user
     * @return integer unix seconds of first timesheet record
     */
    public abstract function getjointime($userID);

    /**
     * returns list of users the given user can watch
     *
     * @param integer $user the user information array
     * @return array
     */
    public abstract function get_user_watchable_users($user);

    /**
     * returns list of users the given customer can watch
     *
     * @param integer $customer the customer information array
     * @return array
     */
    public abstract function get_customer_watchable_users($customer);

    /**
     * returns assoc. array where the index is the ID of a user and the value the time
     * this user has accumulated in the given time with respect to the filtersettings
     *
     * @param integer $start from this timestamp
     * @param integer $end to this timestamp
     * @param array $users ID of user in table users
     * @param array $customers ID of customer in table customers
     * @param array $projects ID of project in table projects
     * @param array $activities
     * @return array
     */
    public abstract function get_time_users($start,$end,$users = null, $customers = null, $projects = null, $activities = null);

    /**
     * returns list of time summary attached to customer ID's within specific timeframe as array
     *
     * @param integer $start start of timeframe in unix seconds
     * @param integer $end end of timeframe in unix seconds
     * @param array $users ID of user in table users
     * @param array $customers ID of customer in table customers
     * @param array $projects ID of project in table projects
     * @param array $activities
     * @return array
     */
    public abstract function get_time_customers($start,$end,$users = null, $customers = null, $projects = null, $activities = null);

    /**
     * returns list of time summary attached to project ID's within specific timeframe as array
     *
     * @param integer $start start time in unix seconds
     * @param integer $end end time in unix seconds
     * @param array $users ID of user in table users
     * @param array $customers ID of customer in table customers
     * @param array $projects ID of project in table projects
     * @param array $activities
     * @return array
     */
    public abstract function get_time_projects($start,$end,$users = null, $customers = null, $projects = null,$activities = null);

    /**
     * returns list of time summary attached to activity ID's within specific timeframe as array
     *
     * @param integer $start start time in unix seconds
     * @param integer $end end time in unix seconds
     * @param array $users ID of user in table users
     * @param array $customers ID of customer in table customers
     * @param array $projects ID of project in table projects
     * @param array $activities
     * @return array
     */
    public abstract function get_time_activities($start,$end,$users = null, $customers = null, $projects = null, $activities = null);

    /**
     * Save hourly rate to database.
     *
     * @param $userID
     * @param $projectID
     * @param $activityID
     * @param $rate
     * @return
     */
    public abstract function save_rate($userID,$projectID,$activityID,$rate);

    /**
     * Read hourly rate from database.
     *
     * @param $userID
     * @param $projectID
     * @param $activityID
     * @return
     */
    public abstract function get_rate($userID,$projectID,$activityID);

    /**
     * Remove hourly rate from database.
     *
     * @param $userID
     * @param $projectID
     * @param $activityID
     * @return
     */
    public abstract function remove_rate($userID,$projectID,$activityID);

    /**
     * Query the database for the best fitting hourly rate for the given user, project and activity.
     *
     * @param $userID
     * @param $projectID
     * @param $activityID
     * @return
     */
    public abstract function get_best_fitting_rate($userID,$projectID,$activityID);

    /**
     * Query the database for all fitting hourly rates for the given user, project and activity.
     *
     * @param $userID
     * @param $projectID
     * @param $activityID
     * @return
     */
    public abstract function allFittingRates($userID,$projectID,$activityID);

    /**
     * Save fixed rate to database.
     *
     * @param $projectID
     * @param $activityID
     * @param $rate
     * @return
     */
    public abstract function save_fixed_rate($projectID,$activityID,$rate);

    /**
     * Read fixed rate from database.
     *
     * @param $projectID
     * @param $activityID
     * @return
     */
    public abstract function get_fixed_rate($projectID,$activityID);

    /**
     * Remove fixed rate from database.
     *
     * @param $projectID
     * @param $activityID
     * @return
     */
    public abstract function remove_fixed_rate($projectID,$activityID);

    /**
     * Query the database for the best fitting fixed rate for the given user, project and activity.
     *
     * @param $projectID
     * @param $activityID
     * @return
     */
    public abstract function get_best_fitting_fixed_rate($projectID,$activityID);

    /**
     * Query the database for all fitting fixed rates for the given user, project and activity.
     *
     * @param $projectID
     * @param $activityID
     * @return
     */
    public abstract function allFittingFixedRates($projectID,$activityID);

    /**
     * Save a new secure key for a user to the database. This key is stored in the users cookie and used
     * to reauthenticate the user.
     *
     * @param $userId
     * @param $keymai
     * @return
     */
    public abstract function user_loginSetKey($userId,$keymai);

    /**
     * Save a new secure key for a customer to the database. This key is stored in the clients cookie and used
     * to reauthenticate the customer.
     *
     * @param $customerId
     * @param $keymai
     * @return
     */
    public abstract function customer_loginSetKey($customerId,$keymai);

    /**
     * Update the ban status of a user. This increments the ban counter.
     * Optionally it sets the start time of the ban to the current time.
     *
     * @param $userId
     * @param bool $resetTime
     * @return
     */
    public abstract function loginUpdateBan($userId,$resetTime = false);


    /**
     * Return all rows for the given sql query.
     *
     * @param string $query the sql query to execute
     */
    public abstract function queryAll($query);


    /**
     * checks if given $projectId exists in the db
     *
     * @param int $projectId
     * @return bool
     */
    public abstract function isValidProjectId($projectId);

    /**
     * checks if given $activityId exists in the db
     *
     * @param int $activityId
     * @return bool
     */
    public abstract function isValidActivityId($activityId);

    /**
     * checks if a given db row based on the $idColumn & $id exists
     * @param string $table
     * @param array $filter
     * @return bool
     */
    protected abstract function rowExists($table, Array $filter);

    /**
     * associates an Activity with a collection of Projects in the context of a user group.
     * Projects that are currently associated with the Activity but not mentioned in the specified id collection, will get un-assigned.
     * The fundamental difference to assign_activityToProjects(activityID, projectIDs) is that this method is aware of potentially existing assignments
     * that are invisible and thus unmanagable to the user as the user lacks access to the Projects.
     * It is implicitly assumed that the user has access to the Activity and the Projects designated by the method parameters.
     *
     * @param integer $activityID the id of the Activity to associate
     * @param array $projectIDs the array of Project ids to associate
     * @param integer $group the user's group id
     * @return bool
     */
    function assignActivityToProjectsForGroup($activityID, $projectIDs, $group)
    {
        $projectIds = array_merge($projectIDs, $this->getNonManagableAssignedElementIds("activity", "project", $activityID, $group));
        return $this->assign_activityToProjects($activityID, $projectIds);
    }

    /**
     * associates a Project with a collection of Activities in the context of a user group.
     * Activities that are currently associated with the Project but not mentioned in the specified id collection, will get un-assigned.
     * The fundamental difference to assign_projectToActivities($projectID, $activityIDs) is that this method is aware of potentially existing assignments
     * that are invisible and thus unmanagable to the user as the user lacks access to the Activities.
     * It is implicitly assumed that the user has access to the Project and the Activities designated by the method parameters.
     *
     * @param integer $projectID the id of the Project to associate
     * @param array $activityIDs the array of Activity ids to associate
     * @param integer $group the user's group id
     * @return bool
     */
    function assignProjectToActivitiesForGroup($projectID, $activityIDs, $group)
    {
        $activityIds = array_merge($activityIDs, $this->getNonManagableAssignedElementIds("project", "activity", $projectID, $group));
        return $this->assign_projectToActivities($projectID, $activityIds);
    }

    /**
     * computes an array of (project- or activity-) ids for Project-Activity-Assignments that are unmanage-able for the given group.
     * This method supports Project-Activity-Assignments as seen from both end points.
     * The returned array contains the ids of all those Projects or Activities that are assigned to Activities or Projects but cannot be seen by the user that
     * looks at the assignments.
     * @param string $parentSubject a string designating the parent in the assignment, must be one of "project" or "activity"
     * @param string $subject a string designating the child in the assignment, must be one of "project" or "activity"
     * @param integer $parentId the id of the parent
     * @param integer $group the id of the user's group
     * @return array the array of ids of those child Projects or Activities that are assigned to the parent Activity or Project but are invisible to the user
     */
    function getNonManagableAssignedElementIds($parentSubject, $subject, $parentId, $group)
    {
        $resultIds = array();
        $selectedIds = array();
        $allElements = array();
        $viewableElements = array();
        switch ($parentSubject . "_" . $subject)
        {
            case 'project_activity':
                $selectedIds = $this->project_get_activities($parentId);
                break;
            case 'activity_project':
                $selectedIds = $this->activity_get_projects($parentId);
                break;
        }

        //if there are no assignments currently, there's nothing too much that could get deleted :)
        if (count($selectedIds) > 0)
        {
            switch ($parentSubject . "_" . $subject)
            {
                case 'project_activity':
                    $allElements = $this->get_activities();
                    $viewableElements = $this->get_activities($group);
                    break;
                case 'activity_project':
                    $allElements = $this->get_projects();
                    $viewableElements = $this->get_projects($group);
                    break;
            }
            //if there are no elements hidden from the group, there's nothing too much that could get deleted either
            if (count($allElements) > count($viewableElements))
            {
                //1st, find the ids of the elements that are invisible for the group
                $startvisibleIds = array();
                $idField = $subject . "_ID";
                foreach ($allElements as $allElement)
                {
                    $seen = false;
                    foreach ($viewableElements as $viewableElement)
                    {
                        if ($viewableElement[$idField] == $allElement[$idField])
                        {
                            $seen = true;
                            break; //element is viewable, so we can stop here
                        }
                    }
                    if(!$seen)
                    {
                        $startvisibleIds[] = $allElement[$idField];
                    }
                }
                if(count($startvisibleIds) > 0)
                {
                    //2nd, find the invisible assigned elements and add them to the result array
                    foreach($selectedIds as $selectedId)
                    {
                        if(in_array($selectedId, $startvisibleIds))
                        {
                            $resultIds[] = $selectedId;
                        }
                    }
                }
            }
        }
        return $resultIds;
    }

}
