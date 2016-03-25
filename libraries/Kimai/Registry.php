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
