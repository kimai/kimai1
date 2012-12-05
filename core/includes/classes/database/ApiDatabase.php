<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2012 Kimai-Development-Team
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
 * Provides the database layer for remote API calls.
 * This was implemented due to the bad maintainability of MySQL and PDO Classes.
 * This class serves as a bridge and currently ONLY for API calls.
 *
 * @author Kevin Papst
 * @author Alexander Bauer
 */
class ApiDatabase {
	private $kga = null;
	private $tablePrefix = null;
	private $dbLayer = null;
	private $conn = null;
	
	public function __construct($kga, $database) {
		$oldDatabase = $database;
		
		$this->tablePrefix = $kga['server_prefix'];
		$this->kga = $kga;
		$this->dbLayer = $oldDatabase;
		$this->conn = $this->dbLayer->getConnectionHandler();
	}
	
	public function __call($fnName, $arguments) {
		return call_user_func_array(array($this->dbLayer, $fnName), $arguments);
	}
	
	/***************************************************************************************************************
	 * Expenses
	 *********************************************/
	
	/**
	 * returns single expense entry as array
	 *
	 * @param integer $id ID of entry in table exp
	 * @global array $kga kimai-global-array
	 * @return array
	 * @author sl
	 */
	public function get_expense($id) {
	    $id    = MySQL::SQLValue($id   , MySQL::SQLVALUE_NUMBER);
		
	    $table = $this->getExpenseTable();
		$projectTable = $this->getProjectTable();
		$customerTable = $this->getCustomerTable();
	  	
	    $query = "SELECT * FROM $table 
	              LEFT JOIN $projectTable USING(projectID)
	              LEFT JOIN $customerTable USING(customerID)
	              WHERE $table.expenseID = $id LIMIT 1;";
	
	    $this->conn->Query($query);
	    return $this->conn->RowArray(0, MYSQL_ASSOC);
	}
	
	/**
	 * Returns the data of a certain expense record
	 *
	 * @param array $expenseID expenseID of the record
	 * @return array the record's data as array, false on failure
	 * @author ob
	 */
	public function expense_get($expId) {
		$kga = $this->kga;
	    $conn = $this->conn;
	    
	    $table = $this->getExpenseTable();
	    
	    $expId = MySQL::SQLValue($expId, MySQL::SQLVALUE_NUMBER);
	
	    if ($expId) {
	        $result = $conn->Query("SELECT * FROM $table WHERE expenseID = " . $expId);
	    } else {
	        $result = $conn->Query("SELECT * FROM $table WHERE userID = ".$kga['user']['userID']." ORDER BY expenseID DESC LIMIT 1");
	    }
	    
	    if (! $result) {
	      return false;
	    } else {
	        return $conn->RowArray(0,MYSQL_ASSOC);
	    }
	}
	
	/**
	 * returns expenses for specific user as multidimensional array
	 * @TODO: needs comments
	 * @param integer $user ID of user in table users
	 * @return array
	 * @author th
	 * @author Alexander Bauer
	 */
	public function get_expenses($start, $end, $users = null, $customers = null, $projects = null, $reverse_order=false, $filter_refundable = -1, $filterCleared = null, $startRows = 0, $limitRows = 0, $countOnly = false) {
	  	$conn = $this->conn;
	  	$kga = $this->kga;
	  
	  	if (!is_numeric($filterCleared)) {
	  		$filterCleared = $kga['conf']['hideClearedEntries']-1; // 0 gets -1 for disabled, 1 gets 0 for only not cleared entries
	  	}
  
	  	$start  = MySQL::SQLValue($start    , MySQL::SQLVALUE_NUMBER);
	  	$end = MySQL::SQLValue($end   , MySQL::SQLVALUE_NUMBER);
	  
	  	$p     = $kga['server_prefix'];
  
	  	$whereClauses = $this->expenses_widthhereClausesFromFilters($users, $customers, $projects);
	  
	  	if (isset($kga['customer']))
	  		$whereClauses[] = "${p}projects.internal = 0";
	
	  	if (!empty($start)) {
	  		$whereClauses[]="timestamp >= $start";
		}
	  	if (!empty($end)) {
	  		$whereClauses[]="timestamp <= $end";
		}
	  	if ($filterCleared > -1) {
	  		$whereClauses[] = "cleared = $filterCleared";
		}

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
  	
	  	if(!empty($limitRows)) {
	  		$startRows = (int)$startRows;
			$limit = "LIMIT $startRows, $limitRows";
	  	} else {
	  		$limit="";
	  	}
  	
  		$select = "SELECT expenseID, timestamp, multiplier, value, projectID, designation, userID, projectID,
  					customerName, customerID, projectName, comment, refundable,
  					commentType, userName, cleared";
				
  		$where = empty($whereClauses) ? '' : "WHERE ".implode(" AND ",$whereClauses);
  		$orderDirection = $reverse_order ? 'ASC' : 'DESC';
  	
	  	if($countOnly) {
	  		$select = "SELECT COUNT(*) AS total";
	  		$limit = "";
	  	}
  	 
  		$query = "$select
  			FROM ${p}expenses
	  		Join ${p}projects USING(projectID)
	  		Join ${p}customers USING(customerID)
	  		Join ${p}users USING(userID)
	  		$where
	  		ORDER BY timestamp $orderDirection $limit";
  	
  		$conn->Query($query);
  	
	  	// return only the number of rows, ignoring LIMIT
	  	if($countOnly) {
	  		$this->conn->MoveFirst();
	  		$row = $this->conn->Row();
	  		return $row->total;
	  	}
  	
  	
	  	$i=0;
	  	$arr = array();
	  	$conn->MoveFirst();
		// toArray();
	  	while (! $conn->EndOfSeek()) {
	  		$row = $conn->Row();
			$arr[$i] = (array)$row;
	  		$i++;
	  	}
	  
		return $arr;
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
   */
  public function expenses_widthhereClausesFromFilters($users, $customers, $projects ) {
  
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
	 * create exp entry 
	 *
	 * @param integer $userId
	 * @param Array $data
	 * @author sl
	 * @author Alexander Bauer
	 */
	function expense_create(Array $data) {
	    $conn = $this->conn;
	    $data = $this->dbLayer->clean_data($data);
	    
		
		if(isset($data ['timestamp'])) {
	    	$values ['timestamp']    =   MySQL::SQLValue($data['timestamp'], MySQL::SQLVALUE_NUMBER );
		}
		if(isset($data ['userID'])) {
	    	$values ['userID']        =   MySQL::SQLValue($data['userID'], MySQL::SQLVALUE_NUMBER );
		}
		if(isset($data ['projectID'])) {
	    	$values ['projectID']        =   MySQL::SQLValue($data['projectID'], MySQL::SQLVALUE_NUMBER );
		}
		if(isset($data ['designation'])) {
	    	$values ['designation']  =   MySQL::SQLValue($data['designation']);
		}
		if(isset($data ['comment'])) {
	    	$values ['comment']      =   MySQL::SQLValue($data['comment']);
		}
		if(isset($data ['commentType'])) {
	    	$values ['commentType'] =   MySQL::SQLValue($data['commentType'], MySQL::SQLVALUE_NUMBER );
		}
		if(isset($data ['refundable'])) {
	    	$values ['refundable']   =   MySQL::SQLValue($data['refundable'], MySQL::SQLVALUE_NUMBER );
		}
		if(isset($data ['cleared'])) {
	    	$values ['cleared']   =   MySQL::SQLValue($data['cleared'], MySQL::SQLVALUE_NUMBER );
		}
		if(isset($data ['multiplier'])) {
	    	$values ['multiplier']   =   MySQL::SQLValue($data['multiplier'], MySQL::SQLVALUE_NUMBER );
		}
		if(isset($data ['value'])) {
	    	$values ['value']        =   MySQL::SQLValue($data['value'], MySQL::SQLVALUE_NUMBER );
		}
		
	    $table = $this->getExpenseTable();
	    return $conn->InsertRow($table, $values);    
	} 

	/**
	 * edit exp entry 
	 *
	 * @param integer $id
	 * @param array $data
	 * @author th
	 * @author Alexander Bauer
	 */
	function expense_edit($id, Array $data) {
	    $conn = $this->conn;
	    $data = $this->dbLayer->clean_data($data);
	   
	    $original_array = $this->expense_get($id);
	    $new_array = array();
	    
	    foreach ($original_array as $key => $value) {
	        if (isset($data[$key]) == true) {
	            $new_array[$key] = $data[$key];
	        } else {
	            $new_array[$key] = $original_array[$key];
	        }
	    }
	
	    $values ['projectID']        = MySQL::SQLValue($new_array ['projectID']       , MySQL::SQLVALUE_NUMBER );
	    $values ['designation']  = MySQL::SQLValue($new_array ['designation']                          );
	    $values ['comment']      = MySQL::SQLValue($new_array ['comment']                              );
	    $values ['commentType'] = MySQL::SQLValue($new_array ['commentType'], MySQL::SQLVALUE_NUMBER );
	    $values ['timestamp']    = MySQL::SQLValue($new_array ['timestamp']   , MySQL::SQLVALUE_NUMBER );
	    $values ['multiplier']   = MySQL::SQLValue($new_array ['multiplier']  , MySQL::SQLVALUE_NUMBER );
	    $values ['value']        = MySQL::SQLValue($new_array ['value']       , MySQL::SQLVALUE_NUMBER );
	    $values ['refundable']   = MySQL::SQLValue($new_array ['refundable']  , MySQL::SQLVALUE_NUMBER );
		$values ['cleared']   	 = MySQL::SQLValue($new_array ['cleared']  , MySQL::SQLVALUE_NUMBER );
	                                   
	    $filter ['expenseID']           = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);
	    $table = $this->getExpenseTable();
	    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
	
	    return $conn->Query($query);
	}
  
  
  	/**
	 * delete exp entry 
	 *
	 * @param integer $userID 
	 * @param integer $id -> ID of record
	 * @global array  $kga kimai-global-array
	 * @author th
	 */
	function expense_delete($id) {
	    $filter["expenseID"] = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);
		
	    $table = $this->getExpenseTable();
	    $query = MySQL::BuildSQLDelete($table, $filter);
	    return $this->conn->Query($query);    
	} 
	
	
}
