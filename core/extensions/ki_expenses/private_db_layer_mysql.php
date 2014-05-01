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
 * delete expense entry 
 *
 * @param integer $userID 
 * @param integer $id -> ID of record
 * @global array  $kga kimai-global-array
 * @author th
 */
function expense_delete($id) {
    global $kga, $database;
    $conn = $database->getConnectionHandler();
    $filter["expenseID"] = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."expenses";
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
function expense_create($userID,$data) {
    global $kga, $database;
    $conn = $database->getConnectionHandler();
 
    $data = $database->clean_data($data);

    $values ['projectID']    =   MySQL::SQLValue( $data ['projectID']   , MySQL::SQLVALUE_NUMBER );
    $values ['designation']  =   MySQL::SQLValue( $data ['designation'] );
    $values ['comment']      =   MySQL::SQLValue( $data ['comment'] );
    $values ['commentType']  =   MySQL::SQLValue( $data ['commentType'] , MySQL::SQLVALUE_NUMBER );
    $values ['timestamp']    =   MySQL::SQLValue( $data ['timestamp']   , MySQL::SQLVALUE_NUMBER );
    $values ['multiplier']   =   MySQL::SQLValue( $data ['multiplier']  , MySQL::SQLVALUE_NUMBER );
    $values ['value']        =   MySQL::SQLValue( $data ['value']       , MySQL::SQLVALUE_NUMBER );
    $values ['userID']       =   MySQL::SQLValue( $userID               , MySQL::SQLVALUE_NUMBER );
    $values ['refundable']   =   MySQL::SQLValue( $data ['refundable']  , MySQL::SQLVALUE_NUMBER );

    $table = $kga['server_prefix']."expenses";
    $result = $conn->InsertRow($table, $values);

    if (!$result) {
        Logger::logfile('expense_create: '.$conn->Error());
        return false;
    }

    return $result;
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
 * @param Array list of IDs of activities to include
 * @return Array list of where clauses to include in the query
 *
 */

function expenses_widthhereClausesFromFilters($users, $customers , $projects ) {
    
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
      $whereClauses[] = "userID in (".implode(',',$users).")";
    }
    
    if (count($customers) > 0) {
      $whereClauses[] = "customerID in (".implode(',',$customers).")";
    }
    
    if (count($projects) > 0) {
      $whereClauses[] = "projectID in (".implode(',',$projects).")";
    }  

    return $whereClauses;

}

/**
 * returns expenses for specific user as multidimensional array
 *
 * @param integer $user ID of user in table users
 * @global array $kga kimai-global-array
 * @return array
 * @author th 
 */

// TODO: Test it!
function get_expenses($start, $end, $users = null, $customers = null, $projects = null,$limit=false, $reverse_order=false, $filter_refundable = -1, $filterCleared = null) {
    global $kga, $database;
    $conn = $database->getConnectionHandler();
    $p     = $kga['server_prefix'];

    if (!is_numeric($filterCleared)) {
      $filterCleared = $kga['conf']['hideClearedEntries']-1; // 0 gets -1 for disabled, 1 gets 0 for only not cleared entries
    }
    
    $start  = MySQL::SQLValue($start    , MySQL::SQLVALUE_NUMBER);
    $end = MySQL::SQLValue($end   , MySQL::SQLVALUE_NUMBER);
    $limit = MySQL::SQLValue($limit , MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];

    $whereClauses = expenses_widthhereClausesFromFilters($users,$customers,$projects);

    if (isset($kga['customer']))
      $whereClauses[] = "${p}projects.internal = 0";

    if ($start)
      $whereClauses[]="timestamp >= $start";
    if ($end)
      $whereClauses[]="timestamp <= $end";
    if ($filterCleared > -1)
      $whereClauses[] = "cleared = $filterCleared";

    switch ($filter_refundable) {
    	case 0:
    		$whereClauses[] = "refundable > 0";
    		break;
    	case 1:
    		$whereClauses[] = "refundable <= 0";
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
    $query = "SELECT expenses.*,
              customer.name AS customerName, customer.customerID AS customerID,
              project.name AS projectName, project.comment AS projectComment,
              user.name AS userName, user.alias AS userAlias
             FROM ${p}expenses AS expenses
             Join ${p}projects AS project USING(projectID)
             Join ${p}customers AS customer USING(customerID)
             Join ${p}users AS user USING(userID) "
              .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
             ' ORDER BY timestamp '.($reverse_order?'ASC ':'DESC ') . $limit . ";";
    
    $conn->Query($query);

    $i=0;
    $arr=array();
    /* TODO: needs revision as foreach loop */
    $conn->MoveFirst();
    while (! $conn->EndOfSeek()) {
      $row = $conn->Row();
      $arr[$i]['expenseID']      = $row->expenseID;
      $arr[$i]['timestamp']      = $row->timestamp;
      $arr[$i]['multiplier']     = $row->multiplier;
      $arr[$i]['value']          = $row->value;
      $arr[$i]['designation']    = $row->designation;
      $arr[$i]['comment']        = $row->comment;
      $arr[$i]['commentType']    = $row->commentType;
      $arr[$i]['refundable']     = $row->refundable;
      $arr[$i]['cleared']        = $row->cleared;

      $arr[$i]['customerID']     = $row->customerID;
      $arr[$i]['customerName']   = $row->customerName;

      $arr[$i]['projectID']      = $row->projectID;
      $arr[$i]['projectName']    = $row->projectName;
      $arr[$i]['projectComment'] = $row->projectComment;

      $arr[$i]['userID']         = $row->userID;
      $arr[$i]['userName']       = $row->userName;
      $arr[$i]['userAlias']      = $row->userAlias;
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
function get_expense($id) {
    global $kga, $database;
    $conn = $database->getConnectionHandler();

    $id    = MySQL::SQLValue($id   , MySQL::SQLVALUE_NUMBER);
    $p     = $kga['server_prefix'];
  
    $query = "SELECT * FROM ${p}expenses WHERE expenseID = $id LIMIT 1;";

    $conn->Query($query);
    return $conn->RowArray(0,MYSQL_ASSOC);
}



/**
 * Returns the data of a certain expense record
 *
 * @param array $expenseID        expenseID of the record
 * @global array $kga          kimai-global-array
 * @return array               the record's data as array, false on failure
 * @author ob
 */
function expense_get($expenseID) {
    global $kga, $database;
    $conn = $database->getConnectionHandler();
    
    $p = $kga['server_prefix'];
    
    $expenseID = MySQL::SQLValue($expenseID, MySQL::SQLVALUE_NUMBER);

    if ($expenseID) {
        $result = $conn->Query("SELECT * FROM ${p}expenses WHERE expenseID = " . $expenseID);
    } else {
        $result = $conn->Query("SELECT * FROM ${p}expenses WHERE userID = ".$kga['user']['userID']." ORDER BY expenseID DESC LIMIT 1");
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
 
function expense_edit($id,$data) {
    global $kga, $database;
    $conn = $database->getConnectionHandler();
    
    $data = $database->clean_data($data);
   
    $original_array = expense_get($id);
    $new_array = array();
    
    foreach ($original_array as $key => $value) {
        if (isset($data[$key]) == true) {
            $new_array[$key] = $data[$key];
        } else {
            $new_array[$key] = $original_array[$key];
        }
    }

    $values ['projectID']    = MySQL::SQLValue($new_array ['projectID']   , MySQL::SQLVALUE_NUMBER );
    $values ['designation']  = MySQL::SQLValue($new_array ['designation']                          );
    $values ['comment']      = MySQL::SQLValue($new_array ['comment']                              );
    $values ['commentType']  = MySQL::SQLValue($new_array ['commentType'], MySQL::SQLVALUE_NUMBER );
    $values ['timestamp']    = MySQL::SQLValue($new_array ['timestamp']   , MySQL::SQLVALUE_NUMBER );
    $values ['multiplier']   = MySQL::SQLValue($new_array ['multiplier']  , MySQL::SQLVALUE_NUMBER );
    $values ['value']        = MySQL::SQLValue($new_array ['value']       , MySQL::SQLVALUE_NUMBER );
    $values ['refundable']   = MySQL::SQLValue($new_array ['refundable']  , MySQL::SQLVALUE_NUMBER );
                                   
    $filter ['expenseID']           = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."expenses";
    $query = MySQL::BuildSQLUpdate($table, $values, $filter);

    $success = true;
    
    if (! $conn->Query($query)) $success = false;
    
    return $success;
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
function expenses_by_user($start,$end,$users = null,$customers = null,$projects = null) {
    global $kga, $database;
    $conn = $database->getConnectionHandler();
    
    $start = MySQL::SQLValue($start, MySQL::SQLVALUE_NUMBER);
    $end   = MySQL::SQLValue($end  , MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];
    $whereClauses = expenses_widthhereClausesFromFilters($users,$customers,$projects);
    $whereClauses[] = "${p}users.trash = 0";

    if ($start)
      $whereClauses[]="timestamp >= $start";
    if ($end)
      $whereClauses[]="timestamp <= $end"; 

   $query = "SELECT SUM(value*multiplier) as expenses, userID
             FROM ${p}expenses
             Join ${p}projects USING(projectID)
             Join ${p}customers USING(customerID)
             Join ${p}users USING(userID) ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
             " GROUP BY userID;";

    $result = $conn->Query($query);
    if (! $result) return array();
    $rows = $conn->RecordsArray(MYSQL_ASSOC);
    if (!$rows) return array();
   

    $arr = array(); 
    foreach($rows as $row) {
        $arr[$row['userID']] = $row['expenses'];
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
function expenses_by_customer($start,$end,$users = null,$customers = null,$projects = null) {
    global $kga, $database;
    $conn = $database->getConnectionHandler();
    
    $start = MySQL::SQLValue($start, MySQL::SQLVALUE_NUMBER);
    $end   = MySQL::SQLValue($end  , MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];

    $whereClauses = expenses_widthhereClausesFromFilters($users,$customers,$projects);
    $whereClauses[] = "${p}customers.trash = 0";

    if ($start)
      $whereClauses[]="timestamp >= $start";
    if ($end)
      $whereClauses[]="timestamp <= $end"; 
    
    $query = "SELECT SUM(value*multiplier) as expenses, customerID FROM ${p}expenses
            Left Join ${p}projects USING(projectID)
            Left Join ${p}customers USING(customerID) ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
            " GROUP BY customerID;";

    $result = $conn->Query($query);
    if (! $result) return array();
    $rows = $conn->RecordsArray(MYSQL_ASSOC);
    if (!$rows) return array();

    $arr = array();
    foreach ($rows as $row) {
        $arr[$row['customerID']] = $row['expenses'];
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
function expenses_by_project($start,$end,$users = null,$customers = null,$projects = null) {
    global $kga, $database;
    $conn = $database->getConnectionHandler();
    
    $start = MySQL::SQLValue($start, MySQL::SQLVALUE_NUMBER);
    $end   = MySQL::SQLValue($end  , MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];
    $whereClauses = expenses_widthhereClausesFromFilters($users,$customers,$projects);
    $whereClauses[] = "${p}projects.trash = 0";

    if ($start)
      $whereClauses[]="timestamp >= $start";
    if ($end)
      $whereClauses[]="timestamp <= $end";
 
    $query = "SELECT sum(value*multiplier) as expenses, projectID FROM ${p}expenses
            Left Join ${p}projects USING(projectID)
            Left Join ${p}customers USING(customerID) ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
       " GROUP BY projectID;";

    $result = $conn->Query($query);
    if (! $result) return array();
    $rows = $conn->RecordsArray(MYSQL_ASSOC);
    if (!$rows) return array();

    $arr = array();
    foreach ($rows as $row) {
        $arr[$row['projectID']] = $row['expenses'];
    }
    return $arr;
}


?>
