<?php
	header('Content-type: text/css');
	$table_header = "../../../skins/standard/grfx/g3_table_header.png";
?>

#expenses {
    border:1px solid black; 
    margin:0;
    padding:0;
    background-color:#eee;
    position:absolute;
    overflow:auto;
    left:10px;
}



#expenses { 
    top:150px;
    z-index:2;
}

#expenses_head {
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

#expenses_head { top:125px; }

#expenses_head table { border:none; }

#expenses_head td {
    font-weight:bold;
    color:white;
}


