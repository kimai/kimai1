<?php
	header('Content-type: text/css');
	$table_header = "../../../skins/standard/grfx/g3_table_header.png";
	$filter_bg = "../../../skins/standard/grfx/filter_bg.jpg";
?>

#deb_ext_kga_wrap {
    border:1px solid #000;
    position:absolute;
    overflow:hidden;
    margin:0;
}

#deb_ext_kga {
    padding:10px;
    font-size:110%;
    font-family: monospace;
    background-color: #000;
	color:#00D700;
    overflow:auto;
}

#deb_ext_logfile_wrap {
    padding:10px;
    overflow:hidden;
    position:absolute;
}


#deb_ext_logfile_header,
#deb_ext_kga_header {
    background-image: url('<?php echo $table_header; ?>');
    border:1px solid #000;
    color:#fff;
    padding:5px 10px;
    height:20px;
    overflow:hidden;
    position:absolute;
}

#deb_ext_buttons {
    float:right;
}


#deb_ext_logfile_header a {
    color:#fff;
}

#deb_ext_logfile {
    border:1px solid #000;
    padding:10px;
    font-size:110%;
    font-family: monospace;
    background-color: #000;
	color:#00D700;
    overflow:auto;
}


#deb_ext_shoutbox {
    float:right;
    margin-top:-19px;
}
    
#deb_ext_shoutbox input {
    background-image:url('<?php echo $filter_bg; ?>');
    width:150px;
    border:1px solid #555;
    height:12px;
    margin:20px;
    padding:1px;
    color:#fff;
    font-size:9px;
}

#deb_ext_shoutbox input:focus {
    background: #fff;
    color:#333;
}

