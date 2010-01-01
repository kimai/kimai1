<?php


function exp_clean_data($data) {
    global $kga;   
    foreach ($data as $key => $value) {
        $return[$key] = urldecode(strip_tags($data[$key]));
        $return[$key] = str_replace('"','_',$data[$key]);
        $return[$key] = str_replace("'",'_',$data[$key]);
        $return[$key] = str_replace('\\','',$data[$key]);
    if ($kga['utf8']) $return[$key] = utf8_decode($return[$key]);
    }
    
    return $return;
}

/**
 * delete exp entry 
 *
 * @param integer $usr_ID 
 * @param integer $id -> ID of record
 * @global array  $kga kimai-global-array
 * @author th
 */
function exp_delete_record($id) {
    global $kga,$conn;
    $filter["exp_ID"] = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."exp";
    $query = MySQL::BuildSQLDelete($table, $filter);
    return $conn->Query($query);    
} 

/**
 * create exp entry 
 *
 * @param integer $id    ID of record
 * @param integer $data  array with record data
 * @global array  $kga    kimai-global-array
 * @author sl
 */
function exp_create_record($usr_ID,$data) {
    global $kga;
    global $conn;
 
    $data = clean_data($data);
    
    $values ['exp_pctID']        =   MySQL::SQLValue( $data ['exp_pctID']        , MySQL::SQLVALUE_NUMBER );
    $values ['exp_designation']  =   MySQL::SQLValue( $data ['exp_designation'] );
    $values ['exp_comment']      =   MySQL::SQLValue( $data ['exp_comment'] );
    $values ['exp_comment_type'] =   MySQL::SQLValue( $data ['exp_comment_type'] , MySQL::SQLVALUE_NUMBER );
    $values ['exp_timestamp']    =   MySQL::SQLValue( $data ['exp_timestamp']    , MySQL::SQLVALUE_NUMBER );
    $values ['exp_value']        =   MySQL::SQLValue( $data ['exp_value']        , MySQL::SQLVALUE_NUMBER );
    $values ['exp_usrID']        =   MySQL::SQLValue( $usr_ID                    , MySQL::SQLVALUE_NUMBER );
    
    $table = $kga['server_prefix']."exp";
    return $conn->InsertRow($table, $values);    
} 

/**
 * returns expenses for specific user as multidimensional array
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return array
 * @author th 
 */

// TODO: Test it!
function get_arr_exp($start,$end,$users = null,$customers = null,$projects = null,$limit=false) {
    global $kga,$conn;
    
    if (!is_array($users)) $users = array();
    if (!is_array($customers)) $customers = array();
    if (!is_array($projects)) $projects = array();
    
    $start  = MySQL::SQLValue($start    , MySQL::SQLVALUE_NUMBER);
    $end = MySQL::SQLValue($end   , MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($users);$i++)
      $users[$i] = MySQL::SQLValue($users[$i], MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($customers);$i++)
      $customers[$i] = MySQL::SQLValue($customers[$i], MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($projects);$i++)
      $projects[$i] = MySQL::SQLValue($projects[$i], MySQL::SQLVALUE_NUMBER);
    $limit = MySQL::SQLValue($limit , MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];

    $whereClauses = array();
    
    if (count($users) > 0) {
      $whereClauses[] = "exp_usrID in (".implode(',',$users).")";
    }
    
    if (count($customers) > 0) {
      $whereClauses[] = "knd_ID in (".implode(',',$customers).")";
    }
    
    if (count($projects) > 0) {
      $whereClauses[] = "pct_ID in (".implode(',',$projects).")";
    }  

    if ($start)
      $whereClauses[]="exp_timestamp >= $start";
    if ($end)
      $whereClauses[]="exp_timestamp <= $end";

    if ($limit) {
        if (isset($kga['conf']['rowlimit'])) {
            $limit = "LIMIT " .$kga['conf']['rowlimit'];
        } else {
            $limit="LIMIT 100";
        }
    } else {
        $limit="";
    }
    $query = "SELECT exp_ID, exp_timestamp, exp_value, exp_pctID, exp_designation, exp_usrID, pct_ID, knd_name, pct_kndID, pct_name, exp_comment, exp_comment_type, usr_name, exp_cleared
             FROM " . $kga['server_prefix'] . "exp 
             Join " . $kga['server_prefix'] . "pct ON exp_pctID = pct_ID
             Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID
             Join " . $kga['server_prefix'] . "usr ON exp_usrID = usr_ID "
              .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses). " ORDER BY exp_timestamp DESC " . $limit . ";";
    
    $conn->Query($query);
    
  
    
//    logfile("********************* USER ID:" . $user);
//    logfile("********************* QUERY: $query");
//    logfile("*********************" . mysql_error());
    
    $i=0;
    $arr=array();
    /* TODO: needs revision as foreach loop */
    $conn->MoveFirst();
    while (! $conn->EndOfSeek()) {
      $row = $conn->Row();
      $arr[$i]['exp_ID']           = $row->exp_ID;
      $arr[$i]['exp_timestamp']    = $row->exp_timestamp;
      $arr[$i]['exp_value']        = $row->exp_value;
      $arr[$i]['exp_pctID']        = $row->exp_pctID;
      $arr[$i]['exp_designation']  = $row->exp_designation;
      $arr[$i]['exp_usrID']        = $row->exp_usrID;
      $arr[$i]['pct_ID']           = $row->pct_ID;
      $arr[$i]['knd_name']         = $row->knd_name;
      $arr[$i]['pct_kndID']        = $row->pct_kndID;
      $arr[$i]['pct_name']         = $row->pct_name;
      $arr[$i]['exp_comment']      = $row->exp_comment;
      $arr[$i]['exp_comment_type'] = $row->exp_comment_type;
      $arr[$i]['usr_name']         = $row->usr_name;
      $arr[$i]['exp_cleared']      = $row->exp_cleared;
      $i++;
    }
    
    return $arr;
}


/**
 * returns single expense entry as array
 *
 * @param integer $id ID of entry in table exp
 * @global array $kga kimai-global-array
 * @return array
 * @author sl
 */
function get_entry_exp($id) {
    global $kga,$conn;

    $id    = MySQL::SQLValue($id   , MySQL::SQLVALUE_NUMBER);
    $p     = $kga['server_prefix'];
  
    $query = "SELECT * FROM ${p}exp 
              Left Join ${p}pct ON exp_pctID = pct_ID 
              Left Join ${p}knd ON pct_kndID = knd_ID 
              WHERE exp_ID = $id LIMIT 1;";

    $conn->Query($query);
    return $conn->RowArray(0,MYSQL_ASSOC);
}



/**
 * Returns the data of a certain expense record
 *
 * @param array $exp_id        exp_id of the record
 * @global array $kga          kimai-global-array
 * @return array               the record's data as array, false on failure
 * @author ob
 */
function exp_get_data($exp_id) {
    global $kga,$conn;
    
    $p = $kga['server_prefix'];
    
    $exp_id = MySQL::SQLValue($exp_id, MySQL::SQLVALUE_NUMBER);

    if ($exp_id) {
        $result = $conn->Query("SELECT * FROM ${p}exp WHERE exp_ID = " . $exp_id);
    } else {
        $result = $conn->Query("SELECT * FROM ${p}exp WHERE exp_usrID = ".$kga['usr']['usr_ID']." ORDER BY exp_ID DESC LIMIT 1");
    }
    
    if (! $result) {
      return false;
    } else {
        return $conn->RowArray(0,MYSQL_ASSOC);
    }
}


/**
 * edit exp entry 
 *
 * @param integer $id ID of record
 * @global array $kga kimai-global-array
 * @param integer $data  array with new record data
 * @author th
 */
 
function exp_edit_record($id,$data) {
    global $kga,$conn;
    
    $data = clean_data($data);
   
    $original_array = exp_get_data($id);
    $new_array = array();
    
    foreach ($original_array as $key => $value) {
        if (isset($data[$key]) == true) {
            $new_array[$key] = $data[$key];
        } else {
            $new_array[$key] = $original_array[$key];
        }
    }

    $values ['exp_pctID']        = MySQL::SQLValue($new_array ['exp_pctID']       , MySQL::SQLVALUE_NUMBER );
    $values ['exp_designation']  = MySQL::SQLValue($new_array ['exp_designation']                          );
    $values ['exp_comment']      = MySQL::SQLValue($new_array ['exp_comment']                              );
    $values ['exp_comment_type'] = MySQL::SQLValue($new_array ['exp_comment_type'], MySQL::SQLVALUE_NUMBER );
    $values ['exp_timestamp']    = MySQL::SQLValue($new_array ['exp_timestamp']   , MySQL::SQLVALUE_NUMBER );
    $values ['exp_value']        = MySQL::SQLValue($new_array ['exp_value']       , MySQL::SQLVALUE_NUMBER );
                                   
    $filter ['exp_ID']           = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."exp";
    $query = MySQL::BuildSQLUpdate($table, $values, $filter);

    $success = true;
    
    if (! $conn->Query($query)) $success = false;
    
    if ($success) {
        if (! $conn->TransactionEnd()) $conn->Kill();
    } else {
        if (! $conn->TransactionRollback()) $conn->Kill();
    }

    return $success;
    
    $original_array = exp_get_data($id);
    $new_array = array();
    
    foreach ($original_array as $key => $value) {
        if (isset($data[$key]) == true) {
            $new_array[$key] = $data[$key];
        } else {
            $new_array[$key] = $original_array[$key];
        }
    
    }
} 

function get_arr_exp_usr($start,$end,$users = null,$customers = null,$projects = null) {
    global $kga,$conn;
    
    if (!is_array($users)) $users = array();
    if (!is_array($customers)) $customers = array();
    if (!is_array($projects)) $projects = array();
    
    $start = MySQL::SQLValue($start, MySQL::SQLVALUE_NUMBER);
    $end   = MySQL::SQLValue($end  , MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($users);$i++)
      $users[$i] = MySQL::SQLValue($users[$i], MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($customers);$i++)
      $customers[$i] = MySQL::SQLValue($customers[$i], MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($projects);$i++)
      $projects[$i] = MySQL::SQLValue($projects[$i], MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];
    $whereClauses = array("${p}usr.usr_trash = 0");
    
    if (count($users) > 0) {
      $whereClauses[] = "exp_usrID in (".implode(',',$users).")";
    }
    
    if (count($customers) > 0) {
      $whereClauses[] = "knd_ID in (".implode(',',$customers).")";
    }
    
    if (count($projects) > 0) {
      $whereClauses[] = "pct_ID in (".implode(',',$projects).")";
    }  

    if ($start)
      $whereClauses[]="exp_timestamp >= $start";
    if ($end)
      $whereClauses[]="exp_timestamp <= $end"; 

   $query = "SELECT SUM(exp_value) as expenses, usr_ID
             FROM ${p}exp 
             Join ${p}pct ON exp_pctID = pct_ID
             Join ${p}knd ON pct_kndID = knd_ID
             Join ${p}usr ON exp_usrID = usr_ID ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
             " GROUP BY usr_ID;";

    $result = $conn->Query($query);
    if (! $result) return array();
    $rows = $conn->RecordsArray(MYSQL_ASSOC);
    if (!$rows) return array();
   

    $arr = array(); 
    foreach($rows as $row) {
        $arr[$row['usr_ID']] = $row['expenses'];
    }
    
    return $arr;
}


function get_arr_exp_knd($start,$end,$users = null,$customers = null,$projects = null) {
    global $kga,$conn;
    
    if (!is_array($users)) $users = array();
    if (!is_array($customers)) $customers = array();
    if (!is_array($projects)) $projects = array();
    
    $start = MySQL::SQLValue($start, MySQL::SQLVALUE_NUMBER);
    $end   = MySQL::SQLValue($end  , MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($users);$i++)
      $users[$i] = MySQL::SQLValue($users[$i], MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($customers);$i++)
      $customers[$i] = MySQL::SQLValue($customers[$i], MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($projects);$i++)
      $projects[$i] = MySQL::SQLValue($projects[$i], MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];
    $whereClauses = array("${p}knd.knd_trash = 0");
    
    if (count($users) > 0) {
      $whereClauses[] = "exp_usrID in (".implode(',',$users).")";
    }
    
    if (count($customers) > 0) {
      $whereClauses[] = "knd_ID in (".implode(',',$customers).")";
    }
    
    if (count($projects) > 0) {
      $whereClauses[] = "pct_ID in (".implode(',',$projects).")";
    }  

    if ($start)
      $whereClauses[]="exp_timestamp >= $start";
    if ($end)
      $whereClauses[]="exp_timestamp <= $end"; 
    
    $query = "SELECT SUM(exp_value) as expenses, knd_ID FROM ${p}exp 
            Left Join ${p}pct ON exp_pctID = pct_ID
            Left Join ${p}knd ON pct_kndID = knd_ID  ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
            " GROUP BY knd_ID;";

    $result = $conn->Query($query);
    if (! $result) return array();
    $rows = $conn->RecordsArray(MYSQL_ASSOC);
    if (!$rows) return array();

    $arr = array();
    foreach ($rows as $row) {
        $arr[$row['knd_ID']] = $row['expenses'];
    }
    
    return $arr;
}

function get_arr_exp_pct($start,$end,$users = null,$customers = null,$projects = null) {
    global $kga,$conn;
    
    if (!is_array($users)) $users = array();
    if (!is_array($customers)) $customers = array();
    if (!is_array($projects)) $projects = array();
    
    $start = MySQL::SQLValue($start, MySQL::SQLVALUE_NUMBER);
    $end   = MySQL::SQLValue($end  , MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($users);$i++)
      $users[$i] = MySQL::SQLValue($users[$i], MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($customers);$i++)
      $customers[$i] = MySQL::SQLValue($customers[$i], MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($projects);$i++)
      $projects[$i] = MySQL::SQLValue($projects[$i], MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];
    $whereClauses = array("${p}pct.pct_trash = 0");
    
    if (count($users) > 0) {
      $whereClauses[] = "exp_usrID in (".implode(',',$users).")";
    }
    
    if (count($customers) > 0) {
      $whereClauses[] = "knd_ID in (".implode(',',$customers).")";
    }
    
    if (count($projects) > 0) {
      $whereClauses[] = "pct_ID in (".implode(',',$projects).")";
    }  

    if ($start)
      $whereClauses[]="exp_timestamp >= $start";
    if ($end)
      $whereClauses[]="exp_timestamp <= $end";
 
    $query = "SELECT sum(exp_value) as expenses,exp_pctID FROM ${p}exp
            Left Join ${p}pct ON exp_pctID = pct_ID
            Left Join ${p}knd ON pct_kndID = knd_ID  ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
       " GROUP BY exp_pctID;";

    $result = $conn->Query($query);
    if (! $result) return array();
    $rows = $conn->RecordsArray(MYSQL_ASSOC);
    if (!$rows) return array();

    $arr = array();
    foreach ($rows as $row) {
        $arr[$row['exp_pctID']] = $row['expenses'];
    }
    return $arr;
}


?>