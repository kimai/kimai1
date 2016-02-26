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
 * Class holding information about a Kimai version.
 */
class Kimai_Update_Version
{

    private $vars = array();

    public function __construct(array $versionInfo)
    {
        if (!$this->validate($versionInfo)) {
            throw new Exception("Invalid version infos given");
        }
        $this->vars = $versionInfo;
    }

    /**
     * Compare the version to the given values.
     *
     * @param $version
     * @param $revision
     * @return bool
     */
    public function compare($version, $revision = null)
    {
        $current = $version;
        $remote  = $this->vars['version'];

        if ($revision != null) {
            $current = $current.'.'.$revision;
            $remote  = $remote.'.'.$this->vars['revision'];
        }

        return version_compare($remote, $current);
    }

    public function isBeta()
    {
        return (strtolower($this->vars['status']) != 'stable');
    }

    public function isStable()
    {
        return !$this->isBeta();
    }

    /**
     * Check if all required fields are set.
     *
     * @param array $versionInfo
     * @return bool
     */
    protected function validate(array $versionInfo)
    {
        $required = array("time", "version", "status", "revision");
        foreach($required as $k) {
            if (!isset($versionInfo[$k])) {
                return false;
            }
        }

        return true;
    }

} 