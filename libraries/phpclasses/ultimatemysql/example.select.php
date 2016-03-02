<?php
// --- TUTORIAL FOR SELECTING DATA USING ULTIMATE MYSQL ---
// Let's walk through the basics on how to query the database.
// Remember that you can execute a query that does not contain
// returned results, but in this example will use a SQL SELECT
// query to demonstrate showing results. You must have a basic
// working knowledge of SQL in order to use this class.

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
-- Records
-- --------------------------------------------
INSERT INTO `test` VALUES ('1', 'Red', '7');
INSERT INTO `test` VALUES ('2', 'Blue', '3');
INSERT INTO `test` VALUES ('3', 'Green', '10');
INSERT INTO `test` VALUES ('4', 'Yellow', '1');
-- --------------------------------------------
*/

// Make sure you include the class
include("mysql.class.php");

// We will pass in our connection information but please note that
// this information can be set aurtomatically for you in the header
// of the mysql.class.php file and these parameters are all optional.
// See the top section of the mysql.class.php file for more info.
$db = new MySQL(true, "test", "localhost", "root", "password");

// This checks for errors and if there is one, terminates the script
// while showing the last MySQL error.
if ($db->Error()) $db->Kill();

// Or use: if ($db->Error()) die($db->Error());
// Or: if ($db->Error()) echo $db->Error();

// Execute our query
if (! $db->Query("SELECT * FROM Test")) $db->Kill();

// Let's show how many records were returned
echo $db->RowCount() . " records returned.<br />\n<hr />\n";

// Loop through the records using the MySQL object (prefered)
$db->MoveFirst();
while (! $db->EndOfSeek()) {
    $row = $db->Row();

    echo "Row " . $db->SeekPosition() . ": ";
    echo $row->Color . " and " . $row->Age . "<br />\n";
}

// =========================================================================
// The rest of this tutorial covers addition methods of getting to the data
// and is completely optional.
// =========================================================================

echo "<hr />\n"; // ---------------------------------------------------------

// Loop through the records using a counter and display the values
for ($index = 0; $index < $db->RowCount(); $index++) {
    $row = $db->Row($index);

    echo "Index " . $index . ": ";
    echo  $row->Color . " and " . $row->Age . "<br />\n";
}

echo "<hr />\n"; // ---------------------------------------------------------

// Now let's just show all the data as an HTML table
// This method is great for testing or displaying simple results
echo $db->GetHTML(false);

echo "<hr />\n"; // ---------------------------------------------------------

// Now let's grab the first row of data as an associative array
// The paramters are completely optional. Every time you grab a
// row, the cursor is automatically moved to the next row. Here,
// we will specify the the first row (0) to reset our position.
// We will also specify what type of array we want returned.
$array = $db->RowArray(0, MYSQL_ASSOC);

// Display the array
echo "<pre>\n";
print_r($array);
echo "</pre>\n";

echo "<hr />\n"; // ---------------------------------------------------------

// And now show the individual columns in the array
echo $array[Color] . " and " . $array[Age] . "<br />\n";

// Grab the next row as an array. Notice how we didn't specify
// a row (0) like above? It's completely optional.
$array = $db->RowArray();
echo $array[Color] . " and " . $array[Age] . "<br />\n";

// There are so many different ways to use the Ultimate MySQL class!
?>