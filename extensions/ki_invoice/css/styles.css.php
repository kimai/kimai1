<?php
	header('Content-type: text/css');
	$table_header = "../../../skins/standard/grfx/g3_table_header.png";
?>

#invoice_extension_wrap {
    border: 1px solid #000;
    border-top: none;
    position: absolute;
    overflow: hidden;
    margin: 0;
}

#invoice_extension {
    padding: 10px;
    font-size: 20px;
    background-color: #eee;
    color: #000;
    overflow: auto;
}

#invoice_extension_header {
    background-image: url('<?php echo $table_header; ?>');
    border: 1px solid #000;
    color: #fff;
    padding: 5px 10px;
    height: 20px;
    overflow: hidden;
    position: absolute;
}

#invoice_extension_advanced {
    padding: 10px;
}

#invoice_timespan {
    padding: 20px 0;
}

#invoice_button {
    padding: 20px 0;
}

#invoice_screenshot {
    float: left;
    margin-right: 10px;
}