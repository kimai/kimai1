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
 * The base class for authentication methods. All methods have to subclass this
 * class and implement the authenticate function.
 *
 * The order of the functions is closely related to the order they are called.
 *
 * First it is checked if automatic login is possible with this method.
 * If so it is tried via the performAutoLogin function.
 * When a user provides credentials the authenticate function is called.
 *
 * @author sl
 * @author Kevin Papst
 */
abstract class Kimai_Auth_Abstract
{
    /**
     * @var array
     */
    protected $kga = null;
    /**
     * @var Kimai_Database_Abstract
     */
    protected $database = null;

    /**
     * @param Kimai_Database_Abstract $database
     * @param array $kga
     */
    public function __construct($database = null, $kga = null)
    {
        if ($database !== null) {
            $this->setDatabase($database);
        }

        if ($kga !== null) {
            $this->setKga($kga);
        }
    }

    /**
     * @param array $kga
     */
    public function setKga(array $kga)
    {
        $this->kga = $kga;
    }

    /**
     * @return array
     */
    protected function getKga()
    {
        return $this->kga;
    }

    /**
     * @param Kimai_Database_Abstract $database
     */
    public function setDatabase(Kimai_Database_Abstract $database)
    {
        $this->database = $database;
    }

    /**
     * @return Kimai_Database_Abstract
     */
    protected function getDatabase()
    {
        return $this->database;
    }

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
        return false;
    }

    /**
     * Try to authenticate the user before he sees the login page.
     *
     * @param int $userId is set to the id of the user in Kimai. If none exists it will be <code>false</code>
     * @return boolean either <code>true</code> if the user could be authenticated or <code>false</code> otherwise
     **/
    public function performAutoLogin(&$userId)
    {
        return false;
    }

    /**
     * Authenticate a combination of username and password. Both is given as
     * the user wrote it into the input fields.
     *
     * @param string $username name of the user who wants to authenticate
     * @param string $plainPassword password in plaintext the user has provided
     * @param int $userId is set to the id of the user in Kimai. If none exists it will be <code>false</code>
     * @return boolean either <code>true</code> if the credentials were correct, or <code>false</code> otherwise
     **/
    public function authenticate($username, $plainPassword, &$userId)
    {
        $database = $this->getDatabase();

        $id = $database->user_name2id($username);

        // The $username !== "" check was here in a previous version. When
        // user_name2id() works in a sound way, it should not be needed.
        // However, after all this is a security related function so better
        // leave the check here, it will not cost us much.
        if ($id !== false && $username !== "") {
            $userData = $database->user_get_data($id);
            $userId   = $userData['userID'];
            $hash     = $userData['password'];
        } else {
            // We don't know a user with this name. Let's use some dummy values
            // so that there is no timing difference between a valid user name
            // and an invalid one.
            //
            // crypt("dummypw", "$5$rounds=5000$c30424ddd28d4de5") ==>
            // $5$rounds=5000$c30424ddd28d4de5$Em1h8dk2KH8.X1eUAQDW2875Wq/SYxolTenLbWZeeOB

            // TODO is there a better way to execute verify_password() and ensure "false" as return?
            // TODO this also needs upadate if we want to update the used password encoding :(
            $userId        = false;
            $hash          = '$5$rounds=5000$c30424ddd28d4de5$Em1h8dk2KH8.X1eUAQDW2875Wq/SYxolTenLbWZeeOB';
            $plainPassword = "I am the wrong password!";
        }

        $authenticated = $this->verify_password($plainPassword, $hash);

        if ($authenticated && $this->password_needs_reencoding($hash)) {
            // the user provided the correct password, but the format of the
            // hash stored in the DB is outdated.
            $data = array();
            $data['password'] = $this->encode_password($plainPassword);
            $database->user_edit($id, $data);
        }

        return $authenticated;
    }

    /**
     * Return a map from group IDs to membership role IDs to which users should be added, if they authenticated but are not known to Kimai.
     * The default implementation uses the second group and and a membership role named 'User', otherwise the first one it can find.
     *
     * @return integer id of the group to add the user to
     **/
    public function getDefaultGroups()
    {
        $database = $this->getDatabase();
        $groups   = $database->get_groups();

        $group = 0;
        if (count($groups) > 1) {
            $group = $groups[1]['groupID'];
        }

        if (count($groups) === 1) {
            $group = $groups[0]['groupID'];
        }

        $memberships = $database->membership_roles();
        $membership = $memberships[0]['membershipRoleID'];
        foreach ($memberships as $membership)
          if ($membership['name'] == 'User')
            $membership = $membership['membershipRoleID'];

        return array($group => $membership);
    }

    /**
     * Return an ID of a global role to which users should be added, if they authenticated but are not known to Kimai.
     * The default implementation uses the first role or, if present, a role called 'User'.
     * @return integer global role ID
     */
    public function getDefaultGlobalRole()
    {
        $database = $this->getDatabase();

        $roles = $database->global_roles();

        $globalRoleID = $roles[0]['globalRoleID'];
        foreach ($roles as $role)
          if ($role['name'] == 'User')
            $globalRoleID = $role['globalRoleID'];

        return $globalRoleID;
    }

    /**
     * Encode a provided password as we need it to store in the DB.
     *
     * @param $password the password string to encode
     * @return the encoded password string
     */
    protected function encode_password($password)
    {
        // use crypt with SHA-256 and 5000 rounds. As the actual salt we take the
        // first 16 chars from a MD5 string.
        $salt = '$5$rounds=5000$' . substr(md5(microtime()), 0, 16);
        $pepper = $this->kga['password_salt'];
        return crypt($pepper . $password, $salt);
    }

    /**
     * Check whether the provided password matches the given hash.
     *
     * @param $password the password string provided by the user
     * @param $hash the hash valued retrieved from storage
     * @return boolean either <code>true</code> if the password could be verified or <code>false</code> otherwise
     */
    protected function verify_password($password, $hash)
    {
        $pepper = $this->kga['password_salt'];

        if (strlen($hash) == 32) {
            // old hash format
            return md5($pepper . $password . $pepper) === $hash;
        } else {
            // the new hash format, let crypt() handle everything
            return crypt($pepper . $password, $hash) === $hash;
        }
    }

    /**
     * Check whether the hash should be renewed.
     * If this function returns <code>true</code> then the user's password
     * should be re-encoded and the hash should be replaced by the new one in
     * the storage.
     *
     * @param $hash the hash that should be checked
     * @return boolean either <code>true</code> if the hash should be re-encoded or <code>false</code> otherwise
     */
    protected function password_needs_reencoding($hash)
    {
        // The old storage format was a string returned by md5(),
        // thus return true if the length is 32 nibbles == 128 bit.
        return strlen($hash) == 32;
    }

}
