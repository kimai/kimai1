<?php
	header('Content-type: text/css');
	$table_header = "../../../skins/standard/grfx/g3_table_header.png";
?>

#xp {
    border:1px solid black; 
    margin:0;
    padding:0;
    background-color:#eee;
    position:absolute;
    overflow:auto;
    left:10px;
}

#xp { 
    top:200px;
    z-index:2;
}

#export_head {
    border:1px solid black; 
    border-bottom:none;
    background-image: url('<?php echo $table_header; ?>');
    position:absolute;
    height:25px;
    text-align:left;
    color:#FFF;
    left:10px;
    font-size:11px;
    font-weight:bold;
}

#export_head { top:175px; }

#export_head table { border:none; }

#export_head td {
    font-weight:bold;
    color:white;
}

.export_time_help_table {
  border-collapse:collapse;
}

.export_time_description {
    border-bottom:1px solid gray;
}

.export_time_shortcut {
    font-weight:bold;
    border-bottom:1px solid gray;
    padding-right:10px;
}