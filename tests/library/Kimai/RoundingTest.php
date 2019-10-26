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

use Kimai_Rounding;

/**
 * @coversDefaultClass Kimai_Rounding
 */
class RoundingTest extends TestCase
{
    /**
     * @covers ::roundTimespan
     */
    public function testRoundTimespanWithStepsZero()
    {
        $start = time() - 3600;
        $end = time() + 3600;
        $actual = Kimai_Rounding::roundTimespan($start, $end, 0, true);

        $this->assertInternalType('array', $actual);
        $this->assertArrayHasKey('start', $actual);
        $this->assertArrayHasKey('end', $actual);

        $this->assertEquals($start, $actual['start']);
        $this->assertEquals($end, $actual['end']);
    }

    /**
     * @covers ::roundTimespan
     */
    public function testRoundTimespanWithSteps15MinAndNoRoundDownOnExactStep()
    {
        $start = 1572109200;
        $end = 1572110100;
        $actual = Kimai_Rounding::roundTimespan($start, $end, 15, false);

        $this->assertInternalType('array', $actual);
        $this->assertArrayHasKey('start', $actual);
        $this->assertArrayHasKey('end', $actual);

        $this->assertEquals($start, $actual['start']);
        $this->assertEquals($end, $actual['end']);
    }

    /**
     * @covers ::roundTimespan
     */
    public function testRoundTimespanWithSteps15MinAndNoRoundDownOnAnyMinute()
    {
        $start = 1572109500;
        $end = 1572110400;
        $actual = Kimai_Rounding::roundTimespan($start, $end, 15, false);

        $this->assertInternalType('array', $actual);
        $this->assertArrayHasKey('start', $actual);
        $this->assertArrayHasKey('end', $actual);

        $this->assertEquals(1572110100, $actual['start']);
        $this->assertEquals(1572111000, $actual['end']);
    }

    /**
     * @covers ::roundTimespan
     */
    public function testRoundTimespanWithSteps15MinAndRoundDown(){

        $start = 1572109200;
        $end = 1572110100;
        $actual = Kimai_Rounding::roundTimespan($start, $end, 15, true);

        $this->assertInternalType('array', $actual);
        $this->assertArrayHasKey('start', $actual);
        $this->assertArrayHasKey('end', $actual);

        $this->assertEquals($start, $actual['start']);
        $this->assertEquals($end, $actual['end']);
    }

    /**
     * @covers ::roundTimespan
     */
    public function testRoundTimespan()
    {
        $start = 1458406233;
        $end = 1458413297;
        $actual = Kimai_Rounding::roundTimespan($start, $end, 15, true);

        $this->assertInternalType('array', $actual);
        $this->assertArrayHasKey('start', $actual);
        $this->assertArrayHasKey('end', $actual);

        $this->assertEquals(1458405900, $actual['start']);
        $this->assertEquals(1458413100, $actual['end']);
    }
}
