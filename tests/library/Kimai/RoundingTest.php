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
        $start = strtotime('2019-10-26T17:00:00+00:00');
        $end =   strtotime('2019-10-26T17:15:00+00:00');
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
        $start = strtotime('2019-10-26T17:05:00+00:00'); // round up to 17:15
        $end =   strtotime('2019-10-26T17:20:00+00:00'); // round up to 17:30
        $actual = Kimai_Rounding::roundTimespan($start, $end, 15, false);

        $this->assertInternalType('array', $actual);
        $this->assertArrayHasKey('start', $actual);
        $this->assertArrayHasKey('end', $actual);

        $this->assertEquals(strtotime('2019-10-26T17:15:00+00:00'), $actual['start']);
        $this->assertEquals(strtotime('2019-10-26T17:30:00+00:00'), $actual['end']);
    }

    /**
     * @covers ::roundTimespan
     */
    public function testRoundTimespanWithSteps15MinAndRoundDownOnExactStep(){

        $start = strtotime('2019-10-26T17:00:00+00:00');
        $end =   strtotime('2019-10-26T17:15:00+00:00');
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
    public function testRoundTimespanWithSteps15MinAndRoundDown(){

        $start = strtotime('2019-10-26T17:05:00+00:00'); // round down to 17:00
        $end =   strtotime('2019-10-26T17:16:00+00:00'); // round up to 17:30
        $actual = Kimai_Rounding::roundTimespan($start, $end, 15, true);

        $this->assertInternalType('array', $actual);
        $this->assertArrayHasKey('start', $actual);
        $this->assertArrayHasKey('end', $actual);

        $this->assertEquals(strtotime('2019-10-26T17:00:00+00:00'), $actual['start']);
        $this->assertEquals(strtotime('2019-10-26T17:30:00+00:00'), $actual['end']);
    }

    /**
     * @covers ::roundTimespan
     */
    public function testRoundTimespan()
    {
        $start = strtotime('2016-03-19T16:59:33+00:00'); // round down to 16:45
        $end =   strtotime('2016-03-19T18:46:17+00:00'); // round up to 19:00
        $actual = Kimai_Rounding::roundTimespan($start, $end, 15, true);

        $this->assertInternalType('array', $actual);
        $this->assertArrayHasKey('start', $actual);
        $this->assertArrayHasKey('end', $actual);

        $this->assertEquals(strtotime('2016-03-19T16:45:00+00:00'), $actual['start']);
        $this->assertEquals(strtotime('2016-03-19T19:00:00+00:00'), $actual['end']);
    }
}
