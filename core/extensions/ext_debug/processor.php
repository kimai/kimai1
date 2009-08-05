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
 * along with Kimai; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 */

// ===================
// = DEBUG PROCESSOR =
// ===================

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require("../../includes/kspi.php");



switch ($axAction) {
    
    case "reloadLogfile":    
        $logdatei=WEBROOT."temporary/logfile.txt";
        $fh = fopen($logdatei, 'r');
        
        $theData = "";
        $i = 0;
        
        $lines = $kga['logfile_lines'];
        $filearray ="";
        
        while (!feof($fh)) {
            $filearray[$i] = fgets($fh);
            $i++;
        }

        fclose($fh);

        if ($kga['logfile_lines'] !="@") {
            $start = count($filearray);
            $goal = $start-$lines;
            for ($line=$start-1; ($line>$goal && $line>0); $line--) {
                if ($filearray[$line]!="") $theData .= $filearray[$line] ."<br/>";
            }
        } else {
            foreach ($filearray as $line) {
                if ($line!="") $theData .= $line ."<br/>";
            }
        }
        
        echo $theData;
    break;
    
    case "clearLogfile":
        if ($kga['delete_logfile']) {
            $logdatei=fopen(WEBROOT."temporary/logfile.txt","w");
            fwrite($logdatei,"");
            fclose($logdatei);
            echo $theData;
        } else {
            die();
        }
    break;

    case "shoutbox":
        logfile("text: " .$axValue);
    break;



    
}

?>
