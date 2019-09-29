<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
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

$isCoreProcessor = 0;
$dir_templates = 'templates/';
require '../../includes/kspi.php';

$database = Kimai_Registry::getDatabase();

require 'private_db_layer_mysql.php';

switch ($axAction) {
    case 'add_edit_record':
        if (isset($kga['customer'])) {
            die();
        }

        $projects = makeSelectBox('project', $kga['user']['groups']);
        $view->assign('projects', $projects); // select for projects
        $view->assign('activities', makeSelectBox('activity', $kga['user']['groups'])); // select for activities

        // ==============================================
        // = display edit dialog for timesheet record   =
        // ==============================================
        if ($id) {
            $expense = expense_get($id);

            // check if this entry may be edited
            if (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_expenses-ownEntry-edit')) {
                break;
            }

            if (!isset($view->projects[$expense['projectID']])) {
                // add the currently assigned project to the list
                $projectData = $database->project_get_data($expense['projectID']);
                $customerData = $database->customer_get_data($projectData['customerID']);
                $view->projects[$projectData['projectID']] = $customerData['name'] . ':' . $projectData['name'];
            }
        } else {
            // defaults
            $expense = [
                'timestamp' => time(),
                'commentType' => '',
                'comment' => '',
                'refundable' => true,
                'designation' => '',
                'projectID' => array_keys($projects)[0],
                'value' => '',
                'multiplier' => 1
            ];

            // check if this entry may be added
            if (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_expenses-ownEntry-add')) {
                break;
            }
        }
        $view->assign('expense', $expense);

        echo $view->render('floaters/add_edit_record.php');

        break;
}
