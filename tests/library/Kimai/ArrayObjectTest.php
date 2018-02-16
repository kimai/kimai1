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

use Kimai_ArrayObject;

/**
 * @coversDefaultClass Kimai_ArrayObject
 */
class ArrayObjectTest extends TestCase
{

    /**
     * @covers ::get
     */
    public function testGetReturnsDefault()
    {
        $sut = new Kimai_ArrayObject();
        $sut->set('foo', 'hello');

        $this->assertEquals($sut->get('bar', 'bla'), 'bla');
        $this->assertEquals($sut->get('foo', 'test'), 'hello');
    }

    /**
     * @covers ::has
     * @covers ::set
     */
    public function testSetAndHas()
    {
        $sut = new Kimai_ArrayObject();
        $this->assertFalse($sut->has('foo'));
        $this->assertFalse($sut->has('bar'));

        $sut['bar'] = 'world';
        $sut->set('foo', 'hello');

        $this->assertTrue($sut->has('foo'));
        $this->assertTrue($sut->has('bar'));

        $this->assertEquals($sut->get('foo'), 'hello');
        $this->assertEquals($sut['foo'], 'hello');
        $this->assertEquals($sut->get('bar'), 'world');
        $this->assertEquals($sut['bar'], 'world');
    }

    /**
     * @covers ::get
     * @covers ::add
     */
    public function testAddSetMultipleValues()
    {
        $sut = new Kimai_ArrayObject();
        $this->assertFalse($sut->has('foo'));
        $this->assertFalse($sut->has('bar'));

        $sut->add(['foo' => 'hello', 'bar' => 'world']);

        $this->assertTrue($sut->has('foo'));
        $this->assertTrue($sut->has('bar'));

        $this->assertEquals($sut->get('foo'), 'hello');
        $this->assertEquals($sut->get('bar'), 'world');
    }
}
