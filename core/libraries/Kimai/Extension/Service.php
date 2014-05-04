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


class Kimai_Extension_Service
{
    protected $extensions = array();
    protected $kga = null;
    protected $directory = null;

    public function __construct($kga, $directory)
    {
        $this->kga = $kga;
        $this->directory = $directory;
    }

    /**
     * Load a Extension by its $name from the given $directory.
     *
     * @param $directory the directory where to search in
     * @param $name the plugin name (directory name of the extension)
     * @return Kimai_Extension_Extension
     * @throws Exception if a plugin is incompatible
     */
    protected function loadExtension($directory, $name)
    {
        $config = $directory . '/' . $name . '/config.php';

        if (!file_exists($config)) {
            throw new Exception('A plugin seems not to be compatible: ' . $name);
        }
        $pluginConfig = require $config;
        $pluginConfig['basepath'] = realpath($config);
        $pluginConfig['basename'] = $name;
        return new Kimai_Extension_Extension($pluginConfig);
    }

    /**
     * Find all none deactivated plugin in the given $directory.
     *
     * @param $directory
     * @return array
     */
    protected function findExtensionsDirs($directory)
    {
        $all = array();
        $pattern = realpath($directory) . '/*';
        $dirs = glob($pattern, GLOB_ONLYDIR);

        foreach($dirs as $dirname)
        {
            $extDir = basename($dirname);
            if ($extDir[0] == '#' || $extDir[0] == '.') {
                continue;
            }
            $all[] = $extDir;
        }

        return $all;
    }

    /**
     * Return an array of Kimai_Extension_Extension objects.
     *
     * @return array(Kimai_Extension_Extension)
     */
    public function getAll()
    {
        if (empty($this->extensions)) {
            $dirNames = $this->findExtensionsDirs($this->directory);
            foreach($dirNames as $name) {
                try {
                    $temp = $this->loadExtension($this->directory, $name);
                    if (!$this->checkAccess($temp)) {
                        continue;
                    }
                    $this->extensions[] = $temp;
                } catch (Exception $ex) {
                    // FIXME add logger
                }
            }

            usort($this->extensions, function($a, $b) {
                if ($a->getPosition() < $b->getPosition()) return -1;
                if ($a->getPosition() == $b->getPosition()) return 0;
                return 1;
            });
        }
        return $this->extensions;
    }

    protected function checkAccess(Kimai_Extension_Extension $extension)
    {
        if (!isset($this->kga['user']) && !$extension->isCustomerAllowed()) {
            return false;
        }

        $database = Kimai_Registry::getDatabase();
        $permission = $extension->getPermission();
        if (!$database->global_role_allows($this->kga['user']['globalRoleID'], $permission)) {
            return false;
        }

        return true;
    }

} 