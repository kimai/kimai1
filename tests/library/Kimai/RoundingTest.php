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
        $this->assertArrayHasKey('duration', $actual);

        $this->assertEquals($actual['start'], $start);
        $this->assertEquals($actual['end'], $end);
        $this->assertEquals($actual['duration'], $end - $start);
    }

    /**
     * @covers ::roundTimespan
     * @covers ::roundTimespanCheckIfBetter
     */
    public function testRoundTimespan()
    {
        $start = 1458406233;
        $end = 1458413297;
        $actual = Kimai_Rounding::roundTimespan($start, $end, 15, true);

        $this->assertInternalType('array', $actual);
        $this->assertArrayHasKey('start', $actual);
        $this->assertArrayHasKey('end', $actual);
        $this->assertArrayHasKey('duration', $actual);
        $this->assertArrayHasKey('totalDeviation', $actual);

        $this->assertEquals($actual['start'], 1458405900);
        $this->assertEquals($actual['end'], 1458413100);
        $this->assertEquals($actual['duration'], 7200);
        $this->assertEquals($actual['totalDeviation'], 530);
    }
}
