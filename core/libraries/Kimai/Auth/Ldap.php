<?php

/**
 * Copyright (C) 2011 by Skaldrom Y. Sarg of oncode.info
 *  
 * This is free software. Use it however you want.
 */

class Kimai_Auth_Ldap extends Kimai_Auth_Abstract {

    /** Your LDAP-Server */
    private $LDAP_SERVER = 'ldap://localhost';
    /** Case-insensitivity of some Servers may confuse the case-sensitive-accounting system. */
    private $LDAP_FORCE_USERNAME_LOWERCASE = true;
    /** When retrieving user info, search for username on sAMAccountName attribute? For ActiveDirectory servers */
    private $LDAP_SEARCH_BY_SMACCOUNTNAME = true;
    /** Base DN to search on when trying to collect more user info, e.g. display name, email address */
    private $LDAP_BASE_DN = 'OU=Users,OU=Regions,DC=example,DC=com';
    /** Make sure user belongs to this group */
    private $LDAP_ENFORCE_GROUP = 'CN=Kimai Groups,OU=Groups,OU=Regions,DC=example,DC=com';
    /** Preprends to username */
    private $LDAP_USERNAME_PREFIX = 'cn=';
    /** Appends to username */
    private $LDAP_USERNAME_POSTFIX = ',dc=example,dc=com';
    /** Accounts that should be locally verified */
    private $LDAP_LOCAL_ACCOUNTS = array('admin');
    /** Automatically create a user in kimai if the login is successful. */
    private $LDAP_USER_AUTOCREATE = true;
    /**
     * @var Kimai_Auth_Kimai|null
     */
    private $kimaiAuth = null;

    public function __construct($database = null, $kga = null) {
        parent::__construct($database, $kga);
        $this->kimaiAuth = new Kimai_Auth_Kimai($database, $kga);
    }

    public function authenticate($username, $password, &$userId) {
        // Check if username should be authenticated locally
        if (in_array($username, $this->LDAP_LOCAL_ACCOUNTS)) {
            return $this->kimaiAuth->authenticate($username, $password, $userId);
        }

        // Check environment sanity
        if (!function_exists('ldap_bind')) {
            echo 'ldap is not installed!';
            $userId = false;
            return false;
        }

        // Check if username is legal
        $check_username = trim($username);

        if (!$check_username || !trim($password) || ($this->LDAP_FORCE_USERNAME_LOWERCASE && strtolower($check_username) !== $check_username)) {
            $userId = false;
            return false;
        }

        // Connect to LDAP
        $connect_result = ldap_connect($this->LDAP_SERVER);
        if (!$connect_result) {
            echo "Cannot connect to ", $this->LDAP_SERVER;
            $userId = false;
            return false;
        }

        ldap_set_option($connect_result, LDAP_OPT_PROTOCOL_VERSION, 3);

        // Try to bind. Binding means user and pwd are valid.
        error_reporting(E_ERROR); 
        $bind_result = ldap_bind($connect_result, $this->LDAP_USERNAME_PREFIX . $check_username . $this->LDAP_USERNAME_POSTFIX, $password);

        if (!$bind_result) {
            // Nope!
            $userId = false;
            return false;
        }

        // User is authenticated. Does it exist in Kimai yet?
        $check_username = $this->LDAP_FORCE_USERNAME_LOWERCASE ? strtolower($check_username) : $check_username;
        if (strpos($check_username, '\\')) $check_username = substr($check_username, strpos($check_username, '\\') + 1);

        $info = $this->get_ldap_user_info($connect_result, $check_username);

        $userId = $this->database->user_name2id($check_username);
        ldap_unbind($connect_result);
        if ($userId === false)  {
            // User does not exist (yet)
            if ($this->LDAP_USER_AUTOCREATE) { // Create it!
                Logger::logfile("Creating new user $check_username, " . $info[0]['displayname'][0] . ',' . $info[0]['mail'][0]);
                $userId   = $this->database->user_create(array(
                    'name' => $check_username,
                    'alias' => $info[0]['displayname'][0],
                    'mail' => $info[0]['mail'][0],
                    'globalRoleID' => $this->getDefaultGlobalRole(),
                    'active' => 1
                ));
                $this->database->setGroupMemberships($userId,array($this->getDefaultGroups()));

                // Set a password, to calm kimai down
                $usr_data = array('password' => md5($this->kga['password_salt'] . md5(uniqid(rand(), true)) . $this->kga['password_salt']));
                $this->database->user_edit($userId, $usr_data);
            } else {
                $userId = false;
                return false;
            }
        }

        return true;
    }
    
    protected function get_ldap_user_info($connect_result, $username) {
        if ($this->LDAP_SEARCH_BY_SMACCOUNTNAME)
            $attribute = 'sAMAccountName';
        else
            $attribute = 'distinguishedName';
        $query = "($attribute=" . $this->LDAP_USERNAME_PREFIX . $username . $this->LDAP_USERNAME_POSTFIX . ")";
        if ($this->LDAP_ENFORCE_GROUP != '') $query .= "(memberOf=".$this->LDAP_ENFORCE_GROUP.")";
        //$query .= "(displayname=Francois*)";
        //echo "(&$query)";die;
        $sr = ldap_search($connect_result, $this->LDAP_BASE_DN, "(&$query)");
        $info = ldap_get_entries($connect_result, $sr);
        //var_dump($info); die;
        return $info;
    }

}

// There should be NO trailing whitespaces.
?>
