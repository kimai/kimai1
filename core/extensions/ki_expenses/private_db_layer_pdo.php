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
    global $kga, $database;
    $pdo_conn = $database->getConnectionHandler();
    $p = $kga['server_prefix'];

    $pdo_query = $pdo_conn->prepare("DELETE FROM ${p}expenses WHERE `expenseID` = ? LIMIT 1;");
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
    global $kga, $database;
    $pdo_conn = $database->getConnectionHandler();
    $p = $kga['server_prefix'];

    $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}expense (  
    `projectID`, 
    `designation`,
    `comment`,
    `commentType`,
    `timestamp`,
    `multiplier`,
    `value`,
    `userID`,
    `refundable`
    ) VALUES (?,?,?,?,?,?,?,?,?)
    ;");
    
    $result = $pdo_query->execute(array(
    $data['projectID'],
    $data['designation'],
    $data['comment'],
    $data['commentType'] ,
    $data['timestamp'],
    $data['multiplier'],
    $data['value'],
    $usr_ID,
    $data['refundable']
    ));
    

    if ($result == false) {
        return $result;
    }    
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
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return array
 * @author th 
 */

function get_arr_exp($start,$end,$users = null,$customers = null,$projects = null,$limit=false, $reverse_order = false, $filter_refundable = -1,$filterCleared = null) {
    global $kga, $database;
    $pdo_conn = $database->getConnectionHandler();
    $p = $kga['server_prefix'];

    if (!is_numeric($filterCleared)) {
      $filterCleared = $kga['conf']['hideClearedEntries']-1; // 0 gets -1 for disabled, 1 gets 0 for only not cleared entries
    }


    
    $whereClauses = exp_whereClausesFromFilters($users,$customers,$projects);

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
    $pdo_query = $pdo_conn->prepare("SELECT expenseID, timestamp, multiplier, value, projectID, designation, userID,
              projectID, customer.name AS customerName, customerID, project.name AS projectName, comment, commentType, refundable, user.name AS userName, cleared
             FROM ${p}expenses
             Join ${p}projects AS project USING(projectID)
             Join ${p}customers AS customer USING(customerID)
             Join ${p}users AS user USING(userID) "
              .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
             ' ORDER BY timestamp '.($reverse_order?'ASC ':'DESC ') . $limit . ";");
    
             $pdo_query->execute();  
  
    
    $i=0;
    $arr=array();
    /* TODO: needs revision as foreach loop */
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $arr[$i]['expenseID']      = $row['expenseID'];
        $arr[$i]['timestamp']      = $row['timestamp'];
        $arr[$i]['multiplier']     = $row['multiplier'];
        $arr[$i]['value']          = $row['value'];
        $arr[$i]['projectID']      = $row['projectID'];
        $arr[$i]['designation']    = $row['designation'];
        $arr[$i]['userID']         = $row['userID'];
        $arr[$i]['projectID']      = $row['projectID'];
        $arr[$i]['customerName']   = $row['customerName'];
        $arr[$i]['customerID']     = $row['customerID'];
        $arr[$i]['projectName']    = $row['projectName'];
        $arr[$i]['comment']        = $row['comment'];
        $arr[$i]['commentType']    = $row['commentType'];
        $arr[$i]['refundable']     = $row['refundable'];
        $arr[$i]['userName']       = $row['userName'];
        $arr[$i]['cleared']        = $row['cleared'];
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
    global $kga, $database;
    $pdo_conn = $database->getConnectionHandler();
    $p = $kga['server_prefix'];
  
    $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}expenses
    Left Join ${p}projects USING(projectID)
    Left Join ${p}customers USING(customerID)
    WHERE expenseID = ? LIMIT 1;");
  
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
    global $kga, $database;
    $pdo_conn = $database->getConnectionHandler();
    $p = $kga['server_prefix'];

    if ($exp_id) {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}expenses WHERE expenseID = ?");
    } else {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}expenses WHERE userID = ".$kga['usr']['userID']." ORDER BY expenseID DESC LIMIT 1");
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
    global $kga, $database;
    $pdo_conn = $database->getConnectionHandler();
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



    $pdo_query = $pdo_conn->prepare("UPDATE ${p}expense SET
    projectID = ?,
    designation = ?,
    comment = ?,
    commentType = ?,
    timestamp = ?,
    multiplier = ?,
    value = ?,
    refundable = ?
    WHERE expenseID = ?;");    
    
    $result = $pdo_query->execute(array(
    $new_array['projectID'],
    $new_array['designation'] ,
    $new_array['comment'],
    $new_array['commentType'] ,
    $new_array['timestamp'],
    $new_array['multiplier'],
    $new_array['value'],
    $new_array['refundable'],
    $id
    ));
    

    if ($result == false) {
        return $result;
    }
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
    global $kga, $database;
    $pdo_conn = $database->getConnectionHandler();
    $p = $kga['server_prefix'];

    $whereClauses = exp_whereClausesFromFilters($users,$customers,$projects);
    $whereClauses[] = "${p}users.trash = 0";

    if ($in)
      $whereClauses[]="timestamp >= $in";
    if ($out)
      $whereClauses[]="timestamp <= $out"; 

   $pdo_query = $pdo_conn->prepare("SELECT sum(value) as expenses, userID
             FROM ${p}expenses
             Join ${p}projects USING(projectID)
             Join ${p}customers USING(customerID)
             Join ${p}users USING(userID) ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
             " GROUP BY userID;");
             $pdo_query->execute();
   

    $arr = array(); 
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($row['userID'])) break;
        $arr[$row['userID']] = $row['expenses'];
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
    global $kga, $database;
    $pdo_conn = $database->getConnectionHandler();
    $p = $kga['server_prefix'];

    $whereClauses = exp_whereClausesFromFilters($users,$customers,$projects);
    $whereClauses[] = "${p}customers.trash = 0";

    if ($in)
      $whereClauses[]="timestamp >= $in";
    if ($out)
      $whereClauses[]="timestamp <= $out"; 
    
    $pdo_query = $pdo_conn->prepare("SELECT SUM(value) as expenses, customerID FROM ${p}expenses
            Left Join ${p}projects USING(projectID)
            Left Join ${p}customers USING(customerID)  ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
            " GROUP BY customerID;");
    $pdo_query->execute();

    $arr = array();
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($row['customerID'])) break;
        $arr[$row['customerID']] = $row['expenses'];
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
    global $kga, $database;
    $pdo_conn = $database->getConnectionHandler();
    $p = $kga['server_prefix'];


    $whereClauses = exp_whereClausesFromFilters($users,$customers,$projects);
    $whereClauses[] = "${p}projects.trash = 0";

    if ($in)
      $whereClauses[]="timestamp >= $in";
    if ($out)
      $whereClauses[]="timestamp <= $out"; 
    $pdo_query = $pdo_conn->prepare("SELECT sum(value) as expenses, projectID FROM ${p}expenses
            Left Join ${p}projects USING(projectID)
            Left Join ${p}customers USING(customerID) ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
       " GROUP BY projectID;");
    $pdo_query->execute();

    $arr = array();
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($row['projectID'])) break;
        $arr[$row['projectID']] = $row['expenses'];
    }
    return $arr;
}


?>