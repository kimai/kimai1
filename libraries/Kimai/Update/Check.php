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
 * This class is used checking if a new version is available.
 */
class Kimai_Update_Check
{
    const URL = 'https://raw.githubusercontent.com/kimai/kimai/master/kimai.json';
    const CURRENT = -1;
    const BETA = 0;
    const RELEASE = 1;

    public function checkForUpdate($currentVersion, $revision)
    {
        $json = file_get_contents(self::URL);
        $json = json_decode($json, true);

        $version = new Kimai_Update_Version($json);
        $result = $version->compare($currentVersion);

        if ($result > 0) {
            return self::RELEASE;
        }

        $result = $version->compare($currentVersion, $revision);
        if ($result > 0) {
            return self::BETA;
        }
        return self::CURRENT;
    }

} 