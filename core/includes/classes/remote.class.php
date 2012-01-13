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
 * This file is the base class for remote calls.
 * It can and should be utilized for all remote APIs, currently:
 * - Soap
 * - JSON (in work)
 *
 * @author Kevin Papst <kpapst@gmx.net>
 * @author Alexander Bauer (patch for structural change of responses)
 */

/**
 * The real class, answering all SOAP methods.
 *
 * Every public method in here, will be available for SOAP Requests and auto-discovered for WSDL queries.
 */
class Kimai_Remote_Api
{
	private $backend = null;
	private $user = null;
	private $kga = null;

	public function __construct()
	{
		// Bootstrap Kimai the old fashioned way ;-)
		require(dirname(__FILE__) . "/../basics.php");

		// and remember the most important stuff
		$this->kga     = $kga;
		$this->backend = $database;
	}

	/**
	 * Returns the database object to access Kimais system.
	 *
	 * @return DatabaseLayer
	 */
	private function getBackend()
	{
		return $this->backend;
	}

	/**
	 * Returns the current users config array.
	 *
	 * @return array
	 */
	private function getUser()
	{
		return $this->user;
	}

	/**
	 * Returns the current kimai environment.
	 *
	 * @return array
	 */
	private function getKimaiEnv()
	{
		return $this->kga;
	}

	/**
	 * Checks if the given $apiKey is allowed to fetch data from this system.
	 * If so, sets all internal values to their needed state and returns true.
	 *
	 * @param string $apiKey
	 * @return boolean
	 */
	private function init($apiKey, $permission = null, $allowCustomer = false)
	{
		if ($this->getBackend() === null) {
			return false;
		}

		$uName = $this->getBackend()->getUserByApiKey($apiKey);
        if ($uName === null || $uName === false) {
            return false;
        }

		$this->user = $this->getBackend()->checkUserInternal($uName);

		if ($permission !== null)
		{
			// if we ever want to check permissions!
		}

		// do not let customers access the SOAP API
        if ($this->user === null || (!$allowCustomer && isset($this->kga['customer']))) {
            return false;
		}

	    return true;
	}

    /**
     * Returns the configured Authenticator for Kimai.
     *
     * @return AuthBase
     */
    protected function getAuthenticator()
    {
        $kga      = $this->getKimaiEnv();
        $database = $this->getBackend();

        // load authenticator
        if (!is_file(WEBROOT.'auth/' . $kga['authenticator'] . '.php')) {
            $kga['authenticator'] = 'kimai';
        }
        require(WEBROOT.'auth/' . $kga['authenticator'] . '.php');
        $authClass = ucfirst($kga['authenticator']).'Auth';

        $authPlugin = new $authClass();
        $authPlugin->setDatabase($database);
        $authPlugin->setKga($kga);
        return $authPlugin;
    }

    /**
     * Authenticates a user and returns the API key.
     *
     * The result is either an empty string (not allowed or need to login first via web-interface) or
     * a string with max 30 character, representing the users API key.
     *
     * @param string $username
     * @param string $password
     * @return string
     */
    public function authenticate($username, $password)
    {
        $userId     = null;
        $authPlugin = $this->getAuthenticator();
        $result     = $authPlugin->authenticate($username, $password, $userId);

        // user could not be authenticated or has no account yet ...
        // ... like an SSO account, where the user has to login at least once in web-frontend before using remote API
        if ($result === false || $userId === false || $userId === null) {
            return '';
        }

        $apiKey = null;

        // if the user already has an API key, only return the existing one
        $user = $this->getBackend()->checkUserInternal($username);
        if ($user !== null && isset($user['apikey']) && !empty($user['apikey'])) {
            return $user['apikey'];
        }

        // if the user has no api key yet, create one
        while ($apiKey === null) {
            $apiKey = substr(md5(mt_rand()) . sha1(mt_rand()), 0, 25);
            $uid = $this->getBackend()->getUserByApiKey($apiKey);
            // if the apiKey already exists, we cannot use it again!
            if ($uid !== null && $uid !== false) {
                $apiKey = null;
            }
        }

        // set the apiKey to the user
        $this->getBackend()->usr_edit($userId, array('apikey' => $apiKey));

        return $apiKey;
    }

    /**
     * Returns the result array for failed authentication.
     *
     * @return array
     */
    protected function getAuthErrorResult()
    {
        return $this->getErrorResult('Unknown user or no permissions.');
    }

    /**
     * Returns the array for failure messages.
     * Returned messages will always be a string, but might be empty!
     *
     * @param string $msg
     * @return array
     */
    protected function getErrorResult($msg = null)
    {
        if ($msg === null) {
            $msg = '';
        }

        return array('success' => false, 'message' => $msg);
    }

    /**
     * The user started the recording of an event via the buzzer. If this method
     * is called while another recording is running the first one will be stopped.
     *
     * @param string $apiKey
     * @param integer $projectId
     * @param integer $eventId
     * @return boolean
     */
	public function startRecord($apiKey, $projectId, $eventId)
	{
        if (!$this->init($apiKey, 'startRecord')) {
            return $this->getAuthErrorResult();
        }
		
		// check for valid params
		if(!$this->getBackend()->isValidProjectId($projectId) || 
			!$this->getBackend()->isValidEventId($eventId))
		{
			return false;
		}

        $user = $this->getUser();
        $uid  = $user['usr_ID'];

        if ($this->getBackend()->get_rec_state($uid)) {
            $this->getBackend()->stopRecorder();
        }

        $result = $this->getBackend()->startRecorder($projectId, $eventId, $uid);
        return (bool)$result;
	}

    /**
     * Stops the currently running recording.
     *
     * @param string $apiKey
     * @return boolean
     */
	public function stopRecord($apiKey)
	{
        if (!$this->init($apiKey, 'stopRecord')) {
			return $this->getAuthErrorResult();
        }

        $result = $this->getBackend()->stopRecorder();
        return (bool)$result;
	}


    /**
     * Return a list of users. Customers are not shown any users. The
     * type of the current user decides which users are shown to him.
     *
     * Returns false if the call could not be executed, null if no users
     * could be found or an array of users.
     *
     * @param string $apiKey
     * @see get_arr_watchable_users
     * @see processor.php: 'reload_usr'
     * @return array|boolean
     */
	public function getUsers($apiKey)
	{
        if (!$this->init($apiKey, 'getUsers')) {
			return $this->getAuthErrorResult();
        }

		$users = $this->getBackend()->get_arr_watchable_users($this->getUser());

        if (count($users) > 0) {
			$results = array();
			foreach ($users as $row) {
				$results[] = array('user_ID' => $row['usr_ID'], 'usr_name' => $row['usr_name']);
			}
			return $results;
        }

        return array();
	}


    /**
     * Return a list of customers. A customer can only see himself.
     *
     * @param string $apiKey
     * @see 'reload_knd'
     * @return array|boolean
     */
	public function getCustomers($apiKey)
	{
        if (!$this->init($apiKey, 'getCustomers', true)) {
			return $this->getAuthErrorResult();
        }

        $kga = $this->getKimaiEnv();
        if (isset($kga['customer'])) {
          return array(
			'knd_ID' => $kga['customer']['knd_ID'], 'knd_name' => $kga['customer']['knd_name']
          );
		}

		$customers = $this->getBackend()->get_arr_knd($kga['usr']['groups']);

        if (count($customers) > 0) {
			$results = array();
			foreach ($customers as $row) {
				$results[] = array('knd_ID' => $row['knd_ID'], 'knd_name' => $row['knd_name']);
			}
			return $results;
        }

        return array();
	}

    /**
     * Return a list of projects. Customers are only shown their projects.
     *
     * @param string $apiKey
     * @see 'reload_pct'
     * @return array|boolean
     */
	public function getProjects($apiKey)
	{
        if (!$this->init($apiKey, 'getProjects', true)) {
			return $this->getAuthErrorResult();
        }

        $projects = array();
        $kga      = $this->getKimaiEnv();
        $user     = $this->getUser();

        if (isset($kga['customer'])) {
			$projects = $this->getBackend()->get_arr_pct_by_knd($kga['customer']['knd_ID']);
		} else {
			$projects = $this->getBackend()->get_arr_pct($user['groups']);
		}

        if (count($projects) > 0) {
			return $projects;
        }

        return array();
	}


    /**
     * Return a list of tasks. Customers are only shown tasks which are
     * used for them. If a project is set as filter via the pct parameter
     * only tasks for that project are shown.
     *
     * @param string $apiKey
     * @param integer|array $projectId
     * @see 'reload_evt'
     * @return array|boolean
     */
	public function getTasks($apiKey, $projectId = null)
	{
        if (!$this->init($apiKey, 'getTasks', true)) {
			return $this->getAuthErrorResult();
        }

        $tasks = array();
        $kga   = $this->getKimaiEnv();
        $user  = $this->getUser();

        // @FIXME
        if (isset($kga['customer'])) {
          $tasks = $this->getBackend()->get_arr_evt_by_knd($kga['customer']['knd_ID']);
		} else if ($projectId !== null) {
          $tasks = $this->getBackend()->get_arr_evt_by_pct($projectId, $user['groups']);
		  /**
		   * we need to copy the array with new keys (remove the knd_ID key)
		   * if we do not do this, soap server will break our response scheme
		   */
		  $tempTasks = array();
		  foreach ($tasks as $task)
		  {
			$tempTasks[] = array(
				'evt_ID'       => $task['evt_ID'],
				'evt_name'     => $task['evt_name'],
				'evt_visible'  => $task['evt_visible'],
				'evt_budget'   => $task['evt_budget'],
				'evt_approved' => $task['evt_approved'],
				'evt_effort'   => $task['evt_effort']
			);
		  }
		  $tasks = $tempTasks;
        } else {
          $tasks = $this->getBackend()->get_arr_evt($user['groups']);
		}

        if (count($tasks) > 0) {
			return $tasks;
        }

        return array();
	}

	/**
	 * Returns an array with values of the currently active task.
	 * If no task is found, this returns an empty array.
	 *
     * @param string $apiKey
	 * @return array
	 */
	public function getActiveTask($apiKey)
	{
        if (!$this->init($apiKey, 'getActiveTask', true)) {
			return array();
        }

        $result = $this->getBackend()->get_event_last();

		// no "last" event existing
        if ($result == false) {
			return array();
		}

        // do not return any values if the task is no more active
        if (!empty($result['zef_out'])) {
            return array();
        }

        // do not expose all values, but only the public visible ones
        $keys    = array('zef_ID', 'zef_evtID', 'zef_pctID', 'zef_in', 'zef_out', 'zef_time');
        $current = array();
        foreach($keys as $key) {
			if (array_key_exists($key, $result)) {
				$current[$key] = $result[$key];
			}
		}
		/**
		 * add current server time
		 * this maybe needed to synchronize elapsed time on any extern api calls
		 */
		$current['zef_servertime'] =  time();
		/**
		 * add customerId & Name
		 */
		
		$zef = $this->getBackend()->get_arr_zef($current['zef_in'], $current['zef_out']);
		$current['zef_knd_ID'] = $zef[0]['pct_kndID'];
		$current['zef_knd_name'] = $zef[0]['knd_name'];
		$current['zef_pct_name'] = $zef[0]['pct_name'];
		$current['zef_evt_name'] = $zef[0]['evt_name'];
		
		
        return array($current);
	}

}
