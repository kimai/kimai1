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
    $values ['exp_multiplier']   =   MySQL::SQLValue( $data ['exp_multiplier']   , MySQL::SQLVALUE_NUMBER );
    $values ['exp_value']        =   MySQL::SQLValue( $data ['exp_value']        , MySQL::SQLVALUE_NUMBER );
    $values ['exp_usrID']        =   MySQL::SQLValue( $usr_ID                    , MySQL::SQLVALUE_NUMBER );
    $values ['exp_refundable']   =   MySQL::SQLValue( $data ['exp_refundable']   , MySQL::SQLVALUE_NUMBER );
    
    $table = $kga['server_prefix']."exp";
    return $conn->InsertRow($table, $values);    
} 



/**
 *  Creates an array of clauses which can be joined together in the WHERE part
 *  of a sql query. The clauses describe whether a line should be included
 *  depending on the filters set.
 *  
 *  This method also makes the values SQL-secure.
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

    for ($i = 0;$i<count($users);$i++)
      $users[$i] = MySQL::SQLValue($users[$i], MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($customers);$i++)
      $customers[$i] = MySQL::SQLValue($customers[$i], MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($projects);$i++)
      $projects[$i] = MySQL::SQLValue($projects[$i], MySQL::SQLVALUE_NUMBER);

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
function get_arr_exp($start, $end, $users = null, $customers = null, $projects = null,$limit=false, $reverse_order=false, $filter_refundable = -1, $filterCleared = null) {
    global $kga,$conn;
    $p     = $kga['server_prefix'];

    if (!is_numeric($filterCleared)) {
      $filterCleared = $kga['conf']['hideClearedEntries']-1; // 0 gets -1 for disabled, 1 gets 0 for only not cleared entries
    }
    
    $start  = MySQL::SQLValue($start    , MySQL::SQLVALUE_NUMBER);
    $end = MySQL::SQLValue($end   , MySQL::SQLVALUE_NUMBER);
    $limit = MySQL::SQLValue($limit , MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];

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
    $query = "SELECT exp_ID, exp_timestamp, exp_multiplier, exp_value, exp_pctID, exp_designation, exp_usrID, pct_ID,
              knd_name, pct_kndID, pct_name, exp_comment, exp_refundable,
              exp_comment_type, usr_name, exp_cleared
             FROM ${p}exp 
             Join ${p}pct ON exp_pctID = pct_ID
             Join ${p}knd ON pct_kndID = knd_ID
             Join ${p}usr ON exp_usrID = usr_ID "
              .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
             ' ORDER BY exp_timestamp '.($reverse_order?'ASC ':'DESC ') . $limit . ";";
    
    $conn->Query($query);
    
    $i=0;
    $arr=array();
    /* TODO: needs revision as foreach loop */
    $conn->MoveFirst();
    while (! $conn->EndOfSeek()) {
      $row = $conn->Row();
      $arr[$i]['exp_ID']             = $row->exp_ID;
      $arr[$i]['exp_timestamp']      = $row->exp_timestamp;
      $arr[$i]['exp_multiplier']     = $row->exp_multiplier;
      $arr[$i]['exp_value']          = $row->exp_value;
      $arr[$i]['exp_pctID']          = $row->exp_pctID;
      $arr[$i]['exp_designation']    = $row->exp_designation;
      $arr[$i]['exp_usrID']          = $row->exp_usrID;
      $arr[$i]['pct_ID']             = $row->pct_ID;
      $arr[$i]['knd_name']           = $row->knd_name;
      $arr[$i]['pct_kndID']          = $row->pct_kndID;
      $arr[$i]['pct_name']           = $row->pct_name;
      $arr[$i]['exp_comment']        = $row->exp_comment;
      $arr[$i]['exp_comment_type']   = $row->exp_comment_type;
      $arr[$i]['exp_refundable']     = $row->exp_refundable;
      $arr[$i]['usr_name']           = $row->usr_name;
      $arr[$i]['exp_cleared']        = $row->exp_cleared;
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
    $values ['exp_multiplier']   = MySQL::SQLValue($new_array ['exp_multiplier']  , MySQL::SQLVALUE_NUMBER );
    $values ['exp_value']        = MySQL::SQLValue($new_array ['exp_value']       , MySQL::SQLVALUE_NUMBER );
    $values ['exp_refundable']   = MySQL::SQLValue($new_array ['exp_refundable']  , MySQL::SQLVALUE_NUMBER );
                                   
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

/**
 * Get the sum of expenses for every user.
 * @param int $start Time from which to take the expenses into account.
 * @param int $end Time until which to take the expenses into account.
 * @param array $users Array of user IDs to filter the expenses by.
 * @param array $customers Array of customer IDs to filter the expenses by.
 * @param array $projects Array of project IDs to filter the expenses by.
 * @return array Array which assigns every user (via his ID) the sum of his expenses.
 */
function get_arr_exp_usr($start,$end,$users = null,$customers = null,$projects = null) {
    global $kga,$conn;
    
    $start = MySQL::SQLValue($start, MySQL::SQLVALUE_NUMBER);
    $end   = MySQL::SQLValue($end  , MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];
    $whereClauses = exp_whereClausesFromFilters($users,$customers,$projects);
    $whereClauses[] = "${p}usr.usr_trash = 0";

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


/**
 * Get the sum of expenses for every customer.
 * @param int $start Time from which to take the expenses into account.
 * @param int $end Time until which to take the expenses into account.
 * @param array $users Array of user IDs to filter the expenses by.
 * @param array $customers Array of customer IDs to filter the expenses by.
 * @param array $projects Array of project IDs to filter the expenses by.
 * @return array Array which assigns every customer (via his ID) the sum of his expenses.
 */
function get_arr_exp_knd($start,$end,$users = null,$customers = null,$projects = null) {
    global $kga,$conn;
    
    $start = MySQL::SQLValue($start, MySQL::SQLVALUE_NUMBER);
    $end   = MySQL::SQLValue($end  , MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];

    $whereClauses = exp_whereClausesFromFilters($users,$customers,$projects);
    $whereClauses[] = "${p}knd.knd_trash = 0";

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

/**
 * Get the sum of expenses for every project.
 * @param int $start Time from which to take the expenses into account.
 * @param int $end Time until which to take the expenses into account.
 * @param array $users Array of user IDs to filter the expenses by.
 * @param array $customers Array of customer IDs to filter the expenses by.
 * @param array $projects Array of project IDs to filter the expenses by.
 * @return array Array which assigns every project (via his ID) the sum of his expenses.
 */
function get_arr_exp_pct($start,$end,$users = null,$customers = null,$projects = null) {
    global $kga,$conn;
    
    $start = MySQL::SQLValue($start, MySQL::SQLVALUE_NUMBER);
    $end   = MySQL::SQLValue($end  , MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];
    $whereClauses = exp_whereClausesFromFilters($users,$customers,$projects);
    $whereClauses[] = "${p}pct.pct_trash = 0";

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