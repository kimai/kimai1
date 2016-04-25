<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2012 Kimai-Development-Team
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
        $skin = Kimai_Config::getDefault(Kimai_Config::DEFAULT_SKIN);

        if (isset($this->view->kga['conf']['skin'])) {
            $skin = $this->view->kga['conf']['skin'];
        } else if (isset($this->view->kga['skin'])) {
            $skin = $this->view->kga['skin'];
        }

        return $this->view->escape($skin);
    }
} 
