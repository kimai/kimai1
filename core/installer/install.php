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
 * Perform the installation by creating all necessary tables
 * and some basic entries.
 */

/**
 * Execute an sql query in the database. The correct database connection
 * will be chosen and the query will be logged with the success status.
 *
 * @param $query query to execute as string
 */
function exec_query($query) {
    global $database, $errors;

    $conn = $database->getConnectionHandler();
    $success = $conn->Query($query);

    //Logger::logfile($query);
    if (!$success) {
        $errorInfo = serialize($conn->Error());
        Logger::logfile('[ERROR] in ['.$query.'] => ' . $errorInfo);
        $errors=true;
    }
}

function get_last_id($table) {
    global $database, $errors;

    $conn = $database->getConnectionHandler();
    $return = $conn->GetLastId($table);
    return $return;
}

function quoteForSql($input) {
  global $kga, $database;

  return "'".mysql_real_escape_string($input)."'";
}

function setAI($table,$val){
    $val = $val -1;
    if ($val < 1) return;
    exec_query(
            "SELECT setval(pg_get_serial_sequence('\"".$table."\"',
                    (SELECT              
                      pa.attname
                    FROM pg_index pi, pg_class pc, pg_attribute pa
                    WHERE
                      pc.oid = '\"".$table."\"'::regclass AND
                      indrelid = pc.oid AND
                      pa.attrelid = pc.oid AND
                      pa.attnum = any(pi.indkey) AND
                      indisprimary
                      limit 1)::text

                    ), ".$val.");"
            );
}

if (!isset($_REQUEST['accept'])) {
    header("Location: ../index.php?disagreedGPL=1");
    exit;
}

include('../includes/basics.php');
$db_layer = $kga['server_conn'];
if ($db_layer == '') $db_layer = $_REQUEST['db_layer'];

date_default_timezone_set($_REQUEST['timezone']);

$randomAdminID = random_number(9);

Logger::logfile("-- begin install ----------------------------------");

// if any of the queries fails, this will be true
$errors=false;

$p = $kga['server_prefix'];

$query =
"CREATE TABLE ${p}users (
  \"userID\" serial NOT NULL  PRIMARY KEY,
  \"name\" varchar(160) NOT NULL,
  \"alias\" varchar(160),
  \"trash\" smallint NOT NULL default '0',
  \"active\" smallint NOT NULL default '1',
  \"mail\" varchar(160) NOT NULL DEFAULT '',
  \"password\" varchar(254) NULL DEFAULT NULL,
  \"passwordResetHash\" varchar(32) NULL DEFAULT NULL,
  \"ban\" int NOT NULL default '0',
  \"banTime\" int NOT NULL default '0',
  \"secure\" varchar(60) NOT NULL default '0',
  \"lastProject\" int NOT NULL default '1',
  \"lastActivity\" int NOT NULL default '1',
  \"lastRecord\" int NOT NULL default '0',
  \"timeframeBegin\" varchar(60) NOT NULL default '0',
  \"timeframeEnd\" varchar(60) NOT NULL default '0',
  \"apikey\" varchar(30) NULL DEFAULT NULL,
  \"globalRoleID\" int NOT NULL);"
. "create unique index \"${p}users_name_idx\" on \"${p}users\"(\"name\");"
. "create unique index \"${p}users_apikey_idx\" on \"${p}users\"(\"apikey\");";

exec_query($query);

$query = "CREATE TABLE \"${p}preferences\" (
  \"userID\" integer NOT NULL,
  \"option\" varchar(255) NOT NULL,
  \"value\" varchar(255) NOT NULL,
  PRIMARY KEY (\"userID\",\"option\")
);";
exec_query($query);

$query=
"CREATE TABLE \"${p}activities\" (
  \"activityID\" serial NOT NULL PRIMARY KEY,
  \"name\" varchar(255) NOT NULL,
  \"comment\" TEXT NOT NULL,
  \"visible\" smallint NOT NULL DEFAULT '1',
  \"filter\" smallint NOT NULL DEFAULT '0',
  \"trash\" smallint NOT NULL DEFAULT '0'
);";
exec_query($query);
setAI(${p}.'activities',1);

$query=
"CREATE TABLE \"${p}groups\" (
  \"groupID\" serial NOT NULL PRIMARY KEY,
  \"name\" varchar(160) NOT NULL,
  \"trash\" smallint NOT NULL DEFAULT '0'
) ;";
exec_query($query);
setAI(${p}.'groups',1);

$query=
"CREATE TABLE \"${p}groups_users\" (
  \"groupID\" integer NOT NULL,
  \"userID\" integer NOT NULL,
  \"membershipRoleID\" integer NOT NULL,
  PRIMARY KEY (\"groupID\",\"userID\")
) ;";
exec_query($query);
setAI(${p}.'groups_users',1);

// group/customer cross-table (groups n:m customers)
$query="CREATE TABLE \"${p}groups_customers\" (
  \"groupID\" INT NOT NULL,
  \"customerID\" INT NOT NULL,
  PRIMARY KEY(\"groupID\" ,\"customerID\"));";
exec_query($query);

// group/project cross-table (groups n:m projects)
$query="CREATE TABLE \"${p}groups_projects\" (
  \"groupID\" INT NOT NULL,
  \"projectID\" INT NOT NULL,
  PRIMARY KEY(\"groupID\" ,\"projectID\"));";
exec_query($query);

// group/event cross-table (groups n:m events)
$query="CREATE TABLE \"${p}groups_activities\" (
  \"groupID\" INT NOT NULL,
  \"activityID\" INT NOT NULL,
  PRIMARY KEY(\"groupID\" ,\"activityID\"));";
exec_query($query);

// project/event cross-table (projects n:m events)
$query="CREATE TABLE \"${p}projects_activities\" (
  \"projectID\" INT NOT NULL,
  \"activityID\" INT NOT NULL,
  \"budget\" DECIMAL( 10, 2 ) NULL DEFAULT '0.00',
  \"effort\" DECIMAL( 10, 2 ) NULL ,
  \"approved\" DECIMAL( 10, 2 ) NULL,
  PRIMARY KEY(\"projectID\" ,\"activityID\")) ;";
exec_query($query);

$query=
"CREATE TABLE \"${p}customers\" (
  \"customerID\" serial NOT NULL PRIMARY KEY,
  \"name\" varchar(255) NOT NULL,
  \"password\" varchar(255),
  \"passwordResetHash\" char(32) NULL DEFAULT NULL,
  \"secure\" varchar(60) NOT NULL default '0',
  \"comment\" TEXT NOT NULL,
  \"visible\" smallint NOT NULL DEFAULT '1',
  \"filter\" smallint NOT NULL DEFAULT '0',
  \"company\" varchar(255) NOT NULL,
  \"vat\" varchar(255) NOT NULL,
  \"contact\" varchar(255) NOT NULL,
  \"street\" varchar(255) NOT NULL,
  \"zipcode\" varchar(255) NOT NULL,
  \"city\" varchar(255) NOT NULL,
  \"phone\" varchar(255) NOT NULL,
  \"fax\" varchar(255) NOT NULL,
  \"mobile\" varchar(255) NOT NULL,
  \"mail\" varchar(255) NOT NULL,
  \"homepage\" varchar(255) NOT NULL,
  \"timezone\" varchar(255) NOT NULL,
  \"trash\" smallint NOT NULL DEFAULT '0'
) ;";
exec_query($query);
setAI(${p}.'customers',1);

$query=
"CREATE TABLE \"${p}projects\" (
  \"projectID\" serial NOT NULL PRIMARY KEY,
  \"customerID\" integer NOT NULL,
  \"name\" varchar(255) NOT NULL,
  \"comment\" TEXT NOT NULL,
  \"visible\" smallint NOT NULL DEFAULT '1',
  \"filter\" smallint NOT NULL DEFAULT '0',
  \"trash\" smallint NOT NULL DEFAULT '0',
  \"budget\" decimal(10,2) NOT NULL DEFAULT '0.00',
  \"effort\" DECIMAL( 10, 2 ) NULL,
  \"approved\" DECIMAL( 10, 2 ) NULL,
  \"internal\" smallint NOT NULL DEFAULT 0);"
. "create index \"${p}projects_customerID_idx\" on \"${p}projects\"(\"customerID\");";
exec_query($query);
setAI(${p}.'projects',1);

$query=
"CREATE TABLE \"${p}timeSheet\" (
  \"timeEntryID\" serial NOT NULL PRIMARY KEY,
  \"start\" integer NOT NULL default '0',
  \"end\" integer NOT NULL default '0',
  \"duration\" integer NOT NULL default '0',
  \"userID\" integer NOT NULL,
  \"projectID\" integer NOT NULL,
  \"activityID\" integer NOT NULL,
  \"description\" TEXT NULL,
  \"comment\" TEXT NULL DEFAULT NULL,
  \"commentType\" smallint NOT NULL DEFAULT '0',
  \"cleared\" smallint NOT NULL DEFAULT '0',
  \"location\" VARCHAR(50),
  \"trackingNumber\" varchar(30),
  \"rate\" DECIMAL( 10, 2 ) NOT NULL DEFAULT '0',
  \"fixedRate\" DECIMAL( 10, 2 ) NOT NULL DEFAULT '0',
  \"budget\" DECIMAL( 10, 2 ) NULL,
  \"approved\" DECIMAL( 10, 2 ) NULL,
  \"statusID\" SMALLINT NOT NULL,
  \"billable\" smallint NULL);"
. "create index \"${p}timeSheet_userID_idx\" on \"${p}timeSheet\"(\"userID\");"
. "create index \"${p}timeSheet_projectID_idx\" on \"${p}timeSheet\"(\"projectID\");"
. "create index \"${p}timeSheet_activityID_idx\" on \"${p}timeSheet\"(\"activityID\");";
exec_query($query);
setAI(${p}.'timeSheet',1);

$query=
"CREATE TABLE \"${p}configuration\" (
  \"option\" varchar(255) NOT NULL,
  \"value\" varchar(255) NOT NULL,
  PRIMARY KEY  (\"option\")
);";
exec_query($query);

$query=
"CREATE TABLE \"${p}rates\" (
  \"userID\" integer DEFAULT NULL,
  \"projectID\" integer DEFAULT NULL,
  \"activityID\" integer DEFAULT NULL,
  \"rate\" decimal(10,2) NOT NULL,
  PRIMARY KEY(\"userID\", \"projectID\", \"activityID\")
);";
exec_query($query);

$query=
"CREATE TABLE \"${p}fixedRates\" (
  \"projectID\" integer DEFAULT NULL,
  \"activityID\" integer DEFAULT NULL,
  \"rate\" decimal(10,2) NOT NULL,
  PRIMARY KEY(\"projectID\", \"activityID\")
);";
exec_query($query);

$query=
"CREATE TABLE \"${p}expenses\" (
  \"expenseID\" serial NOT NULL PRIMARY KEY,
  \"timestamp\" integer NOT NULL DEFAULT '0',
  \"userID\" integer NOT NULL,
  \"projectID\" integer NOT NULL,
  \"designation\" text NOT NULL,
  \"comment\" text NOT NULL,
  \"commentType\" smallint NOT NULL DEFAULT '0',
  \"refundable\" smallint NOT NULL default '0',
  \"cleared\" smallint NOT NULL DEFAULT '0',
  \"multiplier\" decimal(10,2) NOT NULL DEFAULT '1.00',
  \"value\" decimal(10,2) NOT NULL DEFAULT '0.00'
) ;"
. "create index \"${p}expenses_userID_idx\" on \"${p}expenses\"(\"userID\");"
. "create index \"${p}expenses_projectID_idx\" on \"${p}expenses\"(\"projectID\");"
. "COMMENT ON COLUMN \"${p}expenses\".\"refundable\" is 'expense refundable to employee (0 = no, 1 = yes)';";
exec_query($query);
setAI(${p}.'expenses',1);

$query = 
"CREATE TABLE \"${p}statuses\" (
\"statusID\" serial NOT NULL PRIMARY KEY,
\"status\" VARCHAR( 200 ) NOT NULL
) ";
exec_query($query);
setAI(${p}.'statuses',1);

// The included script only sets up the initial permissions.
// Permissions that were later added follow below.
require("installPermissions.php");

foreach (array('customer', 'project', 'activity', 'group', 'user') as $object) {
  exec_query("ALTER TABLE \"${p}globalRoles\" ADD \"core-$object-otherGroup-view\" smallint DEFAULT 0;");
  exec_query("UPDATE \"${p}globalRoles\" SET \"core-$object-otherGroup-view\" = 1 WHERE \"name\" = 'Admin';");
}

exec_query("INSERT INTO \"${p}statuses\" (\"status\") VALUES ('open'), ('review'), ('closed');");

// GROUPS
$defaultGroup=$kga['lang']['defaultGroup'];
$query="INSERT INTO \"${p}groups\" (\"name\") VALUES ('admin');";
exec_query($query);



// MISC
$query="INSERT INTO \"${p}activities\" ( \"name\", \"comment\") VALUES ( '".$kga['lang']['testActivity']."', '');";
exec_query($query);

$query="INSERT INTO \"${p}customers\" ( \"name\", \"comment\", \"company\", \"vat\", \"contact\", \"street\", \"zipcode\", \"city\", \"phone\", \"fax\", \"mobile\", \"mail\", \"homepage\", \"timezone\") VALUES ( '".$kga['lang']['testCustomer']."', '', '', '', '', '', '', '', '', '', '', '','',".quoteForSql($_REQUEST['timezone']).");";
exec_query($query);

$query="INSERT INTO \"${p}projects\" ( \"customerID\", \"name\", \"comment\") VALUES ( 1, '".$kga['lang']['testProject']."', '');";
exec_query($query);


// ADMIN USER
$adminPassword =  md5($kga['password_salt'].'changeme'.$kga['password_salt']);
setAI(${p}.'users', $randomAdminID + 2); //why random id in a ai field?
$query="INSERT INTO \"${p}users\" (\"userID\",\"name\",\"mail\",\"password\", \"globalRoleID\" ) VALUES ('$randomAdminID','admin','admin@yourwebspace.de','$adminPassword',1);";
exec_query($query);

$query="INSERT INTO \"${p}preferences\" (\"userID\",\"option\",\"value\") VALUES
('$randomAdminID','ui.rowlimit','100'),
('$randomAdminID','ui.skin','standard'),
('$randomAdminID','ui.showCommentsByDefault','0'),
('$randomAdminID','ui.hideOverlapLines','1'),
('$randomAdminID','ui.showTrackingNumber','1'),
('$randomAdminID','timezone',".quoteForSql($_REQUEST['timezone']).");";
exec_query($query);


// CROSS TABLES
$query="INSERT INTO \"${p}groups_users\" (\"groupID\",\"userID\", \"membershipRoleID\") VALUES ('1','$randomAdminID','1');";
exec_query($query);

$query="INSERT INTO \"${p}groups_activities\" (\"groupID\", \"activityID\") VALUES (1, 1);";
exec_query($query);

$query="INSERT INTO \"${p}groups_customers\" (\"groupID\", \"customerID\") VALUES (1, 1);";
exec_query($query);

$query="INSERT INTO \"${p}groups_projects\" (\"groupID\", \"projectID\") VALUES (1, 1);";
exec_query($query);



// VARS
$query="INSERT INTO \"${p}configuration\" (\"option\", \"value\") VALUES ('version', '" . $kga['version'] . "');";
exec_query($query);

$query="INSERT INTO \"${p}configuration\" (\"option\", \"value\") VALUES ('login', '1');";
exec_query($query);

$query="INSERT INTO \"${p}configuration\" (\"option\", \"value\") VALUES ('kimail', 'kimai@yourwebspace.com');";
exec_query($query);

$query="INSERT INTO \"${p}configuration\" (\"option\", \"value\") VALUES ('adminmail', 'admin@yourwebspace.com');";
exec_query($query);

$query="INSERT INTO \"${p}configuration\" (\"option\", \"value\") VALUES ('loginTries', '3');";
exec_query($query);

$query="INSERT INTO \"${p}configuration\" (\"option\", \"value\") VALUES ('loginBanTime', '900');";
exec_query($query);

$query="INSERT INTO \"${p}configuration\" (\"option\", \"value\") VALUES ('lastdbbackup', '0');";
exec_query($query);

$query="INSERT INTO \"${p}configuration\" (\"option\", \"value\") VALUES ('revision', '" . $kga['revision'] . "');";
exec_query($query);

exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('currency_name','Euro')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('currency_sign','€')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('currency_first','0')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('show_sensible_data','0')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('show_update_warn','1')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('check_at_startup','0')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('show_daySeperatorLines','1')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('show_gabBreaks','0')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('show_RecordAgain','1')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('show_TrackingNr','1')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('date_format_0','%d.%m.%Y')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('date_format_1','%d.%m.')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('date_format_2','%d.%m.%Y')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('language','$kga[language]')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('roundPrecision','0')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('decimalSeparator',',')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('durationWithSeconds','0')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('exactSums','0')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('defaultVat','0')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\",\"value\") VALUES('editLimit','-')");
exec_query("INSERT INTO \"${p}configuration\" (\"option\" ,\"value\") VALUES ('roundTimesheetEntries', '0' );");
exec_query("INSERT INTO \"${p}configuration\" (\"option\" ,\"value\") VALUES ('roundMinutes', '0');");
exec_query("INSERT INTO \"${p}configuration\" (\"option\" ,\"value\") VALUES ('roundSeconds', '0');");
exec_query("INSERT INTO \"${p}configuration\" (\"option\" ,\"value\") VALUES ('allowRoundDown', '0');");
exec_query("INSERT INTO \"${p}configuration\" (\"option\" ,\"value\") VALUES ('defaultStatusID', '1');");

if ($errors) {

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(WEBROOT . '/libraries/'),
        )
    )
);

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

$view = new Zend_View();
$view->setBasePath(WEBROOT . '/templates');

    $view->headline = $kga['lang']['errors'][1]['hdl'];
    $view->message = $kga['lang']['errors'][1]['txt'];
    echo $view->render('misc/error.php');
    Logger::logfile("-- showing install error --------------------------");
} else {
    Logger::logfile("-- installation finished without error ------------");
    header("Location: ../index.php");
}
?>
