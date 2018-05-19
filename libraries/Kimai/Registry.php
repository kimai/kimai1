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
 * Registry to fetch several global Kimai objects.
 */
class Kimai_Registry extends Zend_Registry
{
    /**
     * Sets the configuration to use.
     *
     * @param Kimai_Config $config
     */
    public static function setConfig(Kimai_Config $config)
    {
        self::set('Kimai_Config', $config);
    }

    /**
     * Return the global configuration, merged with all user related configurations.
     *
     * @return Kimai_Config
     */
    public static function getConfig()
    {
        return self::get('Kimai_Config');
    }

    /**
     * Returns the database layer to use.
     *
     * @return Kimai_Database_Mysql
     */
    public static function getDatabase()
    {
        return self::get('database');
    }

    /**
     * Sets the global database layer.
     *
     * @param Kimai_Database_Mysql $database
     */
    public static function setDatabase(Kimai_Database_Mysql $database)
    {
        self::set('database', $database);
    }

    /**
     * Sets the current active user.
     *
     * @param Kimai_User $user
     */
    public static function setUser(Kimai_User $user)
    {
        self::set('Kimai_User', $user);
    }

    /**
     * @return Kimai_User
     */
    public static function getUser()
    {
        return self::get('Kimai_User');
    }

    /**
     * Sets the global cache object.
     *
     * @param Zend_Cache_Core $cache
     */
    public static function setCache(Zend_Cache_Core $cache)
    {
        self::set('Zend_Cache', $cache);
    }

    /**
     * Returns the global cache object.
     * This should be used, if you have no use for a dedicated cache.
     *
     * @return mixed
     * @throws Zend_Exception
     */
    public static function getCache()
    {
        return self::get('Zend_Cache');
    }
    
    /**
     * @param Kimai_Auth_Abstract $authenticator
     */
    public static function setAuthenticator(Kimai_Auth_Abstract $authenticator)
    {
        self::set('Kimai_Auth', $authenticator);
    }

    /**
     * @return Kimai_Auth_Abstract
     */
    public static function getAuthenticator()
    {
        return self::get('Kimai_Auth');
    }

    /**
     * @param Kimai_Translation_Data $translation
     */
    public static function setTranslation(Kimai_Translation_Data $translation)
    {
        self::getConfig()->setTranslation($translation);
        self::set('Kimai_Translation', $translation);
    }

    /**
     * @return Kimai_Translation_Data
     */
    public static function getTranslation()
    {
        return self::get('Kimai_Translation');
    }
}
