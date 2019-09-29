<?php
	header('Content-type: text/css');
?>

.adminPanel_extension_panel_header {
  margin: 0;
  padding: 0;
}
.adminPanel_extension_panel_header a {
	background: -moz-linear-gradient(90deg, rgba(188,188,188,1) 0%, rgba(188,188,188,1) 45%, rgba(213,213,213,1) 65%, rgba(213,213,213,1) 100%); /* ff3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(213,213,213,1)), color-stop(35%, rgba(213,213,213,1)), color-stop(55%, rgba(188,188,188,1)), color-stop(100%, rgba(188,188,188,1))); /* safari4+,chrome */
	background: -webkit-linear-gradient(90deg, rgba(188,188,188,1) 0%, rgba(188,188,188,1) 45%, rgba(213,213,213,1) 65%, rgba(213,213,213,1) 100%); /* safari5.1+,chrome10+ */
	background: -o-linear-gradient(90deg, rgba(188,188,188,1) 0%, rgba(188,188,188,1) 45%, rgba(213,213,213,1) 65%, rgba(213,213,213,1) 100%); /* opera 11.10+ */
	background: -ms-linear-gradient(90deg, rgba(188,188,188,1) 0%, rgba(188,188,188,1) 45%, rgba(213,213,213,1) 65%, rgba(213,213,213,1) 100%); /* ie10+ */
	background: linear-gradient(0deg, rgba(188,188,188,1) 0%, rgba(188,188,188,1) 45%, rgba(213,213,213,1) 65%, rgba(213,213,213,1) 100%); /* w3c */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#D5D5D5', endColorstr='#BCBCBC',GradientType=0 ); /* ie6-9 */

  height: 19px;
  text-align: left;
  color: #333333;
  font-size: 11px;
  display: block;
  padding: 3px;
  height: 19px;
  cursor: pointer;
}
div.active .adminPanel_extension_panel_header a {
	background: -moz-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* ff3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(102,102,102,1)), color-stop(100%, rgba(51,51,51,1))); /* safari4+,chrome */
	background: -webkit-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* safari5.1+,chrome10+ */
	background: -o-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* opera 11.10+ */
	background: -ms-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* ie10+ */
	background: linear-gradient(180deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* w3c */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#666666', endColorstr='#333333',GradientType=0 ); /* ie6-9 */

	color: #ffffff !important;
	font-weight: bold;
	cursor: default;
}
div.active .adminPanel_extension_panel_header a:hover {
  color: #ffffff;
}
div.active span.adminPanel_extension_accordeon_triangle {
  background: url("../grfx/accordion_active.png") no-repeat;
  background-position: 0px 8px;
}
.adminPanel_extension_subtab {
  border-bottom: 1px solid black;
  overflow: auto;
  padding: 10px;
}
span.adminPanel_extension_accordeon_triangle {
  padding: 5px;
  background: url("../grfx/accordion.png") no-repeat;
  background-position: 0px 8px;
}
.adminPanel_extension_advanced_row { margin-bottom: 3px; }
