<?php
  header('Content-type: text/css');

	$table_header = "../../../skins/standard/grfx/g3_table_header.png";
?>

#timeSheet {
    border:1px solid black; 
    margin:0;
    padding:0;
    background-color:#eee;
    position:absolute;
    overflow:auto;
    left:10px;
}



#timeSheet { 
    top:150px;
    z-index:2;
}

#timeSheet_head {
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

#timeSheet_head { top:125px; }

#timeSheet_head table { border:none; }

#timeSheet_head td {
    font-weight:bold;
    color:white;
}


