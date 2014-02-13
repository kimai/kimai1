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
    /** Requires users to log in with sAMAccountName instead of the usual distinguishedName */
    private $LDAP_BIND_TO_SAMACCOUNTNAME = true;
    
    /** Generic server username and password required when binding to sAMAccountName */
    private $LDAP_SERVER_USERNAME = 'CN=generic,OU=Service Accounts,OU=Users,OU=Admin,DC=example,DC=com';
    private $LDAP_SERVER_PASSWORD = '';
    /** Base DN for search scope when binding to sAMAccountName */
    private $LDAP_BASE_DN = 'OU=Users,OU=My Office,DC=example,DC=com';
    
    /** Preprends to username */
    private $LDAP_USERNAME_PREFIX = 'cn=';
    /** Appends to username */
    private $LDAP_USERNAME_POSTFIX = ',dc=example,dc=com';
    /** Accounts that sould be locally verified */
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

        if ($this->LDAP_BIND_TO_SAMACCOUNTNAME) {
            //Bind to LDAP with generic login details
            $bind_result = ldap_bind($connect_result, $this->LDAP_SERVER_USERNAME, $this->LDAP_SERVER_PASSWORD);

            if (!$bind_result) {
                // Nope!
                $userId = false;
                return false;
            }
            $sAMAccountName = $check_username;
            //Search for sAMAccountName with given username
            $sr = ldap_search($connect_result, $this->LDAP_BASE_DN, "(sAMAccountName=$check_username)");
            $info = ldap_get_entries($connect_result, $sr);
            
            //return false when no matching entries have been found
            if ($info['count'] == 0) {
                $userId = false;
                return false;
            }
            
            //Use only the first result. Maybe enforce only one result later?
            $check_username = $info[0]['distinguishedname'][0];
            $check_username = $this->LDAP_FORCE_USERNAME_LOWERCASE ? strtolower($check_username) : $check_username;
            
            ldap_unbind($connect_result);
            $connect_result = ldap_connect($this->LDAP_SERVER);
        }

        // Try to bind. Binding means user and pwd are valid. Prefix and Postfix only necessary when binding directly to DN
        $full_dn = $this->LDAP_BIND_TO_SAMACCOUNTNAME ? $check_username : $this->LDAP_USERNAME_PREFIX . $check_username . $this->LDAP_USERNAME_POSTFIX;
        $bind_result = ldap_bind($connect_result, $full_dn, $password);

        if (!$bind_result) {
            // Nope!
            $userId = false;
            return false;
        }
        ldap_unbind($connect_result);

        // User is authenticated. Does it exist in Kimai yet?
        $check_username = $this->LDAP_BIND_TO_SAMACCOUNTNAME ? $sAMAccountName : $check_username; //set back to account name
        $check_username = $this->LDAP_FORCE_USERNAME_LOWERCASE ? strtolower($check_username) : $check_username;

        $userId = $this->database->user_name2id($check_username);
        if ($userId === false)  {
            // User does not exist (yet)
            if ($this->LDAP_USER_AUTOCREATE) { // Create it!
		$userId   = $this->database->user_create(array(
			'name' => $check_username,
                    'alias' => $this->LDAP_BIND_TO_SAMACCOUNTNAME ? $info[0]['displayName'][0] : null,
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

}

// There should be NO trailing whitespaces.
?>
