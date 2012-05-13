<?php
	header('Content-type: text/css');
	$table_header = "../../../skins/standard/grfx/g3_table_header.png";
?>

#adminPanel_extension_panel {
    top:120px;
    z-index:2;
}

#adminPanel_extension_panel,
#adminPanel_extension_knd,
#adminPanel_extension_pct,
#adminPanel_extension_evt 
{
    border:1px solid black; 
    margin:0;
    padding:0;
    background-color:#eee;
    position:absolute;
    overflow:auto;
    left:10px;
}

#adminPanel_extension_knd, 
#adminPanel_extension_evt, 
#adminPanel_extension_pct 
{ 
	width:200px;
	background-color:#FFF;
	font-size: 12px;
    height:175px;
    border-top:none;
    z-index:1;
}

#adminPanel_extension_knd_head,
#adminPanel_extension_project_head,
#adminPanel_extension_activity_head
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

adminPanel_extension_panel {
	padding:0;
}

