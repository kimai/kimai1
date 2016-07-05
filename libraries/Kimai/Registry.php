<?php
/**
 * Registry to fetch several global Kimai objects.
 *
 * @author Kevin Papst
 */
class Kimai_Registry extends Zend_Registry
{
    /**
     * Sets the configuration to use.
     *
     * @param Zend_Config $config
     */
    public static function setConfig(Zend_Config $config)
    {
        self::set('Zend_Config', $config);
    }

    /**
     * Return the global configuration, merged with all user related configurations.
     *
     * @return Zend_Config
     */
    public static function getConfig()
    {
        return self::get('Zend_Config');
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
}
