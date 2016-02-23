<?php

/**
 * Round timespans according to a defined interval. (see methods)
 */
class Rounding {

  /**
  * Find a beginning and end time whose timespan is as close to
  * the real timepsan as possible while being a multiple of $steps (in minutes).
  *
  * e.g.: 16:07:31 - 17:15:16 is "rounded" to 16:00:00 - 17:15:00
  *       with steps set to 15
  *
  *@param $start the beginning of the timespan
  *@param $end   the end of the timespan
  *@param $steps the steps in minutes (has to divide an hour, e.g. 5 is valid while 7 is not)
  *
  */
  public static function roundTimespan($start,$end,$steps,$allowRoundDown) {
    // calculate how long a steps is (e.g. 15 second steps are 900 seconds long)
    $stepWidth=$steps*60;

    if ($steps == 0) {
      $bestTime = array();
      $bestTime['start']    = $start;
      $bestTime['end']      = $end;
      return $bestTime;
    }


    // calculate how many seconds we are over the previous full step
    $startSecondsOver = $start%$stepWidth;
    $endSecondsOver   = $end%$stepWidth;

    // calculate earlier and later times of full step width
    $earlierStart = $start-$startSecondsOver;
    $earlierEnd   = $end-$endSecondsOver;
    $laterStart   = $start+($stepWidth-$startSecondsOver);
    $laterEnd     = $end+($stepWidth-$endSecondsOver);


    // assuming the earlier start end end time are the best (likely not always true)
    $bestTime = array();
    $bestTime['start']    = $earlierStart;
    $bestTime['end']      = $earlierEnd;
    $bestTime['duration'] = $earlierEnd-$earlierStart;
    $bestTime['totalDeviation'] = abs($start-$earlierStart)+abs($end-$earlierEnd);

    // check for better start and end times
    self::roundTimespanCheckIfBetter($bestTime,$earlierStart,$laterEnd,$start,$end,$allowRoundDown);
    self::roundTimespanCheckIfBetter($bestTime,$laterStart,$earlierEnd,$start,$end,$allowRoundDown);
    self::roundTimespanCheckIfBetter($bestTime,$laterStart,$laterEnd,$start,$end,$allowRoundDown);

    return $bestTime;
  }

  /**
  * Check if the new time values are better than the old once in the array.
  *
  * @param $bestTime (called by reference)
  *                  Array containing the, until now, best time data
  * @param $newStart suggestion for a better start time
  * @param $newEnd   suggestion for a better end time
  * @param $realStart the real start time
  * @param $realEnd   the real end time
  */
  private static function roundTimespanCheckIfBetter(&$bestTime,$newStart,$newEnd,$realStart,$realEnd,$allowRoundDown) {
    $realDuration = $realEnd-$realStart;
    $newDuration = $newEnd-$newStart;
    
    if ($allowRoundDown) {
      if (abs($realDuration-$newDuration) > abs($realDuration - $bestTime['duration'])) {
        // new times are definitely worse, as the timespan is furher away from the real duration
        return;
      }
  
      // still, this might be closer to the real time
      if (abs($realStart-$newStart)+abs($realEnd-$newEnd) >= $bestTime['totalDeviation']) {
        // it is not
        return;
      }
    }
    else {
      if ($newDuration < $realDuration)
        return;

      if ($newDuration > $bestTime['duration'] && $bestTime['duration'] > $realDuration)
        return;
}

    // new time is better, update array
    $bestTime['start']    = $newStart;
    $bestTime['end']      = $newEnd;
    $bestTime['duration'] = $newEnd-$newStart;
    $bestTime['totalDeviation'] = abs($realStart-$newStart)+abs($realEnd-$newEnd);
  }
}

?>
