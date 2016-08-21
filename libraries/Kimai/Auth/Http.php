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
 * HtAccess automatic login/authorization, based on standard Kimai auth
 * additions (c) 2012 Kristofer Sweger, Larkspur, CA
 * Last revision: February 21, 2012
 */
class Kimai_Auth_Http extends Kimai_Auth_Abstract
{
    // Set true to allow web server authorized automatic logins
    protected $HTAUTH_ALLOW_AUTOLOGIN = true;

    // Set true to force username to lower case before searching Kimai database
    protected $HTAUTH_FORCE_USERNAME_LOWERCASE = false;

    // Set true to create Kimai user for web server authorized users not in database
    protected $HTAUTH_USER_AUTOCREATE = false;

    // Check for PHP_AUTH_USER server variable
    protected $HTAUTH_PHP_AUTH_USER = false;

    // Check for REMOTE_USER server variable
    protected $HTAUTH_REMOTE_USER = true;

    // Check for REDIRECT_REMOTE_USER server variable
    protected $HTAUTH_REDIRECT_REMOTE_USER = false;

    /**
     * Decides whether this authentication method should be used to authenticate
     * users before they have provided any credentials.
     *
     * This allows users to be logged in automatically. Mostly used with SSO (single sign on) solutions.
     *
     * @return boolean <code>true</code> if this authentication method can login users without credentials,
     *   <code>false</code> otherwise
     */
    public function autoLoginPossible()
    {
        return $this->HTAUTH_ALLOW_AUTOLOGIN;
    }

    /**
     * Try to authenticate the user before he sees the login page.
     *
     * @param int $userId is set to the id of the user. If none exists it will be false
     * @return boolean either true if the user could be authenticated or false otherwise
     */
    public function performAutoLogin(&$userId)
    {
        $userId = false;

        // No autologin if not allowed or if no remote user authorized by web server
        if (!$this->HTAUTH_ALLOW_AUTOLOGIN) {
            return false;
        }
        $check_username = '';
        if ($this->HTAUTH_REMOTE_USER) {
            if (isset($_SERVER['REMOTE_USER'])) {
                $check_username = $_SERVER['REMOTE_USER'];
            }
        }
        if ($check_username == '' && $this->HTAUTH_REDIRECT_REMOTE_USER) {
            if (isset($_SERVER['REDIRECT_REMOTE_USER'])) {
                $check_username = $_SERVER['REDIRECT_REMOTE_USER'];
            }
        }
        if ($check_username == '' && $this->HTAUTH_PHP_AUTH_USER) {
            if (isset($_SERVER['PHP_AUTH_USER'])) {
                $check_username = $_SERVER['PHP_AUTH_USER'];
            }
        }
        if ($check_username == '' || $check_username == false) {
            return false;
        }

        // User is authenticated by web server. Does the user exist in Kimai yet?
        
        $check_username = $this->HTAUTH_FORCE_USERNAME_LOWERCASE ? strtolower($check_username) : $check_username;
        $userId = $this->database->user_name2id($check_username);
        if ($userId !== false) {
            return true;
        }

        // User does not exist (yet)
        if ($this->HTAUTH_USER_AUTOCREATE) {
            // AutoCreate the user and return true
            // Set a random password, unknown to the user. Autologin must be used until user sets own password
            $userId = $this->database->user_create(array(
                'name' => $check_username,
                'globalRoleID' => $this->getDefaultGlobalRole(),
                'active' => 1,
                'password' => encode_password(md5(uniqid(rand(), true)))
            ));
            $this->database->setGroupMemberships($userId, array($this->getDefaultGroups()));
            return true;
        }

        return false;
    }

    /**
     * @param string $username
     * @param string $password
     * @param int $userId
     * @return bool
     */
    public function authenticate($username, $password, &$userId)
    {
        $userId = $this->database->user_name2id($username);
        if ($userId === false) {
            return true;
        }

        $userData = $this->database->user_get_data($userId);
        $pass = $userData['password'];
        $userId = $userData['userID'];
        $passCrypt = encode_password($password);

        return $pass == $passCrypt && $username != '';
    }
}
