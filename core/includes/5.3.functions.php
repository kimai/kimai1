<?php

if (!function_exists('array_replace')) {
  function array_replace( array &$array, array &$array1, $filterEmpty=false ) {
    $args = func_get_args();
    $count = func_num_args()-1;

    for ($i = 0; $i < $count; ++$i) {
      if (is_array($args[$i])) {
        foreach ($args[$i] as $key => $val) {
          if ($filterEmpty && empty($val)) continue;
          $array[$key] = $val;
        }
      }
      else {
        trigger_error(
        __FUNCTION__ . '(): Argument #' . ($i+1) . ' is not an array',
        E_USER_WARNING
        );
        return NULL;
      }
    }

    return $array;
  }
}  
?>