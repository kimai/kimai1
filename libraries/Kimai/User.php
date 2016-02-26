<?php
/**
 * A user within Kimai
 *
 * @author Kevin Papst
 */
class Kimai_User
{
    const USER = 2;
    const ADMIN = 0;

    /**
     * @var integer
     */
    private $userID = null;
    /**
     * @var string
     */
    private $name = null;
    /**
     * @var integer
     */
    private $status = self::USER;
    /**
     * @var boolean
     */
    private $trash = false;
    /**
     * @var boolean
     */
    private $active = false;
    /**
     * @var string
     */
    private $mail = null;
    /**
     * @var string
     */
    private $password = null;
    /**
     * @var boolean
     */
    private $ban = false;
    /**
     * @var integer
     */
    private $banTime = 0;
    /**
     * @var string
     */
    private $secure = null;
    /**
     * @var integer
     */
    private $lastProject = null;
    /**
     * @var integer
     */
    private $lastActivity = null;
    /**
     * @var integer
     */
    private $lastRecord = null;
    /**
     * @var integer
     */
    private $timeframeBegin = null;
    /**
     * @var integer
     */
    private $timeframeEnd = null;
    /**
     * @var string
     */
    private $apikey = null;
    /**
     * @var array()
     */
    private $groups = array();

    /**
     * Create a new user instance, either empty or with the given $settings.
     *
     * @param array|null $settings
     */
    public function __construct($settings = null)
    {
        if ($settings === null) {
            return;
        }

        // set all values
        foreach($settings as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->status == self::ADMIN;
    }

    /**
     * @return bool
     */
    public function isUser()
    {
        return $this->status == self::USER;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return boolean
     */
    public function isBanned()
    {
        return $this->ban;
    }

    /**
     * @return boolean
     */
    public function isTrashed()
    {
        return $this->trash;
    }

    /**
     * @return string
     */
    public function getApikey()
    {
        return $this->apikey;
    }

    /**
     * @return int
     */
    public function getBanTime()
    {
        return $this->banTime;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return int
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * @return int
     */
    public function getLastProject()
    {
        return $this->lastProject;
    }

    /**
     * @return int
     */
    public function getLastRecord()
    {
        return $this->lastRecord;
    }

    /**
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSecure()
    {
        return $this->secure;
    }

    /**
     * @see Kimai_User::USER
     * @see Kimai_User::ADMIN
     * @return int
     */
    public function getType()
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getTimeframeBegin()
    {
        return $this->timeframeBegin;
    }

    /**
     * @return int
     */
    public function getTimeframeEnd()
    {
        return $this->timeframeEnd;
    }

    /**
     * @return int
     */
    public function getUserID()
    {
        return $this->userID;
    }

}
