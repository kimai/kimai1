<?
include("mysql.class.php");

$db = new MySQL();

// *OR* you can fill in these details when the object is created
// $db = new MySQL(true, "Test", "localhost", "root", "password");

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

// --- Open the database --------------------------------------------
// (Also note that you can fill in the variables in the top of the class
// if you want to automatically connect when the object is created. If
// you fill in the values when you create the obect, this is not needed.)
if (! $db->Open("test", "localhost", "root", "password")) {
    $db->Kill();
}
echo "You are connected to the database<br />\n";

// --- Insert a new record ------------------------------------------
$sql = "INSERT INTO Test (Color, Age) Values ('Red', 7)";
if (! $db->Query($sql)) {
    $db->Kill();
}
echo "Last ID inserted was: " . $db->GetLastInsertID();

// --- Or insert a new record with transaction processing -----------
$sql = "INSERT INTO Test (Color, Age) Values ('Blue', 3)";
$db->TransactionBegin();
if ($db->Query($sql)) {
    $db->TransactionEnd();
    echo "Last ID inserted was: " . $db->GetLastInsertID() . "<br /><br />\n";
} else {
    $db->TransactionRollback();
    echo "<p>Query Failed</p>\n";
}

// --- Query and show the data --------------------------------------
// (Note: $db->Query also returns the result set)
if ($db->Query("SELECT * FROM Test")) {
    echo $db->GetHTML();
} else {
    echo "<p>Query Failed</p>";
}

// --- Getting the record count is easy -----------------------------
echo "\n<p>Record Count: " . $db->RowCount() . "</p>\n";


// --- Loop through the records -------------------------------------
while ($row = $db->Row()) {
    echo $row->Color . " - " . $row->Age . "<br />\n";
}

// --- Loop through the records another way -------------------------
$db->MoveFirst();
while (! $db->EndOfSeek()) {
    $row = $db->Row();
    echo $row->Color . " - " . $row->Age . "<br />\n";
}

// --- Loop through the records with an index -----------------------
for ($index = 0; $index < $db->RowCount(); $index++) {
    $row = $db->Row($index);
    echo "Row " . $index . ":" . $row->Color . " - " . $row->Age . "<br />\n";
}

// --- We can even grab array data from the last result set ---------
$myArray = $db->RecordsArray();

// --- List all of the tables in the database -----------------------
$tables = $db->GetTables();
foreach ($tables as $table) {
    echo $table . "<br />\n";
}

// --- Show the columns (field names) in a table --------------------
$columns = $db->GetColumnNames("test");
foreach ($columns as $column) {
    echo $column . "<br />\n";
}

// --- Find a column (field) type and length ------------------------
echo "Type: " . $db->GetColumnDataType("Color", "Test") . "<br />\n";
echo "Length: " . $db->GetColumnLength("Color", "Test") . "<br />\n";

// --- Get a column's ordinal position (the column number) ----------
echo $db->GetColumnID("Age", "Test") . "<br />\n";

// --- Check for errors ---------------------------------------------
if ($db->Error()) {
    echo "<h3>" . $db->Error() . "</h3>\n";
} else {
    echo "<p>There were no errors</p>\n";
}

// --- Format some values ready for SQL -----------------------------
// You do not have to create the object to use these. Simply include
// the class in your PHP file. These are called "Static" methods.
echo "<br /><br />\n\n";
echo MySQL::SQLValue("Let's format some text") . "<br />\n";
echo MySQL::SQLValue(date("m/d/Y"), MySQL::SQLVALUE_DATE) . "<br />\n";
echo MySQL::SQLValue(123, MySQL::SQLVALUE_NUMBER) . "<br />\n";

// --- Format some values ready for SQL based on a boolean value ----
echo MySQL::SQLBooleanValue(false, "1", "0", MySQL::SQLVALUE_NUMBER);
echo MySQL::SQLBooleanValue("ON", "Ya", "Nope");
echo MySQL::SQLBooleanValue(1, '+', '-');
?>
