<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
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
 * A user within Kimai.
 *
 * No methods to access fields: password, secure
 *
 * @author Kevin Papst
 */
class Kimai_User extends Kimai_ArrayObject
{

    /**
     * Create a new user instance, either empty or with the given $settings.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data, \ArrayObject::ARRAY_AS_PROPS);
    }

    public function setGroups(array $groups)
    {
        return $this->set('groups', $groups);
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->get('groups', []);
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->get('active');
    }

    /**
     * @return boolean
     */
    public function isBanned()
    {
        return $this->get('ban');
    }

    /**
     * @return boolean
     */
    public function isTrashed()
    {
        return $this->get('trash');
    }

    /**
     * @return string
     */
    public function getApikey()
    {
        return $this->get('apikey');
    }

    /**
     * @return int
     */
    public function getBanTime()
    {
        return $this->get('banTime');
    }

    /**
     * @return int
     */
    public function getLastActivity()
    {
        return $this->get('lastActivity');
    }

    /**
     * @return int
     */
    public function getLastProject()
    {
        return $this->get('lastProject');
    }

    /**
     * @return int
     */
    public function getLastRecord()
    {
        return $this->get('lastRecord');
    }

    /**
     * @return string
     */
    public function getMail()
    {
        return $this->get('mail');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->get('name');
    }

    public function getStatus()
    {
        return $this->get('status');
    }

    /**
     * @return int
     */
    public function getTimeframeBegin()
    {
        return $this->get('timeframeBegin');
    }

    /**
     * @return int
     */
    public function getTimeframeEnd()
    {
        return $this->get('timeframeEnd');
    }

    /**
     * @return int
     */
    public function getUserID()
    {
        return $this->get('userID');
    }
}
