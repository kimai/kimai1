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
 * This file calculates a checksum for the invoice number.
 * Feel free to add more checksum functions as needed in different countries.
 *
 * @author Gustav Johansson
 */

function checksum($type, $id, $args)
{
    switch ($type) {
        case 'OCR':
            return OCR($id, $args);
        break;
    }
}

 
function OCR($id, $addLength = true)
{
    /**
 * Calculates the checksum with length number according to the swedish OCR
 * system. I.e., 123456 will have a length number added to it (including the
 * length number itself and a checksum digit. The return invoice id will be
 * a valid OCR-number: 12345682 where the next to last digit is the total
 * length and the last digit is the checksum.
 */
    //Check length. Max is 25 including checksum and length no.
    if ($addLength) {
        $max    = 23;
    } else {
        $max    = 24;
    }
    if (strlen($id) > $max) {
        return -1;
    }

    //Calculate the length number (only last digit)
    $len    = (strlen($id) + 2) % 10;
    if ($addLength) {
        $invoice    = $id.$len;
    } else {
        $invoice    = $id;
    }

    //Calculate checksum
    $inReverse    = array_reverse(str_split($invoice));
    $sum    = 0;
    $even    = true;
    foreach ($inReverse as $num) {
        if ($even) {
            $even    = false;
            $tmp    = $num * 2;
            if ($tmp > 9) {
                $tmp    = $tmp - 9;
            }
            $sum    = $sum + $tmp;
        } else {
            $even    = true;
            $sum    = $sum + $num;
        }
    }
    $check    = 10 - ($sum % 10);
    //Make sure we use 0 and not 10
    if ($check == 10) {
        $check = 0;
    }
    $checksum    = $invoice.$check;
    return $checksum;
}
