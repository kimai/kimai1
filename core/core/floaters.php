<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team
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
require("../includes/kspi.php");

switch ($axAction) {

    /**
     * Display the credits floater.
     */
    case 'credits':
        $view->devtimespan = '2006-'.date('y');

        echo $view->render("floaters/credits.php");
    break;

    /**
     * Display a warning in case the installer is still present.
     */
    case 'securityWarning':
        if ($axValue == 'installer') {
          echo $view->render("floaters/security_warning.php");
        }
    break;
   
    /**
     * Display the preferences dialog.
     */
    case 'prefs':
        if (isset($kga['customer'])) die();

        $skins = array();
        $langs = array();

        $allSkins = glob(__DIR__."/../skins/*", GLOB_ONLYDIR);
        foreach($allSkins as $skin) {
            $name = basename($skin);
            $skins[$name] = $name;
        }

        foreach(Translations::langs() as $lang) {
            $langs[$lang] = $lang;
        }

        $view->skins = $skins;
        $view->langs = $langs;
        $view->timezones = timezoneList();
        $view->user = $kga['user'];
        $view->rate = $database->get_rate($kga['user']['userID'],NULL,NULL);

        echo $view->render("floaters/preferences.php");
    break;
    
    /**
     * Display the dialog to add or edit a customer.
     */
    case 'add_edit_customer':
        $oldGroups = array();
        if ($id)
          $oldGroups = $database->customer_get_groupIDs($id);

        if (!checkGroupedObjectPermission('Customer', $id?'edit':'add', $oldGroups, $oldGroups)) die();

        if ($id) {
            // Edit mode. Fill the dialog with the data of the customer.

            $data = $database->customer_get_data($id);
            if ($data) {
                $view->name      = $data['name'    ];
                $view->comment   = $data['comment' ];
                $view->password  = $data['password'];
                $view->timezone  = $data['timezone'];
                $view->company   = $data['company' ];
                $view->vat       = $data['vat'     ];
                $view->contact   = $data['contact' ];
                $view->street    = $data['street'  ];
                $view->zipcode   = $data['zipcode' ];
                $view->city      = $data['city'    ];
                $view->phone     = $data['phone'   ];
                $view->fax       = $data['fax'     ];
                $view->mobile    = $data['mobile'  ];
                $view->mail      = $data['mail'    ];
                $view->homepage  = $data['homepage'];
                $view->visible   = $data['visible' ];
                $view->filter    = $data['filter'  ];
                $view->selectedGroups = $database->customer_get_groupIDs($id);
                $view->id = $id;
            }
        }
        else {
          $view->timezone = $kga['timezone'];
        }

        $view->timezones = timezoneList();

        $view->groups = makeSelectBox("group",$kga['user']['groups']);

        // A new customer is assigned to the group of the current user by default.
        if (!$id) {
            $view->selectedGroups = $kga['user']['groups'];
            $view->id = 0;
        }

        echo $view->render("floaters/add_edit_customer.php");
    break;
        
    /**
     * Display the dialog to add or edit a project.
     */
    case 'add_edit_project':
        $oldGroups = array();
        if ($id)
          $oldGroups = $database->project_get_groupIDs($id);

        if (!checkGroupedObjectPermission('Project', $id?'edit':'add', $oldGroups, $oldGroups)) die();
 
        $view->customers = makeSelectBox("customer",$kga['user']['groups'],isset($data)?$data['customerID']:null);
        $view->groups = makeSelectBox("group",$kga['user']['groups']);
        $view->allActivities = $database->get_activities($kga['user']['groups']);

        if ($id) {
            $data = $database->project_get_data($id);
            if ($data) {
                $view->name         = $data['name'        ];
                $view->comment      = $data['comment'     ];
                $view->visible      = $data['visible'     ];
                $view->internal     = $data['internal'    ];
                $view->filter       = $data['filter'      ];
                $view->budget       = $data['budget'      ];
                $view->effort       = $data['effort'      ];
                $view->approved     = $data['approved' 	 ];
                $view->selectedCustomer  = $data['customerID'];
                $view->selectedActivities    = $database->project_get_activities($id);
                $view->defaultRate = $data['defaultRate'];
                $view->myRate      = $data['myRate'     ];
                $view->fixedRate   = $data['fixedRate'  ];
                $view->selectedGroups = $database->project_get_groupIDs($id);
                $view->id = $id;

                if (!isset($view->customers[$data['customerID']])) {
                  // add the currently assigned customer to the list although the user is in no group to see him
                  $customerData = $database->customer_get_data($data['customerID']);
                  $view->customers[$data['customerID']] = $customerData['name'];
                }
            }
        }
        
        if (!isset($view->id)) {
          $view->selectedActivities = array();
          $view->internal = false;
        }
        
        // Set defaults for a new project.
        if (!$id) {
            $view->selectedGroups = $kga['user']['groups'];

            $view->selectedCustomer = null;
            $view->id = 0;
        }

        echo $view->render("floaters/add_edit_project.php");
    break;
    
    /**
     * Display the dialog to add or edit an activity.
     */
    case 'add_edit_activity':
        $oldGroups = array();
        if ($id)
          $oldGroups = $database->activity_get_groupIDs($id);

        if (!checkGroupedObjectPermission('Activity', $id?'edit':'add', $oldGroups, $oldGroups)) die();

        if ($id) {
            $data = $database->activity_get_data($id);
            if ($data) {
                $view->name         = $data['name'        ];
                $view->comment      = $data['comment'     ];
                $view->visible      = $data['visible'     ];
                $view->filter       = $data['filter'      ];
                $view->defaultRate  = $data['defaultRate'];
                $view->myRate       = $data['myRate'     ];
                $view->fixedRate    = $data['fixedRate'  ];
                $view->selectedGroups = $database->activity_get_groups($id);
                $view->selectedProjects = $database->activity_get_projects($id);
                $view->id = $id;
        
            }
        }

        // Create a <select> element to chosse the groups.
        $view->groups = makeSelectBox("group",$kga['user']['groups']);

        // Create a <select> element to chosse the projects.
        $view->projects = makeSelectBox("project",$kga['user']['groups']);

        // Set defaults for a new project.
        if (!$id) {
            $view->selectedGroups = $kga['user']['groups'];
            $view->id = 0;
        }

        echo $view->render("floaters/add_edit_activity.php");
    break;
    
}
