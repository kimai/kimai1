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
 * - Soap // WORK IN PROGRESS
 * - JSON // WORK IN PROGRESS
 *
 * @author Kevin Papst <kpapst@gmx.net>
 * @author Alexander Bauer
 */

/**
 * The real class, answering all SOAP methods.
 *
 * Every public method in here, will be available for SOAP/JSON Requests and auto-discovered for WSDL queries.
 */
class Kimai_Remote_Api
{
	private $backend = null;
	private $user = null;
	private $kga = null;
	
	/**
	 * @var Kimai_Database_Abstract
	 */
	private $oldDatabase = null;

	public function __construct()
	{
		// Bootstrap Kimai the old fashioned way ;-)
		require(dirname(__FILE__) . "/../basics.php");
		require(dirname(__FILE__) . "/database/ApiDatabase.php");

		// and remember the most important stuff
		$this->kga     = $kga;
		$this->backend = new ApiDatabase($kga, $database);
		$this->oldDatabase = $database;
	}

	/**
	 * Returns the database object to access Kimais system.
	 *
	 * @return Kimai_Database_Abstract
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
     * @return Kimai_Auth_Abstract
     */
    protected function getAuthenticator()
    {
        $kga      = $this->getKimaiEnv();
        $database = $this->getBackend();

        // load authenticator
        $authClass = 'Kimai_Auth_' . ucfirst($kga['authenticator']);
        if (!class_exists($authClass)) {
            $authClass = 'Kimai_Auth_' . ucfirst($kga['authenticator']);
        }

        $authPlugin = new $authClass();
        $authPlugin->setDatabase($this->oldDatabase);
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
            return $this->getAuthErrorResult();
        }

        $apiKey = null;

        // if the user already has an API key, only return the existing one
        $user = $this->getBackend()->checkUserInternal($username);
        if ($user !== null && isset($user['apikey']) && !empty($user['apikey'])) {
            return $this->getSuccessResult(array(array('apiKey' => $user['apikey'])));
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
        $this->getBackend()->user_edit($userId, array('apikey' => $apiKey));

        return $this->getSuccessResult(array(array('apiKey' => $apiKey)));
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
            $msg = 'An unhandled error occured.';
        }

        return array('success' => false, 'error' => array('msg' => $msg));
    }
	
  /**
   * Returns the array for success responses.
   * 
   * @param array $items
   * @param int $total = 0
   * @return array
   */
  protected function getDebugResult(Array $items, Array $debugItems) {
    $total = count($items);
    return array('success' => true, 'items' => $items, 'total' => $total, 'debug' => $debugItems);
  }

  
	/**
	 * Returns the array for success responses.
	 * 
	 * @param array $items
	 * @param int $total = 0
	 * @return array
	 */
	protected function getSuccessResult(Array $items, $total = 0) {
		if(empty($total)) {
			$total = count($items);
		}
		
		return array('success' => true, 'items' => $items, 'total' => $total);
	}

    /**
     * The user started the recording of an activity via the buzzer. If this method
     * is called while another recording is running the first one will be stopped.
     *
     * @param string $apiKey
     * @param integer $projectId
     * @param integer $activityId
     * @return array
     */
	public function startRecord($apiKey, $projectId, $activityId)
	{
        if (!$this->init($apiKey, 'startRecord')) {
            return $this->getAuthErrorResult();
        }
		
		// check for valid params
		if(!$this->getBackend()->isValidProjectId($projectId) || 
			!$this->getBackend()->isValidActivityId($activityId))
		{
			return $this->getErrorResult("Invalid project or task");
		}

        $user = $this->getUser();
        $uid  = $user['userID'];

        /*
        if (count($this->getBackend()->get_current_recordings($uid)) > 0) {
            $this->getBackend()->stopRecorder();
        }
        */

        $result = $this->getBackend()->startRecorder($projectId, $activityId, $uid, time());
		if($result) {
			return $this->getSuccessResult(array());
		} else {
			return $this->getErrorResult("Unable to start, invalid params?");
		}
        return $this->getErrorResult();
	}

    /**
     * Stops the currently running recording.
     *
     * @param string $apiKey
     * @param integer $entryId
     * @return boolean
     */
	public function stopRecord($apiKey, $entryId)
	{
        if (!$this->init($apiKey, 'stopRecord')) {
			return $this->getAuthErrorResult();
        }

        $result = $this->getBackend()->stopRecorder($entryId);
		if($result) {
			return $this->getSuccessResult(array());
		} else {
			return $this->getErrorResult("Unable to stop, not recording?");
		}
        return $this->getErrorResult();
	}


    /**
     * Return a list of users. Customers are not shown any users. The
     * type of the current user decides which users are shown to him.
     *
     * Returns false if the call could not be executed, null if no users
     * could be found or an array of users.
     *
     * @param string $apiKey
     * @see get_watchable_users
     * @see processor.php: 'reload_users'
     * @return array|boolean
     */
	public function getUsers($apiKey)
	{
        if (!$this->init($apiKey, 'getUsers')) {
			return $this->getAuthErrorResult();
        }

		$users = $this->getBackend()->get_user_watchable_users($this->getUser());

        if (count($users) > 0) {
			$results = array();
			foreach ($users as $row) {
				$results[] = array('userID' => $row['userID'], 'name' => $row['name']);
			}
			return $this->getSuccessResult($results);
        }

        return $this->getErrorResult();
	}


    /**
     * Return a list of customers. A customer can only see himself.
     *
     * @param string $apiKey
     * @see 'reload_customers'
     * @return array|boolean
     */
	public function getCustomers($apiKey)
	{
        if (!$this->init($apiKey, 'getCustomers', true)) {
			return $this->getAuthErrorResult();
        }
		
		$user = $this->getUser();
        $kga = $this->getKimaiEnv();
        if (isset($kga['customer'])) {
          return array(
			'customerID' => $kga['customer']['customerID'], 'ame' => $kga['customer']['name']
          );
		}

		$customers = $this->getBackend()->get_customers($user['groups']);

        if (count($customers) > 0) {
			$results = array();
			foreach ($customers as $row) {
				$results[] = array('customerID' => $row['customerID'], 'name' => $row['name']);
			}
			return $this->getSuccessResult($results);
        }

        return $this->getErrorResult();
	}

    /**
     * Return a list of projects. Customers are only shown their projects.
     *
     * @param string $apiKey
     * @see 'reload_projects'
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
			$projects = $this->getBackend()->get_projects_by_customer($kga['customer']['customerID']);
		} else {
			$projects = $this->getBackend()->get_projects($user['groups']);
		}

        if (count($projects) > 0) {
			return $this->getSuccessResult($projects);
        }

        return $this->getErrorResult();
	}


    /**
     * Return a list of tasks. Customers are only shown tasks which are
     * used for them. If a project is set as filter via the project parameter
     * only tasks for that project are shown.
     *
     * @param string $apiKey
     * @param integer|array $projectId
     * @see 'reload_activities'
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
          $tasks = $this->getBackend()->get_activities_by_customer($kga['customer']['customerID']);
		} else if ($projectId !== null) {
          $tasks = $this->getBackend()->get_activities_by_project($projectId, $user['groups']);
		  /**
		   * we need to copy the array with new keys (remove the customerID key)
		   * if we do not do this, soap server will break our response scheme
		   */
		  $tempTasks = array();
		  foreach ($tasks as $task)
		  {
			$tempTasks[] = array(
				'activityID'       => $task['activityID'],
				'name'     => $task['name'],
				'visible'  => $task['visible'],
				'budget'   => $task['budget'],
				'approved' => $task['approved'],
				'effort'   => $task['effort']
			);
		  }
		  $tasks = $tempTasks;
        } else {
          $tasks = $this->getBackend()->get_activities($user['groups']);
		}

        if (!empty($tasks)) {
			return $this->getSuccessResult($tasks);
        }

        return $this->getErrorResult();
	}

	/**
	 * Returns an array with values of the currently active recording.
	 *
     * @param string $apiKey
	 * @return array
	 */
	public function getActiveRecording($apiKey)
	{
        if (!$this->init($apiKey, 'getActiveTask', true)) {
			return $this->getAuthErrorResult();
        }

        $user = $this->getUser();
        $uid  = $user['userID'];

        $result = $this->getBackend()->get_current_recordings($uid);

		// no "last" activity existing
        if (count($result) == 0) {
			return $this->getErrorResult('No active recording.');
		}

        // get the data of the first active recording
        $result = $this->getBackend()->timeSheet_get_data($result[0]);

        // do not expose all values, but only the public visible ones
        $keys    = array('timeEntryID', 'activityID', 'projectID', 'start', 'end', 'duration');
        $current = array();
        foreach($keys as $key) {
			if (array_key_exists($key, $result)) {
				$current[$key] = $result[$key];
			}
		}
		/**
		 * add current server time
		 * this is needed to synchronize time on any extern api calls
		 */
		$current['servertime'] =  time();
		/**
		 * add customerId & Name
		 */
		
    $timeSheet = $this->getBackend()->get_timeSheet($current['start'], $current['end'], array($uid));
		$current['customerID'] = $timeSheet[0]['customerID'];
		$current['customerName'] = $timeSheet[0]['customerName'];
		$current['projectName'] = $timeSheet[0]['projectName'];
		$current['activityName'] = $timeSheet[0]['activityName'];
		
    /*
    $debugItems = array();
    $debugItems['get_timeSheet'] = $timeSheet;
    $result = $this->getDebugResult(array($current), array($debugItems));
    */	
		
		$result = $this->getSuccessResult(array($current));
        return $result;
	}
	
	/**
	 * Returns a list of recorded times.
     * @param string $apiKey
	 * @param string $from a MySQL DATE/DATETIME/TIMESTAMP
	 * @param string $to a MySQL DATE/DATETIME/TIMESTAMP
	 * @param int $cleared -1 no filtering, 0 uncleared only, 1 cleared only
	 * @param int $start limit start
	 * @param int $limit count rows to select
	 * @return array
	 */
	public function getTimesheet($apiKey, $from = 0, $to = 0, $cleared = -1, $start = 0, $limit = 0)
	{
		if (!$this->init($apiKey, 'getTimesheet', true)) {
			return $this->getAuthErrorResult();
        }
		
		$kga = $this->getKimaiEnv();
		$backend = $this->getBackend();
		$user = $this->getUser();
		
		$in = (int)strtotime($from);
		$out = (int)strtotime($to);

		// Get the array of timesheet entries.
		if (isset($kga['customer'])) {
		  $timeSheetEntries = $backend->get_timeSheet($in, $out, null, array($kga['customer']['customerID']), false, $cleared, $start, $limit);
		  $totalCount = $backend->get_timeSheet($in, $out, null, array($kga['customer']['customerID']), false, $cleared, $start, $limit, true);
		  return $this->getSuccessResult($timeSheetEntries, $totalCount);
		} else {
		  $timeSheetEntries = $backend->get_timeSheet($in, $out, array($user['userID']), null, null, null, true, false, $cleared, $start, $limit);
		  $totalCount = $backend->get_timeSheet($in, $out, array($user['userID']), null, null, null, true, false, $cleared, $start, $limit, true);
		  return $this->getSuccessResult($timeSheetEntries, $totalCount);
		}
		
		$result = $this->getErrorResult();
		return $result;
	}
	
	/**
	 * @param string $apiKey
	 * @param int $id
	 * @return array
	 */
	public function getTimesheetRecord($apiKey, $id) {
		if (!$this->init($apiKey, 'getTimesheetRecord', true)) {
			return $this->getAuthErrorResult();
        }
		
		$id = (int)$id;
		// valid id?
		if(empty($id)) {
			return $this->getErrorResult('Invalid ID');
		}
		
		$backend = $this->getBackend();
		$timeSheetEntry = $backend->timeSheet_get_data($id);
		
		// valid entry?
		if(!empty($timeSheetEntry)) {
			return $this->getSuccessResult(array($timeSheetEntry));
		}
		
		$result = $this->getErrorResult();
		return  $result;
	}
	
	/**
	 * @param string $apiKey
	 * @param array $record
	 * @param bool $doUpdate
	 * @return array
	 */
	public function setTimesheetRecord($apiKey, Array $record, $doUpdate) {
		if (!$this->init($apiKey, 'setTimesheetRecord', true)) {
			return $this->getAuthErrorResult();
        }
		
		// valid $record?
		if(empty($record)) {
			return $this->getErrorResult('Invalid record');
		}
		
		$backend = $this->getBackend();
		$kga = $this->getKimaiEnv();
		$user = $this->getUser();
		
		// check for project
		$record['projectId'] = (int)$record['projectId'];
		if(empty($record['projectId'])) {
			return $this->getErrorResult('Invalid projectId.');
		}
		//check for task
		$record['taskId'] = (int)$record['taskId'];
		if(empty($record['taskId'])) {
			return $this->getErrorResult('Invalid taskId.');
		}
		
		// check from/to
		$in = (int)strtotime($record['start']); // has to be a MySQL DATE/DATETIME/TIMESTAMP
		$out = (int)strtotime($record['end']); // has to be a MySQL DATE/DATETIME/TIMESTAMP
		
		// make sure the timestamp is not negative
		if($in <= 0 || $out <= 0 || $out-$in <= 0) {
			return $this->getErrorResult('Invalid from/to, make sure there is at least a second difference.');
		}
		
		// prepare data array
		/**
		 * requried
		 */
		$data['userID'] = $user['userID'];
		$data['projectID'] = $record['projectId'];
		$data['activityID'] = $record['taskId'];
		$data['start'] = $in;
		$data['end'] = $out;
		$data['duration'] = $out-$in;
		
		
		/**
		 * optional
		 */
		if(isset($record['location'])) {
			$data['location'] = $record['location'];
		}
		
		if(isset($record['trackingNumber'])) {
			$data['trackingNumber']     = $record['trackingNumber'];
		}
		if(isset($record['description'])) {
			$data['description']    = $record['description'];
		}
		if(isset($record['comment'])) {
			$data['comment']        = $record['comment'];
		}
		if(isset($record['commentType'])) {
			$data['commentType']   = (int)$record['commentType'];
		}
		if(isset($record['rate'])) {
			$data['rate'] = (double)$record['rate'];
		}
		if(isset($record['fixedRate'])) {
			$data['fixedRate'] = (double)$record['fixedRate'];
		}
		if(isset($record['flagCleared'])) {
			$data['cleared'] = (int)$record['flagCleared'];
		}
		if(isset($record['statusId'])) {
			$data['statusID'] = (int)$record['statusId'];
		}
		if(isset($record['flagBillable'])) {
			$data['billable'] = (int)$record['flagBillable'];
		}
		if(isset($record['budget'])) {
			$data['budget'] = (double)$record['budget'];
		}
		if(isset($record['approved'])) {
			$data['approved'] = (double)$record['approved'];
		}
		
		
		if($doUpdate) {
			$id = isset($record['id']) ? (int)$record['id'] : 0;
			if(!empty($id)) {
				$backend->timeEntry_edit($id, $data);
				return $this->getSuccessResult(array());
			} else {
				return $this->getErrorResult('Performed an update, but missing id property.');
			}
		} else {
			$id = $backend->timeEntry_create($data);
			if(!empty($id)) {
				return $this->getSuccessResult(array(array('id' => $id)));
			} else {
				return $this->getErrorResult('Failed to add entry.');
			}
		}
		$result = $this->getErrorResult();
		return $result;
	}

	/**
	 * @param string $apiKey
	 * @param int $id
	 * @return array
	 */
	public function removeTimesheetRecord($apiKey, $id) {
		if (!$this->init($apiKey, 'removeTimesheetRecord', true)) {
			return $this->getAuthErrorResult();
        }
		
		
		$id = (int)$id;
		$result = $this->getErrorResult('Invalid ID');
		// valid id?
		if(empty($id)) {
			return $result;
		}
		
		$backend = $this->getBackend();
		$kga = $this->getKimaiEnv();
		
		if($backend->timeEntry_delete($id)) {
			$result = $this->getSuccessResult(array());
		}
		return $result;	
	}

	/**
	 * Returns a list of expenses.
     * @param string $apiKey
	 * @param string $from a MySQL DATE/DATETIME/TIMESTAMP
	 * @param string $to a MySQL DATE/DATETIME/TIMESTAMP
	 * @param int $refundable -1 all, 0 only refundable
	 * @param int $cleared -1 no filtering, 0 uncleared only, 1 cleared only
	 * @param int $start limit start
	 * @param int $limit count rows to select
	 * @return array
	 */
	public function getExpenses($apiKey, $from = 0, $to = 0, $refundable = -1, $cleared = -1, $start = 0, $limit = 0) {
		
		if (!$this->init($apiKey, 'getExpenses', true)) {
			return $this->getAuthErrorResult();
        }
        
		$kga = $this->getKimaiEnv();
		$backend = $this->getBackend();
		$user = $this->getUser();
		
		$in = (int)strtotime($from);
		$out = (int)strtotime($to);
		
		
		// Get the array of timesheet entries.
		if (isset($kga['customer'])) {
		  $arr_exp = $backend->get_expenses($in, $out, array($kga['customer']['customerID']), null, null, false, $refundable, $cleared, $start, $limit);
		  $totalCount = $backend->get_expenses($in, $out, array($kga['customer']['customerID']), null, null, false, $refundable, $cleared, $start, $limit, true);
		} else {
			$arr_exp = $backend->get_expenses($in, $out, array($user['userID']), null, null, false, $refundable, $cleared, $start, $limit);
			$totalCount = $backend->get_expenses($in, $out, array($user['userID']), null, null, false, $refundable, $cleared, $start, $limit, true);
		}
		$result = $this->getSuccessResult($arr_exp, $totalCount);
		
		return $result;
	}

	/**
	 * @param string $apiKey
	 * @param int $id
	 * @return array
	 */
	public function getExpenseRecord($apiKey, $id) {
		if (!$this->init($apiKey, 'getExpenseRecord', true)) {
			return $this->getAuthErrorResult();
        }
		
		$id = (int)$id;
		// valid id?
		if(empty($id)) {
			return $this->getErrorResult('Invalid ID');
		}
		
		$backend = $this->getBackend();
		$expense = $backend->get_expense($id);
		
		// valid entry?
		if(!empty($expense)) {
			return $this->getSuccessResult(array($expense));
		}
		
		$result = $this->getErrorResult();
		return  $result;
	}
	/**
	 * @param string $apiKey
	 * @param array $record
	 * @param bool $doUpdate
	 * @return array
	 */
	public function setExpenseRecord($apiKey, Array $record, $doUpdate) {
		if (!$this->init($apiKey, 'setTimesheetRecord', true)) {
			return $this->getAuthErrorResult();
        }
		
		// valid $record?
		if(empty($record)) {
			return $this->getErrorResult('Invalid record');
		}
		
		$backend = $this->getBackend();
		$kga = $this->getKimaiEnv();
		$user = $this->getUser();
		
		// check for project
		$record['projectId'] = (int)$record['projectId'];
		if(empty($record['projectId'])) {
			return $this->getErrorResult('Invalid projectId.');
		}
		
		// converto to timestamp
		$timestamp = (int)strtotime($record['date']); // has to be a MySQL DATE/DATETIME/TIMESTAMP
		
		// make sure the timestamp is not negative
		if($timestamp <= 0 ) {
			return $this->getErrorResult('Invalid date, make sure there is a valid date property.');
		}
		
		// prepare data array
		/**
		 * requried
		 */
		$data['userID'] = $user['userID'];
		$data['projectID'] = (int)$record['projectId'];
		$data['timestamp'] = $timestamp;
		
		
		/**
		 * optional
		 */
		if(isset($record['designation'])) {
	    	$data['designation'] = $record['designation'];
		}
		if(isset($record['comment'])) {
	    	$data['comment'] = $record['comment'];
		}
		if(isset($record['commentType'])) {
	    	$data['commentType'] = (int)$record['commentType'];
		}
		if(isset($record['refundable'])) {
	    	$data['refundable'] = (int)$record['refundable'];
		}
		if(isset($record['cleared'])) {
	    	$data['cleared'] = (int)$record['cleared'];
		}
		if(isset($record['multiplier'])) {
	    	$data['multiplier'] = (double)$record['multiplier'];
		}
		if(isset($record['value'])) {
	    	$data['value'] = (double)$record['value'];
		}
		
		
		if($doUpdate) {
			$id = isset($record['id']) ? (int)$record['id'] : 0;
			if(!empty($id)) {
				$backend->expense_edit($id, $data);
				return $this->getSuccessResult(array());
			} else {
				return $this->getErrorResult('Performed an update, but missing id property.');
			}
		} else {
			$id = $backend->expense_create($data);
			if(!empty($id)) {
				return $this->getSuccessResult(array(array('id' => $id)));
			} else {
				return $this->getErrorResult('Failed to add entry.');
			}
		}
		$result = $this->getErrorResult();
		return $result;
	}

	/**
	 * @param string $apiKey
	 * @param int $id
	 * @return array
	 */
	public function removeExpenseRecord($apiKey, $id) {
		if (!$this->init($apiKey, 'removeTimesheetRecord', true)) {
			return $this->getAuthErrorResult();
        }
		
		
		$id = (int)$id;
		$result = $this->getErrorResult('Invalid ID');
		// valid id?
		if(empty($id)) {
			return $result;
		}
		
		$backend = $this->getBackend();
		$kga = $this->getKimaiEnv();
		
		if($backend->expense_delete($id)) {
			$result = $this->getSuccessResult(array());
		}
		return $result;	
	}
	

}
