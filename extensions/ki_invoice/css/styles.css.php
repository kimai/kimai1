<?php
	header('Content-type: text/css');
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
    font-size: 16px;
    background-color: #eee;
    color: #000;
    overflow: auto;
}

#invoice_extension_header {
    border: 1px solid #000;
    color: #fff;
    padding: 5px 10px;
    height: 20px;
    overflow: hidden;
    position: absolute;
    
    background: -moz-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* ff3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(102,102,102,1)), color-stop(100%, rgba(51,51,51,1))); /* safari4+,chrome */
	background: -webkit-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* safari5.1+,chrome10+ */
	background: -o-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* opera 11.10+ */
	background: -ms-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* ie10+ */
	background: linear-gradient(180deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* w3c */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#666666', endColorstr='#333333',GradientType=0 ); /* ie6-9 */

}

#invoice_extension_advanced {
    padding: 0 10px;
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

#invoice_extension_advanced div {
    border-bottom: 1px solid #ccc;
    padding: 10px 0;
}

#invoice_extension_advanced div select {
    min-width: 200px;
}

#invoice_extension_advanced label {
    min-width: 200px;
    display: inline-block;
}

#invoice_extension_advanced div#invoice_button {
    border: none;
}