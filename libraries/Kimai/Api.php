<?php

/**
 * Class Kimai_Api
 *
 * Example:
 *
 * require_once 'path/to/kimai/libraries/Kimai/Api.php';
 * $api = new \Kimai_Api([
 *     'key' => 'api key of user',
 *     'path' => '/path/to/kimai'
 * ]);
 * $customers = $api->getCustomers();
 */
class Kimai_Api
{

    /**
     * The API Key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $kimaiPath;

    /**
     * @var Kimai_Remote_Database
     */
    protected $database;

    /**
     * @var Kimai_User
     */
    protected $user;

    /**
     * @var Kimai_Config
     */
    protected $kga;

    /**
     * Kimai_Api constructor.
     *
     * $kimai = new Kimai_Api(['key' => 'API KEY', 'path' => '/absolute/or/relative/path/to/kimai'])
     *
     * @param array $params
     */
    public function __construct($params = array())
    {
        if (!is_array($params)) {
            throw new \InvalidArgumentException('The configuration options must be an array.');
        }
        if (!array_key_exists('key', $params) || empty($params['key'])) {
            throw new \InvalidArgumentException('Kimai API key is required.');
        }
        if (!array_key_exists('path', $params) || empty($params['path'])) {
            throw new \InvalidArgumentException('Path to kimai installation is required.');
        }
        $this->setApiKey($params['key']);
        $this->setKimaiPath($params['path']);

        $this->init();
    }

    /**
     * Set the API key
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Set the path to kimai installation
     * @param string $path
     */
    public function setKimaiPath($path)
    {
        $this->kimaiPath = $path;
    }

    /**
     * Load Kimai and connect to kimai database
     *
     * @throws \Exception
     */
    protected function init()
    {
        require_once rtrim($this->kimaiPath, '/') . '/includes/basics.php';

        if (!array_key_exists('kga', $GLOBALS)) {
            throw new \Exception('Kimai not loaded');
        }

        $this->database = new Kimai_Remote_Database($GLOBALS['kga'], $GLOBALS['database']);
        $this->kga = $GLOBALS['kga'];
    }

    // Public API

    public function getCustomers()
    {
        if (!$this->checkPermission($this->apiKey, 'getCustomers', true)) {
            throw new \Kimai_Auth_Exception('Unknown user or no permissions');
        }

        return $this->getDatabase()->get_customers($this->user->getGroups());
    }

    // Private methods

    /**
     * @return Kimai_Database_Mysql
     */
    protected function getDatabase()
    {
        return $this->database->getDbLayer();
    }

    /**
     * @param string $apiKey
     * @param string $permission
     * @param bool $allowCustomer
     * @return bool
     */
    protected function checkPermission($apiKey, $permission = null, $allowCustomer = false)
    {
        if ($this->getDatabase() === null) {
            return false;
        }

        $userName = $this->getDatabase()->getUserByApiKey($apiKey);
        if ($userName === null || $userName === false) {
            return false;
        }

        $this->user = $this->getDatabase()->checkUserInternal($userName);

        if ($permission !== null) {
            // if we ever want to check permissions
        }

        // do not let customers access the API
        if ($this->user === null || (!$allowCustomer && isset($this->kga['customer']))) {
            return false;
        }

        return true;
    }
}
