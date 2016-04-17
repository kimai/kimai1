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
 * Returns the URL to a skin related file.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_Skin extends Zend_View_Helper_Abstract
{

    /**
     * @var string
     */
    protected $skinName = null;
    /**
     * @var string
     */
    protected $fileName = null;

    /**
     * @param string $file
     * @return string
     */
    public function skin($file = null)
    {
        if ($file !== null) {
            $this->fileName = $file;
        }

        return $this;
    }

    public function __toString()
    {
        $url = '../skins/' . $this->getName() . '/';

        if ($this->fileName !== null) {
            $url .= $this->fileName;
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (null === $this->skinName) {
            $skin = Kimai_Config::getDefault(Kimai_Config::DEFAULT_SKIN);
            $kga = Kimai_Registry::getConfig();

            if (!empty($kga->getSettings()->getSkin())) {
                $skin = $kga->getSettings()->getSkin();
            } else if (!empty($kga->getSkin())) {
                $skin = $kga->getSkin();
            }

            $this->skinName = $this->view->escape($skin);
        }

        return $this->skinName;
    }
} 
