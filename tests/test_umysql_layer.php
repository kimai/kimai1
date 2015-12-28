<?php

// ==================================
// = implementing standard includes =
// ==================================
include('../includes/basics.php');
// $usr = checkUser();

// append (!) config to $kga
get_config($usr['usr_ID']);

echo "waahh!<br/>";

// echo $usr['usr_ID'] . "<br/>";
// ----> 234268989

$in    = "1200000000";
$out   = "1220252446";



// $dings =  get_arr_grp();

$dings =  grp_get_ldrs(1);

echo "<pre>";
print_r($dings);
// echo $dings;
echo "</pre>";


$version_temp  = get_DBversion();
$versionDB  = $version_temp[0];
$revisionDB = $version_temp[1];
unset($version_temp);

echo "--------------" . $revisionDB;


/*

$dings = getjointime(234268989);

echo "<pre>";
print_r($dings);
// echo $dings;
echo "</pre>";
*/



/*
// $dings = get_arr_time_evt(234268989,$in,$out);
// $dings = get_zef_time_year(2008,234268989);

// $dings = get_DBversion();
// $dings = get_seq("admin");
$dings = get_arr_grp();
    echo "<pre>";
    print_r($dings);
    echo $dings;
    echo "</pre>";
    
  */  






/*
$dings = get_arr_time_pct(234268989,$in,$out);

    echo "<pre>";
    print_r($dings);
    echo "</pre>";
*/





/*

$dings = get_arr_time_knd(234268989,$in,$out);
echo "<pre>";
print_r($dings);
echo "</pre>";

*/





/*

$dings = get_arr_knd(1);
echo "<pre>";
print_r($dings);
echo "</pre>";

*/






/*

$dings = get_entry_zef(55);
echo "<pre>";
print_r($dings);
echo "</pre>";

*/







/*


$limit = 100;


$arr = get_arr_zef(234268989,$in,$out,$limit);


echo "<pre>";
print_r($arr);
echo "</pre>";

*/







/*

$dings = get_event_last(234268989);

echo "<pre>";
print_r($dings);
echo "</pre>";

*/







/*

$values     ['pct_name']            = "iuiui";
$values     ['pct_comment']         = "blabla";
$values     ['pct_logo']            = "";

$values     ['pct_kndID']           = 42;
                                  
$values     ['pct_visible']         = 1;
$values     ['pct_filter']          = 0;

$id = pct_create($values);


echo $id;
*/





/*
$values     ['knd_name']            = "fffff";
knd_edit(38, $values);
$test = knd_get_data(38);
*/


// echo time();
// 
// echo "<pre>";
// 
// print_r($usr);
// print_r($kga);
// echo "</pre>";



// $m = get_rec_state(25369545);


// 
// $m = get_arr_knd("all");
// echo "<pre>";
// print_r($m);
// echo "</pre>";



// echo $conn->GetHTML();


// 
// $rr= get_DBversion();
// echo $rr;
// echo $rr[0] ." ";
// echo $rr[1];

// echo "<pre>";
// print_r($test);
// echo "</pre>";
// 




























/*
UPDATER: 

ALTER TABLE `kimai_knd` 
    CHANGE `knd_ID` `knd_ID` INT(10) NOT NULL AUTO_INCREMENT,
    CHANGE `knd_name` `knd_name` VARCHAR(255) NOT NULL,
    CHANGE `knd_comment` `knd_comment` TEXT NULL DEFAULT NULL,
    CHANGE `knd_company` `knd_company` VARCHAR(255) NULL DEFAULT NULL,
    CHANGE `knd_street` `knd_street` VARCHAR(255) NULL DEFAULT NULL,
    CHANGE `knd_zipcode` `knd_zipcode` VARCHAR(255) NULL DEFAULT NULL,
    CHANGE `knd_city` `knd_city` VARCHAR(255) NULL DEFAULT NULL,
    CHANGE `knd_tel` `knd_tel` VARCHAR(255) NULL DEFAULT NULL,
    CHANGE `knd_fax` `knd_fax` VARCHAR(255) NULL DEFAULT NULL,
    CHANGE `knd_mobile` `knd_mobile` VARCHAR(255) NULL DEFAULT NULL,
    CHANGE `knd_mail` `knd_mail` VARCHAR(255) NULL DEFAULT NULL,
    CHANGE `knd_homepage` `knd_homepage` VARCHAR(255) NULL DEFAULT NULL,
    CHANGE `knd_logo` `knd_logo` VARCHAR(255) NULL DEFAULT NULL
    ;
    
    
     ALTER TABLE `kimai_pct` CHANGE `pct_comment` `pct_comment` TEXT NULL;
     ALTER TABLE `kimai_evt` CHANGE `evt_comment` `evt_comment` TEXT NULL;
     
     
     
     
*/
?>