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

/**
 * Provides methods for formatting and parsing various data.
 */
class Kimai_Format
{

    /**
     * Format a duration given in seconds according to the global setting. Either
     * seconds are shown or not.
     *
     * @param int|array one value in seconds or an array of values in seconds
     * @return int|array depending on the $sek param which contains the formatted duration
     */
    public static function formatDuration($sek)
    {
        $kga = Kimai_Registry::getConfig();
        if (is_array($sek)) {
            // Convert all values of the array.
            $arr = [];
            foreach ($sek as $key => $value) {
                $arr[$key] = self::formatDuration($value);
            }
            return $arr;
        }

        // Format accordingly.
        if ($kga['conf']['durationWithSeconds'] == 0) {
            return sprintf('%d:%02d', $sek / 3600, $sek / 60 % 60);
        }

        return sprintf('%d:%02d:%02d', $sek / 3600, $sek / 60 % 60, $sek % 60);
    }

    /**
     * Format a currency or an array of currencies accordingly.
     *
     * @param int|array $number one value or an array of decimal numbers
     * @param bool $htmlNoWrap
     * @return int|array|string formatted string(s)
     */
    public static function formatCurrency($number, $htmlNoWrap = true)
    {
        $kga = Kimai_Registry::getConfig();
        if (is_array($number)) {
            // Convert all values of the array.
            $arr = [];
            foreach ($number as $key => $value) {
                $arr[$key] = self::formatCurrency($value);
            }
            return $arr;
        }

        $value = str_replace(".", $kga['conf']['decimalSeparator'], sprintf("%01.2f", $number));
        if ($kga->isDisplayCurrencyFirst()) {
            $value = $kga->getCurrencySign() . " " . $value;
        } else {
            $value = $value . " " . $kga->getCurrencySign();
        }

        if ($htmlNoWrap) {
            return "<span style=\"white-space: nowrap;\">$value</span>";
        }

        return $value;
    }

    /**
     * Format the annotations and only include data which the user wants to see.
     * The array which is passed to the method will be modified.
     *
     * @param $ann array the annotation array (userid => (time, costs) )
     */
    public static function formatAnnotations(&$ann)
    {
        $kga = Kimai_Registry::getConfig();

        $type = 0;
        if (isset($kga['user'])) {
            $type = $kga->getSettings()->getSublistAnnotationType();
        }

        $userIds = array_keys($ann);

        if ($type == null) {
            $type = 0;
        }

        switch ($type) {
            case 0:
                // just time
                foreach ($userIds as $userId) {
                    $ann[$userId] = self::formatDuration($ann[$userId]['time']);
                }
                break;
            case 1:
                // just costs
                foreach ($userIds as $userId) {
                    $ann[$userId] = self::formatCurrency($ann[$userId]['costs']);
                }
                break;
            case 2:
            default:
                // both
                foreach ($userIds as $userId) {
                    $time = self::formatDuration($ann[$userId]['time']);
                    $costs = self::formatCurrency($ann[$userId]['costs']);
                    $ann[$userId] = "<span style=\"white-space: nowrap;\">$time |</span>  $costs";
                }
                break;
        }
    }

    /**
     * Returns hours, minutes and seconds as array.
     *
     * @param int $sek number of seconds to extract the time from
     * @return array
     */
    public static function hourminsec($sek)
    {
        $i['h'] = $sek / 3600 % 24;
        $i['i'] = $sek / 60 % 60;
        $i['s'] = $sek % 60;
        return $i;
    }

    /**
     * http://www.alfasky.com/?p=20
     * This little function will help you truncate a string to a specified
     * length when copying data to a place where you can only store or display
     * a limited number of characters, then it will append “…” to it showing
     * that some characters were removed from the original entry.
     *
     * @param $string
     * @param $length
     * @param string $end
     * @return string
     */
    public static function addEllipsis($string, $length, $end = '...')
    {
        if (strlen($string) > $length) {
            $length -= strlen($end); // $length =  $length - strlen($end);
            $string = substr($string, 0, $length);
            $string .= $end; //  $string =  $string . $end;
        }
        return $string;
    }

    /**
     * Preprocess shortcut for date entries.
     * Allowed shortcut formats are shown in the dialogue for edit timesheet entries (click the "?")
     *
     * @param string $date shortcut date
     * @return string
     */
    public static function expand_date_shortcut($date)
    {
        $date = str_replace(" ", "", $date);

        // empty string can't be a time value
        if (strlen($date) == 0) {
            return false;
        }

        // get the parts
        $parts = preg_split("/\./", $date);

        if (count($parts) == 0 || count($parts) > 3) {
            return false;
        }

        // check day
        if (strlen($parts[0]) == 1) {
            $parts[0] = "0" . $parts[0];
        }

        // check month
        if (!isset($parts[1])) {
            $parts[1] = date("m");
        } elseif (strlen($parts[1]) == 1) {
            $parts[1] = "0" . $parts[1];
        }

        // check year
        if (!isset($parts[2])) {
            $parts[2] = date("Y");
        } elseif (strlen($parts[2]) == 2) {
            if ($parts[2] > 70) {
                $parts[2] = "19" . $parts[2];
            } else {
                if ($parts[2] < 10) {
                    $parts[2] = "200" . $parts[2];
                } else {
                    $parts[2] = "20" . $parts[2];
                }
            }
        }

        $return = implode(".", $parts);

        if (!preg_match("/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{2,4})/", $return)) {
            $return = false;
        }

        return $return;
    }

    /**
     * Preprocess shortcut for time entries.
     * Allowed shortcut formats are shown in the dialogue for edit timesheet entries (click the "?").
     *
     * @return string
     */
    public static function expand_time_shortcut($time)
    {
        $time = str_replace(" ", "", $time);

        // empty string can't be a time value
        if (strlen($time) == 0) {
            return false;
        }

        // get the parts
        $parts = preg_split("/:|\./", $time);

        for ($i = 0; $i < count($parts); $i++) {
            switch (strlen($parts[$i])) {
                case 0:
                    return false;
                case 1:
                    $parts[$i] = "0" . $parts[$i];
            }
        }

        // fill unsued parts (eg. 12:00 given but 12:00:00 is needed)
        while (count($parts) < 3) {
            $parts[] = "00";
        }

        $return = implode(":", $parts);

        $regex23 = '([0-1][0-9])|(2[0-3])'; // regular expression for hours
        $regex59 = '([0-5][0-9])'; // regular expression for minutes and seconds

        if (!preg_match("/^($regex23):($regex59):($regex59)$/", $return)) {
            $return = false;
        }

        return $return;
    }

    /**
     * Check if a parset string matches with the following time-formatting: 20.08.2008-19:00:00.
     *
     * @param string $timestring
     * @return boolean
     */
    public static function check_time_format($timestring)
    {
        if (!preg_match("/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{2,4})-([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $timestring)) {
            return false; // WRONG format
        }
        $ok = 1;

        $hours = substr($timestring, 11, 2);
        $minutes = substr($timestring, 14, 2);
        $seconds = substr($timestring, 17, 2);

        if ((int)$hours >= 24) {
            $ok = 0;
        }
        if ((int)$minutes >= 60) {
            $ok = 0;
        }
        if ((int)$seconds >= 60) {
            $ok = 0;
        }

        Kimai_Logger::logfile("timecheck: " . $ok);

        $day = substr($timestring, 0, 2);
        $month = substr($timestring, 3, 2);
        $year = substr($timestring, 6, 4);

        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            $ok = 0;
        }

        Kimai_Logger::logfile("time/datecheck: " . $ok);

        if ($ok) {
            return true;
        }

        return false;
    }
}
