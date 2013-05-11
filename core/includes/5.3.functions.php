<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2009 Kimai-Development-Team
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

// from http://de2.php.net/manual/de/function.array-replace-recursive.php#92574
if (!function_exists('array_replace_recursive')) {
function array_replace_recursive($array, $array1)
   {
     if (!function_exists('recurse')) {
       function recurse($array, $array1)
       {
         foreach ($array1 as $key => $value)
         {
           // create new key in $array, if it is empty or not an array
           if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key])))
           {
             $array[$key] = array();
           }
     
           // overwrite the value in the base array
           if (is_array($value))
           {
             $value = recurse($array[$key], $value);
           }
           $array[$key] = $value;
         }
         return $array;
       }
     }
   
     // handle the arguments, merge one by one
     $args = func_get_args();
     $array = $args[0];
     if (!is_array($array))
     {
       return $array;
     }
     for ($i = 1; $i < count($args); $i++)
     {
       if (is_array($args[$i]))
       {
         $array = recurse($array, $args[$i]);
       }
     }
     return $array;
   }
}  
?>