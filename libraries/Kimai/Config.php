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
                return [0, 50, 100];
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
        return [
            // number of lines shown from the logfile in debug extension. Set to "@" to display the entire file (might freeze your browser...)
            'logfile_lines' => 100,
            // TODO remove this setting completely, can always be turned on (or move to extension)
            // can the logfile be cleaned via debug_ext?
            'delete_logfile' => 1,
            'server_charset' => 'utf8',
            // here you can set a custom start day for the date-picker.
            // if this is not set the day of the users first day in the system will be taken
            // Format: ... = "DD/MM/YYYY";
            'calender_start' => '0',
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
        ];
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
     * @param bool $system whether the system language is re
     * @return string|null
     */
    public function getLanguage($system = false)
    {
        if (!$system) {
            $language = $this->getSettings()->getLanguage();
            if (!empty($language)) {
                return $language;
            }
        }

        return $this->get('language');
    }

    /**
     * Sets the system language.
     *
     * @param $language
     */
    public function setLanguage($language)
    {
        $this->set('language', $language);
    }

    /**
     * Returns the applications default authenticator.
     *
     * @return string
     */
    public function getAuthenticator()
    {
        return $this->get('authenticator');
    }

    /**
     * Sets the authenticator.
     *
     * @param $authenticator
     */
    public function setAuthenticator($authenticator)
    {
        $this->set('authenticator', $authenticator);
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
     * Sets the skin.
     *
     * @param $skin
     */
    public function setSkin($skin)
    {
        $this->set('skin', $skin);
    }

    /**
     * @return array
     */
    public function getBillable()
    {
        return $this->get('billable');
    }

    /**
     * Sets the billable values as array.
     *
     * @param array $billable
     */
    public function setBillable(array $billable)
    {
        $this->set('billable', $billable);
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
        return $this->get('statuses');
    }

    /**
     * Sets all available statuses.
     *
     * @param array $statuses
     */
    public function setStatuses($statuses)
    {
        $this->set('statuses', $statuses);
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
     * @return string
     */
    public function getCurrencySign()
    {
        return $this->get('currency_sign', '€');
    }

    /**
     * @return string
     */
    public function getCurrencyName()
    {
        return $this->get('currency_name', 'Euro');
    }

    /**
     * @return int
     */
    public function getDefaultVat()
    {
        return $this->get('defaultVat', 0);
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
     * @return string
     */
    public function getTableTimeFormat()
    {
        return $this->get('table_time_format', '%H:%M');
    }

    /**
     * @return bool
     */
    public function isRoundDownRecorderTimes()
    {
        return (bool)$this->get('allowRoundDown', false);
    }

    /**
     * @return bool
     */
    public function isUseExactSums()
    {
        return (bool)$this->get('exactSums', false);
    }

    /**
     * @return bool
     */
    public function isDisplayCurrencyFirst()
    {
        return (bool)$this->get('currency_first', false);
    }

    /**
     * @return bool
     */
    public function isEditLimit()
    {
        return $this->getEditLimit() > 0;
    }

    /**
     * @return bool
     */
    public function isShowGabBreaks()
    {
        return (bool)$this->get('show_gabBreaks', false);
    }

    /**
     * @return bool
     */
    public function isShowDaySeperatorLines()
    {
        return (bool)$this->get('show_daySeperatorLines', true);
    }

    /**
     * @return bool
     */
    public function isShowRecordAgain()
    {
        return (bool)$this->get('show_RecordAgain', true);
    }
}
