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
class Kimai_Config
{
    const DEFAULT_LANGUAGE = 'language';
    const DEFAULT_AUTHENTICATOR = 'authenticator';
    const DEFAULT_BILLABLE = 'billable';
    const DEFAULT_SKIN = 'skin';

    /**
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
}
