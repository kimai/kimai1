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
 * Class Kimai_Config
 */
class Kimai_Config extends Kimai_ArrayObject
{

    const DEFAULT_LANGUAGE = 'language';
    const DEFAULT_AUTHENTICATOR = 'authenticator';
    const DEFAULT_BILLABLE = 'billable';
    const DEFAULT_SKIN = 'skin';

    /**
     * Prefill the configuration object with the given array.
     *
     * @param array $kga
     */
    public function __construct(array $kga)
    {
        $data = array_merge(
            $this->getCoreDefaults(),
            $kga
        );
        parent::__construct($data, \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Returns the default value for a given CONSTANT (see DEFAULT_*)
     *
     * @param $config
     * @return array|null|string
     */
    public static function getDefault($config)
    {
        switch ($config) {
            case self::DEFAULT_BILLABLE:
                return array(0,50,100);
            case self::DEFAULT_AUTHENTICATOR:
                return 'kimai';
            case self::DEFAULT_SKIN:
                return 'standard';
            case self::DEFAULT_LANGUAGE:
                return 'en';
        }
        return null;
    }

    /**
     * Return default settings for the application.
     *
     * @return array
     */
    protected function getCoreDefaults()
    {
        return array(
            // turn this on to display sensible data in the debug/developer extension
            // CAUTION - THINK TWICE IF YOU REALLY WANNA DO THIS AND DON'T FORGET TO TURN IT OFF IN A PRODUCTION ENVIRONMENT!!!
            // DON'T BLAME US - YOU HAVE BEEN WARNED!
            'show_sensible_data' => 1,
            // number of lines shown from the logfile in debug extension. Set to "@" to display the entire file (might freeze your browser...)
            'logfile_lines' => 100,
            // TODO remove this setting completely, can always be turned on (or move to extension)
            // can the logfile be cleaned via debug_ext?
            'delete_logfile' => 1,
            // set to 1 if utf-8 CONVERSION (!) is needed - this is not always the case (depends on server settings)
            'utf8' => 0,
            // here you can set a custom start day for the date-picker.
            // if this is not set the day of the users first day in the system will be taken
            // Format: ... = "DD/MM/YYYY";
            'calender_start' => '0',
            // TODO remove me once we are sure that the array values are not used any longer
            'date_format' => array(
                0 => '%d.%m.%Y',
                1 => '%d.%m.',
                2 => '%d.%m.%Y',
                3 => 'd.m.Y',
            ),
            // date formats for display and export
            'date_format_0' => '%d.%m.%Y',
            'date_format_1' => '%d.%m.',
            'date_format_2' => '%d.%m.%Y',
            'date_format_3' => 'd.m.Y',
            'language' => self::getDefault(self::DEFAULT_LANGUAGE),
            'authenticator' => self::getDefault(self::DEFAULT_AUTHENTICATOR),
            'billable' => self::getDefault(self::DEFAULT_BILLABLE),
            'skin' => self::getDefault(self::DEFAULT_SKIN),
            'conf' => new Kimai_Config_Settings()
        );
    }

    /**
     * Returns the administrators email address.
     *
     * @return string
     */
    public function getAdminEmail()
    {
        return $this->get('adminmail');
    }

    /**
     * Returns whether the tracking number field is editable.
     *
     * @return mixed|null
     */
    public function isTrackingNumberEnabled()
    {
        return $this->get('show_TrackingNr');
    }

    /**
     * Returns the actual Kimai version from the database.
     * This is NOT the installed software version, but the version of the database structure.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->get('version');
    }

    /**
     * Returns the actual Kimai revision from the database.
     * This is NOT the installed software revision, but the revision of the database structure.
     *
     * @return int
     */
    public function getRevision()
    {
        return (int)$this->get('revision');
    }

    /**
     * Returns the timezone of the Kimai installation, which could be user specific if configured.
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->get('timezone');
    }

    /**
     * Sets the timezone.
     *
     * @param $timezone
     */
    public function setTimezone($timezone)
    {
        $this->set('timezone', $timezone);
    }

    /**
     * Returns the current language.
     * Can either be user specific, admin specific or system specific.
     *
     * @return string|null
     */
    public function getLanguage()
    {
        $language = $this->getSettings()->getLanguage();
        if (!empty($language)) {
            return $language;
        }

        return $this->get('language');
    }

    /**
     * Returns the applications default skin.
     *
     * @return mixed|null
     */
    public function getSkin()
    {
        return $this->get('skin');
    }

    /**
     * Return user specific settings.
     *
     * @return Kimai_Config_Settings
     */
    public function getSettings()
    {
        return $this->get('conf');
    }

    /**
     * There is no "getTranslation()" as you should access them through Kimai_Registry
     *
     * @deprecated do not call directly, only meant for backward compatibility
     *
     * @param Kimai_Translation_Data $data
     */
    public function setTranslation(Kimai_Translation_Data $data)
    {
        $this->set('lang', $data);
    }

    /**
     * There is no "getUser()" as you should access them through Kimai_Registry
     *
     * @deprecated do not call directly, only meant for backward compatibility
     *
     * @param Kimai_User $user
     */
    public function setUser(Kimai_User $user)
    {
        $this->set('user', $user);
    }

    /**
     * @return int
     */
    public function getDefaultStatus()
    {
        return (int)$this->get('defaultStatusID', 1);
    }

    /**
     * Returns an array of all available statuses.
     *
     * @return array
     */
    public function getStatuses()
    {
        return $this->get('status');
    }

    /**
     * Sets all available statuses.
     *
     * @param array $statuses
     */
    public function setStatuses($statuses)
    {
        $this->set('status', $statuses);
    }

    /**
     * Set one of the date formats.
     *
     * @param int $id
     * @param string $value
     */
    public function setDateFormat($id, $value)
    {
        // backward compatibility, will be removed soon
        $this->date_format[$id] = $value;
        $this->set('date_format_' . $id, $value);
    }

    /**
     * Set one of the date formats.
     *
     * @param int $id
     * @return string
     */
    public function getDateFormat($id)
    {
        return $this->get('date_format_' . $id);
    }

    /**
     * @return bool
     */
    public function isRoundDownRecorderTimes()
    {
        return (bool)$this->get('allowRoundDown', false);
    }

    /**
     * @return int
     */
    public function getRoundPrecisionRecorderTimes()
    {
        return (int)$this->get('roundPrecision', 0);
    }

    /**
     * @return int
     */
    public function getLoginBanTime()
    {
        return (int)$this->get('loginBanTime', 900);
    }

    /**
     * @return int
     */
    public function getLoginTriesBeforeBan()
    {
        return (int)$this->get('loginTries', 3);
    }

    /**
     * @return bool
     */
    public function isDisplayCurrencyFirst()
    {
        return (bool)$this->get('currency_first', false);
    }

    /**
     * @return int
     */
    public function getDefaultVat()
    {
        return $this->get('defaultVat', 0);
    }

    /**
     * @return bool
     */
    public function isUseExactSums()
    {
        return (bool)$this->get('exactSums', false);
    }

    /**
     * @return int
     */
    public function getEditLimit()
    {
        $editLimit = $this->get('editLimit', '-');
        return (int)($editLimit !== '-' ? $editLimit : 0);
    }

    /**
     * @return bool
     */
    public function isEditLimit()
    {
        return $this->getEditLimit() > 0;
    }
}
