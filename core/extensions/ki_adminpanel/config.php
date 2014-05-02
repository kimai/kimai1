<?php

return array(
    'name'          => "Admin Panel",
    'id'            => "adminPanel_extension",
    'permission'    => 'adminPanel_extension-access',
    'position'      => 20,
    'stylesheets'   => array(
        'css/styles.css'
    ),
    'javascripts'   => array(
        'js/ap_func.js',
        'js/ap_init.js',
        'js/flotala.js'
    )
    // FIXME extensions [2]
    // TAB_CHANGE_TRIGGER = "adminPanel_extension_tab_changed();"
    // CHANGE_CUSTOMER_TRIGGER = "adminPanel_extension_customers_changed();"
    // CHANGE_PROJECT_TRIGGER = "adminPanel_extension_projects_changed();"
    // CHANGE_ACTIVITY_TRIGGER = "dminPanel_extension_activities_changed();"
    // CHANGE_USER_TRIGGER = "dminPanel_extension_users_changed();"
);
