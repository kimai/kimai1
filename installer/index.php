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

$installsteps = 8;
$kga = array();
require("../includes/version.php");
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link type="text/css" rel="stylesheet" href="styles.css">
    <script type="text/javascript" src="../libraries/jQuery/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="installscript.js"></script>
    <title>Kimai Installation</title>
    
    <script type="text/javascript">
        var step = 1;
        var current = 0;
        var back = '';
        var target = '';
        var new_database = false;
        var language = '';
        var database = '';
        var prefix = '';
        var hostname = '';
        var username = '';
        var password = '';
        var timezone = '';
    </script>
</head>
<body>
    <div id="wrapper" class="invisible">
        <div id="header">
            <div id="progressbar">
            <?php
                for ($i = 0; $i < $installsteps; $i++) {
                    echo "<span class=\"step_nope\">&nbsp;</span>";
                }
                $width = $i * 15;
                echo "<script type=\"text/javascript\">
                    $('#progressbar').css('width','${width}px');
                </script>";
            ?>
            </div>
            <h1>Installation <?php echo "v" . $kga['version'] . "." . $kga['revision'] ?></h1>
        </div>
        <div id="body">
            <div id="jswarn">
                JavaScript MUST be activated!<br/>
                JavaScript MUSS aktiviert sein!
            </div>
            <div class="invisible" id="installsteps">
                <?php include 'steps/10_language.php'; ?>
            </div>      
        </div>
        <div id="footer" class="invisible"></div>
    </div>
</body>
</html>