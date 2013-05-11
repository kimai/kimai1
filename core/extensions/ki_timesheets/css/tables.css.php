<?php
  header('Content-type: text/css');

	$table_header = "../../../skins/standard/grfx/g3_table_header.png";
	$add =          "../../../skins/standard/grfx/add.png";
	$schraff0 =     "../../../skins/standard/grfx/schraff0.gif";
	$schraff1 =     "../../../skins/standard/grfx/schraff1.gif";
	$schraff2 =     "../../../skins/standard/grfx/schraff2.gif";
?>

div.ki_timesheet table {
    border-collapse: collapse;
    font-size: 11px;
    color: #363636;
    border-bottom:1px solid #888;
}
div.ki_timesheet table a.preselect_lnk {
    color: #363636;
    text-decoration:none;
    border-bottom:1px dotted #bbb;
}

div.ki_timesheet table a:hover {
    color: #0F9E00;
    border-bottom:none;
}

div.ki_timesheet table thead {
    height:25px;
    text-align:left;
    color:#FFF;
}

div.ki_timesheet table thead th {
    background-image: url('<?php echo $table_header; ?>');
}

div.ki_timesheet tr.even td,
div.ki_timesheet tr.odd td
{
    border-bottom: none;
    border-left: none;
    border-right: 1px dotted #CCC;
/*    border-top: 1px solid #DDD;*/
    padding: 3px 4px 4px 5px;
}
#timeSheet_head td {padding: 3px 4px 4px 6px;}

div.ki_timesheet tr.hover td {
    background: #FFC !important;
/*    border-top: 1px solid #666 !important;
    border-bottom: 1px solid #666 !important;*/
}

div.ki_timesheet tr.even td {
    background: #FFF;
}

div.ki_timesheet tr.odd td {
    background: #EEE;
}

div#timeSheetTable tr td.time {
    border-bottom:1px dotted white;;
}

div#timeSheetTable tr.even td.time {
    background: #A4E7A5;
}

div#timeSheetTable tr.odd td.time {
    background: #64BF61;
}

div#timeSheetTable tr.active td.time {
    background: #F00;
}

div.ki_timesheet tr td.option,
div.ki_timesheet tr td.date,
div.ki_timesheet tr td.from,
div.ki_timesheet tr td.to,
div.ki_timesheet tr td.time,
div.ki_timesheet tr td.wage {
    text-align:center;
}

div.ki_timesheet>div#timeSheet>div#timeSheetTable>table>tbody>tr>td.username {
    border-right: none;
}

#timeSheet_head td { white-space:nowrap; }

#timeSheet_head td.option,
#timeSheet td.option
{
    width:70px;
}

#timeSheet_head td.date,
#timeSheet td.date
{
    width:50px;
}

#timeSheet_head td.time,
#timeSheet td.time
{
    width:40px;
}

#timeSheet_head td.wage,
#timeSheet td.wage
{
    width:40px;
}

#timeSheet_head td.from,
#timeSheet_head td.to,
#timeSheet td.from,
#timeSheet td.to
{
    width:50px;
}

div#timeSheet_head div.left
{ 
    position:absolute;
    overflow:hidden;
    width:32px;
    height:20px;
    top:3px;
    left:3px;
}

div#timeSheet_head div.left a
{ 
    background-image: url('<?php echo $add; ?>');
    overflow:hidden;
    display:block;
    width:22px;
    height:16px;
    text-indent:-500px;
}

div#timeSheet_head td {padding: 3px 4px 4px 6px;}


tbody tr.comm0 td {
    padding:5px;
    background: #fff;
    background-image:url('<?php echo $schraff0; ?>');
}

tbody tr.comm1 td {
    padding:5px;
    background: #ff9;
    background-image:url('<?php echo $schraff1; ?>');
}

tbody tr.comm2 td {
    padding:5px;
    background: #f00;
    background-image:url('<?php echo $schraff2; ?>');
    font-weight:bold;
    color:#fff;
}

.break_day {
	border-top:1px solid black;
}
.break_gap {
	border-top:2px dotted #FF6FCF; /* Kathrin wollte das Pink haben ... */
}
.time_overlap {
        border-top:2px solid red;
}



