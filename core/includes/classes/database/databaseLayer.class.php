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
  	return $this->kga['server_prefix'].'pct';
  }
  
  /**
   * @return string the tablename with the server prefix
   */
  public function getEventTable()
  {
  	return $this->kga['server_prefix'].'evt';
  }

  /**
  * Add a new customer to the database.
  *
  * @param array $data  name, address and other data of the new customer
  * @return int         the knd_ID of the new customer, false on failure
  */
  public abstract function knd_create($data);

  /**
  * Returns the data of a certain customer
  *
  * @param array $knd_id  knd_id of the customer
  * @return array         the customer's data (name, address etc) as array, false on failure
  */
  public abstract function knd_get_data($knd_id);

  /**
  * Edits a customer by replacing his data by the new array
  *
  * @param array $knd_id  knd_id of the customer to be edited
  * @param array $data    name, address and other new data of the customer
  * @return boolean       true on success, false on failure
  */
  public abstract function knd_edit($knd_id, $data);

  /**
  * Assigns a customer to 1-n groups by adding entries to the cross table
  *
  * @param int $knd_id         knd_id of the customer to which the groups will be assigned
  * @param array $grp_array    contains one or more grp_IDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_knd2grps($knd_id, $grp_array);

  /**
  * returns all the groups of the given customer
  *
  * @param array $knd_id  knd_id of the customer
  * @return array         contains the grp_IDs of the groups or false on error
  */
  public abstract function knd_get_grps($knd_id);

  /**
  * deletes a customer
  *
  * @param array $knd_id  knd_id of the customer
  * @return boolean       true on success, false on failure
  */
  public abstract function knd_delete($knd_id);

  /**
  * Adds a new project
  *
  * @param array $data  name, comment and other data of the new project
  * @return int         the pct_ID of the new project, false on failure
  */
  public abstract function pct_create($data);

  /**
  * Returns the data of a certain project
  *
  * @param array $pct_id  pct_id of the project

  * @return array         the project's data (name, comment etc) as array, false on failure
  */
  public abstract function pct_get_data($pct_id);

  /**
  * Edits a project by replacing its data by the new array
  *
  * @param array $pct_id   pct_id of the project to be edited
  * @param array $data     name, comment and other new data of the project
  * @return boolean        true on success, false on failure
  */
  public abstract function pct_edit($pct_id, $data);

  /**
  * Assigns a project to 1-n groups by adding entries to the cross table
  *
  * @param int $pct_id        pct_id of the project to which the groups will be assigned
  * @param array $grp_array    contains one or more grp_IDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_pct2grps($pct_id, $grp_array);

  /**
  * returns all the groups of the given project
  *
  * @param array $pct_id  pct_id of the project
  * @return array         contains the grp_IDs of the groups or false on error
  */
  public abstract function pct_get_grps($pct_id);

  /**
  * deletes a project
  *
  * @param array $pct_id  pct_id of the project
  * @return boolean       true on success, false on failure
  */
  public abstract function pct_delete($pct_id);

  /**
  * Adds a new event
  *
  * @param array $data   name, comment and other data of the new event
  * @return int          the evt_ID of the new project, false on failure
  */
  public abstract function evt_create($data);

  /**
  * Returns the data of a certain task
  *
  * @param array $evt_id  evt_id of the project
  * @return array         the event's data (name, comment etc) as array, false on failure
  */
  public abstract function evt_get_data($evt_id);

  /**
  * Edits an event by replacing its data by the new array
  *
  * @param array $evt_id  evt_id of the project to be edited
  * @param array $data    name, comment and other new data of the event
  * @return boolean       true on success, false on failure
  */
  public abstract function evt_edit($evt_id, $data);

  /**
  * Assigns an event to 1-n groups by adding entries to the cross table
  *
  * @param int $evt_id         evt_id of the project to which the groups will be assigned
  * @param array $grp_array    contains one or more grp_IDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_evt2grps($evt_id, $grp_array);

  /**
  * Assigns an event to 1-n projects by adding entries to the cross table
  *
  * @param int $evt_id         id of the event to which projects will be assigned
  * @param array $gpct_array    contains one or more pct_IDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_evt2pcts($evt_id, $pct_array);

  /**
  * Assigns 1-n events to a project by adding entries to the cross table
  *
  * @param int $pct_id         id of the project to which events will be assigned
  * @param array $evt_array    contains one or more evt_IDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_pct2evts($pct_id, $evt_array);

  /**
  * returns all the projects to which the event was assigned
  *
  * @param array $evt_id  evt_id of the project
  * @return array         contains the pct_IDs of the projects or false on error
  */
  public abstract function evt_get_pcts($evt_id);

  /**
  * returns all the events which were assigned to a project
  *
  * @param integer $pct_id  pct_id of the project
  * @return array         contains the evt_IDs of the events or false on error
  */
  public abstract function pct_get_evts($pct_id);

  /**
  * returns all the groups of the given event
  *
  * @param array $evt_id  evt_id of the project
  * @return array         contains the grp_IDs of the groups or false on error
  */
  public abstract function evt_get_grps($evt_id);

  /**
  * deletes an event
  *
  * @param array $evt_id  evt_id of the event
  * @return boolean       true on success, false on failure
  */
  public abstract function evt_delete($evt_id);

  /**
  * Assigns a group to 1-n customers by adding entries to the cross table
  * (counterpart to assign_knd2grp)
  *
  * @param array $grp_id        grp_id of the group to which the customers will be assigned
  * @param array $knd_array    contains one or more knd_IDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_grp2knds($grp_id, $knd_array);

  /**
  * Assigns a group to 1-n projects by adding entries to the cross table
  * (counterpart to assign_pct2grp)
  *
  * @param array $grp_id        grp_id of the group to which the projects will be assigned
  * @param array $pct_array    contains one or more pct_IDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_grp2pcts($grp_id, $pct_array);

  /**
  * Assigns a group to 1-n events by adding entries to the cross table
  * (counterpart to assign_evt2grp)
  *
  * @param array $grp_id        grp_id of the group to which the events will be assigned
  * @param array $evt_array    contains one or more evt_IDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_grp2evts($grp_id, $evt_array);

  /**
  * Adds a new user
  *
  * @param array $data  username, email, and other data of the new user
  * @return boolean     true on success, false on failure
  */
  public abstract function usr_create($data);

  /**
  * Returns the data of a certain user
  *
  * @param array $usr_id  knd_id of the user
  * @return array         the user's data (username, email-address, status etc) as array, false on failure
  */
  public abstract function usr_get_data($usr_id);

  /**
  * Edits a user by replacing his data and preferences by the new array
  *
  * @param array $usr_id  usr_id of the user to be edited
  * @param array $data    username, email, and other new data of the user
  * @return boolean       true on success, false on failure
  */
  public abstract function usr_edit($usr_id, $data);

  /**
  * deletes a user
  *
  * @param array $usr_id  usr_id of the user
  * @return boolean       true on success, false on failure
  */
  public abstract function usr_delete($usr_id);

  /**
  * Get a preference for a user. If no user ID is given the current user is used.
  *
  * @param string  $key     name of the preference to fetch
  * @param integer $userId  (optional) id of the user to fetch the preference for
  * @return string value of the preference or null if there is no such preference
  */
  public abstract function usr_get_preference($key,$userId=null);

  /**
  * Get several preferences for a user. If no user ID is given the current user is used.
  *
  * @param array   $keys    names of the preference to fetch in an array
  * @param integer $userId  (optional) id of the user to fetch the preference for
  * @return array  with keys for every found preference and the found value
  */
  public abstract function usr_get_preferences(array $keys,$userId=null);

  /**
  * Get several preferences for a user which have a common prefix. The returned preferences are striped off
  * the prefix.
  * If no user ID is given the current user is used.
  *
  * @param string  $prefix   prefix all preferenc keys to fetch have in common
  * @param integer $userId  (optional) id of the user to fetch the preference for
  * @return array  with keys for every found preference and the found value
  */
  public abstract function usr_get_preferences_by_prefix($prefix,$userId=null);

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
  public abstract function usr_set_preferences(array $data,$prefix='',$userId=null);

  /**
  * Assigns a leader to 1-n groups by adding entries to the cross table
  *
  * @param int $ldr_id        usr_id of the group leader to whom the groups will be assigned
  * @param array $grp_array    contains one or more grp_IDs
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_ldr2grps($ldr_id, $grp_array);

  /**
  * Assigns a group to 1-n group leaders by adding entries to the cross table
  * (counterpart to assign_ldr2grp)
  *
  * @param array $grp_id        grp_id of the group to which the group leaders will be assigned
  * @param array $ldr_array    contains one or more usr_ids of the leaders)
  * @return boolean            true on success, false on failure
  */
  public abstract function assign_grp2ldrs($grp_id, $ldr_array);

  /**
  * returns all the groups of the given group leader
  *
  * @param array $ldr_id  usr_id of the group leader
  * @return array         contains the grp_IDs of the groups or false on error
  */
  public abstract function ldr_get_grps($ldr_id);

  /**
  * returns all the group leaders of the given group
  *
  * @param array $grp_id  grp_id of the group
  * @return array         contains the usr_IDs of the group's group leaders or false on error
  */
  public abstract function grp_get_ldrs($grp_id);

  /**
  * Adds a new group
  *
  * @param array $data  name and other data of the new group
  * @return int         the grp_id of the new group, false on failure
  */
  public abstract function grp_create($data);

  /**
  * Returns the data of a certain group
  *
  * @param array $grp_id  grp_id of the group
  * @return array         the group's data (name, leader ID, etc) as array, false on failure
  */
  public abstract function grp_get_data($grp_id);

  /**
  * Returns the number of users in a certain group
  *
  * @param array $grp_id   grp_id of the group
  * @return int            the number of users in the group
  */
  public abstract function grp_count_users($grp_id);

  /**
  * Edits a group by replacing its data by the new array
  *
  * @param array $grp_id  grp_id of the group to be edited
  * @param array $data    name and other new data of the group
  * @return boolean       true on success, false on failure
  */
  public abstract function grp_edit($grp_id, $data);

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
  public abstract function grp_delete($grp_id);

  /**
  * Returns all configuration variables
  *
  * @return array       array with the vars from the var table
  */
  public abstract function var_get_data();

  /**
  * Edits a configuration variables by replacing the data by the new array
  *
  * @param array $data    variables array
  * @return boolean       true on success, false on failure
  */
  public abstract function var_edit($data);

  /**
  * checks whether there is a running zef-entry for a given user
  *
  * @param integer $user ID of user in table usr
  * @return boolean true=there is an entry, false=there is none (actually 1 or 0 is returnes as number!)
  */
  public abstract function get_rec_state($usr_id);

  /**
  * Returns the data of a certain time record
  *
  * @param array $zef_id  zef_id of the record
  * @return array         the record's data (time, event id, project id etc) as array, false on failure
  */
  public abstract function zef_get_data($zef_id);

  /**
  * delete zef entry
  *
  * @param integer $id -> ID of record
  */
  public abstract function zef_delete_record($id);

  /**
  * create zef entry
  *
  * @param integer $id    ID of record
  * @param integer $data  array with record data
  */
  public abstract function zef_create_record($usr_ID,$data);

  /**
  * edit zef entry
  *
  * @param integer $id ID of record
  * @param integer $data  array with new record data
  * @author th
  */
  public abstract function zef_edit_record($id,$data);

  /**
  * saves timespace of user in database (table conf)
  *
  * @param string $timespace_in unix seconds
  * @param string $timespace_out unix seconds
  * @param string $user ID of user
  */
  public abstract function save_timespace($timespace_in,$timespace_out,$user);

  /**
  * returns list of projects for specific group as array
  *
  * @param integer $user ID of user in database
  * @return array
  */
  public abstract function get_arr_pct(array $groups = null);

  /**
  * returns list of projects for specific group and specific customer as array
  *
  * @param integer $knd_id customer id
  * @param array $groups list of group ids
  * @return array
  */
  public abstract function get_arr_pct_by_knd($knd_id,array $groups = null);

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
  * @param integer $in start of timespace in unix seconds
  * @param integer $out end of timespace in unix seconds
  * @param integer $filterCleared where -1 (default) means no filtering, 0 means only not cleared entries, 1 means only cleared entries
  * @return array
  */
  public abstract function get_arr_zef($in,$out,$users = null, $customers = null, $projects = null, $events = null,$limit = false, $reverse_order = false, $filterCleared = null, $startRows = 0, $limitRows = 0);

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
  * [usr_ID] user ID,
  * [usr_sts] user status (rights),
  * [usr_name] username
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
  * returns ID of running timesheet event for specific user
  *
  * <pre>
  * ['zef_ID'] ID of last recorded task
  * ['zef_in'] in point of timesheet record in unix seconds
  * ['zef_pctID']
  * ['zef_evtID']
  * </pre>
  *
  * @return integer
  */
  public abstract function get_event_last();

  /**
  * returns time summary of current timesheet
  *
  * @param integer $user ID of user in table usr
  * @param integer $in start of timespace in unix seconds
  * @param integer $out end of timespace in unix seconds
  * @return integer
  */
  public abstract function get_zef_time($in,$out,$users = null, $customers = null, $projects = null, $events = null,$filterCleared = null);

  /**
  * returns list of customers in a group as array
  *
  * @param integer $group ID of group in table grp or "all" for all groups
  * @return array
  */
  public abstract function get_arr_knd(array $groups = null);

  ## Load into Array: Events
  public abstract function get_arr_evt(array $groups = null);

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
  public abstract function get_arr_evt_by_pct($pct, array $groups = null);

  /**
  * returns list of events used with specified customer
  *
  * @param integer $customer filter for only this ID of a customer
  * @return array
  */
  public abstract function get_arr_evt_by_knd($customer_ID);

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
  * [usr_ID] => 23103741
  * [usr_name] => admin
  * [usr_sts] => 0
  * [grp_name] => miesepriem
  * [usr_mail] => 0
  * [usr_active] => 0
  *
  * @param array $groups list of group ids the users must be a member of
  * @return array
  */
  public abstract function get_arr_usr($trash=0,array $groups = null);

  /**
  * returns array of all groups
  *
  * [0]=> array(6) {
  *      ["grp_ID"]      =>  string(1) "1"
  *      ["grp_name"]    =>  string(5) "admin"
  *      ["grp_leader"]  =>  string(9) "1234"
  *      ["grp_trash"]   =>  string(1) "0"
  *      ["count_users"] =>  string(1) "2"
  *      ["leader_name"] =>  string(5) "user1"
  * }
  *
  * [1]=> array(6) {
  *      ["grp_ID"]      =>  string(1) "2"
  *      ["grp_name"]    =>  string(4) "Test"
  *      ["grp_leader"]  =>  string(9) "12345"
  *      ["grp_trash"]   =>  string(1) "0"
  *      ["count_users"] =>  string(1) "1"
  *      ["leader_name"] =>  string(7) "user2"
  *  }
  *
  * @return array
  *
  */
  public abstract function get_arr_grp($trash=0);

  /**
  * returns array of all groups of a group leader
  *
  * [0]=> array(6) {
  *      ["grp_ID"]      =>  string(1) "1"
  *      ["grp_name"]    =>  string(5) "admin"
  *      ["grp_leader"]  =>  string(9) "1234"
  *      ["grp_trash"]   =>  string(1) "0"
  *      ["count_users"] =>  string(1) "2"
  *      ["leader_name"] =>  string(5) "user1"
  * }
  *
  * [1]=> array(6) {
  *      ["grp_ID"]      =>  string(1) "2"
  *      ["grp_name"]    =>  string(4) "Test"
  *      ["grp_leader"]  =>  string(9) "12345"
  *      ["grp_trash"]   =>  string(1) "0"
  *      ["count_users"] =>  string(1) "1"
  *      ["leader_name"] =>  string(7) "user2"
  *  }
  *
  * @return array
  */
  public abstract function get_arr_grp_by_leader($leader_id,$trash=0);

  /**
  * performed when the stop buzzer is hit.
  * Checks which record is currently recording and
  * writes the end time into that entry.
  * if the measured timevalue is longer than one calendar day
  * it is split up and stored in the DB by days
  *
  * @param integer $user ID of user
  * @return boolean
  */
  public abstract function stopRecorder();

  /**
  * starts timesheet record
  *
  * @param integer $pct_ID ID of project to record
  * @return boolean
  */
  public abstract function startRecorder($pct_ID,$evt_ID,$user);

  /**
  * Just edit the project for an entry. This is used for changing the project
  * of a running entry.
  *
  * @param $zef_id id of the timesheet entry
  * @param $pct_id id of the project to change to
  */
  public abstract function zef_edit_pct($zef_id,$pct_id);

  /**
  * Just edit the task for an entry. This is used for changing the task
  * of a running entry.
  *
  * @param $zef_id id of the timesheet entry
  * @param $evt_id id of the task to change to
  */
  public abstract function zef_edit_evt($zef_id,$evt_id);

  /**
  * Just edit the comment an entry. This is used for editing the comment
  * of a running entry.
  *
  * @param $zef_ID id of the timesheet entry
  * @param $comment_type new type of the comment
  * @param $comment the comment text
  */
  public abstract function zef_edit_comment($zef_ID,$comment_type,$comment);


  /**
   * return ID of specific customer named 'XXX'
   *
   * @param string $name name of the customer in table knd
   * @return integer
   */
  public abstract function knd_name2id($name);

  /**
  * return ID of specific user named 'XXX'
  *
  * @param integer $name name of user in table usr
  * @return id of the customer
  */
  public abstract function usr_name2id($name);

  /**
  * return name of a user with specific ID
  *
  * @param string $id the user's usr_ID
  * @return int
  */
  public abstract function usr_id2name($id);

  /**
  * returns the date of the first timerecord of a user (when did the user join?)
  * this is needed for the datepicker
  * @param integer $id of user
  * @return integer unix seconds of first timesheet record
  */
  public abstract function getjointime($usr_id);

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
  * @param integer $in from this timestamp
  * @param integer $out to this  timestamp
  * @param integer $user ID of user in table usr
  * @param integer $customer ID of customer in table knd
  * @param integer $project ID of project in table pct
  * @return array
  */
  public abstract function get_arr_time_usr($in,$out,$users = null, $customers = null, $projects = null, $events = null);

  /**
  * returns list of time summary attached to customer ID's within specific timespace as array
  *
  * @param integer $in start of timespace in unix seconds
  * @param integer $out end of timespace in unix seconds
  * @param integer $user filter for only this ID of auser
  * @param integer $customer filter for only this ID of a customer
  * @param integer $project filter for only this ID of a project
  * @return array
  */
  public abstract function get_arr_time_knd($in,$out,$users = null, $customers = null, $projects = null, $events = null);

  /**
  * returns list of time summary attached to project ID's within specific timespace as array
  *
  * @param integer $in start time in unix seconds
  * @param integer $out end time in unix seconds
  * @param integer $user filter for only this ID of auser
  * @param integer $customer filter for only this ID of a customer
  * @param integer $project filter for only this ID of a project
  * @return array
  */
  public abstract function get_arr_time_pct($in,$out,$users = null, $customers = null, $projects = null,$events = null);

  /**
  * returns list of time summary attached to event ID's within specific timespace as array
  *
  * @param integer $in start time in unix seconds
  * @param integer $out end time in unix seconds
  * @param integer $user filter for only this ID of auser
  * @param integer $customer filter for only this ID of a customer
  * @param integer $project filter for only this ID of a project
  * @return array
  */
  public abstract function get_arr_time_evt($in,$out,$users = null, $customers = null, $projects = null, $events = null);

  /**
  * Set field usr_sts for users to 1 if user is a group leader, otherwise to 2.
  * Admin status will never be changed.
  * Calling function should start and end sql transaction.
  */
  public abstract function update_leader_status();

  /**
  * Save hourly rate to database.
  */
  public abstract function save_rate($user_id,$project_id,$event_id,$rate);

  /**
  * Read hourly rate from database.
  */
  public abstract function get_rate($user_id,$project_id,$event_id);

  /**
  * Remove hourly rate from database.
  */
  public abstract function remove_rate($user_id,$project_id,$event_id);

  /**
  * Query the database for the best fitting hourly rate for the given user, project and event.
  */
  public abstract function get_best_fitting_rate($user_id,$project_id,$event_id);

  /**
  * Query the database for all fitting hourly rates for the given user, project and event.
  */
  public abstract function allFittingRates($user_id,$project_id,$event_id);

  /**
  * Save fixed rate to database.
  */
  public abstract function save_fixed_rate($project_id,$event_id,$rate);

  /**
  * Read fixed rate from database.
  */
  public abstract function get_fixed_rate($project_id,$event_id);

  /**
  * Remove fixed rate from database.
  */
  public abstract function remove_fixed_rate($project_id,$event_id);

  /**
  * Query the database for the best fitting fixed rate for the given user, project and event.
  */
  public abstract function get_best_fitting_fixed_rate($project_id,$event_id);

  /**
  * Query the database for all fitting fixed rates for the given user, project and event.
  */
  public abstract function allFittingFixedRates($project_id,$event_id);

  /**
  * Save a new secure key for a user to the database. This key is stored in the users cookie and used
  * to reauthenticate the user.
  */
  public abstract function usr_loginSetKey($userId,$keymai);

  /**
  * Save a new secure key for a customer to the database. This key is stored in the clients cookie and used
  * to reauthenticate the customer.
  */
  public abstract function knd_loginSetKey($customerId,$keymai);

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
  public abstract function isValidEventId($eventId);
  
  /**
   * checks if a given db row based on the $idColumn & $id exists
   * @param string $table
   * @param array $filter
   * @return bool
   */
  protected abstract function rowExists($table, Array $filter);
  

}
