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
    abstract public function authenticate($username, $plainPassword, &$userId);

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

}
