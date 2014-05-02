<?php

return array(
    'name'          => "Timesheet",
    'id'            => "ki_timesheet",
    'customer'      => true,
    'permission'    => 'ki_timesheet-access',
    'position'      => 10,
    'stylesheets'   => array(
        'css/styles.css'
    ),
    'javascripts'   => array(
        'js/ts_func.js',
        'js/ts_init.js'
    )
    // FIXME extensions [2]
    // TAB_CHANGE_TRIGGER       = "timesheet_extension_tab_changed();"
    // CHANGE_CUSTOMER_TRIGGER  = "timesheet_extension_customers_changed();"
    // CHANGE_PROJECT_TRIGGER   = "timesheet_extension_projects_changed();"
    // CHANGE_ACTIVITY_TRIGGER  = "timesheet_extension_activities_changed();"
    // LIST_FILTER_TRIGGER      = "ts_ext_reload();"
    // RESIZE_TRIGGER           = "ts_ext_resize();"
);
