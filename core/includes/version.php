<?php
$kga['version']  = "0.9.3";
// $kga['revision'] is the sourceforge SVN revision.
// After the migration to git this number will be used as the database
// revision number. It is incremented whenever the database changes.
// fcw: 2012-04-25: 503 (wird dann nach Durchlauf des updater.php in der DB / svn_var / revision: 1369)
$kga['revision'] = '0503';
// when Kimai moved from syncom to sourceforge we had r866 ...
$kga['revision'] = (int)$kga['revision'] += 866;
$kga['status']   = "development version"; // leave blank if stable ...
?>
