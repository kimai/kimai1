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
		$odlDatabaseLayer = $database;
		
		$this->tablePrefix = $kga['server_prefix'];
		$this->kga = $kga;
		$this->dbLayer = $odlDatabaseLayer;
		$this->conn = $this->dbLayer->getConnection();
	}
	
	public function __call($fnName, $arguments) {
		return call_user_func_array(array($this->dbLayer, $fnName), $arguments);
	}
	
	
	/**
   * returns expenses for specific user as multidimensional array
   * @TODO: needs comments
   * @param integer $user ID of user in table usr
   * @return array
   * @author th
   * @author Alexander Bauer
   */
  public function get_arr_exp($start, $end, $users = null, $customers = null, $projects = null, $reverse_order=false, $filter_refundable = -1, $filterCleared = null, $startRows = 0, $limitRows = 0, $countOnly = false) {
  	$conn = $this->conn;
  	$kga = $this->kga;
  
  	if (!is_numeric($filterCleared)) {
  		$filterCleared = $kga['conf']['hideClearedEntries']-1; // 0 gets -1 for disabled, 1 gets 0 for only not cleared entries
  	}
  
  	$start  = MySQL::SQLValue($start    , MySQL::SQLVALUE_NUMBER);
  	$end = MySQL::SQLValue($end   , MySQL::SQLVALUE_NUMBER);
  
  	$p     = $kga['server_prefix'];
  
  	$whereClauses = $this->exp_whereClausesFromFilters($users, $customers, $projects);
  
  	if (isset($kga['customer']))
  		$whereClauses[] = "${p}pct.pct_internal = 0";

  	if (!empty($start)) {
  		$whereClauses[]="exp_timestamp >= $start";
	}
  	if (!empty($end)) {
  		$whereClauses[]="exp_timestamp <= $end";
	}
  	if ($filterCleared > -1) {
  		$whereClauses[] = "exp_cleared = $filterCleared";
	}

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
  	
  	if(!empty($limitRows)) {
  		$startRows = (int)$startRows;
		$limit = "LIMIT $startRows, $limitRows";
  	} else {
  		$limit="";
  	}
  	
  	$select = "SELECT exp_ID, exp_timestamp, exp_multiplier, exp_value, exp_pctID, exp_designation, exp_usrID, pct_ID,
  				knd_name, pct_kndID, pct_name, exp_comment, exp_refundable,
  				exp_comment_type, usr_name, exp_cleared";
				
  	$where = empty($whereClauses) ? '' : "WHERE ".implode(" AND ",$whereClauses);
  	$orderDirection = $reverse_order ? 'ASC' : 'DESC';
  	
  	if($countOnly) {
  		$select = "SELECT COUNT(*) AS total";
  		$limit = "";
  	}
  	 
  	$query = "$select
  		FROM ${p}exp
	  	Join ${p}pct ON exp_pctID = pct_ID
	  	Join ${p}knd ON pct_kndID = knd_ID
	  	Join ${p}usr ON exp_usrID = usr_ID 
	  	$where
	  	ORDER BY exp_timestamp $orderDirection $limit";
  	
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
   * @param Array list of IDs of events to include
   * @return Array list of where clauses to include in the query
   */
  public function exp_whereClausesFromFilters($users, $customers, $projects ) {
  
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
	
	
}
