<?php

/**
 * Round timespans according to a defined interval. (see methods)
 */
class Kimai_Rounding
{
    /**
     * Find a beginning and end time whose timespan is as close to
     * the real timespan as possible while being a multiple of $minutes.
     *
     * e.g.: 16:07:31 - 17:15:16 is "rounded" to 16:00:00 - 17:15:00
     *       with steps set to 15 min
     *
     * @param int $start the beginning of the timespan
     * @param int $end the end of the timespan
     * @param int $minutes the steps in minutes (has to divide an hour, e.g. 5 is valid while 7 is not)
     * @param string $method
     * @return array
     */
    public static function roundTimespan($start, $end, $minutes, $method = 'default')
    {
        switch ($method) {
            case 'closest':
                return self::closestRounding($start, $end, $minutes);
            case 'ceil':
                return self::ceilRounding($start, $end, $minutes);
            case 'default':
            default:
                return self::defaultRounding($start, $end, $minutes);
        }
    }

    // --- default

    /**
     * @param int $start
     * @param int $end
     * @param int $minutes
     * @return array
     */
    private static function defaultRounding($start, $end, $minutes)
    {
        $roundedStart = self::getDefaultRoundingStart($start, $minutes);
        $roundedEnd = self::getDefaultRoundingEnd($end, $minutes);

        return [
            'start' => $roundedStart !== null ? $roundedStart : $start,
            'end' => $roundedEnd !== null ? $roundedEnd : $end,
        ];
    }

    /**
     * @param int $start
     * @param int $minutes
     *
     * @return float|int|null
     */
    private static function getDefaultRoundingStart($start, $minutes)
    {
        if ($minutes <= 0) {
            return null;
        }

        $timestamp = $start;
        $seconds = $minutes * 60;
        $diff = $timestamp % $seconds;

        if (0 === $diff) {
            return $start;
        }

        return $timestamp - $diff;
    }

    /**
     * @param int $end
     * @param int $minutes
     *
     * @return float|int|null
     */
    private static function getDefaultRoundingEnd($end, $minutes)
    {
        if ($minutes <= 0) {
            return null;
        }

        $timestamp = $end;
        $seconds = $minutes * 60;
        $diff = $timestamp % $seconds;

        if (0 === $diff) {
            return $end;
        }

        return $timestamp - $diff + $seconds;
    }

    // --- closest

    /**
     * @param int $start
     * @param int $end
     * @param int $minutes
     * @return array
     */
    private static function closestRounding($start, $end, $minutes)
    {
        $roundedStart = self::getClosestRoundingStart($start, $minutes);
        $roundedEnd = self::getClosestRoundingEnd($end, $minutes);

        return [
            'start' => $roundedStart !== null ? $roundedStart : $start,
            'end' => $roundedEnd !== null ? $roundedEnd : $end,
        ];
    }

    /**
     * @param int $start
     * @param int $minutes
     *
     * @return float|int|null
     */
    private static function getClosestRoundingStart($start, $minutes)
    {
        if ($minutes <= 0) {
            return null;
        }

        $timestamp = $start;
        $seconds = $minutes * 60;
        $diff = $timestamp % $seconds;

        if (0 === $diff) {
            return $start;
        }

        if ($diff > ($seconds / 2)) {
            return $timestamp - $diff + $seconds;
        }

        return $timestamp - $diff;
    }

    /**
     * @param int $end
     * @param int $minutes
     *
     * @return float|int|null
     */
    private static function getClosestRoundingEnd($end, $minutes)
    {
        if ($minutes <= 0) {
            return null;
        }

        $timestamp = $end;
        $seconds = $minutes * 60;
        $diff = $timestamp % $seconds;

        if (0 === $diff) {
            return $end;
        }

        if ($diff > ($seconds / 2)) {
            return $timestamp - $diff + $seconds;
        }

        return $timestamp - $diff;
    }

    // --- ceil

    /**
     * @param int $start
     * @param int $end
     * @param int $minutes
     *
     * @return array
     */
    private static function ceilRounding($start, $end, $minutes)
    {
        $roundedStart = self::getCeilRoundingStart($start, $minutes);
        $roundedEnd = self::getCeilRoundingEnd($end, $minutes);

        return [
            'start' => $roundedStart !== null ? $roundedStart : $start,
            'end' => $roundedEnd !== null ? $roundedEnd : $end,
        ];
    }

    /**
     * @param int $start
     * @param int $minutes
     *
     * @return float|int|null
     */
    private static function getCeilRoundingStart($start, $minutes)
    {
        if ($minutes <= 0) {
            return null;
        }

        $timestamp = $start;
        $seconds = $minutes * 60;
        $diff = $timestamp % $seconds;

        if (0 === $diff) {
            return $start;
        }

        return $timestamp - $diff + $seconds;
    }

    /**
     * @param int $end
     * @param int $minutes
     *
     * @return float|int|null
     */
    private static function getCeilRoundingEnd($end, $minutes)
    {
        if ($minutes <= 0) {
            return null;
        }

        $timestamp = $end;
        $seconds = $minutes * 60;
        $diff = $timestamp % $seconds;

        if (0 === $diff) {
            return $end;
        }

        return $timestamp - $diff + $seconds;
    }
}
