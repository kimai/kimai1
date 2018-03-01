<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
 * (c) Kimai-Development-Team since 2006
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
class Kimai_Remote_Database
{
    /**
     * @var Kimai_Config|null
     */
    private $kga = null;
    /**
     * @var string
     */
    private $tablePrefix = null;
    /**
     * @var Kimai_Database_Mysql
     */
    private $dbLayer = null;
    /**
     * @var MySQL
     */
    private $conn = null;

    /**
     * Kimai_Remote_Database constructor.
     * @param Kimai_Config $kga
     * @param Kimai_Database_Mysql $database
     */
    public function __construct($kga, $database)
    {
        $this->kga = $kga;
        $this->dbLayer = $database;
        $this->tablePrefix = $this->dbLayer->getTablePrefix();
        $this->conn = $this->dbLayer->getConnectionHandler();
    }

    /**
     * @param string $fnName
     * @param array $arguments
     * @return mixed
     */
    public function __call($fnName, $arguments)
    {
        return call_user_func_array(array($this->dbLayer, $fnName), $arguments);
    }

    /**
     * returns single expense entry as array
     *
     * @param int $id ID of entry in table exp
     * @global array $kga kimai-global-array
     * @return array
     * @author sl
     */
    public function get_expense($id)
    {
        $id = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);

        $table = $this->getExpenseTable();
        $projectTable = $this->getProjectTable();
        $customerTable = $this->getCustomerTable();

        $query = "SELECT * FROM $table
	              LEFT JOIN $projectTable USING(projectID)
	              LEFT JOIN $customerTable USING(customerID)
	              WHERE $table.expenseID = $id LIMIT 1;";

        $this->conn->Query($query);
        return $this->conn->RowArray(0, MYSQLI_ASSOC);
    }

    /**
     * Returns the data of a certain expense record
     *
     * @param int $expId
     * @return array the record's data as array, false on failure
     * @author ob
     */
    public function expense_get($expId)
    {
        $kga = $this->kga;
        $conn = $this->conn;

        $table = $this->getExpenseTable();

        $expId = MySQL::SQLValue($expId, MySQL::SQLVALUE_NUMBER);

        if ($expId) {
            $result = $conn->Query("SELECT * FROM $table WHERE expenseID = " . $expId);
        } else {
            $result = $conn->Query("SELECT * FROM $table WHERE userID = " . $kga['user']['userID'] . " ORDER BY expenseID DESC LIMIT 1");
        }

        if (!$result) {
            return false;
        } else {
            return $conn->RowArray(0, MYSQLI_ASSOC);
        }
    }

    /**
     * returns expenses for specific user as multidimensional array
     *
     * @TODO: needs comments
     * @param int $start
     * @param int $end
     * @param int $users ID of user in table users
     * @param null $customers
     * @param null $projects
     * @param bool $reverse_order
     * @param int $filter_refundable
     * @param int $filterCleared
     * @param int $startRows
     * @param int $limitRows
     * @param bool $countOnly
     * @return array
     * @author th
     * @author Alexander Bauer
     */
    public function get_expenses($start, $end, $users = null, $customers = null, $projects = null, $reverse_order = false, $filter_refundable = -1, $filterCleared = null, $startRows = 0, $limitRows = 0, $countOnly = false)
    {
        $conn = $this->conn;
        $kga = $this->kga;

        // -1 for disabled, 0 for only not cleared entries
        if (!is_numeric($filterCleared)) {
            $filterCleared = -1;
            if ($kga->getSettings()->isHideClearedEntries()) {
                $filterCleared = 0;
            }
        }

        $start = MySQL::SQLValue($start, MySQL::SQLVALUE_NUMBER);
        $end = MySQL::SQLValue($end, MySQL::SQLVALUE_NUMBER);

        $p = $kga['server_prefix'];

        $whereClauses = $this->dbLayer->timeSheet_whereClausesFromFilters($users, $customers, $projects);

        if (isset($kga['customer'])) {
            $whereClauses[] = "${p}projects.internal = 0";
        }

        if (!empty($start)) {
            $whereClauses[] = "timestamp >= $start";
        }
        if (!empty($end)) {
            $whereClauses[] = "timestamp <= $end";
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

        if (!empty($limitRows)) {
            $startRows = (int)$startRows;
            $limit = "LIMIT $startRows, $limitRows";
        } else {
            $limit = "";
        }

        $select = "SELECT e.expenseID, e.timestamp, e.multiplier, e.value, e.projectID, e.designation, e.userID,
  					c.name AS customerName, c.customerID, p.name AS projectName, e.comment, e.refundable,
  					e.commentType, u.name AS userName, e.cleared";

        $where = empty($whereClauses) ? '' : "WHERE " . implode(" AND ", $whereClauses);
        $orderDirection = $reverse_order ? 'ASC' : 'DESC';

        if ($countOnly) {
            $select = "SELECT COUNT(*) AS total";
            $limit = "";
        }

        $query = "$select
  			FROM ${p}expenses e
	  		Join ${p}projects p USING(e.projectID)
	  		Join ${p}customers p USING(p.customerID)
	  		Join ${p}users u USING(e.userID)
	  		$where
	  		ORDER BY timestamp $orderDirection $limit";

        $conn->Query($query);

        // return only the number of rows, ignoring LIMIT
        if ($countOnly) {
            $this->conn->MoveFirst();
            $row = $this->conn->Row();
            return $row->total;
        }


        $i = 0;
        $arr = array();
        $conn->MoveFirst();
        // toArray();
        while (!$conn->EndOfSeek()) {
            $row = $conn->Row();
            $arr[$i] = (array)$row;
            $i++;
        }

        return $arr;
    }

    /**
     * create exp entry
     *
     * @param array $data
     * @return int
     */
    public function expense_create(array $data)
    {
        $conn = $this->conn;
        $data = $this->dbLayer->clean_data($data);

        $values = array();
        if (isset($data['timestamp'])) {
            $values['timestamp'] = MySQL::SQLValue($data['timestamp'], MySQL::SQLVALUE_NUMBER);
        }
        if (isset($data['userID'])) {
            $values['userID'] = MySQL::SQLValue($data['userID'], MySQL::SQLVALUE_NUMBER);
        }
        if (isset($data['projectID'])) {
            $values['projectID'] = MySQL::SQLValue($data['projectID'], MySQL::SQLVALUE_NUMBER);
        }
        if (isset($data['designation'])) {
            $values['designation'] = MySQL::SQLValue($data['designation']);
        }
        if (isset($data['comment'])) {
            $values['comment'] = MySQL::SQLValue($data['comment']);
        }
        if (isset($data['commentType'])) {
            $values['commentType'] = MySQL::SQLValue($data['commentType'], MySQL::SQLVALUE_NUMBER);
        }
        if (isset($data['refundable'])) {
            $values['refundable'] = MySQL::SQLValue($data['refundable'], MySQL::SQLVALUE_NUMBER);
        }
        if (isset($data['cleared'])) {
            $values['cleared'] = MySQL::SQLValue($data['cleared'], MySQL::SQLVALUE_NUMBER);
        }
        if (isset($data['multiplier'])) {
            $values['multiplier'] = MySQL::SQLValue($data['multiplier'], MySQL::SQLVALUE_NUMBER);
        }
        if (isset($data['value'])) {
            $values['value'] = MySQL::SQLValue($data['value'], MySQL::SQLVALUE_NUMBER);
        }

        return $conn->InsertRow($this->getExpenseTable(), $values);
    }

    /**
     * edit exp entry
     *
     * @param int $id
     * @param array $data
     * @return object
     */
    public function expense_edit($id, array $data)
    {
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

        $values['projectID'] = MySQL::SQLValue($new_array ['projectID'], MySQL::SQLVALUE_NUMBER);
        $values['designation'] = MySQL::SQLValue($new_array ['designation']);
        $values['comment'] = MySQL::SQLValue($new_array ['comment']);
        $values['commentType'] = MySQL::SQLValue($new_array ['commentType'], MySQL::SQLVALUE_NUMBER);
        $values['timestamp'] = MySQL::SQLValue($new_array ['timestamp'], MySQL::SQLVALUE_NUMBER);
        $values['multiplier'] = MySQL::SQLValue($new_array ['multiplier'], MySQL::SQLVALUE_NUMBER);
        $values['value'] = MySQL::SQLValue($new_array ['value'], MySQL::SQLVALUE_NUMBER);
        $values['refundable'] = MySQL::SQLValue($new_array ['refundable'], MySQL::SQLVALUE_NUMBER);
        $values['cleared'] = MySQL::SQLValue($new_array ['cleared'], MySQL::SQLVALUE_NUMBER);

        $filter ['expenseID'] = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);
        $query = MySQL::BuildSQLUpdate($this->getExpenseTable(), $values, $filter);

        return $conn->Query($query);
    }

    /**
     * delete exp entry
     *
     * @param int $id -> ID of record
     * @return object
     */
    public function expense_delete($id)
    {
        $filter['expenseID'] = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);

        $query = MySQL::BuildSQLDelete($this->getExpenseTable(), $filter);
        return $this->conn->Query($query);
    }
}
