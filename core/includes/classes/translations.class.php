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
class Translations
{

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
    public static function langs()
    {
        $arr_files = array();
        $arr_files[] = "";
        $handle = opendir(WEBROOT . '/language/');
        while (false !== ($readdir = readdir($handle))) {
            if ($readdir != '.' && $readdir != '..' && substr($readdir, 0, 1) != '.' && endsWith($readdir, '.php')) {
                $arr_files[] = str_replace(".php", "", $readdir);
            }
        }
        closedir($handle);
        sort($arr_files);

        return $arr_files;
    }

    /**
     * Load a translation into the kga.
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
