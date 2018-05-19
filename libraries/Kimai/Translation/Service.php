<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
 * (c) Kimai-Development-Team - since 2006
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
 * Class Kimai_Translation_Service
 *
 * All things related to translations.
 */
class Kimai_Translation_Service
{

    /**
     * Returns an array of all language codes.
     *
     * @return array
     */
    public static function getAvailableLanguages()
    {
        $languages = [];
        foreach (glob(WEBROOT . '/language/*.php') as $langFile) {
            $languages[] = str_replace(".php", "", basename($langFile));
        }
        sort($languages);

        return $languages;
    }

    /**
     * Load a translation data.
     *
     * @param $name
     * @return Kimai_Translation_Data
     */
    public function load($name)
    {
        return new Kimai_Translation_Data($name);
    }
}
