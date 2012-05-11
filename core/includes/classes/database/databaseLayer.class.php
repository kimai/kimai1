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
abstract class DatabaseLayer {

  protected $kga;
  protected $conn;

  /**
   * Instantiate a new database layer..
   * The provided kimai global array will be stored as a reference.
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
   * Connect to the database.
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


  public function getZefTable()
  {
  	return $this->kga['server_prefix'].'timeSheet';
  }
  
  public function getExpenseTable() {
  	return $this->kga['server_prefix'].'expenses';
  }
  
  public function getUserTable() {
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
  * @param array $grp_array    contains one or more groupIDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_customerToGroups($customerID, $grp_array);

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
  * @param array $grp_array    contains one or more groupIDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_projectToGroups($projectID, $grp_array);

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
  * Returns the data of a certain task
  *
  * @param array $activityID  activityID of the project
  * @return array         the event's data (name, comment etc) as array, false on failure
  */
  public abstract function activity_get_data($activityID);

  /**
  * Edits an event by replacing its data by the new array
  *
  * @param array $activityID  activityID of the project to be edited
  * @param array $data    name, comment and other new data of the event
  * @return boolean       true on success, false on failure
  */
  public abstract function activity_edit($activityID, $data);

  /**
  * Assigns an event to 1-n groups by adding entries to the cross table
  *
  * @param int $activityID         activityID of the project to which the groups will be assigned
  * @param array $grp_array    contains one or more groupIDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_activityToGroups($activityID, $grp_array);

  /**
  * Assigns an event to 1-n projects by adding entries to the cross table
  *
  * @param int $activityID         id of the event to which projects will be assigned
  * @param array $gpct_array    contains one or more projectIDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_activityToProjects($activityID, $pct_array);

  /**
  * Assigns 1-n events to a project by adding entries to the cross table
  *
  * @param int $projectID         id of the project to which events will be assigned
  * @param array $evt_array    contains one or more activityIDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_projectToActivities($projectID, $evt_array);

  /**
  * returns all the projects to which the event was assigned
  *
  * @param array $activityID  activityID of the project
  * @return array         contains the projectIDs of the projects or false on error
  */
  public abstract function activity_get_projects($activityID);

  /**
  * returns all the events which were assigned to a project
  *
  * @param integer $projectID  ID of the project
  * @return array         contains the activityIDs of the events or false on error
  */
  public abstract function project_get_activities($projectID);

  /**
  * returns all the groups of the given event
  *
  * @param array $activityID  activityID of the project
  * @return array         contains the groupIDs of the groups or false on error
  */
  public abstract function activity_get_groups($activityID);

  /**
  * deletes an event
  *
  * @param array $activityID  activityID of the event
  * @return boolean       true on success, false on failure
  */
  public abstract function activity_delete($activityID);

  /**
  * Assigns a group to 1-n customers by adding entries to the cross table
  * (counterpart to assign_customerToGroups)
  *
  * @param array $grp_id        grp_id of the group to which the customers will be assigned
  * @param array $knd_array    contains one or more IDs of customers
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_groupToCustomers($grp_id, $knd_array);

  /**
  * Assigns a group to 1-n projects by adding entries to the cross table
  * (counterpart to assign_pct2grp)
  *
  * @param array $grp_id        grp_id of the group to which the projects will be assigned
  * @param array $pct_array    contains one or more projectIDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_groupToProjects($grp_id, $pct_array);

  /**
  * Assigns a group to 1-n events by adding entries to the cross table
  * (counterpart to assign_evt2grp)
  *
  * @param array $grp_id        grp_id of the group to which the events will be assigned
  * @param array $evt_array    contains one or more activityIDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_groupToActivities($grp_id, $evt_array);

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
  * @return boolean       true on success, false on failure
  */
  public abstract function user_delete($userID);

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
  * Assigns a leader to 1-n groups by adding entries to the cross table
  *
  * @param int $userID        userID of the group leader to whom the groups will be assigned
  * @param array $grp_array    contains one or more groupIDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_groupleaderToGroups($userID, $grp_array);

  /**
  * Assigns a group to 1-n group leaders by adding entries to the cross table
  * (counterpart to assign_ldr2grp)
  *
  * @param array $grp_id        grp_id of the group to which the group leaders will be assigned
  * @param array $ldr_array    contains one or more userIDs of the leaders)
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_groupToGroupleaders($grp_id, $ldr_array);

  /**
  * returns all the groups of the given group leader
  *
  * @param array $userID  userID of the group leader
  * @return array         contains the groupIDs of the groups or false on error
  */
  public abstract function groupleader_get_groups($userID);

  /**
  * returns all the group leaders of the given group
  *
  * @param array $grp_id  grp_id of the group
  * @return array         contains the userIDs of the group's group leaders or false on error
  */
  public abstract function group_get_groupleaders($grp_id);

  /**
  * Adds a new group
  *
  * @param array $data  name and other data of the new group
  * @return int         the grp_id of the new group, false on failure
  */
  public abstract function group_create($data);

  /**
  * Returns the data of a certain group
  *
  * @param array $grp_id  grp_id of the group
  * @return array         the group's data (name, leader ID, etc) as array, false on failure
  */
  public abstract function group_get_data($grp_id);

  /**
  * Returns the number of users in a certain group
  *
  * @param array $grp_id   grp_id of the group
  * @return int            the number of users in the group
  */
  public abstract function group_count_users($grp_id);

  /**
  * Edits a group by replacing its data by the new array
  *
  * @param array $grp_id  grp_id of the group to be edited
  * @param array $data    name and other new data of the group
  * @return boolean       true on success, false on failure
  */
  public abstract function group_edit($grp_id, $data);

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
  * @param array $grp_id  grp_id of the group
  * @return boolean       true on success, false on failure
  */
  public abstract function group_delete($grp_id);

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
  * @param integer $user ID of user in table usr
  * @return array with all IDs of current recordings. This array will be empty if there are none.
  */
  public abstract function get_current_recordings($userID);

  /**
  * Returns the data of a certain time record
  *
  * @param array $zef_id  zef_id of the record
  * @return array         the record's data (time, event id, project id etc) as array, false on failure
  */
  public abstract function timeSheet_get_data($zef_id);

  /**
  * delete zef entry
  *
  * @param integer $id -> ID of record
  */
  public abstract function timeEntry_delete($id);

  /**
  * create zef entry
  *
  * @param integer $data  array with record data
  */
  public abstract function timeEntry_create($data);

  /**
  * edit zef entry
  *
  * @param integer $id ID of record
  * @param array $data  array with new record data
  * @author th
  */
  public abstract function timeEntry_edit($id, Array $data);

  /**
  * saves timespace of user in database (table conf)
  *
  * @param string $timeframeBegin unix seconds
  * @param string $timeframeEnd unix seconds
  * @param string $user ID of user
  */
  public abstract function save_timeframe($timeframeBegin,$timeframeEnd,$user);

  /**
  * returns list of projects for specific group as array
  *
  * @param integer $user ID of user in database
  * @return array
  */
  public abstract function get_arr_projects(array $groups = null);

  /**
  * returns list of projects for specific group and specific customer as array
  *
  * @param integer $customerID customer id
  * @param array $groups list of group ids
  * @return array
  */
  public abstract function get_arr_projects_by_customer($customerID,array $groups = null);

  /**
  *  Creates an array of clauses which can be joined together in the WHERE part
  *  of a sql query. The clauses describe whether a line should be included
  *  depending on the filters set.
  *
  *  This method also makes the values SQL-secure.
  *
  * @param Array list of IDs of users to include
  * @param Array list of IDs of customers to include
  * @param Array list of IDs of projects to include
  * @param Array list of IDs of events to include
  * @return Array list of where clauses to include in the query
  *
  */
  public abstract function zef_whereClausesFromFilters($users, $customers , $projects , $events );

  /**
  * returns timesheet for specific user as multidimensional array
  *
  * @param integer $user ID of user in table usr
  * @param integer $start start of timespace in unix seconds
  * @param integer $end end of timespace in unix seconds
  * @param integer $filterCleared where -1 (default) means no filtering, 0 means only not cleared entries, 1 means only cleared entries
  * @return array
  */
  public abstract function get_arr_timeSheet($start,$end,$users = null, $customers = null, $projects = null, $events = null,$limit = false, $reverse_order = false, $filterCleared = null, $startRows = 0, $limitRows = 0);

  /**
   * Returns a username for the given $apikey.
   *
   * @param string $apikey
   * @return string|null
   */
  public abstract function getUserByApiKey($apikey);

  /**
  * checks if user is logged on and returns user information as array
  * kicks client if is not verified
  * TODO: this and get_config should be one function
  *
  * <pre>
  * returns:
  * [userID] user ID,
  * [status] user status (rights),
  * [name] username
  * </pre>
  *
  * @param integer $user ID of user in table usr
  * @return array
  */
  public abstract function checkUserInternal($kimai_usr);

  public function checkUser()
  {
    if (isset($_COOKIE['kimai_usr']) && isset($_COOKIE['kimai_key']) && $_COOKIE['kimai_usr'] != "0" && $_COOKIE['kimai_key'] != "0") {
        $kimai_usr = addslashes($_COOKIE['kimai_usr']);
        $kimai_key = addslashes($_COOKIE['kimai_key']);

		if ($this->get_seq($kimai_usr) != $kimai_key) {
			kickUser();
		} else {
			return $this->checkUserInternal($kimai_usr);
		}
	}
	kickUser();
  }

  /**
  * write global configuration into $this->kga including defaults for user settings.
  *
  * @param integer $user ID of user in table usr
  * @return array $this->kga
  */
  public abstract function get_global_config();

  /**
  * write details of a specific user into $this->kga
  *
  * @param integer $user ID of user in table usr
  * @return array $this->kga
  */
  public abstract function get_user_config($user);

  /**
  * write details of a specific customer into $this->kga
  *
  * @param integer $user ID of user in table usr
  * @return array $this->kga
  */
  public abstract function get_customer_config($user);

  /**
  * checks if a customer with this name exists
  *
  * @param string name
  * @return integer
  */
  public abstract function is_customer_name($name);

  /**
  * returns time summary of current timesheet
  *
  * @param integer $user ID of user in table usr
  * @param integer $start start of timespace in unix seconds
  * @param integer $end end of timespace in unix seconds
  * @return integer
  */
  public abstract function get_duration($start,$end,$users = null, $customers = null, $projects = null, $events = null,$filterCleared = null);

  /**
  * returns list of customers in a group as array
  *
  * @param integer $group ID of group in table grp or "all" for all groups
  * @return array
  */
  public abstract function get_arr_customers(array $groups = null);

  ## Load into Array: Events
  public abstract function get_arr_activities(array $groups = null);

  /**
  * Get an array of events, which should be displayed for a specific project.
  * Those are events which were assigned to the project or which are assigned to
  * no project.
  *
  * Two joins can occur:
  *  The JOIN is for filtering the events by groups.
  *
  *  The LEFT JOIN gives each event row the project id which it has been assigned
  *  to via the pct_evt table or NULL when there is no assignment. So we only
  *  take rows which have NULL or the project id in that column.
  */
  public abstract function get_arr_activities_by_project($pct, array $groups = null);

  /**
  * returns list of events used with specified customer
  *
  * @param integer $customer filter for only this ID of a customer
  * @return array
  */
  public abstract function get_arr_activities_by_customer($customer_ID);

  /**
  * returns time of currently running event recording as array
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
  * @param integer $user ID of user in table usr
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
  * the key is both stored in the database (usr table) and a cookie on the client.
  * when the keys match the user is allowed to access the Kimai GUI.
  * match test is performed via function userCheck()
  *
  * @param integer $user ID of user in table usr
  * @return string
  */
  public abstract function get_seq($user);

  /**
  * returns array of all users
  *
  * [userID] => 23103741
  * [name] => admin
  * [status] => 0
  * [grp_name] => miesepriem
  * [mail] => 0
  * [active] => 0
  *
  * @param array $groups list of group ids the users must be a member of
  * @return array
  */
  public abstract function get_arr_users($trash=0,array $groups = null);

  /**
  * returns array of all groups
  *
  * [0]=> array(6) {
  *      ["groupID"]      =>  string(1) "1"
  *      ["grp_name"]    =>  string(5) "admin"
  *      ["userID"]  =>  string(9) "1234"
  *      ["grp_trash"]   =>  string(1) "0"
  *      ["count_users"] =>  string(1) "2"
  *      ["leader_name"] =>  string(5) "user1"
  * }
  *
  * [1]=> array(6) {
  *      ["groupID"]      =>  string(1) "2"
  *      ["grp_name"]    =>  string(4) "Test"
  *      ["userID"]  =>  string(9) "12345"
  *      ["grp_trash"]   =>  string(1) "0"
  *      ["count_users"] =>  string(1) "1"
  *      ["leader_name"] =>  string(7) "user2"
  *  }
  *
  * @return array
  *
  */
  public abstract function get_arr_groups($trash=0);

  /**
  * returns array of all groups of a group leader
  *
  * [0]=> array(6) {
  *      ["groupID"]      =>  string(1) "1"
  *      ["grp_name"]    =>  string(5) "admin"
  *      ["userID"]  =>  string(9) "1234"
  *      ["grp_trash"]   =>  string(1) "0"
  *      ["count_users"] =>  string(1) "2"
  *      ["leader_name"] =>  string(5) "user1"
  * }
  *
  * [1]=> array(6) {
  *      ["groupID"]      =>  string(1) "2"
  *      ["grp_name"]    =>  string(4) "Test"
  *      ["userID"]  =>  string(9) "12345"
  *      ["grp_trash"]   =>  string(1) "0"
  *      ["count_users"] =>  string(1) "1"
  *      ["leader_name"] =>  string(7) "user2"
  *  }
  *
  * @return array
  */
  public abstract function get_arr_groups_by_leader($leader_id,$trash=0);

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
  * @return id of the new entry or false on failure
  */
  public abstract function startRecorder($projectID,$activityID,$user);

  /**
  * Just edit the project for an entry. This is used for changing the project
  * of a running entry.
  *
  * @param $zef_id id of the timesheet entry
  * @param $projectID id of the project to change to
  */
  public abstract function timeEntry_edit_project($zef_id,$projectID);

  /**
  * Just edit the task for an entry. This is used for changing the task
  * of a running entry.
  *
  * @param $zef_id id of the timesheet entry
  * @param $activityID id of the task to change to
  */
  public abstract function timeEntry_edit_activity($zef_id,$activityID);


  /**
   * return ID of specific customer named 'XXX'
   *
   * @param string $name name of the customer in table knd
   * @return integer
   */
  public abstract function customer_nameToID($name);

  /**
  * return ID of specific user named 'XXX'
  *
  * @param integer $name name of user in table usr
  * @return id of the customer
  */
  public abstract function user_name2id($name);

  /**
  * return name of a user with specific ID
  *
  * @param string $id the user's userID
  * @return int
  */
  public abstract function user_IDToName($id);

  /**
  * returns the date of the first timerecord of a user (when did the user join?)
  * this is needed for the datepicker
  * @param integer $id of user
  * @return integer unix seconds of first timesheet record
  */
  public abstract function getjointime($userID);

  /**
  * returns list of users the given user can watch
  *
  * @param integer $user the user information array
  * @return array
  */
  public abstract function get_arr_watchable_users($user);

  /**
  * returns assoc. array where the index is the ID of a user and the value the time
  * this user has accumulated in the given time with respect to the filtersettings
  *
  * @param integer $start from this timestamp
  * @param integer $end to this  timestamp
  * @param integer $user ID of user in table usr
  * @param integer $customer ID of customer in table knd
  * @param integer $project ID of project in table pct
  * @return array
  */
  public abstract function get_arr_time_users($start,$end,$users = null, $customers = null, $projects = null, $events = null);

  /**
  * returns list of time summary attached to customer ID's within specific timespace as array
  *
  * @param integer $start start of timespace in unix seconds
  * @param integer $end end of timespace in unix seconds
  * @param integer $user filter for only this ID of auser
  * @param integer $customer filter for only this ID of a customer
  * @param integer $project filter for only this ID of a project
  * @return array
  */
  public abstract function get_arr_time_customers($start,$end,$users = null, $customers = null, $projects = null, $events = null);

  /**
  * returns list of time summary attached to project ID's within specific timespace as array
  *
  * @param integer $start start time in unix seconds
  * @param integer $end end time in unix seconds
  * @param integer $user filter for only this ID of auser
  * @param integer $customer filter for only this ID of a customer
  * @param integer $project filter for only this ID of a project
  * @return array
  */
  public abstract function get_arr_time_projects($start,$end,$users = null, $customers = null, $projects = null,$events = null);

  /**
  * returns list of time summary attached to event ID's within specific timespace as array
  *
  * @param integer $start start time in unix seconds
  * @param integer $end end time in unix seconds
  * @param integer $user filter for only this ID of auser
  * @param integer $customer filter for only this ID of a customer
  * @param integer $project filter for only this ID of a project
  * @return array
  */
  public abstract function get_arr_time_activities($start,$end,$users = null, $customers = null, $projects = null, $events = null);

  /**
  * Set field status for users to 1 if user is a group leader, otherwise to 2.
  * Admin status will never be changed.
  * Calling function should start and end sql transaction.
  */
  public abstract function update_leader_status();

  /**
  * Save hourly rate to database.
  */
  public abstract function save_rate($userID,$projectID,$activityID,$rate);

  /**
  * Read hourly rate from database.
  */
  public abstract function get_rate($userID,$projectID,$activityID);

  /**
  * Remove hourly rate from database.
  */
  public abstract function remove_rate($userID,$projectID,$activityID);

  /**
  * Query the database for the best fitting hourly rate for the given user, project and event.
  */
  public abstract function get_best_fitting_rate($userID,$projectID,$activityID);

  /**
  * Query the database for all fitting hourly rates for the given user, project and event.
  */
  public abstract function allFittingRates($userID,$projectID,$activityID);

  /**
  * Save fixed rate to database.
  */
  public abstract function save_fixed_rate($projectID,$activityID,$rate);

  /**
  * Read fixed rate from database.
  */
  public abstract function get_fixed_rate($projectID,$activityID);

  /**
  * Remove fixed rate from database.
  */
  public abstract function remove_fixed_rate($projectID,$activityID);

  /**
  * Query the database for the best fitting fixed rate for the given user, project and event.
  */
  public abstract function get_best_fitting_fixed_rate($projectID,$activityID);

  /**
  * Query the database for all fitting fixed rates for the given user, project and event.
  */
  public abstract function allFittingFixedRates($projectID,$activityID);

  /**
  * Save a new secure key for a user to the database. This key is stored in the users cookie and used
  * to reauthenticate the user.
  */
  public abstract function user_loginSetKey($userId,$keymai);

  /**
  * Save a new secure key for a customer to the database. This key is stored in the clients cookie and used
  * to reauthenticate the customer.
  */
  public abstract function customer_loginSetKey($customerId,$keymai);

  /**
  * Update the ban status of a user. This increments the ban counter.
  * Optionally it sets the start time of the ban to the current time.
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
   * checks if given $eventId exists in the db
   * 
   * @param int $eventId
   * @return bool
   */
  public abstract function isValidActivityId($eventId);
  
  /**
   * checks if a given db row based on the $idColumn & $id exists
   * @param string $table
   * @param array $filter
   * @return bool
   */
  protected abstract function rowExists($table, Array $filter);
  

  /**
  * associates an Event with a collection of Projects in the context of a user group.
  * Projects that are currently associated with the Event but not mentioned in the specified id collection, will get un-assigned.
  * The fundamental difference to assign_activityToProjects(activityID, pct_array) is that this method is aware of potentially existing assignments 
  * that are invisible and thus unmanagable to the user as the user lacks access to the Projects.
  * It is implicitly assumed that the user has access to the Event and the Projects designated by the method parameters.
  * @param integer $activityID the id of the Event to associate
  * @param array $pct_array the array of Project ids to associate
  * @param integer $group the user's group id 
  */
  function assignActivityToProjectsForGroup($activityID, $pct_array, $group)
  {
      $projectIds = array_merge($pct_array, $this->getNonManagableAssignedElementIds("evt", "pct", $activityID, $group));
      $this->assign_activityToProjects($activityID, $projectIds);
  }

  /**
  * associates a Project with a collection of Events in the context of a user group.
  * Events that are currently associated with the Project but not mentioned in the specified id collection, will get un-assigned.
  * The fundamental difference to assign_projectToActivities($projectID, $evt_array) is that this method is aware of potentially existing assignments 
  * that are invisible and thus unmanagable to the user as the user lacks access to the Events.
  * It is implicitly assumed that the user has access to the Project and the Events designated by the method parameters.
  * @param integer $projectID the id of the Project to associate
  * @param array $evt_array the array of Event ids to associate
  * @param integer $group the user's group id 
  */
  function assignProjectToActivitiesForGroup($projectID, $evt_array, $group)
  {
      $eventIds = array_merge($evt_array, $this->getNonManagableAssignedElementIds("pct", "evt", $projectID, $group));
      $this->assign_projectToActivities($projectID, $eventIds);
  }

  /**
  * computes an array of (project- or event-) ids for Project-Event-Assignments that are unmanage-able for the given group.
  * This method supports Project-Event-Assignments as seen from both end points.
  * The returned array contains the ids of all those Projects or Events that are assigned to Events or Projects but cannot be seen by the user that 
  * looks at the assignments.
  * @param string $parentSubject a string designating the parent in the assignment, must be one of "pct" or "evt"  
  * @param string $subject a string designating the child in the assignment, must be one of "pct" or "evt"  
  * @param integer $parentId the id of the parent 
  * @param integer $group the id of the user's group 
  * @return array the array of ids of those child Projects or Events that are assigned to the parent Event or Project but are invisible to the user
  */
  function getNonManagableAssignedElementIds($parentSubject, $subject, $parentId, $group)
  {
      $resultIds = array();
      $selectedIds = array();
      $allElements = array();
      $viewableElements = array();
      switch ($parentSubject . "_" . $subject)
      {
          case 'pct_evt':
              $selectedIds = $this->project_get_activities($parentId);
              break;
          case 'evt_pct':
              $selectedIds = $this->activity_get_projects($parentId);
              break;
      }

      //if there are no assignments currently, there's nothing too much that could get deleted :)
      if (count($selectedIds) > 0)
      {
          switch ($parentSubject . "_" . $subject)
          {
              case 'pct_evt':
                  $allElements = $this->get_arr_activities();
                  $viewableElements = $this->get_arr_activities($group);
                  break;
              case 'evt_pct':
                  $allElements = $this->get_arr_projects();
                  $viewableElements = $this->get_arr_projects($group);
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
