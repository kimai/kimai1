<?php
	header('Content-type: text/css');
	$table_header = "../../../skins/standard/grfx/g3_table_header.png";
?>

#bgt {
    border:1px solid black; 
    margin:10px;
    background-color:#eee;
    overflow:auto;
}

#bgt { 
    top:150px;
    z-index:2;
}

.project_head {
        background-image: url('<?php echo $table_header; ?>');
        color:#FFF;
        font-size:11px;
        font-weight:bold;
        padding:3px 4px 4px 6px;
        text-align:center;
}

.bgt_project {
  float:left;
  /*min-width:150px;
  height:150px;*/
  background-color:#fafafa;
  border:1px solid black;
  margin:5px;
  padding:2px;
}

.bgt_project_end {
  clear:both;
}

.keys {
  background-color:#fafafa;
  border:1px solid black;  
  margin:5px;
}

.key {
  float:left;
  white-space:nowrap;
  padding:5px;
  font-size: smaller;
}

.key_color {
  float:left;
  width:1em;
  height:1em;
  margin-right: 5px;
  border: 1px solid black;
}

.key_end {
  clear:both;
}