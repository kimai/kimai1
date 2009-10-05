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
?>