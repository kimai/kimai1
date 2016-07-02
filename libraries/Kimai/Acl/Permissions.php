<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team since 2006
 *
 * Kimai is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; Version 3,
 * 29 June 2007
 *
 * Kimai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimai; If not,
 * see <http://www.gnu.org/licenses/>.
 */

/**
 * The class to hold all available permissions.
 */
class Kimai_Acl_Permissions
{
    /**
     * @var array
     */
    protected static $globalRolePermissions = array();

    /**
     * @var array
     */
    protected static $memberShipRolePermissions = array();

    /**
     * @return array
     */
    protected static function getGlobalPreset()
    {
        return array(
            0 => 'adminPanel_extension-access',
            1 => 'adminPanel_extension-editAdvanced',
            2 => 'core-activity-otherGroup-add',
            3 => 'core-activity-otherGroup-assign',
            4 => 'core-activity-otherGroup-delete',
            5 => 'core-activity-otherGroup-edit',
            6 => 'core-activity-otherGroup-unassign',
            7 => 'core-activity-otherGroup-view',
            8 => 'core-customer-otherGroup-add',
            9 => 'core-customer-otherGroup-assign',
            10 => 'core-customer-otherGroup-delete',
            11 => 'core-customer-otherGroup-edit',
            12 => 'core-customer-otherGroup-unassign',
            13 => 'core-customer-otherGroup-view',
            14 => 'core-group-add',
            15 => 'core-group-otherGroup-delete',
            16 => 'core-group-otherGroup-edit',
            17 => 'core-group-otherGroup-view',
            18 => 'core-project-otherGroup-add',
            19 => 'core-project-otherGroup-assign',
            20 => 'core-project-otherGroup-delete',
            21 => 'core-project-otherGroup-edit',
            22 => 'core-project-otherGroup-unassign',
            23 => 'core-project-otherGroup-view',
            24 => 'core-status-add',
            25 => 'core-status-delete',
            26 => 'core-status-edit',
            27 => 'core-user-otherGroup-add',
            28 => 'core-user-otherGroup-assign',
            29 => 'core-user-otherGroup-delete',
            30 => 'core-user-otherGroup-edit',
            31 => 'core-user-otherGroup-unassign',
            32 => 'core-user-otherGroup-view',
            33 => 'deb_ext-access',
            34 => 'demo_ext-access',
            35 => 'ki_budget-access',
            36 => 'ki_expenses-access',
            37 => 'ki_expenses-otherEntry-otherGroup-add',
            38 => 'ki_expenses-otherEntry-otherGroup-delete',
            39 => 'ki_expenses-otherEntry-otherGroup-edit',
            40 => 'ki_expenses-ownEntry-add',
            41 => 'ki_expenses-ownEntry-delete',
            42 => 'ki_expenses-ownEntry-edit',
            43 => 'ki_export-access',
            44 => 'ki_invoice-access',
            45 => 'ki_timesheet-access',
            46 => 'ki_timesheets-editRates',
            47 => 'ki_timesheets-otherEntry-otherGroup-add',
            48 => 'ki_timesheets-otherEntry-otherGroup-delete',
            49 => 'ki_timesheets-otherEntry-otherGroup-edit',
            50 => 'ki_timesheets-ownEntry-add',
            51 => 'ki_timesheets-ownEntry-delete',
            52 => 'ki_timesheets-ownEntry-edit',
            53 => 'ki_timesheets-showRates',
        );
    }

    /**
     * @return array
     */
    protected static function getMembershipPreset()
    {
        return array(
            0 => 'core-activity-add',
            1 => 'core-activity-assign',
            2 => 'core-activity-delete',
            3 => 'core-activity-edit',
            4 => 'core-activity-unassign',
            5 => 'core-customer-add',
            6 => 'core-customer-assign',
            7 => 'core-customer-delete',
            8 => 'core-customer-edit',
            9 => 'core-customer-unassign',
            10 => 'core-group-delete',
            11 => 'core-group-edit',
            12 => 'core-project-add',
            13 => 'core-project-assign',
            14 => 'core-project-delete',
            15 => 'core-project-edit',
            16 => 'core-project-unassign',
            17 => 'core-user-add',
            18 => 'core-user-assign',
            19 => 'core-user-delete',
            20 => 'core-user-edit',
            21 => 'core-user-unassign',
            22 => 'core-user-view',
            23 => 'ki_expenses-otherEntry-ownGroup-add',
            24 => 'ki_expenses-otherEntry-ownGroup-delete',
            25 => 'ki_expenses-otherEntry-ownGroup-edit',
            26 => 'ki_timesheets-otherEntry-ownGroup-add',
            27 => 'ki_timesheets-otherEntry-ownGroup-delete',
            28 => 'ki_timesheets-otherEntry-ownGroup-edit',
        );
    }

    /**
     * @return array
     */
    public static function getMembershipPermissions()
    {
        return array_merge(
            static::getMembershipPreset(),
            static::$memberShipRolePermissions
        );
    }

    /**
     * @return array
     */
    public static function getGlobalPermissions()
    {
        return array_merge(
            static::getGlobalPreset(),
            static::$globalRolePermissions
        );
    }

    /**
     * Adds another global permission.
     * Can be utilized by extensions to add another permission during runtime.
     * These permissions will be available in the "edit global role" admin screen floater.
     *
     * @param string $name
     */
    public static function addGlobalPermission($name)
    {
        self::$globalRolePermissions[] = $name;
    }

    /**
     * Adds another membership permission.
     * Can be utilized by extensions to add another permission during runtime.
     * These permissions will be available in the "edit membership role" admin screen floater.
     *
     * @param string $name
     */
    public static function addMembershipPermission($name)
    {
        self::$memberShipRolePermissions[] = $name;
    }
}
