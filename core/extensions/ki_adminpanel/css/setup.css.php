<?php
	header('Content-type: text/css');
	$table_header = "../../../skins/standard/grfx/g3_table_header.png";
?>

#ap_ext_panel {
    top:120px;
    z-index:2;
}

#ap_ext_panel,
#ap_ext_knd,
#ap_ext_pct,
#ap_ext_evt 
{
    border:1px solid black; 
    margin:0;
    padding:0;
    background-color:#eee;
    position:absolute;
    overflow:auto;
    left:10px;
}

#ap_ext_knd, 
#ap_ext_evt, 
#ap_ext_pct 
{ 
	width:200px;
	background-color:#FFF;
	font-size: 12px;
    height:175px;
    border-top:none;
    z-index:1;
}

#ap_ext_knd_head,
#ap_ext_pct_head,
#ap_ext_evt_head
{
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
    padding:3px 0 0 5px;
}

ap_ext_panel {
	padding:0;
}

