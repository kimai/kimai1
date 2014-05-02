<?php

return array(
    'name'          => "Debug",
    'id'            => "deb_ext",
    'permission'    => 'deb_ext-access',
    'position'      => 70,
    'stylesheets'   => array(
        'css/styles.css.php'
    ),
    'javascripts'   => array(
        'js/func.js',
        'js/init.js'
    )
    // FIXME extensions [2]
    // TAB_CHANGE_TRIGGER = "debug_extension_tab_changed();"
    // REG_TIMEOUTS = "deb_ext_refreshTimer"
);
