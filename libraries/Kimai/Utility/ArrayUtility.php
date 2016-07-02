<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team
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

/**
 * Class with helper functions for array handling
 */
class Kimai_Utility_ArrayUtility
{
    /**
     * Filters keys off from first array that also exist in second array. Comparison is done by keys.
     * This method is a recursive version of php array_diff_assoc()
     *
     * @param array $array1 Source array
     * @param array $array2 Reduce source array by this array
     * @return array Source array reduced by keys also present in second array
     */
    public static function arrayDiffAssocRecursive(array $array1, array $array2)
    {
        $differenceArray = array();
        foreach ($array1 as $key => $value) {
            if (!array_key_exists($key, $array2)) {
                $differenceArray[$key] = $value;
            } elseif (is_array($value)) {
                if (is_array($array2[$key])) {
                    $differenceArray[$key] = self::arrayDiffAssocRecursive($value, $array2[$key]);
                }
            }
        }
        return $differenceArray;
    }

    /**
     * @param array $array
     * @return array
     */
    public static function setKeyFromValue(array $array)
    {
        $newArray = array();
        foreach ($array as $item) {
            $newArray[$item] = $item;
        }
        return $newArray;
    }
}
