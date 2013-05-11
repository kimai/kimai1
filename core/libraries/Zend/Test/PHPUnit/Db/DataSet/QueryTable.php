<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: QueryTable.php 23775 2011-03-01 17:25:24Z ralph $
 */

/**
 * @see PHPUnit_Extensions_Database_DataSet_QueryTable
 */
require_once "PHPUnit/Extensions/Database/DataSet/QueryTable.php";

/**
 * @see PHPUnit_Extensions_Database_DB_IDatabaseConnection
 */
require_once "PHPUnit/Extensions/Database/DB/IDatabaseConnection.php";

/**
 * Represent a PHPUnit Database Extension table with Queries using a Zend_Db adapter for assertion against other tables.
 *
 * @uses       PHPUnit_Extensions_Database_DataSet_QueryTable
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Test_PHPUnit_Db_DataSet_QueryTable extends PHPUnit_Extensions_Database_DataSet_QueryTable
{
    /**
     * Creates a new database query table object.
     *
     * @param string $table_name
     * @param string $query
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection
     */
    public function __construct($tableName, $query, PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection)
    {
        if( !($databaseConnection instanceof Zend_Test_PHPUnit_Db_Connection) ) {
            require_once "Zend/Test/PHPUnit/Db/Exception.php";
            throw new Zend_Test_PHPUnit_Db_Exception("Zend_Test_PHPUnit_Db_DataSet_QueryTable only works with Zend_Test_PHPUnit_Db_Connection connections-");
        }
        parent::__construct($tableName, $query, $databaseConnection);
    }

    /**
     * Load data from the database.
     *
     * @return void
     */
    protected function loadData()
    {
        if($this->data === null) {
            $stmt = $this->databaseConnection->getConnection()->query($this->query);
            $this->data = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
        }
    }

    /**
     * Create Table Metadata
     */
    protected function createTableMetaData()
    {
        if ($this->tableMetaData === NULL)
        {
            $this->loadData();
            $keys = array();
            if(count($this->data) > 0) {
                $keys = array_keys($this->data[0]);
            }
            $this->tableMetaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData(
                $this->tableName, $keys
            );
        }
    }
}