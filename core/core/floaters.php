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

// =============================
// = Floating Window Generator =
// =============================

// insert KSPI
$isCoreProcessor = 1;
$dir_templates = "templates/floaters/";
require("../includes/kspi.php");


// ==================
// = handle request =
// ==================
switch ($axAction) {

    // ====================
    // = displays credits =
    // ====================
    case 'credits':
        $tpl->assign('devtimespan', '2006-'.date('y'));
        $tpl->display("credits.tpl");
    break;
   
    // ==================================
    // = displays preferences dialog... =
    // ==================================
    case 'prefs':
        $tpl->assign('skins', ls("../skins"));
        $tpl->assign('langs', langs());
        $tpl->assign('usr', $kga['usr']);
        $tpl->display("preferences.tpl");
    break;
    
    // ===================================
    // = displays knd/pct/evt dialogs... =
    // ===================================
    case 'add_edit_knd':
        if ($id) {
            $data = knd_get_data($id);
            if ($data) {
                $tpl->assign('knd_name'     , $data['knd_name'    ]);
                $tpl->assign('knd_comment'  , $data['knd_comment' ]);
                $tpl->assign('knd_company'  , $data['knd_company' ]);
                $tpl->assign('knd_street'   , $data['knd_street'  ]);
                $tpl->assign('knd_zipcode'  , $data['knd_zipcode' ]);
                $tpl->assign('knd_city'     , $data['knd_city'    ]);
                $tpl->assign('knd_tel'      , $data['knd_tel'     ]);
                $tpl->assign('knd_fax'      , $data['knd_fax'     ]);
                $tpl->assign('knd_mobile'   , $data['knd_mobile'  ]);
                $tpl->assign('knd_mail'     , $data['knd_mail'    ]);
                $tpl->assign('knd_homepage' , $data['knd_homepage']);
                $tpl->assign('knd_visible'  , $data['knd_visible' ]);
                $tpl->assign('knd_filter'   , $data['knd_filter'  ]);
                $tpl->assign('knd_logo'     , $data['knd_logo'    ]);
                $tpl->assign('grp_selection', knd_get_grps($id));
                $tpl->assign('id', $id);
            }
        }
        $sel = makeSelectBox("grp",$kga['usr']['usr_grp']);
        $tpl->assign('sel_grp_names', $sel[0]);
        $tpl->assign('sel_grp_IDs',   $sel[1]);
        if (!$id) {
            $grp_selection[]=$kga['usr']['usr_grp'];
            $tpl->assign('grp_selection', $grp_selection);
            $tpl->assign('id', 0);
        }
        $tpl->display("add_edit_knd.tpl");
    break;
        
    case 'add_edit_pct':   
        if ($id) {
            $data = pct_get_data($id);
            if ($data) {
                $tpl->assign('pct_name'     , $data['pct_name'    ]);
                $tpl->assign('pct_comment'  , $data['pct_comment' ]);
                $tpl->assign('pct_visible'  , $data['pct_visible' ]);
                $tpl->assign('pct_filter'   , $data['pct_filter'  ]);
                $tpl->assign('pct_logo'     , $data['pct_logo'    ]);
                $tpl->assign('knd_selection', $data['pct_kndID'   ]);
                $tpl->assign('grp_selection', pct_get_grps($id));
                $tpl->assign('id', $id);
            }
        }
    // select for customers
        $sel = makeSelectBox("knd",$kga['usr']['usr_grp']);
        $tpl->assign('sel_knd_names', $sel[0]);
        $tpl->assign('sel_knd_IDs',   $sel[1]);
        
        $sel = makeSelectBox("grp",$kga['usr']['usr_grp']);
        $tpl->assign('sel_grp_names', $sel[0]);
        $tpl->assign('sel_grp_IDs',   $sel[1]);
        if (!$id) {
            $grp_selection[]=$kga['usr']['usr_grp'];
            $tpl->assign('grp_selection', $grp_selection);
            if(!isset($knd_selection))
            	$knd_selection = null;
            $tpl->assign('knd_selection', $knd_selection);
            $tpl->assign('id', 0);
        }
        $tpl->display("add_edit_pct.tpl");
    break;
    
    case 'add_edit_evt':
        if ($id) {
            $data = evt_get_data($id);
            if ($data) {
                $tpl->assign('evt_name'     , $data['evt_name'    ]);
                $tpl->assign('evt_comment'  , $data['evt_comment' ]);
                $tpl->assign('evt_visible'  , $data['evt_visible' ]);
                $tpl->assign('evt_filter'   , $data['evt_filter'  ]);
                $tpl->assign('evt_logo'     , $data['evt_logo'    ]);
                $tpl->assign('grp_selection', evt_get_grps($id));
                $tpl->assign('id', $id);
            }
        }
        $sel = makeSelectBox("grp",$kga['usr']['usr_grp']);
        $tpl->assign('sel_grp_names', $sel[0]);
        $tpl->assign('sel_grp_IDs',   $sel[1]);
        if (!$id) {
            $grp_selection[]=$kga['usr']['usr_grp'];
            $tpl->assign('grp_selection', $grp_selection);
            $tpl->assign('id', 0);
        }
        $tpl->display("add_edit_evt.tpl");
    break;
    
}

?>