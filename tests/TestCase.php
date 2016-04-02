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

namespace KimaiTest;

use PHPUnit_Framework_TestCase;

/**
 * Base and helper class for Kimai Unittests.
 */
class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $kgaLast;


    protected function setKga($kgaNew)
    {
        global $kga;

        if (null !== $kga) {
            $this->kgaLast = clone $kga;
        }
        $kga = $kgaNew;
        \Kimai_Registry::setConfig($kga);
    }

    protected function resetKga()
    {
        if (null === $this->kgaLast) {
            return;
        }

        global $kga;

        $kga = $this->kgaLast;
        \Kimai_Registry::setConfig($kga);
    }

}