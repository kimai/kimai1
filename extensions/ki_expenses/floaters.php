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
$dir_templates = "templates/";
require "../../includes/kspi.php";

include 'private_db_layer_mysql.php';

switch ($axAction)
{

    case "add_edit_record":
        if (isset($kga['customer'])) {
            die();
        }

        $view->assign('projects', makeSelectBox("project", $kga['user']['groups'])); // select for projects
        $view->assign('activities', makeSelectBox("activity", $kga['user']['groups'])); // select for activities

        // ==============================================
        // = display edit dialog for timesheet record   =
        // ==============================================
        if ($id)
        {
            $expense = get_expense($id);
            $view->assign('id', $id);
            $view->assign('comment', $expense['comment']);
            $view->assign('edit_day', date("d.m.Y", $expense['timestamp']));
            $view->assign('edit_time', date("H:i:s", $expense['timestamp']));
            $view->assign('multiplier', $expense['multiplier']);
            $view->assign('edit_value', $expense['value']);
            $view->assign('designation', $expense['designation']);
            $view->assign('selected_project', $expense['projectID']);
            $view->assign('commentType', $expense['commentType']);
            $view->assign('refundable', $expense['refundable']);

            // check if this entry may be edited
            if (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_expenses-ownEntry-edit'))
              break;

            if (!isset($view->projects[$expense['projectID']])) {
              // add the currently assigned project to the list
              $projectData = $database->project_get_data($expense['projectID']);
              $customerData = $database->customer_get_data($projectData['customerID']);
              $view->projects[$projectData['projectID']] = $customerData['name'] . ':' . $projectData['name'];
            }
        }
        else
        {
            $view->assign('id', 0);
            $view->assign('edit_day', date("d.m.Y"));
            $view->assign('edit_time', date("H:i:s"));
            $view->assign('multiplier', '1' . $kga['conf']['decimalSeparator'] . '0');

          // check if this entry may be added
          if (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_expenses-ownEntry-add'))
            break;
        }

        echo $view->render("floaters/add_edit_record.php");

    break;

}
