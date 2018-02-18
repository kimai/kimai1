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

namespace KimaiTest;

/**
 * Base and helper class for Kimai Unittests.
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Kimai_Config
     */
    private $kgaLast;

    /**
     * @param \Kimai_Config $kgaNew
     */
    protected function setKga($kgaNew)
    {
        if (\Kimai_Registry::isRegistered('Kimai_Config')) {
            $kga = \Kimai_Registry::getConfig();

            if (null !== $kga) {
                $this->kgaLast = clone $kga;
            }
        }
        $kga = $kgaNew;
        \Kimai_Registry::setConfig($kga);
    }

    protected function resetKga()
    {
        if (null === $this->kgaLast) {
            return;
        }

        $kga = $this->kgaLast;
        \Kimai_Registry::setConfig($kga);
    }
}
