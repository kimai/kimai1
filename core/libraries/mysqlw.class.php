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
class MySQL
{
        private $database   = null;
        private static $mysql      = false;
        private static $pg         = true;
        
        const SQLVALUE_BIT      = "bit";
	const SQLVALUE_BOOLEAN  = "boolean";
	const SQLVALUE_DATE     = "date";
	const SQLVALUE_DATETIME = "datetime";
	const SQLVALUE_NUMBER   = "number";
	const SQLVALUE_T_F      = "t-f";
	const SQLVALUE_TEXT     = "text";
	const SQLVALUE_TIME     = "time";
	const SQLVALUE_Y_N      = "y-n";
        
	/**
	 * Constructor: Opens the connection to the database
	 *
	 * @param boolean $connect (Optional) Auto-connect when object is created
	 * @param string $database (Optional) Database name
	 * @param string $server   (Optional) Host address
	 * @param string $username (Optional) User name
	 * @param string $password (Optional) Password
	 * @param string $charset  (Optional) Character set
	 */
	public function __construct($connect = true, $database = null, $server = null,
								$username = null, $password = null, $charset = null,
                                                                $schema = null) {
                MySQL::$mysql = false;
                MySQL::$pg = true;
		if(MySQL::$mysql) $this->database = new MySQLNew($connect , $database, $server, $username, $password , $charset);
                else if(MySQL::$pg) {
                    $this->database = new PostgreSQL($connect , $database, $server, $username, $password , $charset, $schema);                    
                }
	}

	/**
	 * Destructor: Closes the connection to the database
	 *
	 */
	public function __destruct() {
		$this->database->__destruct();
	}

	/**
	 * Automatically does an INSERT or UPDATE depending if an existing record
	 * exists in a table
	 *
	 * @param string $tableName The name of the table
	 * @param array $valuesArray An associative array containing the column
	 *                            names as keys and values as data. The values
	 *                            must be SQL ready (i.e. quotes around
	 *                            strings, formatted dates, ect)
	 * @param array $whereArray An associative array containing the column
	 *                           names as keys and values as data. The values
	 *                           must be SQL ready (i.e. quotes around strings,
	 *                           formatted dates, ect).
	 * @return boolean Returns TRUE on success or FALSE on error
	 */
	public function AutoInsertUpdate($tableName, $valuesArray, $whereArray) {
		return $this->database->AutoInsertUpdate($tableName, $valuesArray, $whereArray);
	}

	/**
	 * Returns true if the internal pointer is at the beginning of the records
	 *
	 * @return boolean TRUE if at the first row or FALSE if not
	 */
	public function BeginningOfSeek() {
		return $this->database->BeginningOfSeek();
	}

	/**
	 * [STATIC] Builds a SQL DELETE statement
	 *
	 * @param string $tableName The name of the table
	 * @param array $whereArray (Optional) An associative array containing the
	 *                           column names as keys and values as data. The
	 *                           values must be SQL ready (i.e. quotes around
	 *                           strings, formatted dates, ect). If not specified
	 *                           then all values in the table are deleted.
	 * @return string Returns the SQL DELETE statement
	 */
	static public function BuildSQLDelete($tableName, $whereArray = null) {
		if(MySQL::$mysql) return MySQLNew::BuildSQLDelete ($tableName, $whereArray);
                else if (MySQL::$pg) return PostgreSQL::BuildSQLDelete ($tableName, $whereArray);
	}

	/**
	 * [STATIC] Builds a SQL INSERT statement
	 *
	 * @param string $tableName The name of the table
	 * @param array $valuesArray An associative array containing the column
	 *                            names as keys and values as data. The values
	 *                            must be SQL ready (i.e. quotes around
	 *                            strings, formatted dates, ect)
	 * @return string Returns a SQL INSERT statement
	 */
	static public function BuildSQLInsert($tableName, $valuesArray) {
		if(MySQL::$mysql) return MySQLNew::BuildSQLInsert ($tableName, $valuesArray);
                else if (MySQL::$pg) return PostgreSQL::BuildSQLInsert ($tableName, $valuesArray);
	}

	/**
	 * Builds a simple SQL SELECT statement
	 *
	 * @param string $tableName The name of the table
	 * @param array $whereArray (Optional) An associative array containing the
	 *                          column names as keys and values as data. The
	 *                          values must be SQL ready (i.e. quotes around
	 *                          strings, formatted dates, ect)
	 * @param array/string $columns (Optional) The column or list of columns to select
	 * @param array/string $sortColumns (Optional) Column or list of columns to sort by
	 * @param boolean $sortAscending (Optional) TRUE for ascending; FALSE for descending
	 *                               This only works if $sortColumns are specified
	 * @param integer/string $limit (Optional) The limit of rows to return
	 * @return string Returns a SQL SELECT statement
	 */
	static public function BuildSQLSelect($tableName, $whereArray = null, $columns = null,
										  $sortColumns = null, $sortAscending = true, $limit = null) {
		if(MySQL::$mysql) return MySQLNew::BuildSQLSelect ($tableName, $whereArray, $columns, $sortColumns, $sortAscending, $limit);
                else if (MySQL::$pg) return PostgreSQL::BuildSQLSelect ($tableName, $whereArray, $columns, $sortColumns, $sortAscending, $limit);
	}

	/**
	 * [STATIC] Builds a SQL UPDATE statement
	 *
	 * @param string $tableName The name of the table
	 * @param array $valuesArray An associative array containing the column
	 *                            names as keys and values as data. The values
	 *                            must be SQL ready (i.e. quotes around
	 *                            strings, formatted dates, ect)
	 * @param array $whereArray (Optional) An associative array containing the
	 *                           column names as keys and values as data. The
	 *                           values must be SQL ready (i.e. quotes around
	 *                           strings, formatted dates, ect). If not specified
	 *                           then all values in the table are updated.
	 * @return string Returns a SQL UPDATE statement
	 */
	static public function BuildSQLUpdate($tableName, $valuesArray, $whereArray = null) {
		if(MySQL::$mysql) return MySQLNew::BuildSQLSelect ($tableName, $whereArray, $columns, $sortColumns, $sortAscending, $limit);
                else if (MySQL::$pg) return PostgreSQL::BuildSQLSelect ($tableName, $whereArray, $columns, $sortColumns, $sortAscending, $limit);
	}

	/**
	 * [STATIC] Builds a SQL WHERE clause from an array.
	 * If a key is specified, the key is used at the field name and the value
	 * as a comparison. If a key is not used, the value is used as the clause.
	 *
	 * @param array $whereArray An associative array containing the column
	 *                           names as keys and values as data. The values
	 *                           must be SQL ready (i.e. quotes around
	 *                           strings, formatted dates, ect)
	 * @return string Returns a string containing the SQL WHERE clause
	 */
	static public function BuildSQLWhereClause($whereArray) {
		if(MySQL::$mysql) return MySQLNew::BuildSQLWhereClause($whereArray);
                else if (MySQL::$pg) return PostgreSQL::BuildSQLWhereClause($whereArray);
	}

	/**
	 * Close current MySQL connection
	 *
	 * @return object Returns TRUE on success or FALSE on error
	 */
	public function Close() {
		return $this->database->Close();
	}

	/**
	 * Deletes rows in a table based on a WHERE filter
	 * (can be just one or many rows based on the filter)
	 *
	 * @param string $tableName The name of the table
	 * @param array $whereArray (Optional) An associative array containing the
	 *                          column names as keys and values as data. The
	 *                          values must be SQL ready (i.e. quotes around
	 *                          strings, formatted dates, ect). If not specified
	 *                          then all values in the table are deleted.
	 * @return boolean Returns TRUE on success or FALSE on error
	 */
	public function DeleteRows($tableName, $whereArray = null) {
		return $this->database->DeleteRows($tableName, $whereArray);
	}

	/**
	 * Returns true if the internal pointer is at the end of the records
	 *
	 * @return boolean TRUE if at the last row or FALSE if not
	 */
	public function EndOfSeek() {
		return $this->database->EndOfSeek();
	}

	/**
	 * Returns the last MySQL error as text
	 *
	 * @return string Error text from last known error
	 */
	public function Error() {
		return $this->database->Error();
	}

	/**
	 * Returns the last MySQL error as a number
	 *
	 * @return integer Error number from last known error
	 */
	public function ErrorNumber() {
		return $this->database->ErrorNumber();
	}

	/**
	 * [STATIC] Converts any value of any datatype into boolean (true or false)
	 *
	 * @param mixed $value Value to analyze for TRUE or FALSE
	 * @return boolean Returns TRUE or FALSE
	 */
	static public function GetBooleanValue($value) {
		if(MySQL::$mysql) return MySQLNew::GetBooleanValue($value);
                else if (MySQL::$pg) return PostgreSQL::GetBooleanValue($value);
	}

	/**
	 * Returns the comments for fields in a table into an
	 * array or NULL if the table has not got any fields
	 *
	 * @param string $table Table name
	 * @return array An array that contains the column comments
	 */
	public function GetColumnComments($table) {
		return $this->database->GetColumnComments($table);
	}

	/**
	 * This function returns the number of columns or returns FALSE on error
	 *
	 * @param string $table (Optional) If a table name is not specified, the
	 *                      column count is returned from the last query
	 * @return integer The total count of columns
	 */
	public function GetColumnCount($table = "") {
		return $this->database->GetColumnCount($table);
	}

	/**
	 * This function returns the data type for a specified column. If
	 * the column does not exists or no records exist, it returns FALSE
	 *
	 * @param string $column Column name or number (first column is 0)
	 * @param string $table (Optional) If a table name is not specified, the
	 *                      last returned records are used
	 * @return string MySQL data (field) type
	 */
	public function GetColumnDataType($column, $table = "") {
		return $this->database->GetColumnDataType($column, $table);
	}

	/**
	 * This function returns the position of a column
	 *
	 * @param string $column Column name
	 * @param string $table (Optional) If a table name is not specified, the
	 *                      last returned records are used.
	 * @return integer Column ID
	 */
	public function GetColumnID($column, $table = "") {
		return $this->database->GetColumnID($column, $table);
	}

   /**
	 * This function returns the field length or returns FALSE on error
	 *
	 * @param string $column Column name
	 * @param string $table (Optional) If a table name is not specified, the
	 *                      last returned records are used.
	 * @return integer Field length
	 */
	public function GetColumnLength($column, $table = "") {
		return $this->database->GetColumnLength($column, $table);
	}

   /**
	 * This function returns the name for a specified column number. If
	 * the index does not exists or no records exist, it returns FALSE
	 *
	 * @param string $columnID Column position (0 is the first column)
	 * @param string $table (Optional) If a table name is not specified, the
	 *                      last returned records are used.
	 * @return integer Field Length
	 */
	public function GetColumnName($columnID, $table = "") {
		return $this->database->GetColumnName($columnID, $table);
	}

	/**
	 * Returns the field names in a table or query in an array
	 *
	 * @param string $table (Optional) If a table name is not specified, the
	 *                      last returned records are used
	 * @return array An array that contains the column names
	 */
	public function GetColumnNames($table = "") {
		return $this->database->GetColumnNames($table);
	}

	/**
	 * This function returns the last query as an HTML table
	 *
	 * @param boolean $showCount (Optional) TRUE if you want to show the row count,
	 *                           FALSE if you do not want to show the count
	 * @param string $styleTable (Optional) Style information for the table
	 * @param string $styleHeader (Optional) Style information for the header row
	 * @param string $styleData (Optional) Style information for the cells
	 * @return string HTML containing a table with all records listed
	 */
	public function GetHTML($showCount = true, $styleTable = null, $styleHeader = null, $styleData = null) {
		return $this->database->GetHTML($showCount, $styleTable, $styleHeader, $styleData);
	}

	/**
	* Returns the last query as a JSON document
	*
	* @return string JSON containing all records listed
	*/
	public function GetJSON() {
		return $this->database->GetJSON();
	}

	/**
	 * Returns the last autonumber ID field from a previous INSERT query
	 *
	 * @return  integer ID number from previous INSERT query
	 */
	public function GetLastInsertID() {
		return $this->database->GetLastInsertID();
	}

	/**
	 * Returns the last SQL statement executed
	 *
	 * @return string Current SQL query string
	 */
	public function GetLastSQL() {
		return $this->database->GetLastSQL();
	}

	/**
	 * This function returns table names from the database
	 * into an array. If the database does not contains
	 * any tables, the returned value is FALSE
	 *
	 * @return array An array that contains the table names
	 */
	public function GetTables() {
		return $this->database->GetTables();
	}

	/**
	 * Returns the last query as an XML Document
	 *
	 * @return string XML containing all records listed
	 */
	public function GetXML() {
		return $this->database->GetXML();
	}

	/**
	 * Determines if a query contains any rows
	 *
	 * @param string $sql [Optional] If specified, the query is first executed
	 *                    Otherwise, the last query is used for comparison
	 * @return boolean TRUE if records exist, FALSE if not or query error
	 */
	public function HasRecords($sql = "") {
		return $this->database->HasRecords($sql);
	}

	/**
	 * Inserts a row into a table in the connected database
	 *
	 * @param string $tableName The name of the table
	 * @param array $valuesArray An associative array containing the column
	 *                            names as keys and values as data. The values
	 *                            must be SQL ready (i.e. quotes around
	 *                            strings, formatted dates, ect)
	 * @return integer Returns last insert ID on success or FALSE on failure
	 */
	public function InsertRow($tableName, $valuesArray) {
		return $this->database->InsertRow($tableName, $valuesArray);
	}

	/**
	 * Determines if a valid connection to the database exists
	 *
	 * @return boolean TRUE idf connectect or FALSE if not connected
	 */
	public function IsConnected() {
		return $this->database->IsConnected();
	}

	/**
	 * [STATIC] Determines if a value of any data type is a date PHP can convert
	 *
	 * @param date/string $value
	 * @return boolean Returns TRUE if value is date or FALSE if not date
	 */
	static public function IsDate($value) {
		return $this->database->IsDate($value);
	}

	/**
	 * Stop executing (die/exit) and show last MySQL error message
	 *
	 */
	public function Kill($message = "") {
		$this->database->Kill($message);
	}

	/**
	 * Seeks to the beginning of the records
	 *
	 * @return boolean Returns TRUE on success or FALSE on error
	 */
	public function MoveFirst() {
		return $this->database->MoveFirst();
	}

	/**
	 * Seeks to the end of the records
	 *
	 * @return boolean Returns TRUE on success or FALSE on error
	 */
	public function MoveLast() {
		return $this->database->MoveLast();
	}

	/**
	 * Connect to specified MySQL server
	 *
	 * @param string $database (Optional) Database name
	 * @param string $server   (Optional) Host address
	 * @param string $username (Optional) User name
	 * @param string $password (Optional) Password
	 * @param string $charset  (Optional) Character set
	 * @param boolean $pcon    (Optional) Persistant connection
	 * @return boolean Returns TRUE on success or FALSE on error
	 */
	public function Open($database = null, $server = null, $username = null,
						 $password = null, $charset = null, $pcon = false,
                                                 $schema = null) {
                if(MySQL::$mysql) return $this->database->Open($database, $server, $username, $password, $charset, $pcon);
		else if(MySQL::$pg) return $this->database->Open($database, $server, $username, $password, $charset, $pcon, $schema);
	}

	/**
	 * Executes the given SQL query and returns the records
	 *
	 * @param string $sql The query string should not end with a semicolon
	 * @return object PHP 'mysql result' resource object containing the records
	 *                on SELECT, SHOW, DESCRIBE or EXPLAIN queries and returns;
	 *                TRUE or FALSE for all others i.e. UPDATE, DELETE, DROP
	 *                AND FALSE on all errors (setting the local Error message)
	 */
	public function Query($sql) {
		return $this->database->Query($sql);
	}

	/**
	 * Executes the given SQL query and returns a multi-dimensional array
	 *
	 * @param string $sql The query string should not end with a semicolon
	 * @param integer $resultType (Optional) The type of array
	 *                Values can be: MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH
	 * @return array A multi-dimensional array containing all the data
	 *               returned from the query or FALSE on all errors
	 */
	public function QueryArray($sql, $resultType = "default") {
		if($resultType == "default") return $this->database->QueryArray($sql);
                else if($resultType != "default") return $this->database->QueryArray($sql,$resultType);
	}

	/**
	 * Executes the given SQL query and returns only one (the first) row
	 *
	 * @param string $sql The query string should not end with a semicolon
	 * @return object PHP resource object containing the first row or
	 *                FALSE if no row is returned from the query
	 */
	public function QuerySingleRow($sql) {
		return $this->database->QuerySingleRow($sql);
	}

	/**
	 * Executes the given SQL query and returns the first row as an array
	 *
	 * @param string $sql The query string should not end with a semicolon
	 * @param integer $resultType (Optional) The type of array
	 *                Values can be: MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH
	 * @return array An array containing the first row or FALSE if no row
	 *               is returned from the query
	 */
	public function QuerySingleRowArray($sql, $resultType = "default") {
		if($resultType == "default") return $this->database->QuerySingleRowArray($sql);
                else if($resultType != "default") return $this->database->QuerySingleRowArray($sql,$resultType);
	}

	/**
	 * Executes a query and returns a single value. If more than one row
	 * is returned, only the first value in the first column is returned.
	 *
	 * @param string $sql The query string should not end with a semicolon
	 * @return mixed The value returned or FALSE if no value
	 */
	public function QuerySingleValue($sql) {
		return $this->database->QuerySingleValue($sql);
	}

	/**
	 * Executes the given SQL query, measures it, and saves the total duration
	 * in microseconds
	 *
	 * @param string $sql The query string should not end with a semicolon
	 * @return object PHP 'mysql result' resource object containing the records
	 *                on SELECT, SHOW, DESCRIBE or EXPLAIN queries and returns
	 *                TRUE or FALSE for all others i.e. UPDATE, DELETE, DROP
	 */
	public function QueryTimed($sql) {
		return $this->database->QueryTimed($sql);
	}

	/**
	 * Returns the records from the last query
	 *
	 * @return object PHP 'mysql result' resource object containing the records
	 *                for the last query executed
	 */
	public function Records() {
		return $this->database->Records();
	}

	/**
	 * Returns all records from last query and returns contents as array
	 * or FALSE on error
	 *
	 * @param integer $resultType (Optional) The type of array
	 *                Values can be: MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH
	 * @return Records in array form
	 */
	public function RecordsArray($resultType = "default") {
		if($resultType == "default") return $this->database->RecordsArray();
                else if($resultType != "default") return $this->database->RecordsArray($resultType);
	}

	/**
	 * Frees memory used by the query results and returns the function result
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure
	 */
	public function Release() {
		return $this->database->Release();
	}


	/**
	 * Reads the current row and returns contents as a
	 * PHP object or returns false on error
	 *
	 * @param integer $optional_row_number (Optional) Use to specify a row
	 * @return object PHP object or FALSE on error
	 */
	public function Row($optional_row_number = null) {
		return $this->database->Row($optional_row_number);
	}

	/**
	 * Reads the current row and returns contents as an
	 * array or returns false on error
	 *
	 * @param integer $optional_row_number (Optional) Use to specify a row
	 * @param integer $resultType (Optional) The type of array
	 *                Values can be: MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH
	 * @return array Array that corresponds to fetched row or FALSE if no rows
	 */
	public function RowArray($optional_row_number = null, $resultType = "default") {
		if($resultType == "default") return $this->database->RowArray($optional_row_number);
                else if($resultType != "default") return $this->database->RowArray($optional_row_number,$resultType);
	}

	/**
	 * Returns the last query row count
	 *
	 * @return integer Row count or FALSE on error
	 */
	public function RowCount() {
		return $this->database->RowCount();
	}

	/**
	 * Sets the internal database pointer to the
	 * specified row number and returns the result
	 *
	 * @param integer $row_number Row number
	 * @return object Fetched row as PHP object
	 */
	public function Seek($row_number) {
		return $this->database->Seek($row_number);
	}

	/**
	 * Returns the current cursor row location
	 *
	 * @return integer Current row number
	 */
	public function SeekPosition() {
		return $this->database->SeekPosition();
	}

	/**
	 * Selects a different database and character set
	 *
	 * @param string $database Database name
	 * @param string $charset (Optional) Character set (i.e. utf8)
	 * @return boolean Returns TRUE on success or FALSE on error
	 */
	public function SelectDatabase($database, $charset = "") {
		return $this->database->SelectDatabase($database, $charset);
	}

	/**
	 * Gets rows in a table based on a WHERE filter
	 *
	 * @param string $tableName The name of the table
	 * @param array $whereArray (Optional) An associative array containing the
	 *                          column names as keys and values as data. The
	 *                          values must be SQL ready (i.e. quotes around
	 *                          strings, formatted dates, ect)
	 * @param array/string $columns (Optional) The column or list of columns to select
	 * @param array/string $sortColumns (Optional) Column or list of columns to sort by
	 * @param boolean $sortAscending (Optional) TRUE for ascending; FALSE for descending
	 *                               This only works if $sortColumns are specified
	 * @param integer/string $limit (Optional) The limit of rows to return
	 * @return boolean Returns records on success or FALSE on error
	 */
	public function SelectRows($tableName, $whereArray = null, $columns = null,
							   $sortColumns = null, $sortAscending = true,
							   $limit = null) {
            $return = $this->database->SelectRows($tableName, $whereArray, 
                                                           $columns, $sortColumns, $sortAscending,$limit);
		return $return;
	}

	/**
	 * Retrieves all rows in a specified table
	 *
	 * @param string $tableName The name of the table
	 * @return boolean Returns records on success or FALSE on error
	 */
	public function SelectTable($tableName) {
		return $this->database->SelectTable($tableName);
	}

	/**
	 * [STATIC] Converts a boolean into a formatted TRUE or FALSE value of choice
	 *
	 * @param mixed $value value to analyze for TRUE or FALSE
	 * @param mixed $trueValue value to use if TRUE
	 * @param mixed $falseValue value to use if FALSE
	 * @param string $datatype Use SQLVALUE constants or the strings:
	 *                          string, text, varchar, char, boolean, bool,
	 *                          Y-N, T-F, bit, date, datetime, time, integer,
	 *                          int, number, double, float
	 * @return string SQL formatted value of the specified data type
	 */
	static public function SQLBooleanValue($value, $trueValue, $falseValue, $datatype = self::SQLVALUE_TEXT) {
		if(MySQL::$mysql) return MySQLNew::BuildSQLBooleanValue($value, $trueValue, $falseValue, $datatype);
                else if (MySQL::$pg) return PostgreSQL::BuildSQLBooleanValue($value, $trueValue, $falseValue, $datatype);
	}

	/**
	 * [STATIC] Returns string suitable for SQL
	 *
	 * @param string $value
	 * @return string SQL formatted value
	 */
	static public function SQLFix($value) {
		if(MySQL::$mysql) return MySQLNew::SQLFix($value);
                else if (MySQL::$pg) return PostgreSQL::SQLFix($value);
	}

	/**
	 * [STATIC] Returns MySQL string as normal string
	 *
	 * @param string $value
	 * @return string
	 */
	static public function SQLUnfix($value) {
		if(MySQL::$mysql) return MySQLNew::SQLUnfix($value);
                else if (MySQL::$pg) return PostgreSQL::SQLUnfix($value);
	}

	/**
	 * [STATIC] Formats any value into a string suitable for SQL statements
	 * (NOTE: Also supports data types returned from the gettype function)
	 *
	 * @param mixed $value Any value of any type to be formatted to SQL
	 * @param string $datatype Use SQLVALUE constants or the strings:
	 *                          string, text, varchar, char, boolean, bool,
	 *                          Y-N, T-F, bit, date, datetime, time, integer,
	 *                          int, number, double, float
	 * @return string
	 */
	static public function SQLValue($value, $datatype = self::SQLVALUE_TEXT) {
		if(MySQL::$mysql) return MySQLNew::SQLValue($value, $datatype );
                else if (MySQL::$pg) return PostgreSQL::SQLValue($value, $datatype);
	}

	/**
	 * Returns last measured duration (time between TimerStart and TimerStop)
	 *
	 * @param integer $decimals (Optional) The number of decimal places to show
	 * @return Float Microseconds elapsed
	 */
	public function TimerDuration($decimals = 4) {
		return $this->database->TimerDuration($decimals);
	}

	/**
	 * Starts time measurement (in microseconds)
	 *
	 */
	public function TimerStart() {
		$this->database->TimerStart();
	}

	/**
	 * Stops time measurement (in microseconds)
	 *
	 */
	public function TimerStop() {
		$this->database->TimerStop();
	}

	/**
	 * Starts a transaction
	 *
	 * @return boolean Returns TRUE on success or FALSE on error
	 */
	public function TransactionBegin() {
		return $this->database->TransactionBegin();
	}

	/**
	 * Ends a transaction and commits the queries
	 *
	 * @return boolean Returns TRUE on success or FALSE on error
	 */
	public function TransactionEnd() {
		return $this->database->TransactionEnd();
	}

	/**
	 * Rolls the transaction back
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure
	 */
	public function TransactionRollback() {
		return $this->database->TransactionRollback();
	}

	/**
	 * Truncates a table removing all data
	 *
	 * @param string $tableName The name of the table
	 * @return boolean Returns TRUE on success or FALSE on error
	 */
	public function TruncateTable($tableName) {
		return $this->database->TruncateTable($tableName);
	}

	/**
	 * Updates rows in a table based on a WHERE filter
	 * (can be just one or many rows based on the filter)
	 *
	 * @param string $tableName The name of the table
	 * @param array $valuesArray An associative array containing the column
	 *                            names as keys and values as data. The values
	 *                            must be SQL ready (i.e. quotes around
	 *                            strings, formatted dates, ect)
	 * @param array $whereArray (Optional) An associative array containing the
	 *                           column names as keys and values as data. The
	 *                           values must be SQL ready (i.e. quotes around
	 *                           strings, formatted dates, ect). If not specified
	 *                           then all values in the table are updated.
	 * @return boolean Returns TRUE on success or FALSE on error
	 */
	public function UpdateRows($tableName, $valuesArray, $whereArray = null) {
		return $this->database->UpdateRows($tableName, $valuesArray, $whereArray);
	}
        
        public function GetLastId($table){
                if(MySQL::$mysql) return mysql_insert_id ();
                else if (MySQL::$pg) return $this->database->PGLastId ($table);
        }
}
?>