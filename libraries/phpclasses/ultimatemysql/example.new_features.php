<?php
// Include the Ultimate MySQL class and create the object
include("mysql.class.php");
$db = new MySQL();

// Connect to the database
// CHANGE THESE VALUES TO MATCH YOUR DATABASE!
if (! $db->Open(true, "test", "localhost", "root", "password")) $db->Kill();

// --------------------------------------------------------------------------
// Want to know if you are connected? Use IsConnected()
echo "Are we connected? ";
var_dump($db->IsConnected());
echo "\n<br />\n";

// --------------------------------------------------------------------------
// Now we can generate SQL statements from arrays!

// Let's create an array for the examples
// $arrayVariable["column name"] = formatted SQL value
$values["Name"] = MySQL::SQLValue("Violet");
$values["Age"]  = MySQL::SQLValue(777, MySQL::SQLVALUE_NUMBER);

// Echo out some SQL statements
echo "<pre>" . "\n";
echo MySQL::BuildSQLDelete("Test", $values) . "\n<br />\n";
echo MySQL::BuildSQLInsert("Test", $values) . "\n<br />\n";
echo MySQL::BuildSQLSelect("Test", $values) . "\n<br />\n";
echo MySQL::BuildSQLUpdate("Test", $values, $values) . "\n<br />\n";
echo MySQL::BuildSQLWhereClause($values) . "\n<br />\n";
echo "</pre>" . "\n";

// Or create more advanced SQL SELECT statements
$columns = array("Name", "Age");
$sort = "Name";
$limit = 10;
echo MySQL::BuildSQLSelect("Test", $values, $columns, $sort, true, $limit);
echo "\n<br />\n";

$columns = array("Color Name" => "Name", "Total Age" => "Age");
$sort = array("Age", "Name");
$limit = "10, 20";
echo MySQL::BuildSQLSelect("Test", $values, $columns, $sort, false, $limit);
echo "\n<br />\n";

// The following methods take the same parameters and automatically execute!

// $db->DeleteRows("Test", $values);
// $db->InsertRow("Test", $values);
// $db->SelectRows("Test", $values, $columns, $sort, true, $limit);
// $db->UpdateRows("Test", $values1, $values2);

// You can also select an entire table
// $db->SelectTable("Test");

// Or truncate and clear out an entire table
// $db->TruncateTable("Test");

// --------------------------------------------------------------------------

// Now you can throw exceptions and use try/catch blocks
$db->ThrowExceptions = true;

try {
	// This next line will always cause an error
	$db->Query("BAD SQL QUERY TO CREATE AN ERROR");
} catch(Exception $e) {
	// If an error occurs, do this (great for transaction processing!)
	echo "We caught the error: " . $e->getMessage();
}

// Or let's show a stack trace if we do not use a try/catch
// This shows the stack and tells us exactly where it failed
$db->Query("BAD SQL QUERY TO CREATE AN ERROR");

?>