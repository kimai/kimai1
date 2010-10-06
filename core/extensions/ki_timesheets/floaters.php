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
   
    // =================================================
    // = displays edit comment dialog for running task =
    // =================================================
    case 'edit_running_comment':
        if (isset($kga['customer'])) die();

        $tpl->assign('langs', langs());
        $last_event = get_event_last();
        $tpl->assign('id', $last_event['zef_ID']);
        $tpl->assign('comment', $last_event['zef_comment']);
        $tpl->assign('comment_active', $last_event['zef_comment_type']);
        $tpl->assign('comment_types', $comment_types);
        $tpl->assign('comment_values', array('0','1','2'));
        $tpl->display("edit_running_comment.tpl");
    break;

    case "add_edit_record":  
        if (isset($kga['customer'])) die();  
    // ==============================================
    // = display edit dialog for timesheet record   =
    // ==============================================
    $selected = explode('|',$axValue);
    if ($id) {
        $zef_entry = get_entry_zef($id);
        $tpl->assign('id', $id);
        $tpl->assign('zlocation', $zef_entry['zef_location']);
        
        $tpl->assign('trackingnr', $zef_entry['zef_trackingnr']);
        $tpl->assign('comment', $zef_entry['zef_comment']);
        
        $tpl->assign('rate', $zef_entry['zef_rate']);
        
        $tpl->assign('cleared', $zef_entry['zef_cleared']!=0);
    
        $tpl->assign('edit_in_day', date("d.m.Y",$zef_entry['zef_in']));
        $tpl->assign('edit_out_day', date("d.m.Y",$zef_entry['zef_out']));
    
        $tpl->assign('edit_in_time',  date("H:i:s",$zef_entry['zef_in']));
        $tpl->assign('edit_out_time', date("H:i:s",$zef_entry['zef_out']));

        // preselected
        $tpl->assign('pres_pct', $zef_entry['pct_ID']);
        $tpl->assign('pres_evt', $zef_entry['evt_ID']);
    
        $tpl->assign('comment_active', $zef_entry['zef_comment_type']);

    } else {
        
        $tpl->assign('id', 0);
        
        $tpl->assign('edit_in_day', date("d.m.Y"));
        $tpl->assign('edit_out_day', date("d.m.Y"));
    
        $tpl->assign('edit_in_time',  date("H:i:s"));
        $tpl->assign('edit_out_time', date("H:i:s"));
        $tpl->assign('rate',get_best_fitting_rate($kga['usr']['usr_ID'],$selected[0],$selected[1]));

    }

    $tpl->assign('comment_types', $comment_types);
    $tpl->assign('comment_values', array('0','1','2'));

    // select for projects
    $sel = makeSelectBox("pct",$kga['usr']['usr_grp']);
    $tpl->assign('sel_pct_names', $sel[0]);
    $tpl->assign('sel_pct_IDs',   $sel[1]);

    // select for events
    $sel = makeSelectBox("evt",$kga['usr']['usr_grp']);
    $tpl->assign('sel_evt_names', $sel[0]);
    $tpl->assign('sel_evt_IDs',   $sel[1]);



    $tpl->display("add_edit_record.tpl"); 

    break;        

}

?>

    