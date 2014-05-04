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

require("../../includes/kspi.php");
$view->addBasePath(dirname(__FILE__).'/templates/');

include('private_db_layer_mysql.php');

switch ($axAction)
{

    case "add_edit_record":
        if (isset($kga['customer'])) {
            die();
        }

        $view->commentTypes = $commentTypes;
        $view->projects     = makeSelectBox("project",$kga['user']['groups']); // select for projects
        $view->activities   = makeSelectBox("activity",$kga['user']['groups']); // select for activities

        // ==============================================
        // = display edit dialog for timesheet record   =
        // ==============================================
        if ($id)
        {
            $expense                = get_expense($id);
            $view->id               = $id;
            $view->comment          = $expense['comment'];
            $view->edit_day         = date("d.m.Y",$expense['timestamp']);
            $view->edit_time        = date("H:i:s",$expense['timestamp']);
            $view->multiplier       = $expense['multiplier'];
            $view->edit_value       = $expense['value'];
            $view->designation      = $expense['designation'];
            $view->selected_project = $expense['projectID'];
            $view->commentType      = $expense['commentType'];
            $view->refundable       = $expense['refundable'];

            // check if this entry may be edited
            if (!$database->global_role_allows($kga['user']['globalRoleID'],'ki_expenses-ownEntry-edit'))
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
          $view->id         = 0;
          $view->edit_day   = date("d.m.Y");
          $view->edit_time  = date("H:i:s");
          $view->multiplier = '1'.$kga['conf']['decimalSeparator'].'0';

          // check if this entry may be added
          if (!$database->global_role_allows($kga['user']['globalRoleID'],'ki_expenses-ownEntry-add'))
            break;
        }

        echo $view->render("floaters/add_edit_record.php");

    break;

}
