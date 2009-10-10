<?php
/**
 * This file is part of 
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2009 Kimai-Development-Team
 * 
 * Kimai is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; Version 3, 29 June 2007
 * 
 * Kimai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Kimai; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 */

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/floaters/";
require("../../includes/kspi.php");

if (isset($kga['customer'])) die();

switch ($axAction) {

    // $selected = explode('|',$axValue);

    case "PDF":  
	    // $tpl->assign('comment_types', $comment_types);
	    $tpl->assign('exportPDF', 'exportPDF');
	    $tpl->display("export_PDF.tpl"); 
    break;

    case "XLS":  
	    // $tpl->assign('comment_types', $comment_types);
	    $tpl->assign('exportXLS', 'exportXLS');
	    $tpl->display("export_XLS.tpl"); 
    break;

    case "CSV":  
	    // $tpl->assign('comment_types', $comment_types);
	    $tpl->assign('exportCSV', 'exportCSV');
	    $tpl->display("export_CSV.tpl"); 
    break;

    case "print":  
	    // $tpl->assign('comment_types', $comment_types);
	    $tpl->assign('print', 'print');
	    $tpl->display("print.tpl"); 
    break;

    case "help_timeformat":  
	    // $tpl->assign('comment_types', $comment_types);
	    // $tpl->assign('print', 'print');
	    $tpl->display("help_timeformat.tpl"); 
    break;

}

?>

    