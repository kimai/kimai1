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

/**
 * =============================
 * = Floating Window Generator =
 * =============================
 * 
 * Called via AJAX from the Kimai user interface. Depending on $axAction
 * some HTML will be returned, which will then be shown in a floater.
 */

// insert KSPI
$isCoreProcessor = 1;
$dir_templates = "templates/floaters/"; // folder of the template files
require("../includes/kspi.php");


switch ($axAction) {

    /**
     * Display the credits floater. The copyright will automatically be
     * set from 2006 to the current year.
     */
    case 'credits':
        $tpl->assign('devtimespan', '2006-'.date('y'));

        $tpl->display("credits.tpl");
    break;

    /**
     * Display the credits floater. The copyright will automatically be
     * set from 2006 to the current year.
     */
    case 'securityWarning':
        if ($axValue == 'installer') {

          $tpl->display("security_warning.tpl");
        }
    break;
   
    /**
     * Display the preferences dialog.
     */
    case 'prefs':
        if (isset($kga['customer'])) die();

        $tpl->assign('skins', ls("../skins"));
        $tpl->assign('langs', langs());
        $tpl->assign('timezones', timezoneList());
        $tpl->assign('usr', $kga['usr']);
        $tpl->assign('rate', get_rate($kga['usr']['usr_ID'],NULL,NULL));

        $tpl->display("preferences.tpl");
    break;
    
    /**
     * Display the dialog to add or edit a customer.
     */
    case 'add_edit_knd':
        if (isset($kga['customer']) || $kga['usr']['usr_sts']==2) die();

        if ($id) {
            // Edit mode. Fill the dialog with the data of the customer.

            $data = knd_get_data($id);
            if ($data) {
                $tpl->assign('knd_name'     , $data['knd_name'    ]);
                $tpl->assign('knd_comment'  , $data['knd_comment' ]);
                $tpl->assign('knd_password' , $data['knd_password']);
                $tpl->assign('knd_company'  , $data['knd_company' ]);
                $tpl->assign('knd_vat'      , $data['knd_vat'     ]);
                $tpl->assign('knd_contact'  , $data['knd_contact' ]);
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
                $tpl->assign('grp_selection', knd_get_grps($id));
                $tpl->assign('id', $id);
            }
        }
        // create the <select> element for the groups
        $sel = makeSelectBox("grp",$kga['usr']['usr_grp']);
        $tpl->assign('sel_grp_names', $sel[0]);
        $tpl->assign('sel_grp_IDs',   $sel[1]);

        // A new customer is assigned to the group of the current user by default.
        if (!$id) {
            $grp_selection[]=$kga['usr']['usr_grp'];
            $tpl->assign('grp_selection', $grp_selection);
            $tpl->assign('id', 0);
        }

        $tpl->display("add_edit_knd.tpl");
    break;
        
    /**
     * Display the dialog to add or edit a project.
     */
    case 'add_edit_pct':
        if (isset($kga['customer']) || $kga['usr']['usr_sts']==2) die();
 
        if ($id) {
            $data = pct_get_data($id);
            if ($data) {
                $tpl->assign('pct_name'        , $data['pct_name'        ]);
                $tpl->assign('pct_comment'     , $data['pct_comment'     ]);
                $tpl->assign('pct_visible'     , $data['pct_visible'     ]);
                $tpl->assign('pct_internal'    , $data['pct_internal'    ]);
                $tpl->assign('pct_filter'      , $data['pct_filter'      ]);
                $tpl->assign('pct_budget'      , $data['pct_budget'      ]);
                $tpl->assign('knd_selection'   , $data['pct_kndID'       ]);
                $tpl->assign('evt_selection'   , pct_get_evts($id)        );
                $tpl->assign('pct_default_rate', $data['pct_default_rate']);
                $tpl->assign('pct_my_rate'     , $data['pct_my_rate'     ]);
                $tpl->assign('grp_selection', pct_get_grps($id));
                $tpl->assign('id', $id);
            }
        }
        // Create a <select> element to chosse the customer.
        $sel = makeSelectBox("knd",$kga['usr']['usr_grp'],isset($data)?$data['pct_kndID']:null);
        $tpl->assign('sel_knd_names', $sel[0]);
        $tpl->assign('sel_knd_IDs',   $sel[1]);

        // Create a <select> element to chosse the events.
        $sel = makeSelectBox("evt",$kga['usr']['usr_grp']);
        $tpl->assign('sel_evt_names', $sel[0]);
        $tpl->assign('sel_evt_IDs',   $sel[1]);
        
        // Create a <select> element to chosse the groups.
        $sel = makeSelectBox("grp",$kga['usr']['usr_grp']);
        $tpl->assign('sel_grp_names', $sel[0]);
        $tpl->assign('sel_grp_IDs',   $sel[1]);
        
        // Set defaults for a new project.
        if (!$id) {
            $grp_selection[]=$kga['usr']['usr_grp'];
            $tpl->assign('grp_selection', $grp_selection);

            $tpl->assign('knd_selection', null);
            $tpl->assign('id', 0);
        }

        $tpl->display("add_edit_pct.tpl");
    break;
    
    /**
     * Display the dialog to add or edit an event.
     */
    case 'add_edit_evt':
        if (isset($kga['customer']) || $kga['usr']['usr_sts']==2) die();

        if ($id) {
            $data = evt_get_data($id);
            if ($data) {
                $tpl->assign('evt_name'        , $data['evt_name'        ]);
                $tpl->assign('evt_comment'     , $data['evt_comment'     ]);
                $tpl->assign('evt_visible'     , $data['evt_visible'     ]);
                $tpl->assign('evt_filter'      , $data['evt_filter'      ]);
                $tpl->assign('evt_default_rate', $data['evt_default_rate']);
                $tpl->assign('evt_my_rate'     , $data['evt_my_rate'     ]);
                $tpl->assign('grp_selection', evt_get_grps($id));
                $tpl->assign('pct_selection', evt_get_pcts($id));
                $tpl->assign('id', $id);
        
            }
        }

        // Create a <select> element to chosse the groups.
        $sel = makeSelectBox("grp",$kga['usr']['usr_grp']);
        $tpl->assign('sel_grp_names', $sel[0]);
        $tpl->assign('sel_grp_IDs',   $sel[1]);

        // Create a <select> element to chosse the projects.
        $sel = makeSelectBox("pct",$kga['usr']['usr_grp']);
        $tpl->assign('sel_pct_names', $sel[0]);
        $tpl->assign('sel_pct_IDs',   $sel[1]);

        // Set defaults for a new project.
        if (!$id) {
            $grp_selection[]=$kga['usr']['usr_grp'];
            $tpl->assign('grp_selection', $grp_selection);
            $tpl->assign('id', 0);
        }

        $tpl->display("add_edit_evt.tpl");
    break;
    
}

?>