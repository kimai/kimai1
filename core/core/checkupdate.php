<?php
error_reporting(0);

if(file_exists(realpath(dirname(__FILE__).'/../includes/conf.php')))
	require_once(realpath(dirname(__FILE__).'/../includes/conf.php'));
require('../includes/autoconf.php');
require('../includes/vars.php');
require('../includes/func.php');   
// check the latest stable version of Kimai on the web
if ($kga['check_at_startup'] || $_REQUEST['versionping']) {
    $request = join( '', file('http://versioncheck.kimai.org?revision='.$kga['revision']."&lang=".$kga['language']));
    echo $request;
}
?>