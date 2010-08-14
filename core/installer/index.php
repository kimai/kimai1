<?php
$installsteps=9;
$kga = array();
require("../includes/version.php");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="styles.css" type="text/css" media="screen" title="no title" charset="utf-8">
    <script src="../libraries/jQuery/jquery-1.3.2.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="installscript.js" type="text/javascript" charset="utf-8"></script>
    <title>Kimai Installation</title>
    
    <script type="text/javascript" charset="utf-8">
        var step         = 1;
        var back         = "";
        var new_database = -1;
        var language     = "";
        var db_layer     = "";
        var database     = "";
        var db_type      = "";
        var prefix       = "";
        var hostname     = "";
        var username     = "";
        var password     = "";
        var timezone     = "";
    </script>
</head>

<body>
    <div id="wrapper" class="invisible">
        <div id="header">
        <div id="progressbar">
        <?php
            for ($i=0;$i<$installsteps;$i++) {
                echo "<span class=\"step_nope\">&nbsp;</span>";
            }
            $width = $i*15;
            echo"<script type=\"text/javascript\" charset=\"utf-8\">
                $('#progressbar').css('width','${width}px');
            </script>";
        ?>
        </div>
        <h1>Installation <?php echo "v".$kga['version'].".".$kga['revision'] ?></h1>
        </div>
        <div id="body">
            
            <div id="jswarn">
                JavaScript MUST be activated!<br/>
                JavaScript MUSS aktiviert sein!
            </div>
            
            <div class = "invisible" id = "installsteps">
                <?php include 'steps/10_language.php'; ?>
            </div>      
        </div>
        <div id="footer" class="invisible"></div>
    </div>
</body>
</html>