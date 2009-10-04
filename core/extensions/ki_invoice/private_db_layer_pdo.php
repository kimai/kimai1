<?php

/**
 * returns the data of a costumer
 *
 * @param integer $name name of costumer in table
 * @global array $kga kimai-global-array
 * @return array
 * @author AA
 */
function get_entry_knd($id) {
    global $kga;
    global $pdo_conn;
    $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "knd 
    WHERE knd_name = ? LIMIT 1;");
  
    $pdo_query->execute(array($id));
    $row    = $pdo_query->fetch(PDO::FETCH_ASSOC);
    return $row;
}


?>