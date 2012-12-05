<?php

header("Content-Type: text/html; charset=utf-8");

$path    = realpath(dirname(__FILE__).'/../language/').'/';
$compare = include($path.'de.php');

$allLocales = glob($path.'*.php');

foreach($allLocales as $fullFile)
{
    $fileName = basename($fullFile);
    $locale   = str_replace('.php', '', strtolower($fileName));

    if ($locale == 'de') continue;

    $cmpTo = include($fullFile);
    $result = array_diff_key($compare, $cmpTo);

    if (empty($result)) {
        echo '<p style="color:green">No translation missing!</p>';
        continue;
    }

    if (isset($result['credits'])) {
        $result['credits'] = htmlspecialchars($result['credits']);
    }

    echo '<h1>' . $locale . '</h1>';
    echo '<p style="color:red">Missing translations in file: <b>'.$fileName.'</b></p>';

    echo '<pre>';
    print_r($result);
    echo '</pre>';
}
