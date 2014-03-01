<?php

function buildRoleTableCreateQuery($tableName, $idColumnName, $permissions) {
  global $p;
  $query = 
  "CREATE TABLE `${p}${tableName}` (
  `${idColumnName}` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR( 255 ) NOT NULL,";

  $permissionColumns = array();
  $permissionColumnDefinitions = array();
  foreach ($permissions as $permission) {
    $permissionColumns[] = '`'.$permission.'`';
    $permissionColumnDefinitions[] = '`'.$permission.'` TINYINT DEFAULT 0';
  }
  $query .= implode(', ', $permissionColumnDefinitions);

  $query .= ") ENGINE = InnoDB ";

  return $query;
}

function buildRoleInsertQuery($tableName, $roleName, $allowedPermissions, $allPermissions) {
  global $p;
  foreach  ($allowedPermissions as &$permission)
    $permission = '`'.$permission.'`';

  if (count($allowedPermissions) == 0)
    $query="INSERT INTO `${p}${tableName}` (`name`)  VALUES ('". $roleName . "');";
  else
    $query="INSERT INTO `${p}${tableName}` (`name`, " . implode(', ', $allowedPermissions) . ")  VALUES ('". $roleName . "', ".
    implode(', ', array_fill(0,count($allowedPermissions),'1')) . ");";
  return $query;
}

// Global roles table
$globalPermissions = array();

$membershipPermissions = array();

// extension permissions
foreach (array('deb_ext', 'adminPanel_extension', 'ki_budget', 'ki_expenses', 'ki_export', 'ki_invoice', 'ki_timesheet', 'demo_ext') as $extension)
  $globalPermissions[] = $extension. '-access';

// domain object permissions
foreach (array('customer', 'project', 'activity', 'user') as $object)
  foreach (array('add', 'edit', 'delete', 'assign', 'unassign') as $action) {
    $globalPermissions[] = 'core-' . $object . '-otherGroup-' . $action;
    $membershipPermissions[] = 'core-' .  $object . '-' . $action;
  }

// status permissions
foreach (array('add', 'edit', 'delete') as $action)
  $globalPermissions[] = 'core-status-' . $action;

// group permissions
$globalPermissions[] = 'core-group-add';
$globalPermissions[] = 'core-group-otherGroup-edit';
$globalPermissions[] = 'core-group-otherGroup-delete';
$membershipPermissions[] = 'core-user-view';
$membershipPermissions[] = 'core-group-edit';
$membershipPermissions[] = 'core-group-delete';

// adminpanel permissions
$globalPermissions[] = 'adminPanel_extension-editAdvanced';

// timesheet permissions
$globalPermissions[] = 'ki_timesheets-ownEntry-add';
$membershipPermissions[] = 'ki_timesheets-otherEntry-ownGroup-add';
$globalPermissions[] = 'ki_timesheets-otherEntry-otherGroup-add';
$globalPermissions[] = 'ki_timesheets-ownEntry-edit';
$membershipPermissions[] = 'ki_timesheets-otherEntry-ownGroup-edit';
$globalPermissions[] = 'ki_timesheets-otherEntry-otherGroup-edit';
$globalPermissions[] = 'ki_timesheets-ownEntry-delete';
$membershipPermissions[] = 'ki_timesheets-otherEntry-ownGroup-delete';
$globalPermissions[] = 'ki_timesheets-otherEntry-otherGroup-delete';

$globalPermissions[] = 'ki_timesheets-showRates';
$globalPermissions[] = 'ki_timesheets-editRates';

// expenses permissions
$globalPermissions[] = 'ki_expenses-ownEntry-add';
$membershipPermissions[] = 'ki_expenses-otherEntry-ownGroup-add';
$globalPermissions[] = 'ki_expenses-otherEntry-otherGroup-add';
$globalPermissions[] = 'ki_expenses-ownEntry-edit';
$membershipPermissions[] = 'ki_expenses-otherEntry-ownGroup-edit';
$globalPermissions[] = 'ki_expenses-otherEntry-otherGroup-edit';
$globalPermissions[] = 'ki_expenses-ownEntry-delete';
$membershipPermissions[] = 'ki_expenses-otherEntry-ownGroup-delete';
$globalPermissions[] = 'ki_expenses-otherEntry-otherGroup-delete';


$query = buildRoleTableCreateQuery('globalRoles', 'globalRoleID', $globalPermissions);
exec_query($query);

// global admin role
$query = buildRoleInsertQuery('globalRoles', 'Admin', $globalPermissions, $globalPermissions);
exec_query($query);
$globalAdminRoleID = mysql_insert_id();

// global user role
$allowedPermissions = array(
  'ki_budget-access',
  'ki_expenses-access',
  'ki_export-access',
  'ki_invoice-access',
  'ki_timesheet-access',
  'ki_timesheets-showRates',
  'ki_timesheets-ownEntry-add',
  'ki_timesheets-ownEntry-edit',
  'ki_timesheets-ownEntry-delete',
  'ki_expenses-ownEntry-add',
  'ki_expenses-ownEntry-edit',
  'ki_expenses-ownEntry-delete',
);
$query = buildRoleInsertQuery('globalRoles', 'User', $allowedPermissions, $globalPermissions);
exec_query($query);
$globalUserRoleID = mysql_insert_id();



$query = buildRoleTableCreateQuery('membershipRoles', 'membershipRoleID', $membershipPermissions);
exec_query($query);

// membership admin role
$query = buildRoleInsertQuery('membershipRoles', 'Admin', $membershipPermissions, $membershipPermissions);
exec_query($query);
$membershipAdminRoleID = mysql_insert_id();

// membership user role
$allowedPermissions = array();
$query = buildRoleInsertQuery('membershipRoles', 'User', $allowedPermissions, $membershipPermissions);
exec_query($query);
$membershipUserRoleID = mysql_insert_id();

// membership groupleader role
$allowedPermissions = array_merge($allowedPermissions, array(
  'ki_timesheets-otherEntry-ownGroup-add',
  'ki_timesheets-otherEntry-ownGroup-edit',
  'ki_timesheets-otherEntry-ownGroup-delete',
  'ki_expenses-otherEntry-ownGroup-add',
  'ki_expenses-otherEntry-ownGroup-edit',
  'ki_expenses-otherEntry-ownGroup-delete',
));
$query = buildRoleInsertQuery('membershipRoles', 'Groupleader', $allowedPermissions, $membershipPermissions);
exec_query($query);
$membershipGroupleaderRoleID = mysql_insert_id();

?>