<?php

/**
 * returns the data of a costumer
 *
 * @param integer $name name of costumer in table
 * @global array $kga kimai-global-array
 * @return array
 * @author sl
 */
function get_entry_knd($id) {
    global $kga, $conn;
    $p = $kga['server_prefix'];

    $filter['knd_name'] = MySQL::SQLValue($id);

    $result = $conn->SelectRows($kga['server_prefix'].'knd', $filter,null,null,true,1);

    if (! $result)
      return false;

    return $conn->RowArray(0,MYSQL_ASSOC);
}


?>