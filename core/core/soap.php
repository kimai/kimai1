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
 * ==================================================================
 * Bootstrap Zend
 * ==================================================================
 *
 * - Ensure library/ is on include_path
 * - Register Autoloader
 */
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(APPLICATION_PATH . '/libraries/'),
        )
    )
);

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

/**
 * ==================================================================
 * Prepare environment and execute SOAP mode to execute
 * ==================================================================
 */

ini_set('soap.wsdl_cache_enabled', 0);                              // @FIXME
ini_set('soap.wsdl_cache_dir', APPLICATION_PATH . '/compile/');     // @FIXME
ini_set('soap.wsdl_cache', WSDL_CACHE_NONE);                        // WSDL_CACHE_DISK
ini_set('soap.wsdl_cache_ttl', 0);

$soapOpts = array('soap_version' => SOAP_1_2, 'encoding' => 'UTF-8'/*, 'uri' => $wsdlUrl*/);
$soapOpts = array();

if (isset($_GET['wsdl']) || isset($_GET['WSDL']))
{
	$autodiscover = new Zend_Soap_AutoDiscover();
	$autodiscover->setClass('Kimai_Soap_Server');
	$autodiscover->handle();
}
else
{
	$wsdlUrl =  'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?wsdl';
	$server = new Kimai_Soap_Server();

	$soap = new Zend_Soap_Server($wsdlUrl, $soapOpts);
	$soap->setObject($server);
	$soap->handle();
}

/**
 * The real class, answering all SOAP methods.
 *
 * Every public method in here, will be available for SOAP Requests and auto-discovered for WSDL queries.
 */
class Kimai_Soap_Server
{
	private $backend = null;
	private $user = null;
	private $kga = null;

	public function __construct($backend = null)
	{
		if ($backend !== null) {
			$this->backend = $backend;
		}

		// Bootstrap Kimai the old fashioned way ;-)
		require(APPLICATION_PATH . "/includes/basics.php");

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

		$uid = $this->getBackend()->getUserByApiKey($apiKey);
        if ($uid === null || $uid === false) {
            return false;
        }

		$this->user = $this->getBackend()->checkUserInternal($uid);

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
			return false;
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
			return false;
        }

		$users = $this->getBackend()->get_arr_watchable_users($this->getUser());

        if (count($users) > 0) {
			$results = array();
			foreach ($users as $row) {
				$results[$row['usr_ID']] = $row['usr_name'];
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
			return false;
        }

        $kga = $this->getKimaiEnv();
        if (isset($kga['customer'])) {
          return array(
			$kga['customer']['knd_ID'] => $kga['customer']['knd_name']
          );
		}

		$customers = $this->getBackend()->get_arr_knd($kga['usr']['groups']);

        if (count($customers) > 0) {
			$results = array();
			foreach ($customers as $row) {
				$results[$row['knd_ID']] = $row['knd_name'];
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
			return false;
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
     * @param integer|null $projectId
     * @see 'reload_evt'
     * @return array|boolean
     */
	public function getTasks($apiKey, $projectId = null)
	{
        if (!$this->init($apiKey, 'getTasks', true)) {
			return false;
        }

        $tasks = array();
        $kga   = $this->getKimaiEnv();
        $user  = $this->getUser();

        // @FIXME
        if (isset($kga['customer'])) {
          $tasks = $this->getBackend()->get_arr_evt_by_knd($kga['customer']['knd_ID']);
		} else if ($projectId !== null) {
          $tasks = $this->getBackend()->get_arr_evt_by_pct($projectId, $user['groups']);
        } else {
          $tasks = $this->getBackend()->get_arr_evt($user['groups']);
		}

        if (count($tasks) > 0) {
			return $tasks;
        }

        return array();
	}

}
