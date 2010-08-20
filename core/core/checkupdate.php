<?php
+/**
+ * Query the Kimai project server for information about a new version.
+ * The response will simply be passed through.
+ */
error_reporting(0);
require('../includes/basics.php');

// check the latest stable version of Kimai on the web
$request = join( '', file('http://versioncheck.kimai.de?revision='.$kga['revision']."&lang=".$kga['language']));
echo strip_tags($request, '<span><a>');
?>