<?php

return array(
    'name'          => "Export",
    'id'            => "ki_export",
    'customer'      => true,
    'permission'    => 'ki_export-access',
    'position'      => 50,
    'stylesheets'   => array(
        'css/styles.css'
    ),
    'javascripts'   => array(
        'js/xp_func.js',
        'js/xp_init.js'
    )
    // FIXME extensions [2]
    // TAB_CHANGE_TRIGGER       = "export_extension_tab_changed();"
    // TIMEFRAME_CHANGE_TRIGGER = "export_extension_timeframe_changed();"
    // CHANGE_CUSTOMER_TRIGGER       = "export_extension_customers_changed();"
    // CHANGE_PROJECT_TRIGGER       = "export_extension_projects_changed();"
    // CHANGE_ACTIVITY_TRIGGER       = "export_extension_activities_changed();"
    // LIST_FILTER_TRIGGER      = "export_extension_reload();"
    // RESIZE_TRIGGER           = "export_extension_resize();"
);
