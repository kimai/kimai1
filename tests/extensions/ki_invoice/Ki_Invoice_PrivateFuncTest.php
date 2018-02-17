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

use PHPUnit_Framework_TestCase;

/**
 * Testing functions of invoice extension
 *
 * @package KimaiTest
 */
class Ki_Invoice_PrivateFuncTest extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        include_once APPLICATION_PATH . 'extensions/ki_invoice/private_func.php';
    }

    public function testext_invoice_empty_entry()
    {
        $keys = array(
            'type', 'desc', 'start', 'end', 'hour', 'fDuration', 'duration', 'timestamp', 'amount', 'description',
            'rate', 'comment', 'username', 'useralias', 'location', 'trackingNr', 'projectID', 'projectName',
            'projectComment', 'date'
        );

        $actual = ext_invoice_empty_entry();

        foreach($keys as $key) {
            $this->assertArrayHasKey($key, $actual);
        }

        $this->assertEquals(count($keys), count(array_keys($actual)));
    }

    public function testext_invoice_sort_by_date_asc()
    {
        $actual = ext_invoice_sort_by_date_asc(array('timestamp' => 10), array('timestamp' => 10));
        $this->assertEquals(0, $actual);

        $actual = ext_invoice_sort_by_date_asc(array('timestamp' => 10), array('timestamp' => 20));
        $this->assertEquals(-1, $actual);

        $actual = ext_invoice_sort_by_date_asc(array('timestamp' => 20), array('timestamp' => 10));
        $this->assertEquals(1, $actual);

        $input = array(
            0 => array('desc' => '2', 'timestamp' => 20),
            1 => array('desc' => '1', 'timestamp' => 10),
            2 => array('desc' => '5', 'timestamp' => 50),
            3 => array('desc' => '3', 'timestamp' => 30),
            4 => array('desc' => '4', 'timestamp' => 40),
        );

        uasort($input, 'ext_invoice_sort_by_date_asc');

        $this->assertEquals(
            $input,
            array(
                1 => array('desc' => '1', 'timestamp' => 10),
                0 => array('desc' => '2', 'timestamp' => 20),
                3 => array('desc' => '3', 'timestamp' => 30),
                4 => array('desc' => '4', 'timestamp' => 40),
                2 => array('desc' => '5', 'timestamp' => 50),
            )
        );
    }
    
    public function testext_invoice_sort_by_date_desc()
    {
        $actual = ext_invoice_sort_by_date_desc(array('timestamp' => 10), array('timestamp' => 10));
        $this->assertEquals(0, $actual);

        $actual = ext_invoice_sort_by_date_desc(array('timestamp' => 10), array('timestamp' => 20));
        $this->assertEquals(1, $actual);

        $actual = ext_invoice_sort_by_date_desc(array('timestamp' => 20), array('timestamp' => 10));
        $this->assertEquals(-1, $actual);

        $input = array(
            0 => array('desc' => '2', 'timestamp' => 20),
            1 => array('desc' => '1', 'timestamp' => 10),
            2 => array('desc' => '5', 'timestamp' => 50),
            3 => array('desc' => '3', 'timestamp' => 30),
            4 => array('desc' => '4', 'timestamp' => 40),
        );

        uasort($input, 'ext_invoice_sort_by_date_desc');

        $this->assertEquals(
            $input,
            array(
                2 => array('desc' => '5', 'timestamp' => 50),
                4 => array('desc' => '4', 'timestamp' => 40),
                3 => array('desc' => '3', 'timestamp' => 30),
                0 => array('desc' => '2', 'timestamp' => 20),
                1 => array('desc' => '1', 'timestamp' => 10),
            )
        );
    }

    public function testext_invoice_sort_by_name()
    {
        $actual = ext_invoice_sort_by_name(array('desc' => 'aaa'), array('desc' => 'aaa'));
        $this->assertEquals(0, $actual);

        $actual = ext_invoice_sort_by_name(array('desc' => 'aaa'), array('desc' => 'bbb'));
        $this->assertEquals(-1, $actual);

        $actual = ext_invoice_sort_by_name(array('desc' => 'bbb'), array('desc' => 'aaa'));
        $this->assertEquals(1, $actual);

        $input = array(
            0 => array('desc' => 'b', 'timestamp' => 20),
            1 => array('desc' => 'a', 'timestamp' => 10),
            2 => array('desc' => 'e', 'timestamp' => 50),
            3 => array('desc' => 'c', 'timestamp' => 30),
            4 => array('desc' => 'd', 'timestamp' => 40),
        );

        uasort($input, 'ext_invoice_sort_by_name');

        $this->assertEquals(
            $input,
            array(
                2 => array('desc' => 'e', 'timestamp' => 50),
                4 => array('desc' => 'd', 'timestamp' => 40),
                3 => array('desc' => 'c', 'timestamp' => 30),
                0 => array('desc' => 'b', 'timestamp' => 20),
                1 => array('desc' => 'a', 'timestamp' => 10),
            )
        );
    }

    public function testext_invoice_round_value()
    {
        $this->assertSame(17.0, ext_invoice_round_value(17, 0.0));
        $this->assertSame(22.0, ext_invoice_round_value(22, 0));
        $this->assertSame(35.0, ext_invoice_round_value(35.37, 0));
        $this->assertSame(45.5, ext_invoice_round_value(45.37, 0.5));
        $this->assertSame(55.5, ext_invoice_round_value(55.25, 0.5));
        $this->assertSame(65.0, ext_invoice_round_value(65.24, 0.5));
        $this->assertSame(64.5, ext_invoice_round_value(65.24, 1.5));
        $this->assertSame(67.2, ext_invoice_round_value(66.4, 1.6));
    }
}