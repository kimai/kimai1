<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
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

$isCoreProcessor = 0;
$dir_templates = 'templates/';
require '../../includes/kspi.php';
require 'private_func.php';

$view = new Kimai_View();
$view->addBasePath(__DIR__ . '/templates/');

$filters = explode('|', $axValue);

if (empty($filters[0])) {
    $filterUsers = array();
} else {
    $filterUsers = explode(':', $filters[0]);
}

$filterCustomers = array_map(
    function ($customer) {
        return $customer['customerID'];
    },
    $database->get_customers($kga['user']['groups'])
);

if (!empty($filters[1])) {
    $filterCustomers = array_intersect($filterCustomers, explode(':', $filters[1]));
}

$filterProjects = array_map(
    function ($project) {
        return $project['projectID'];
    },
    $database->get_projects($kga['user']['groups'])
);

if (!empty($filters[2])) {
    $filterProjects = array_intersect($filterProjects, explode(':', $filters[2]));
}

$filterActivities = array_map(
    function ($activity) {
        return $activity['activityID'];
    },
    $database->get_activities($kga['user']['groups'])
);

if (!empty($filters[3])) {
    $filterActivities = array_intersect($filterActivities, explode(':', $filters[3]));
}

// if no userfilter is set, set it to current user
if (isset($kga['user']) && count($filterUsers) === 0) {
    array_push($filterUsers, $kga['user']['userID']);
}

if (isset($kga['customer'])) {
    $filterCustomers = array($kga['customer']['customerID']);
}

// ==================
// = handle request =
// ==================
switch ($axAction) {

    case 'reload':
        // track which activities we want to see, so we can exclude them when we create the plot
        $activitiesFilter = false;
        $projectsFilter = false;
        $projectsSelected = array();
        $activitiesSelected = array();

        if (is_array($filterProjects) && count($filterProjects) > 0) {
            $projectsFilter = $filterProjects;
            $projectsSelected = $projectsFilter;
        }
        if (is_array($filterActivities) && count($filterActivities) > 0) {
            $activitiesFilter = $filterActivities;
            $activitiesSelected = $activitiesFilter;
        }
        // Get all project for the logged in customer or the current user.
        if (isset($kga['customer'])) {
            $projects = $database->get_projects_by_customer(($kga['customer']['customerID']));
            $activities = $database->get_activities();
        } else {
            $customers = $database->get_customers($kga['user']['groups']);
            if (is_array($filterCustomers) && count($filterCustomers) > 0) {
                $projects = array();
                foreach ($filterCustomers as $customerId) {
                    $projects = array_merge($database->get_projects_by_customer($customerId), $projects);
                }
            } else {
                $projects = $database->get_projects($kga['user']['groups']);
            }
            $activities = $database->get_activities($kga['user']['groups']);
        }
        if (is_array($projects) && count($projects) > 0) {
            foreach ($projects as $index => $project) {
                if ($projectsFilter === false) {
                    $projectsSelected[] = $project['projectID'];
                }
                $projects[$index]['activities'] = $database->get_activities_by_project($project['projectID']);

                foreach ($projects[$index]['activities'] as $activity) {
                    if ($activitiesFilter === false) {
                        $activitiesSelected[] = $activity['activityID'];
                    }
                }
            }
        }
        $expensesOccurred = false;

        // If there are any projects create the plot data.
        if (count($projects) > 0)
        {
            $arr_plotdata = budget_plot_data($projects, $projectsSelected, $activitiesSelected, $expensesOccured, $kga);

            $renderProjects = array();
            $plotData = array();

            // filter out projects that are a) not selected or b) have no relevant/zero data to be displayed
            foreach ($projects as $project)
            {
                if (array_search($project['projectID'], $projectsSelected) === false) {
                    continue;
                }

                $temp = $project['projectID'];

                if (!isset($arr_plotdata[$temp])) {
                    continue;
                }

                // do not render projects that have only empty values
                if ($arr_plotdata[$temp]['total'] == 0 &&
                    $arr_plotdata[$temp]['budget'] == 0 &&
                    (!isset($arr_plotdata[$temp][0]['expenses']) || $arr_plotdata[$temp][0]['expenses'] == 0)
                ) {
                    continue;
                }

                $renderProjects[] = $project;

                // filter out activities that have no relevant/zero data to be plotted
                $plotData[$temp] = array();
                foreach ($arr_plotdata[$temp] as $id => $activity)
                {
                    $isActivity = is_array($activity) && isset($activity['name']);

                    if ($isActivity && array_search($id, $activitiesSelected) === false) {
                        continue;
                    }

                    if ($isActivity && $activity['total'] == 0 &&
                        $activity['budget'] == 0 && $activity['budget_total'] == 0 &&
                        $activity['approved'] == 0 && $activity['approved_total'] == 0
                    ) {
                        continue;
                    }

                    $plotData[$temp][$id] = $arr_plotdata[$temp][$id];
                }
            }

            $view->assign('plotdata', $plotData);
            $view->assign('projects', $renderProjects);
            $view->assign('activities', $activities);
        } else {
            $view->assign('projects', array());
        }
        $view->assign('projects_selected', $projectsSelected);
        $view->assign('activities_selected', $activitiesSelected);

        $chartColors = array(
            '#efefef',
            '#4bb2c5',
            '#EAA228',
            '#c5b47f',
            '#579575',
            '#839557',
            '#958c12',
            '#953579',
            '#4b5de4',
            '#d8b83f',
            '#ff5800',
            '#0085cc'
        );
        $view->assign('chartColors', json_encode($chartColors));

        // Create the keys which explain to the user which color means what for the project based charts
        $keys = array();
        $keys[] = array('color' => $chartColors[0], 'name' => $kga['lang']['ext_budget']['unusedBudget']);
        if ($expensesOccurred) {
            $keys[] = array('color' => $chartColors[1], 'name' => $kga['lang']['export_extension']['expenses']);
        }

        // the activity based charts only need numbers
        $view->assign('arr_keys', $keys);
        echo $view->render('charts.php');

        break;
}
