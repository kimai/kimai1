<?php
    header('Content-type: text/css');
?>

#demo_ext_header {
    border: 1px solid black;
    border-bottom: none;

    background: -moz-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* ff3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(102,102,102,1)), color-stop(100%, rgba(51,51,51,1))); /* safari4+,chrome */
	background: -webkit-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* safari5.1+,chrome10+ */
	background: -o-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* opera 11.10+ */
	background: -ms-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* ie10+ */
	background: linear-gradient(180deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* w3c */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#666666', endColorstr='#333333',GradientType=0 ); /* ie6-9 */

    position: absolute;
    height: 25px;
    text-align: left;
    color: #FFF;
    left: 10px;
    font-size: 11px;
    font-weight: bold;
}

div .demo_ext {
    border: 1px solid black;
    padding: 5px;
    margin: 25px 10px 10px;
    color: black;
    background: white;
}

h1 {
    color: #f00;
}

#testdiv {
    padding: 5px;
    color: gray;
    background: yellow;
}
