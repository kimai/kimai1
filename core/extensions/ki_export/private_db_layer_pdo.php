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
    global $kga;
    global $pdo_conn;
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "zef SET zef_cleared = ? WHERE `zef_ID` = ? LIMIT 1;");
    $result = $pdo_query->execute(array($cleared?1:0,$id));
    
    if ($result)
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
    global $kga;
    global $pdo_conn;
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "exp SET exp_cleared = ? WHERE `exp_ID` = ? LIMIT 1;");
    $result = $pdo_query->execute(array($cleared?1:0,$id));
    
    if ($result)
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
    global $kga,$pdo_conn,$all_column_headers;    

    $header_number = array_search($header,$all_column_headers);

    $pdo_query = $pdo_conn->prepare(
      "UPDATE " . $kga['server_prefix'] . "usr SET 
          export_disabled_columns = `export_disabled_columns`^POWER(2,?) 
       WHERE `usr_ID` = ?;");
    $result = $pdo_query->execute(array($header_number,$kga['usr']['usr_ID']));
    
    if ($result)
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
    global $kga,$pdo_conn,$all_column_headers; 

    $disabled_headers = array();

    $pdo_query = $pdo_conn->prepare(
      "SELECT export_disabled_columns FROM " . $kga['server_prefix'] . "usr
       WHERE `usr_ID` = ?;");
    
    if (!$pdo_query->execute(array($user_id))) return 0;

    
    $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);
    $code = $result_array['export_disabled_columns'];

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