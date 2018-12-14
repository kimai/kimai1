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

use Kimai_Config;

/**
 * @coversDefaultClass Kimai_Config
 */
class ConfigTest extends TestCase
{

    /**
     * @covers ::getDefault
     */
    public function testGetDefault()
    {
        $auth = Kimai_Config::getDefault(Kimai_Config::DEFAULT_AUTHENTICATOR);
        $this->assertNotNull($auth);
        $authClass = 'Kimai_Auth_' . ucfirst($auth);
        $this->assertTrue(class_exists($authClass));

        $language = Kimai_Config::getDefault(Kimai_Config::DEFAULT_LANGUAGE);
        $this->assertNotNull($language);
        $this->assertTrue(file_exists(APPLICATION_PATH . 'language/' . $language . '.php'));

        $skin = Kimai_Config::getDefault(Kimai_Config::DEFAULT_SKIN);
        $this->assertNotNull($skin);
        $skinDir = APPLICATION_PATH . 'skins/' . $skin . '/';
        $this->assertTrue(file_exists($skinDir));
        $this->assertTrue(is_dir($skinDir));

        $billable = Kimai_Config::getDefault(Kimai_Config::DEFAULT_BILLABLE);
        $this->assertNotNull($billable);
        $this->assertInternalType('array', $billable);
    }
}
