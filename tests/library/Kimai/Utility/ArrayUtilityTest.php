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

namespace KimaiTest\Utility;

use KimaiTest\TestCase;

/**
 * Test case
 */
class ArrayUtilityTest extends TestCase
{

    // Tests concerning arrayDiffAssocRecursive

    /**
     * @test
     */
    public function arrayDiffAssocRecursiveHandlesOneDimensionalArrays()
    {
        $array1 = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        ];
        $array2 = [
            'key1' => 'value1',
            'key3' => 'value3'
        ];
        $expectedResult = [
            'key2' => 'value2'
        ];
        $actualResult = \Kimai_Utility_ArrayUtility::arrayDiffAssocRecursive($array1, $array2);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function arrayDiffAssocRecursiveHandlesMultiDimensionalArrays()
    {
        $array1 = [
            'key1' => 'value1',
            'key2' => [
                'key21' => 'value21',
                'key22' => 'value22',
                'key23' => [
                    'key231' => 'value231',
                    'key232' => 'value232'
                ]
            ]
        ];
        $array2 = [
            'key1' => 'value1',
            'key2' => [
                'key21' => 'value21',
                'key23' => [
                    'key231' => 'value231'
                ]
            ]
        ];
        $expectedResult = [
            'key2' => [
                'key22' => 'value22',
                'key23' => [
                    'key232' => 'value232'
                ]
            ]
        ];
        $actualResult = \Kimai_Utility_ArrayUtility::arrayDiffAssocRecursive($array1, $array2);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function arrayDiffAssocRecursiveHandlesMixedArrays()
    {
        $array1 = [
            'key1' => [
                'key11' => 'value11',
                'key12' => 'value12'
            ],
            'key2' => 'value2',
            'key3' => 'value3'
        ];
        $array2 = [
            'key1' => 'value1',
            'key2' => [
                'key21' => 'value21'
            ]
        ];
        $expectedResult = [
            'key3' => 'value3'
        ];
        $actualResult = \Kimai_Utility_ArrayUtility::arrayDiffAssocRecursive($array1, $array2);
        $this->assertEquals($expectedResult, $actualResult);
    }
}
