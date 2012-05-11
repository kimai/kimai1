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
        $tpl->assign('langs', Translations::langs());
        $tpl->assign('timezones', timezoneList());
        $tpl->assign('usr', $kga['usr']);
        $tpl->assign('rate', $database->get_rate($kga['usr']['userID'],NULL,NULL));

        $tpl->display("preferences.tpl");
    break;
    
    /**
     * Display the dialog to add or edit a customer.
     */
    case 'add_edit_knd':
        if (isset($kga['customer']) || $kga['usr']['status']==2) die();

        if ($id) {
            // Edit mode. Fill the dialog with the data of the customer.

            $data = $database->customer_get_data($id);
            if ($data) {
                $tpl->assign('name'     , $data['name'    ]);
                $tpl->assign('comment'  , $data['comment' ]);
                $tpl->assign('password' , $data['password']);
                $tpl->assign('timezone' , $data['timezone']);
                $tpl->assign('company'  , $data['company' ]);
                $tpl->assign('vat'      , $data['vat'     ]);
                $tpl->assign('contact'  , $data['contact' ]);
                $tpl->assign('street'   , $data['street'  ]);
                $tpl->assign('zipcode'  , $data['zipcode' ]);
                $tpl->assign('city'     , $data['city'    ]);
                $tpl->assign('phone'    , $data['phone'   ]);
                $tpl->assign('fax'      , $data['fax'     ]);
                $tpl->assign('mobile'   , $data['mobile'  ]);
                $tpl->assign('mail'     , $data['mail'    ]);
                $tpl->assign('homepage' , $data['homepage']);
                $tpl->assign('visible'  , $data['visible' ]);
                $tpl->assign('filter'   , $data['filter'  ]);
                $tpl->assign('selectedGroups', $database->customer_get_groupIDs($id));
                $tpl->assign('id', $id);
            }
        }
        else {
          $tpl->assign('knd_timezone' , $kga['conf']['timezone']);
        }

        $tpl->assign('timezones', timezoneList());

        // create the <select> element for the groups
        $sel = makeSelectBox("grp",$kga['usr']['groups']);
        $tpl->assign('groupNames', $sel[0]);
        $tpl->assign('groupIDs',   $sel[1]);

        // A new customer is assigned to the group of the current user by default.
        if (!$id) {
            $tpl->assign('selectedGroups', $kga['usr']['groups']);
            $tpl->assign('id', 0);
        }

        $tpl->display("add_edit_knd.tpl");
    break;
        
    /**
     * Display the dialog to add or edit a project.
     */
    case 'add_edit_pct':
        if (isset($kga['customer']) || $kga['usr']['status']==2) die();
 
        if ($id) {
            $data = $database->project_get_data($id);
            if ($data) {
                $tpl->assign('name'        , $data['name'        ]);
                $tpl->assign('comment'     , $data['comment'     ]);
                $tpl->assign('visible'     , $data['visible'     ]);
                $tpl->assign('internal'    , $data['internal'    ]);
                $tpl->assign('filter'      , $data['filter'      ]);
                $tpl->assign('budget'      , $data['budget'      ]);
                $tpl->assign('effort'      , $data['effort'      ]);
                $tpl->assign('approved'    , $data['approved' 	 ]);
                $tpl->assign('selectedCustomer' , $data['customerID']);
                $tpl->assign('selectedActivities'   , $database->project_get_activities($id));
                $tpl->assign('defaultRate', $data['defaultRate']);
                $tpl->assign('myRate'     , $data['myRate'     ]);
                $tpl->assign('fixedRate'  , $data['fixedRate'  ]);
                $tpl->assign('selectedGroups', $database->project_get_groupIDs($id));
                $tpl->assign('id', $id);
            }
        }
        // Create a <select> element to chosse the customer.
        $sel = makeSelectBox("knd",$kga['usr']['groups'],isset($data)?$data['customerID']:null);
        $tpl->assign('customerNames', $sel[0]);
        $tpl->assign('customerIDs',   $sel[1]);

        // Create a <select> element to chosse the events.
        $assignableTasks = array();
        $tasks = $database->get_arr_activities($kga['usr']['groups']);
        if(is_array($tasks)) {
	        foreach ($tasks as $task) {
	          if (!$task['assignable']) continue;
	          $assignableTasks[$task['activityID']] = $task['name'];
	        }
        }
        $tpl->assign('assignableTasks',$assignableTasks);
        
        // Create a <select> element to chosse the groups.
        $sel = makeSelectBox("grp",$kga['usr']['groups']);
        $tpl->assign('groupNames', $sel[0]);
        $tpl->assign('groupIDs',   $sel[1]);
        
        // Set defaults for a new project.
        if (!$id) {
            $tpl->assign('selectedGroups', $kga['usr']['groups']);

            $tpl->assign('selectedCustomer', null);
            $tpl->assign('id', 0);
        }

        $tpl->display("add_edit_pct.tpl");
    break;
    
    /**
     * Display the dialog to add or edit an event.
     */
    case 'add_edit_evt':
        if (isset($kga['customer']) || $kga['usr']['status']==2) die();

        if ($id) {
            $data = $database->activity_get_data($id);
            if ($data) {
                $tpl->assign('name'        , $data['name'        ]);
                $tpl->assign('comment'     , $data['comment'     ]);
                $tpl->assign('visible'     , $data['visible'     ]);
                $tpl->assign('filter'      , $data['filter'      ]);
                $tpl->assign('defaultRate', $data['defaultRate']);
                $tpl->assign('myRate'     , $data['myRate'     ]);
                $tpl->assign('fixedRate'  , $data['fixedRate'  ]);
                $tpl->assign('assignable'  , $data['assignable'  ]);
                $tpl->assign('selectedGroups', $database->activity_get_groups($id));
                $tpl->assign('selectedProjects', $database->activity_get_projects($id));
                $tpl->assign('id', $id);
        
            }
        }

        // Create a <select> element to chosse the groups.
        $sel = makeSelectBox("grp",$kga['usr']['groups']);
        $tpl->assign('groupNames', $sel[0]);
        $tpl->assign('groupIDs',   $sel[1]);

        // Create a <select> element to chosse the projects.
        $sel = makeSelectBox("pct",$kga['usr']['groups']);
        $tpl->assign('projectNames', $sel[0]);
        $tpl->assign('projectIDs',   $sel[1]);

        // Set defaults for a new project.
        if (!$id) {
            $tpl->assign('selectedGroups', $kga['usr']['groups']);
            $tpl->assign('id', 0);
        }

        $tpl->display("add_edit_evt.tpl");
    break;
    
}

?>