<?php


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
?>