<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
 * (c) Kimai-Development-Team since 2006
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
 * Floating Window Generator
 *
 * Called via AJAX from the Kimai user interface. Depending on $axAction
 * some HTML will be returned, which will then be shown in a floater.
 */

$isCoreProcessor = 1;
$dir_templates = 'templates/scripts/'; // folder of the template files
require '../includes/kspi.php';

$database = Kimai_Registry::getDatabase();

switch ($axAction) {

    /**
     * Display the credits floater. The copyright will automatically be
     * set from 2006 to the current year.
     */
    case 'credits':
        $view->assign('devtimespan', '2006-' . date('y'));

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
        if (isset($kga['customer'])) {
            die();
        }

        $allSkins = glob(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'skins' . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
        $skins = [];
        foreach ($allSkins as $skin) {
            $name = basename($skin);
            $skins[$name] = $name;
        }

        $languages = [];
        foreach (Kimai_Translation_Service::getAvailableLanguages() as $lang) {
            $languages[$lang] = $lang;
        }

        $view->assign('skins', $skins);
        $view->assign('langs', $languages);
        $view->assign('timezones', timezoneList());
        $view->assign('user', $kga['user']);
        $view->assign('rate', $database->get_rate($kga['user']['userID'], null, null));

        $defaults = [
            'table_time_format' => $kga->getTableTimeFormat()
        ];
        $prefs = $database->user_get_preferences_by_prefix('ui.');
        $view->assign('prefs', array_merge($defaults, $prefs));

        echo $view->render("floaters/preferences.php");
    break;

    /**
     * Display the dialog to add or edit a customer.
     */
    case 'add_edit_customer':
        $oldGroups = [];
        if ($id) {
            $oldGroups = $database->customer_get_groupIDs($id);
        }

        if (!checkGroupedObjectPermission('Customer', $id ? 'edit' : 'add', $oldGroups, $oldGroups)) {
            die();
        }

        if ($id) {
            // Edit mode. Fill the dialog with the data of the customer.
            $data = $database->customer_get_data($id);
            if ($data) {
                $view->assign('name', $data['name']);
                $view->assign('comment', $data['comment']);
                $view->assign('password', $data['password']);
                $view->assign('timezone', $data['timezone']);
                $view->assign('company', $data['company']);
                $view->assign('vat', $data['vat']);
                $view->assign('contact', $data['contact']);
                $view->assign('street', $data['street']);
                $view->assign('zipcode', $data['zipcode']);
                $view->assign('city', $data['city']);
                $view->assign('country', $data['country']);
                $view->assign('phone', $data['phone']);
                $view->assign('fax', $data['fax']);
                $view->assign('mobile', $data['mobile']);
                $view->assign('mail', $data['mail']);
                $view->assign('homepage', $data['homepage']);
                $view->assign('visible', $data['visible']);
                $view->assign('filter', $data['filter']);
                $view->assign('selectedGroups', $database->customer_get_groupIDs($id));
                $view->assign('id', $id);
            }
        } else {
            $view->assign('timezone', $kga['timezone']);
        }

        $view->assign('timezones', timezoneList());
        $view->assign('groups', makeSelectBox("group", $kga['user']['groups']));

        // A new customer is assigned to the group of the current user by default.
        if (!$id) {
            $view->assign('selectedGroups', []);
            foreach ($kga['user']['groups'] as $group) {
               $membershipRoleID = $database->user_get_membership_role($kga['user']['userID'], $group);
               if ($database->membership_role_allows($membershipRoleID, 'core-user-add')) {
                   $view->selectedGroups[] = $group;
               }
            }
            $view->assign('id', 0);
        }

        $countries = Zend_Locale::getTranslationList('Territory', $kga['language'], 2);
        asort($countries);

        $view->assign('countries', $countries);

        echo $view->render("floaters/add_edit_customer.php");
    break;

    /**
     * Display the dialog to add or edit a project.
     */
    case 'add_edit_project':
        $oldGroups = [];
        if ($id) {
            $oldGroups = $database->project_get_groupIDs($id);
        }

        if (!checkGroupedObjectPermission('Project', $id ? 'edit' : 'add', $oldGroups, $oldGroups)) {
            die();
        }

        $view->assign('customers', makeSelectBox("customer", $kga['user']['groups'], (isset($data) ? $data['customerID'] : null)));
        $view->assign('groups', makeSelectBox("group", $kga['user']['groups']));
        $view->assign('allActivities', $database->get_activities($kga['user']['groups']));

        if ($id) {
            $data = $database->project_get_data($id);
            if ($data) {
                $view->assign('name', $data['name']);
                $view->assign('comment', $data['comment']);
                $view->assign('visible', $data['visible']);
                $view->assign('internal', $data['internal']);
                $view->assign('filter', $data['filter']);
                $view->assign('budget', $data['budget']);
                $view->assign('effort', $data['effort']);
                $view->assign('approved', $data['approved']);
                $view->assign('selectedCustomer', $data['customerID']);
                $view->assign('selectedActivities', $database->project_get_activities($id));
                $view->assign('defaultRate', $data['defaultRate']);
                $view->assign('myRate', $data['myRate']);
                $view->assign('fixedRate', $data['fixedRate']);
                $view->assign('selectedGroups', $database->project_get_groupIDs($id));
                $view->assign('id', $id);

                if (!isset($view->customers[$data['customerID']])) {
                    // add the currently assigned customer to the list although
                    // a) the user is in no group to see him
                    // b) the customer is hidden
                    $customerData = $database->customer_get_data($data['customerID']);
                    $view->customers[$data['customerID']] = $customerData['name'];
                }
            }
        }

        if (!isset($view->id)) {
            $view->assign('selectedActivities', []);
            $view->assign('internal', false);
        }

        // Set defaults for a new project.
        if (!$id) {
            $view->assign('selectedGroups', []);
            foreach ($kga['user']['groups'] as $group) {
               $membershipRoleID = $database->user_get_membership_role($kga['user']['userID'], $group);
               if ($database->membership_role_allows($membershipRoleID, 'core-project-add')) {
                    $view->selectedGroups[] = $group;
               }
            }

            $view->assign('selectedCustomer', null);
            $view->assign('id', 0);
        }

        echo $view->render("floaters/add_edit_project.php");
    break;

    /**
     * Display the dialog to add or edit an activity.
     */
    case 'add_edit_activity':
        $oldGroups = [];
        if ($id) {
          $oldGroups = $database->activity_get_groupIDs($id);
        }

        if (!checkGroupedObjectPermission('Activity', $id ? 'edit' : 'add', $oldGroups, $oldGroups)) {
            die();
        }

        $selectedProjectIds = [];

        if ($id) {
            $data = $database->activity_get_data($id);
            if ($data) {
                $view->assign('name', $data['name']);
                $view->assign('comment', $data['comment']);
                $view->assign('visible', $data['visible']);
                $view->assign('filter', $data['filter']);
                $view->assign('defaultRate', $data['defaultRate']);
                $view->assign('myRate', $data['myRate']);
                $view->assign('fixedRate', $data['fixedRate']); // default fixed rate (not assigned to project)
                $view->assign('selectedGroups', $database->activity_get_groups($id));

                $selectedProjectIds = $database->activity_get_projectIds($id);
                $view->assign('selectedProjectIds', $selectedProjectIds);

                $selectedProjects = $database->activity_get_projects($id);
                if (is_array($selectedProjects)) {
                    foreach ($selectedProjects as &$selectedProject) {
                        // edit by reference!
                        $selectedProject['fixedRate'] = $database->get_fixed_rate($selectedProject['projectID'], $id);
                    }
                }
                $view->assign('selectedProjects', $selectedProjects);
                $view->assign('id', $id);
            }
        }

        // Create a <select> element to choose the projects
        $view->assign('allProjects', $database->get_projects($kga['user']['groups']));

        // Create a <select> element to choose the groups
        $view->assign('groups', makeSelectBox("group", $kga['user']['groups']));

        // Set defaults for a new project.
        if (!$id) {
            $selectedGroups = [];
            foreach ($kga['user']['groups'] as $group) {
               $membershipRoleID = $database->user_get_membership_role($kga['user']['userID'], $group);
               if ($database->membership_role_allows($membershipRoleID, 'core-activity-add')) {
                    $selectedGroups[] = $group;
               }
            }
            $view->assign('selectedGroups', $selectedGroups);
            $view->assign('id', 0);
        }

        echo $view->render("floaters/add_edit_activity.php");
    break;

}
