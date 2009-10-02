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

// ================
// = EX PROCESSOR =
// ================

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require("../../includes/kspi.php");

// ==================
// = handle request =
// ==================
switch ($axAction) {
    // =========================================
    // = Erase timesheet entry via quickdelete =
    // =========================================
    /*case 'quickdelete':
        zef_delete_record($id);
        echo 1;
    break;*/

    // ===========================
    // = Load data and return it =
    // ===========================
    case 'reload':
        $filters = explode('|',$axValue);
        if ($filters[0] == "")
          $filterUsr = array();
        else
          $filterUsr = explode(':',$filters[0]);

        if ($filters[1] == "")
          $filterKnd = array();
        else
          $filterKnd = explode(':',$filters[1]);

        if ($filters[2] == "")
          $filterPct = array();
        else
          $filterPct = explode(':',$filters[2]);

        // if no userfilter is set, set it to current user
        if (isset($kga['usr']) && count($filterUsr) == 0)
          array_push($filterUsr,$kga['usr']['usr_ID']);
          
        if (isset($kga['customer']))
          $filterKnd = array($kga['customer']['knd_ID']);

        $arr_data = get_arr_zef($in,$out,$filterUsr,$filterKnd,$filterPct,1);


        if (count($arr_data)>0) {
            $tpl->assign('arr_data', $arr_data);
        } else {
            $tpl->assign('arr_data', 0);
        }
        $tpl->assign('total', intervallApos(get_zef_time($in,$out,$filterUsr,$filterKnd,$filterPct)));

        $ann = get_arr_time_usr($in,$out,$filterUsr,$filterKnd,$filterPct);
        $ann_new = intervallApos($ann);
        $tpl->assign('usr_ann',$ann_new);
        
        $ann = get_arr_time_knd($in,$out,$filterUsr,$filterKnd,$filterPct);
        $ann_new = intervallApos($ann);
        $tpl->assign('knd_ann',$ann_new);

        $ann = get_arr_time_pct($in,$out,$filterUsr,$filterKnd,$filterPct);
        $ann_new = intervallApos($ann);
        $tpl->assign('pct_ann',$ann_new);

        $ann = get_arr_time_evt($in,$out,$filterUsr,$filterKnd,$filterPct);
        $ann_new = intervallApos($ann);
        $tpl->assign('evt_ann',$ann_new);

        $tpl->display("table.tpl");
    break;

}

?>
