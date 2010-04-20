<?php


/**
 * delete zef entry 
 *
 * @param integer $usr_ID 
 * @param integer $id -> ID of record
 * @global array  $kga kimai-global-array
 * @author th
 */
function exp_delete_record($id) {
    global $kga;
    global $pdo_conn;
    $pdo_query = $pdo_conn->prepare("DELETE FROM " . $kga['server_prefix'] . "exp WHERE `exp_ID` = ? LIMIT 1;");
    $result = $pdo_query->execute(array($id));
    
    if ($result == false) {
        return $result;
    }
    
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
    global $pdo_conn;
    
    // logfile("DBL:zef_create_record:id:".$usr_ID);
    // logfile("DBL:zef_create_record:data:".serialize($data));
    // logfile("DBL:zef_create_record:diff:".$data['diff']);

    $pdo_query = $pdo_conn->prepare("INSERT INTO " . $kga['server_prefix'] . "exp (  
    `exp_pctID`, 
    `exp_designation`,
    `exp_comment`,
    `exp_comment_type`,
    `exp_timestamp`,
    `exp_value`,
    `exp_usrID`
    ) VALUES (?,?,?,?,?,?,?)
    ;");
    
    $result = $pdo_query->execute(array(
    $data['exp_pctID'],
    $data['exp_designation'],
    $data['exp_comment'],
    $data['exp_comment_type'] ,
    $data['exp_timestamp'],
    $data['exp_value'],
    $usr_ID
    ));
    

    if ($result == false) {
        return $result;
    }
    
    logfile($result);
    
} 





/**
 *  Creates an array of clauses which can be joined together in the WHERE part
 *  of a sql query. The clauses describe whether a line should be included
 *  depending on the filters set.
 *  
 *
 * @param Array list of IDs of users to include
 * @param Array list of IDs of customers to include
 * @param Array list of IDs of projects to include
 * @param Array list of IDs of events to include
 * @return Array list of where clauses to include in the query
 *
 */

function exp_whereClausesFromFilters($users, $customers , $projects ) {
    
    if (!is_array($users)) $users = array();
    if (!is_array($customers)) $customers = array();
    if (!is_array($projects)) $projects = array();


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
    return $whereClauses;


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
    global $kga;
    global $pdo_conn;

    
    $whereClauses = exp_whereClausesFromFilters($users,$customers,$projects);

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
//    $query = sprintf("SELECT zef_ID, zef_in, zef_out, zef_time, zef_pctID, zef_evtID, zef_usrID, pct_ID, knd_name, grp_name, pct_grpID, pct_kndID, evt_name, pct_name, zef_comment, zef_comment_type
//             FROM %szef 
//             Left Join %spct ON zef_pctID = pct_ID
//             Left Join %sknd ON pct_kndID = knd_ID
//             Left Join %sgrp ON grp_ID    = pct_grpID
//             Left Join %sevt ON evt_ID    = zef_evtID
//             WHERE zef_pctID > 0 AND zef_evtID > 0 AND zef_usrID = '%s' %s ORDER BY zef_in DESC %s;"
    $pdo_query = $pdo_conn->prepare("SELECT exp_ID, exp_timestamp, exp_value, exp_pctID, exp_designation, exp_usrID, pct_ID, knd_name, pct_kndID, pct_name, exp_comment, exp_comment_type, usr_name, exp_cleared
             FROM " . $kga['server_prefix'] . "exp 
             Join " . $kga['server_prefix'] . "pct ON exp_pctID = pct_ID
             Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID
             Join " . $kga['server_prefix'] . "usr ON exp_usrID = usr_ID "
              .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses). " ORDER BY exp_timestamp DESC " . $limit . ";");
    
             $pdo_query->execute();
    
  
    
//    logfile("********************* USER ID:" . $user);
//    logfile("********************* QUERY: $query");
//    logfile("*********************" . mysql_error());
    
    $i=0;
    $arr=array();
    /* TODO: needs revision as foreach loop */
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $arr[$i]['exp_ID']          = $row['exp_ID'];
        $arr[$i]['exp_timestamp']   = $row['exp_timestamp'];
        $arr[$i]['exp_value']       = $row['exp_value'];
        $arr[$i]['exp_pctID']       = $row['exp_pctID'];
        $arr[$i]['exp_designation'] = $row['exp_designation'];
        $arr[$i]['exp_usrID']   = $row['exp_usrID'];
        $arr[$i]['pct_ID']      = $row['pct_ID'];
        $arr[$i]['knd_name']    = $row['knd_name'];
        $arr[$i]['pct_kndID']   = $row['pct_kndID'];
        $arr[$i]['pct_name']    = $row['pct_name'];
        $arr[$i]['exp_comment'] = $row['exp_comment'];
        $arr[$i]['exp_comment_type'] = $row['exp_comment_type'];
        $arr[$i]['usr_name']    = $row['usr_name'];
        $arr[$i]['exp_cleared']    = $row['exp_cleared'];
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
    global $kga;
    global $pdo_conn;     
    $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "exp 
    Left Join " . $kga['server_prefix'] . "pct ON exp_pctID = pct_ID 
    Left Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID 
    WHERE exp_ID = ? LIMIT 1;");
  
    $pdo_query->execute(array($id));
    $row    = $pdo_query->fetch(PDO::FETCH_ASSOC);
    return $row;
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

    global $kga;
    global $pdo_conn;

    if ($exp_id) {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "exp WHERE exp_ID = ?");
    } else {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "exp WHERE exp_usrID = ".$kga['usr']['usr_ID']." ORDER BY exp_ID DESC LIMIT 1");
        // logfile("SELECT * FROM " . $kga['server_prefix'] . "zef ORDER BY zef_ID DESC LIMIT 1");
    }
    
    $result = $pdo_query->execute(array($exp_id));

    if ($result == false) {
        return false;
    } else {
        $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);
        return $result_array;
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
    global $kga;
    global $pdo_conn;
    
    // logfile("editrecord####################");
        
        
    // logfile("DBL:zef_create_record:id:".$usr_ID);
    // logfile("DBL:zef_create_record:data:".serialize($data));
    // logfile("DBL:zef_create_record:diff:".$data['diff']);
    
    $original_array = exp_get_data($id);
    $new_array = array();
    
    foreach ($original_array as $key => $value) {
        if (isset($data[$key]) == true) {
            $new_array[$key] = $data[$key];
        } else {
            $new_array[$key] = $original_array[$key];
        }
    
    }



    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "exp SET
    exp_pctID = ?,
    exp_designation = ?,
    exp_comment = ?,
    exp_comment_type = ?,
    exp_timestamp = ?,
    exp_value = ?
    WHERE exp_id = ?;");    
    
    $result = $pdo_query->execute(array(
    $new_array['exp_pctID'],
    $new_array['exp_designation'] ,
    $new_array['exp_comment'],
    $new_array['exp_comment_type'] ,
    $new_array['exp_timestamp'],
    $new_array['exp_value'],
    $id
    ));
    

    if ($result == false) {
        return $result;
    }
    
    logfile("editrecord:result:".$result);




    
    // $data['pct_ID']       
    // $data['evt_ID']       
    // $data['comment']      
    // $data['comment_type'] 
    // $data['erase']        
    // $data['in']           
    // $data['out']          
    // $data['diff']    
    
    // if wrong time values have been entered in the edit window
    // the following 3 variables arrive as zeros - like so:

    // $data['in']   = 0;
    // $data['out']  = 0;
    // $data['diff'] = 0;   
    
    // in this case the record has to be edited WITHOUT setting new time values
    

     // @oleg: ein zef-eintrag muss auch ohne die zeiten aktualisierbar sein weil die ggf. bei der prüfung durchfallen.



} 

function get_arr_exp_usr($in,$out,$users = null,$customers = null,$projects = null) {
    global $kga;
    global $pdo_conn;

    $whereClauses = exp_whereClausesFromFilters($users,$customers,$projects);
    $whereClauses[] = $kga['server_prefix'].'usr.usr_trash = 0';

    if ($in)
      $whereClauses[]="exp_timestamp >= $in";
    if ($out)
      $whereClauses[]="exp_timestamp <= $out"; 

   $pdo_query = $pdo_conn->prepare("SELECT sum(exp_value) as expenses, usr_ID
             FROM " . $kga['server_prefix'] . "exp 
             Join " . $kga['server_prefix'] . "pct ON exp_pctID = pct_ID
             Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID
             Join " . $kga['server_prefix'] . "usr ON exp_usrID = usr_ID ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
             " ORDER BY exp_timestamp DESC;");
             $pdo_query->execute();
   

    $arr = array(); 
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($row['usr_ID'])) break;
        $arr[$row['usr_ID']] = $row['expenses'];
    }
    
    return $arr;
}


function get_arr_exp_knd($in,$out,$users = null,$customers = null,$projects = null) {
    global $kga;
    global $pdo_conn;

    $whereClauses = exp_whereClausesFromFilters($users,$customers,$projects);
    $whereClauses[] = $kga['server_prefix'].'knd.knd_trash = 0';

    if ($in)
      $whereClauses[]="exp_timestamp >= $in";
    if ($out)
      $whereClauses[]="exp_timestamp <= $out"; 
    
    $pdo_query = $pdo_conn->prepare("SELECT SUM(exp_value) as expenses, knd_ID FROM " . $kga['server_prefix'] . "exp 
            Left Join " . $kga['server_prefix'] . "pct ON exp_pctID = pct_ID
            Left Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID  ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
            " GROUP BY knd_ID;");
    $pdo_query->execute();

    $arr = array();
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($row['knd_ID'])) break;
        $arr[$row['knd_ID']] = $row['expenses'];
    }
    
    return $arr;
}

function get_arr_exp_pct($in,$out,$users = null,$customers = null,$projects = null) {
    global $kga;
    global $pdo_conn;

    $whereClauses = exp_whereClausesFromFilters($users,$customers,$projects);
    $whereClauses[] = $kga['server_prefix'].'pct.pct_trash = 0';

    if ($in)
      $whereClauses[]="exp_timestamp >= $in";
    if ($out)
      $whereClauses[]="exp_timestamp <= $out"; 
    $pdo_query = $pdo_conn->prepare("SELECT sum(exp_value) as expenses,exp_pctID FROM " . $kga['server_prefix'] . "exp
            Left Join " . $kga['server_prefix'] . "pct ON exp_pctID = pct_ID
            Left Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID  ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
       " GROUP BY exp_pctID;");
    $pdo_query->execute();

    $arr = array();
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($row['exp_pctID'])) break;
        $arr[$row['exp_pctID']] = $row['expenses'];
    }
    return $arr;
}


?>