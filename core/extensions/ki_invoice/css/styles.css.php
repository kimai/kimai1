<?php
	header('Content-type: text/css');
	$table_header = "../../../skins/standard/grfx/g3_table_header.png";
?>

#iv_ext_wrap {
    border:1px solid #000;
	border-top:none;
    position:absolute;
    overflow:hidden;
    margin:0;
}

#iv_ext {
    padding:10px;
    font-size: 20px;
    background-color: #eee;
    color:#000;
    overflow:auto;
}


#iv_ext_header {
    background-image: url('<?php echo $table_header; ?>');
    border:1px solid #000;
    color:#fff;
    padding:5px 10px;
    height:20px;
    overflow:hidden;
    position:absolute;
}

#iv_ext_advanced {
   padding:10px;
 }

#iv_timespan {
   padding: 20px 0px;
}


#iv_button {
   padding: 20px 0px;
}

#iv_screenshot
{
float:left;	
margin-right:10px;
}