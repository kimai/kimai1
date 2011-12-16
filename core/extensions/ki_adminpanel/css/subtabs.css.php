<?php
	header('Content-type: text/css');
	$table_header         = "../../../skins/standard/grfx/g3_table_header.png";
	$table_header_lighter = "../../../skins/standard/grfx/g3_table_header_lighter.png";
?>

.ap_ext_panel_header {
  margin: 0;
  padding: 0;
}
.ap_ext_panel_header a {
  background-image: url('<?php echo $table_header_lighter; ?>');
  height: 19px;
  text-align: left;
  color: #333333;
  font-size: 11px;
  display: block;
  padding: 3px;
  height: 19px;
}
div.active .ap_ext_panel_header a {
  background-image: url('<?php echo $table_header; ?>');
  color: #ffffff !important;
  font-weight: bold;
}
div.active .ap_ext_panel_header a:hover {
  background-image: url('<?php echo $table_header; ?>');
  color: #ffffff;
}
div.active span.ap_ext_accordeon_triangle {
  background: url("../grfx/accordion_active.png") no-repeat;
  background-position: 0px 8px;
}
.ap_ext_subtab {
  border-bottom: 1px solid black;
  overflow: auto;
  padding: 10px;
}
span.ap_ext_accordeon_triangle {
  padding: 5px;
  background: url("../grfx/accordion.png") no-repeat;
  background-position: 0px 8px;
}
.ap_ext_advanced_row { margin-bottom: 3px; }
