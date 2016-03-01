<?php
// --- TUTORIAL FOR ULTIMATE MYSQL ---
// Let's walk through the basics on how to manipulate and query records
// in the database using the Ultimate MySQL class.

/*
-- --------------------------------------------
-- SQL to generate test table
-- --------------------------------------------
CREATE TABLE `test` (
  `TestID` int(10)     NOT NULL auto_increment,
  `Color`  varchar(15) default NULL,
  `Age`    int(10)     default NULL,
  PRIMARY KEY  (`TestID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
-- --------------------------------------------
*/

// Make sure you include the class
include("mysql.class.php");

// We will pass in our connection information but please note that
// this information can be set aurtomatically for you in the header
// of the mysql.class.php file and these parameters are all optional.
// See the top section of the mysql.class.php file for more info.

// CHANGE THESE VALUES TO MATCH YOUR DATABASE!
$db = new MySQL(true, "test", "localhost", "root", "password");

// This checks for errors and if there is one, terminates the script
// while showing the last MySQL error.
// $db->Kill() is the same as die($db->Error()) or exit($db->Error());
if ($db->Error()) $db->Kill();

// You could also throw an exception on errors using:
// $db->ThrowExceptions = true;

// =========================================================================
// Example to insert a new row into a table and display it
// =========================================================================

// $arrayVariable["column name"] = formatted SQL value
$values["Color"] = MySQL::SQLValue("Violet");
$values["Age"]  = MySQL::SQLValue(777, MySQL::SQLVALUE_NUMBER);

// Execute the insert
$result = $db->InsertRow("Test", $values);

// If we have an error
if (! $result) {
	// Show the error and kill the script
	$db->Kill();
} else {
	// No error, show the new record's ID
	echo "The new record's ID is: " . $db->GetLastInsertID() . "\n<br />\n";

	// Show the record using the values array to generate the WHERE clause
	// We will use the SelectRows() method to query the database
	$db->SelectRows("Test", $values);

	// Show the results in an HTML table
	echo $db->GetHTML();
}

// =========================================================================
// Example to delete a row (or rows) in a table matching a filter
// =========================================================================

// Now let's delete that record using the same array for the WHERE clause
$db->DeleteRows("Test", $values);

// =========================================================================
// Example to update an existing row into a table
// =========================================================================

// Create an array that holds the update information
// $arrayVariable["column name"] = formatted SQL value
$update["Color"] = MySQL::SQLValue("Red");
$update["Age"]   = MySQL::SQLValue(123, MySQL::SQLVALUE_NUMBER);

// Create a filter array the detemrines which record(s) to process
// (you can specify more than one column if needed)
$where["TestID"] = MySQL::SQLValue(1, "integer");

// Execute the update
$result = $db->UpdateRows("test", $update, $where);

// If we have an error
if (! $result) {
	// Show the error and kill the script
	$db->Kill();
}

// --------------------------------------------------------------------------

// FYI: We can also shortcut and specify the "where" array in the call...
if (! $db->UpdateRow("test", $values, array("TestID" => 1))) $db->Kill();

// =========================================================================
// Here's a standard SQL query INSERT
// =========================================================================

// Build the INSERT SQL statement...
// (this could also be an UPDATE or DELETE SQL statement)
$sql = "INSERT INTO test (Color, Age) VALUES ('Red', '7')";

// Execute our query
if (! $db->Query($sql)) $db->Kill();

// Display the last autonumber ID field from the previous INSERT query
echo "The new ID for this record is " . $db->GetLastInsertID() . "<br />\r";

// =========================================================================
// The rest of this tutorial covers addition methods of inserting data into
// the database and is completely optional.
// =========================================================================

echo "<hr />\n"; // ---------------------------------------------------------

// Now let's do some transactional processing. This is an excellent way to
// keep database integrity. Let's say that you have to insert multiple
// records that depend on one another. If one of the insert queries fail,
// you want to remove the other inserts that were done before it.
// Transaction processing allows us to do this. We start a transaction,
// execute some queries, if one fails, all we have to do it rollback. When
// you rollback, any query executed since you began the transaction is
// removed as if it never happened. If they were all successful, then you
// commit and all changes are saved. This can be really useful on larger
// databases that have parent child relationships on the tables.

// Let's start out by creating a new transaction
$db->TransactionBegin();

// Now let's insert some records. We are going to skip checking for any
// query errors just for this part of the example.
$db->Query("INSERT INTO test (Color, Age) VALUES ('Blue', '3')");
$db->Query("INSERT INTO test (Color, Age) VALUES ('Green', '10')");
$db->Query("INSERT INTO test (Color, Age) VALUES ('Yellow', '1')");

// Oops! We don't really want to save these to the database, let's rollback
$db->TransactionRollback();

// Now if you stopped right here and looked in the database, nothing has
// changed... not one thing was saved. It "rolled back" all these inserts.

// NOTE: Transaction processing also works with the InsertRow() and
//       UpdateRow() methods.
// --------------------------------------------------------------------------

// Let's try that again, but this time, we will commit the changes.
// Begin a new transaction, but this time, let's check for errors.
if (! $db->TransactionBegin()) $db->Kill();

// We'll create a flag to check for any errors
$success = true;

// Insert some records and if there are any errors, set the flag to false
$sql = "INSERT INTO test (Color, Age) VALUES ('Blue', '3')";
if (! $db->Query($sql)) $success = false;
$sql = "INSERT INTO test (Color, Age) VALUES ('Green', '10')";
if (! $db->Query($sql)) $success = false;
$sql = "INSERT INTO test (Color, Age) VALUES ('Yellow', '1')";
if (! $db->Query($sql)) $success = false;

// Notice that you can even view what the new IDs are going to be before
// the records are commited. Transaction processing allows you to
// actually see what the final results will look like in the database.
echo "The new ID for the last inserted record is " . $db->GetLastInsertID();
echo "<br />\r";

// If there were no errors...
If ($success) {

    // Commit the transaction and save these records to the database
    if (! $db->TransactionEnd()) $db->Kill();

} else { // Otherwise, there were errors...

    // Rollback our transaction
    if (! $db->TransactionRollback()) $db->Kill();

}

// Transaction processing works with INSERT, UPDATES, and DELETE queries.
// They are terrific to use in TRY/CATCH blocks for error handling.

// Turn on exception handling
$db->ThrowExceptions = true;

// Here's our try/catch block
try {

	// Begin our transaction
	$db->TransactionBegin();

	//Execute query/queries
	$db->Query($sql);

	// Commit - this line never runs if there is an error
	$db->TransactionEnd();

} catch(Exception $e) {

	// If an error occurs, rollback and show the error
	$db->TransactionRollback();
	exit($e->getMessage());

}

// There are so many different ways to use the Ultimate MySQL class!
?>