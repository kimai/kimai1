<?php
  header('Content-type: text/css');

	$table_header = "../../../skins/standard/grfx/g3_table_header.png";
?>

#zef {
    border:1px solid black; 
    margin:0;
    padding:0;
    background-color:#eee;
    position:absolute;
    overflow:auto;
    left:10px;
}



#zef { 
    top:150px;
    z-index:2;
}

#zef_head {
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

#zef_head { top:125px; }

#zef_head table { border:none; }

#zef_head td {
    font-weight:bold;
    color:white;
}


