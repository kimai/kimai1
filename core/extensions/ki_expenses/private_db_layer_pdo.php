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
    $p = $kga['server_prefix'];

    $pdo_query = $pdo_conn->prepare("DELETE FROM ${p}exp WHERE `exp_ID` = ? LIMIT 1;");
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
    $p = $kga['server_prefix'];

    $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}exp (  
    `exp_pctID`, 
    `exp_designation`,
    `exp_comment`,
    `exp_comment_type`,
    `exp_timestamp`,
    `exp_multiplier`,
    `exp_value`,
    `exp_usrID`,
    `exp_refundable`
    ) VALUES (?,?,?,?,?,?,?,?,?)
    ;");
    
    $result = $pdo_query->execute(array(
    $data['exp_pctID'],
    $data['exp_designation'],
    $data['exp_comment'],
    $data['exp_comment_type'] ,
    $data['exp_timestamp'],
    $data['exp_multiplier'],
    $data['exp_value'],
    $usr_ID,
    $data['exp_refundable']
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

function get_arr_exp($start,$end,$users = null,$customers = null,$projects = null,$limit=false, $reverse_order = false, $filter_refundable = -1,$filterCleared = null) {
    global $kga;
    global $pdo_conn;
    $p = $kga['server_prefix'];

    if (!is_numeric($filterCleared)) {
      $filterCleared = $kga['conf']['hideClearedEntries']-1; // 0 gets -1 for disabled, 1 gets 0 for only not cleared entries
    }


    
    $whereClauses = exp_whereClausesFromFilters($users,$customers,$projects);

    if (isset($kga['customer']))
      $whereClauses[] = "${p}pct.pct_internal = 0";

    if ($start)
      $whereClauses[]="exp_timestamp >= $start";
    if ($end)
      $whereClauses[]="exp_timestamp <= $end";
    if ($filterCleared > -1)
      $whereClauses[] = "exp_cleared = $filterCleared";
      
    switch ($filter_refundable) {
    	case 0:
    		$whereClauses[] = "exp_refundable > 0";
    		break;
    	case 1:
    		$whereClauses[] = "exp_refundable <= 0";
    		break;
    	case -1:
    	default:
    		// return all expenses - refundable and non refundable
    }

    if ($limit) {
        if (isset($kga['conf']['rowlimit'])) {
            $limit = "LIMIT " .$kga['conf']['rowlimit'];
        } else {
            $limit="LIMIT 100";
        }
    } else {
        $limit="";
    }
    $pdo_query = $pdo_conn->prepare("SELECT exp_ID, exp_timestamp, exp_multiplier, exp_value, exp_pctID, exp_designation, exp_usrID,
              pct_ID, knd_name, pct_kndID, pct_name, exp_comment, exp_comment_type, exp_refundable, usr_name, exp_cleared
             FROM ${p}exp 
             Join ${p}pct ON exp_pctID = pct_ID
             Join ${p}knd ON pct_kndID = knd_ID
             Join ${p}usr ON exp_usrID = usr_ID "
              .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
             ' ORDER BY exp_timestamp '.($reverse_order?'ASC ':'DESC ') . $limit . ";");
    
             $pdo_query->execute();  
  
    
    $i=0;
    $arr=array();
    /* TODO: needs revision as foreach loop */
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $arr[$i]['exp_ID']             = $row['exp_ID'];
        $arr[$i]['exp_timestamp']      = $row['exp_timestamp'];
        $arr[$i]['exp_multiplier']     = $row['exp_multiplier'];
        $arr[$i]['exp_value']          = $row['exp_value'];
        $arr[$i]['exp_pctID']          = $row['exp_pctID'];
        $arr[$i]['exp_designation']    = $row['exp_designation'];
        $arr[$i]['exp_usrID']          = $row['exp_usrID'];
        $arr[$i]['pct_ID']             = $row['pct_ID'];
        $arr[$i]['knd_name']           = $row['knd_name'];
        $arr[$i]['pct_kndID']          = $row['pct_kndID'];
        $arr[$i]['pct_name']           = $row['pct_name'];
        $arr[$i]['exp_comment']        = $row['exp_comment'];
        $arr[$i]['exp_comment_type']   = $row['exp_comment_type'];
        $arr[$i]['exp_refundable']     = $row['exp_refundable'];
        $arr[$i]['usr_name']           = $row['usr_name'];
        $arr[$i]['exp_cleared']        = $row['exp_cleared'];
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
    $p = $kga['server_prefix'];
  
    $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}exp 
    Left Join ${p}pct ON exp_pctID = pct_ID 
    Left Join ${p}knd ON pct_kndID = knd_ID 
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
    $p = $kga['server_prefix'];

    if ($exp_id) {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}exp WHERE exp_ID = ?");
    } else {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}exp WHERE exp_usrID = ".$kga['usr']['usr_ID']." ORDER BY exp_ID DESC LIMIT 1");
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
    $p = $kga['server_prefix'];
    
    $original_array = exp_get_data($id);
    $new_array = array();
    
    foreach ($original_array as $key => $value) {
        if (isset($data[$key]) == true) {
            $new_array[$key] = $data[$key];
        } else {
            $new_array[$key] = $original_array[$key];
        }
    
    }



    $pdo_query = $pdo_conn->prepare("UPDATE ${p}exp SET
    exp_pctID = ?,
    exp_designation = ?,
    exp_comment = ?,
    exp_comment_type = ?,
    exp_timestamp = ?,
    exp_multiplier = ?,
    exp_value = ?,
    exp_refundable = ?
    WHERE exp_id = ?;");    
    
    $result = $pdo_query->execute(array(
    $new_array['exp_pctID'],
    $new_array['exp_designation'] ,
    $new_array['exp_comment'],
    $new_array['exp_comment_type'] ,
    $new_array['exp_timestamp'],
    $new_array['exp_multiplier'],
    $new_array['exp_value'],
    $new_array['exp_refundable'],
    $id
    ));
    

    if ($result == false) {
        return $result;
    }
    
    logfile("editrecord:result:".$result);



} 

/**
 * Get the sum of expenses for every user.
 * @param int $in Time from which to take the expenses into account.
 * @param int $out Time until which to take the expenses into account.
 * @param array $users Array of user IDs to filter the expenses by.
 * @param array $customers Array of customer IDs to filter the expenses by.
 * @param array $projects Array of project IDs to filter the expenses by.
 * @return array Array which assigns every user (via his ID) the sum of his expenses.
 */
function get_arr_exp_usr($in,$out,$users = null,$customers = null,$projects = null) {
    global $kga;
    global $pdo_conn;
    $p = $kga['server_prefix'];

    $whereClauses = exp_whereClausesFromFilters($users,$customers,$projects);
    $whereClauses[] = "${p}usr.usr_trash = 0";

    if ($in)
      $whereClauses[]="exp_timestamp >= $in";
    if ($out)
      $whereClauses[]="exp_timestamp <= $out"; 

   $pdo_query = $pdo_conn->prepare("SELECT sum(exp_value) as expenses, usr_ID
             FROM ${p}exp 
             Join ${p}pct ON exp_pctID = pct_ID
             Join ${p}knd ON pct_kndID = knd_ID
             Join ${p}usr ON exp_usrID = usr_ID ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
             " GROUP BY usr_ID;");
             $pdo_query->execute();
   

    $arr = array(); 
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($row['usr_ID'])) break;
        $arr[$row['usr_ID']] = $row['expenses'];
    }
    
    return $arr;
}


/**
 * Get the sum of expenses for every customer.
 * @param int $in Time from which to take the expenses into account.
 * @param int $out Time until which to take the expenses into account.
 * @param array $users Array of user IDs to filter the expenses by.
 * @param array $customers Array of customer IDs to filter the expenses by.
 * @param array $projects Array of project IDs to filter the expenses by.
 * @return array Array which assigns every customer (via his ID) the sum of his expenses.
 */
function get_arr_exp_knd($in,$out,$users = null,$customers = null,$projects = null) {
    global $kga;
    global $pdo_conn;
    $p = $kga['server_prefix'];

    $whereClauses = exp_whereClausesFromFilters($users,$customers,$projects);
    $whereClauses[] = "${p}knd.knd_trash = 0";

    if ($in)
      $whereClauses[]="exp_timestamp >= $in";
    if ($out)
      $whereClauses[]="exp_timestamp <= $out"; 
    
    $pdo_query = $pdo_conn->prepare("SELECT SUM(exp_value) as expenses, knd_ID FROM ${p}exp 
            Left Join ${p}pct ON exp_pctID = pct_ID
            Left Join ${p}knd ON pct_kndID = knd_ID  ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
            " GROUP BY knd_ID;");
    $pdo_query->execute();

    $arr = array();
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($row['knd_ID'])) break;
        $arr[$row['knd_ID']] = $row['expenses'];
    }
    
    return $arr;
}

/**
 * Get the sum of expenses for every project.
 * @param int $in Time from which to take the expenses into account.
 * @param int $out Time until which to take the expenses into account.
 * @param array $users Array of user IDs to filter the expenses by.
 * @param array $customers Array of customer IDs to filter the expenses by.
 * @param array $projects Array of project IDs to filter the expenses by.
 * @return array Array which assigns every project (via his ID) the sum of his expenses.
 */
function get_arr_exp_pct($in,$out,$users = null,$customers = null,$projects = null) {
    global $kga;
    global $pdo_conn;
    $p = $kga['server_prefix'];


    $whereClauses = exp_whereClausesFromFilters($users,$customers,$projects);
    $whereClauses[] = "${p}pct.pct_trash = 0";

    if ($in)
      $whereClauses[]="exp_timestamp >= $in";
    if ($out)
      $whereClauses[]="exp_timestamp <= $out"; 
    $pdo_query = $pdo_conn->prepare("SELECT sum(exp_value) as expenses,exp_pctID FROM ${p}exp
            Left Join ${p}pct ON exp_pctID = pct_ID
            Left Join ${p}knd ON pct_kndID = knd_ID  ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
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