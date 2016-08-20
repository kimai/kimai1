<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team since 2006
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
 * Copyright (C) 2011 by Skaldrom Y. Sarg of oncode.info
 *
 * This is free software. Use it however you want.
 */
class Kimai_Auth_Ldap extends Kimai_Auth_Abstract
{

    /**
     * Your LDAP-Server
     * @var string
     */
    private $LDAP_SERVER = 'ldap://localhost';
    /**
     * Case-insensitivity of some server may confuse the case-sensitive-accounting system
     * @var bool
     */
    private $LDAP_FORCE_USERNAME_LOWERCASE = true;
    /**
     * Prefix for username in LDAP query
     * @var string
     */
    private $LDAP_USERNAME_PREFIX = 'cn=';
    /**
     * Postfix for username in LDAP query
     * @var string
     */
    private $LDAP_USERNAME_POSTFIX = ',dc=example,dc=com';
    /**
     * Accounts that should be verified locally only (in Kimai database)
     * @var array
     */
    private $LDAP_LOCAL_ACCOUNTS = array('admin');
    /**
     * Automatically create a user in kimai if the login is successful
     * @var bool
     */
    private $LDAP_USER_AUTOCREATE = true;
    /**
     * The original Kimai authenticator for local authentication and user creation.
     * @var Kimai_Auth_Kimai
     */
    private $kimaiAuth = null;

    /**
     * {@inherit}
     */
    public function __construct($database = null, $kga = null)
    {
        if (!function_exists('ldap_bind')) {
            throw new Kimai_Auth_Exception('LDAP-Extension is not installed');
        }
        parent::__construct($database, $kga);
        $this->kimaiAuth = new Kimai_Auth_Kimai($database, $kga);
    }

    /**
     * @param string $username
     * @param string $password
     * @param int $userId
     * @return bool
     */
    public function authenticate($username, $password, &$userId)
    {
        // Check if username should be authenticated locally
        if (in_array($username, $this->LDAP_LOCAL_ACCOUNTS)) {
            return $this->kimaiAuth->authenticate($username, $password, $userId);
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
        $bind_result = ldap_bind($connect_result, $this->LDAP_USERNAME_PREFIX . $check_username . $this->LDAP_USERNAME_POSTFIX, $password);

        if (!$bind_result) {
            // Nope!
            $userId = false;
            return false;
        }
        ldap_unbind($connect_result);

        // User is authenticated. Does it exist in Kimai yet?
        $check_username = $this->LDAP_FORCE_USERNAME_LOWERCASE ? strtolower($check_username) : $check_username;

        $userId = $this->database->user_name2id($check_username);
        if ($userId === false) {
            // User does not exist (yet)
            if ($this->LDAP_USER_AUTOCREATE) { // Create it!
                $userId = $this->database->user_create(array(
                    'name' => $check_username,
                    'globalRoleID' => $this->getDefaultGlobalRole(),
                    'active' => 1
                ));
                $this->database->setGroupMemberships($userId, array($this->getDefaultGroups()));

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
