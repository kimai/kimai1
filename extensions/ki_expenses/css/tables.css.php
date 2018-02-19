<?php
	header('Content-type: text/css');
	$add = "../../../skins/standard/grfx/add.png";
?>

div.ki_expenses table {
    border-collapse: collapse;
    font-size: 11px;
    color: #363636;
    border-bottom:1px solid #888;
}
div.ki_expenses table a.preselect_lnk {
    color: #363636;
    text-decoration:none;
    border-bottom:1px dotted #bbb;
}

div.ki_expenses table a:hover {
    color: #0F9E00;
    border-bottom:none;
}

div.ki_expenses table thead {
    height:25px;
    text-align:left;
    color:#FFF;
}

div.ki_expenses table thead th {
    background: -moz-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* ff3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(102,102,102,1)), color-stop(100%, rgba(51,51,51,1))); /* safari4+,chrome */
	background: -webkit-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* safari5.1+,chrome10+ */
	background: -o-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* opera 11.10+ */
	background: -ms-linear-gradient(270deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* ie10+ */
	background: linear-gradient(180deg, rgba(102,102,102,1) 0%, rgba(51,51,51,1) 100%); /* w3c */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#666666', endColorstr='#333333',GradientType=0 ); /* ie6-9 */
}

div.ki_expenses tr.even td,
div.ki_expenses tr.odd td
{
    border-bottom: none;
    border-left: none;
    border-right: 1px dotted #CCC;
/*    border-top: 1px solid #DDD;*/
    padding: 3px 4px 4px 5px;
}
#expenses_head td {padding: 3px 4px 4px 6px;}

div.ki_expenses tr.hover td {
    background: #FFC !important;
/*    border-top: 1px solid #666 !important;
    border-bottom: 1px solid #666 !important;*/
}

div.ki_expenses tr.even td {
    background: #FFF;
}

div.ki_expenses tr.odd td {
    background: #EEE;
}

div#expensestable tr td.time {
    border-bottom:1px dotted white;;
}


div#expensestable tr.even td.value {
    background: #A4E7A5;
}

div#expensestable tr.odd td.value {
    background: #64BF61;
}

div.ki_expenses tr td.option,
div.ki_expenses tr td.date,
div.ki_expenses tr td.time {
    text-align:center;
}

div.ki_expenses a {
    margin: 0 3px;
}

div.ki_expenses>div#expenses>div#expensestable>table>tbody>tr>td.activity {
    border-right: none;
}


#expenses_head td { white-space:nowrap; }

#expenses_head td.option,
#expenses td.option
{
    width:70px;
}

#expenses_head td.date,
#expenses td.date
{
    width:50px;
}

#expenses_head td.time,
#expenses td.time
{
    width:40px;
}

#expenses_head td.value,
#expenses td.value
{
    width:40px;
}

#expenses_head td.designation,
#expenses td.designation
{
    min-width:20px;
}

div#expenses_head div.left
{ 
    position:absolute;
    overflow:hidden;
    width:32px;
    height:20px;
    top:3px;
    left:3px;
}

div#expenses_head div.left a
{ 
    background-image: url('<?php echo $add; ?>');
    overflow:hidden;
    display:block;
    width:22px;
    height:16px;
    text-indent:-500px;
}

div#expenses_head td {padding: 3px 4px 4px 6px;}



