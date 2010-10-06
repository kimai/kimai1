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


/**
 * set cleared state for zef entry
 *
 * @param integer $id -> ID of record
 * @param boolean $cleared -> true if record is cleared, otherwise false
 * @global array  $kga kimai-global-array
 * @author sl
 */
function xp_zef_set_cleared($id,$cleared) {
    global $kga,$conn;

    $table                 = $kga['server_prefix']."zef";
    $values['zef_cleared'] = $cleared?1:0;
    $filter['zef_ID']      = MySQL::SQLValue($id,MySQL::SQLVALUE_NUMBER);
    $query = MySQL::BuildSQLUpdate($table, $values, $filter);

    if ($conn->Query($query))
      return true;
    else
      return false;
} 

/**
 * set cleared state for exp entry
 *
 * @param integer $id -> ID of record
 * @param boolean $cleared -> true if record is cleared, otherwise false
 * @global array  $kga kimai-global-array
 * @author sl
 */
function xp_exp_set_cleared($id,$cleared) {
    global $kga,$conn;

    $table                 = $kga['server_prefix']."exp";
    $values['exp_cleared'] = $cleared?1:0;
    $filter['exp_ID']      = MySQL::SQLValue($id,MySQL::SQLVALUE_NUMBER);
    $query = MySQL::BuildSQLUpdate($table, $values, $filter);

    if ($conn->Query($query))
      return true;
    else
      return false;
} 


/**
 * save deselection of columns
 *
 * @param string  $header -> header name
 * @global array  $kga kimai-global-array
 * @global array  $all_column_headers array containing all columns
 * @author sl
 */
function xp_toggle_header($header) {
    global $kga,$conn,$all_column_headers;    

    $header_number = array_search($header,$all_column_headers);

    $table                 = $kga['server_prefix']."preferences";
    $values['value']       = "`value`^POWER(2,$header_number)";
    $filter['userID']      = MySQL::SQLValue($kga['usr']['usr_ID'],MySQL::SQLVALUE_NUMBER);
    $filter['var']         = MySQL::SQLValue('export_disabled_columns');
    $query = MySQL::BuildSQLUpdate($table, $values, $filter);

    if ($conn->Query($query))
      return true;
    else 
      return false;
} 

/**
 * get list of deselected columns
 *
 * @param integer $user_id -> header name
 * @global array  $kga kimai-global-array
 * @global array  $all_column_headers array containing all columns
 * @author sl
 */
function xp_get_disabled_headers($user_id) {
    global $kga,$conn,$all_column_headers; 

    $disabled_headers = array();

    $filter['userID'] = MySQL::SQLValue($user_id, MySQL::SQLVALUE_NUMBER);
    $filter['var']    = MySQL::SQLValue('export_disabled_columns');
    $table = $kga['server_prefix']."preferences";

    if (!$conn->SelectRows($table, $filter)) return 0;

    $result_array = $conn->RowArray(0,MYSQL_ASSOC);
    $code = $result_array['value'];

    $i = 0;
    while ($code>0) {
       if ($code%2==1) // bit set?
        $disabled_headers[$all_column_headers[$i]] = true;

       // next bit and array element
       $code = $code/2;
       $i++;
    }
    return $disabled_headers;
}
?>