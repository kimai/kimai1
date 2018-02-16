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

use Kimai_Format;

/**
 * @coversDefaultClass Kimai_Format
 */
class FormatTest extends TestCase
{

    /**
     * @covers ::hourminsec
     */
    public function testhourminsec()
    {
        $actual = Kimai_Format::hourminsec(100000);
        $this->assertArrayHasKey('h', $actual);
        $this->assertArrayHasKey('i', $actual);
        $this->assertArrayHasKey('s', $actual);
        $this->assertEquals(3, $actual['h']);
        $this->assertEquals(46, $actual['i']);
        $this->assertEquals(40, $actual['s']);

        $actual = Kimai_Format::hourminsec(74159);
        $this->assertArrayHasKey('h', $actual);
        $this->assertArrayHasKey('i', $actual);
        $this->assertArrayHasKey('s', $actual);
        $this->assertEquals(20, $actual['h']);
        $this->assertEquals(35, $actual['i']);
        $this->assertEquals(59, $actual['s']);

        $actual = Kimai_Format::hourminsec(1);
        $this->assertArrayHasKey('h', $actual);
        $this->assertArrayHasKey('i', $actual);
        $this->assertArrayHasKey('s', $actual);
        $this->assertEquals(0, $actual['h']);
        $this->assertEquals(0, $actual['i']);
        $this->assertEquals(1, $actual['s']);
    }

    /**
     * @covers ::addEllipsis
     */
    public function testaddEllipsis()
    {
        $actual = Kimai_Format::addEllipsis('HelloWorldThisIsMe', 40);
        $this->assertEquals('HelloWorldThisIsMe', $actual);

        $actual = Kimai_Format::addEllipsis('HelloWorldThisIsMe', 13);
        $this->assertEquals('HelloWorld...', $actual);

        $actual = Kimai_Format::addEllipsis('HelloWorldThisIsMe', 13, ' -/');
        $this->assertEquals('HelloWorld -/', $actual);
    }
}
