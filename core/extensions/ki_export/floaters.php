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
 * along with Kimai; If not, see <http://www.gnu.org/licenses/>.
 */

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/floaters/";
require("../../includes/kspi.php");

switch ($axAction) {

    case "PDF":
      $defaults = array('print_comments'=>1, 'print_summary'=>1, 'create_bookmarks'=>1, 'download_pdf'=>1,
           'customer_new_page'=>0, 'reverse_order'=>0, 'pdf_format'=>'export_pdf');
      $prefs = usr_get_preferences_by_prefix('ki_export.pdf.');
      $tpl->assign('prefs', array_merge($defaults,$prefs));
      
	    $tpl->display("export_PDF.tpl"); 
    break;

    case "XLS":  
      $defaults = array('reverse_order'=>0);
      $prefs = usr_get_preferences_by_prefix('ki_export.xls.');
      $tpl->assign('prefs', array_merge($defaults,$prefs));

	    $tpl->display("export_XLS.tpl"); 
    break;

    case "CSV":  
      $defaults = array('column_delimiter'=>',','quote_char'=>'"','reverse_order'=>0);
      $prefs = usr_get_preferences_by_prefix('ki_export.csv.');
      $tpl->assign('prefs', array_merge($defaults,$prefs));

	    $tpl->display("export_CSV.tpl"); 
    break;

    case "print":  
      $defaults = array('print_summary'=>1,'reverse_order'=>0);
      $prefs = usr_get_preferences_by_prefix('ki_export.print.');
      $tpl->assign('prefs', array_merge($defaults,$prefs));

	    $tpl->display("print.tpl"); 
    break;

    case "help_timeformat":  
	    $tpl->display("help_timeformat.tpl"); 
    break;

}

?>

    