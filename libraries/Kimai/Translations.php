<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team
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
 * All things related to translations.
 * It's currently just listing available languages and loading them into the $kga.
 */
class Kimai_Translations
{
    /**
     * @var array
     */
    private $kga;

    public function __construct(&$kga)
    {
        $this->kga = & $kga;
        // load default language file
        $this->kga['lang'] = require(WEBROOT . 'language/en.php');
    }

    /**
     * returns array of language files
     *
     * @param none
     * @return array
     * @author unknown/th
     */
    public static function languages()
    {
        $files = glob(WEBROOT . 'language' . DIRECTORY_SEPARATOR . '*.php');
        $languages = array('');
        foreach ($files as $file) {
            $languages[] = str_replace('.php', '', basename($file));
        }
        sort($languages);
        return $languages;
    }

    /**
     * Load a translation into the kga.
     *
     * @param string $name
     */
    public function load($name)
    {
        $languageName = basename($name); // prevents potential directory traversal
        $languageFile = WEBROOT . 'language/' . $languageName . '.php';

        if (file_exists($languageFile)) {
            $this->kga['lang'] = array_replace_recursive($this->kga['lang'], include($languageFile));
        }
    }
}
