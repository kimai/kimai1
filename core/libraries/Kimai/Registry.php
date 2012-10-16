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
     * @return DatabaseLayer
     */
    public static function getDatabase()
    {
        return self::get('database');
    }

    /**
     * Sets the global database layer.
     *
     * @param DatabaseLayer $database
     */
    public static function setDatabase(DatabaseLayer $database)
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
}
