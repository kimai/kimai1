<?php
	header('Content-type: text/css');
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
    border:1px solid #000;
    color:#fff;
    padding:5px 10px;
    height:20px;
    overflow:hidden;
    position:absolute;
    
    background: -moz-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* ff3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(102,102,102,1)), color-stop(100%, rgba(51,51,51,1))); /* safari4+,chrome */
	background: -webkit-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* safari5.1+,chrome10+ */
	background: -o-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* opera 11.10+ */
	background: -ms-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* ie10+ */
	background: linear-gradient(180deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* w3c */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#666666', endColorstr='#333333',GradientType=0 ); /* ie6-9 */
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
    background: -moz-linear-gradient(270deg, rgba(136,136,136,1) 0%, rgba(96,96,96,1) 100%); /* ff3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(136,136,136,1)), color-stop(100%, rgba(96,96,96,1))); /* safari4+,chrome */
	background: -webkit-linear-gradient(270deg, rgba(136,136,136,1) 0%, rgba(96,96,96,1) 100%); /* safari5.1+,chrome10+ */
	background: -o-linear-gradient(270deg, rgba(136,136,136,1) 0%, rgba(96,96,96,1) 100%); /* opera 11.10+ */
	background: -ms-linear-gradient(270deg, rgba(136,136,136,1) 0%, rgba(96,96,96,1) 100%); /* ie10+ */
	background: linear-gradient(180deg, rgba(136,136,136,1) 0%, rgba(96,96,96,1) 100%); /* w3c */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#888888', endColorstr='#606060',GradientType=0 ); /* ie6-9 */

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

