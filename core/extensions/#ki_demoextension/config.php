<?php

return array(
    // Fallback title if this extensions title is not translated
    'name'          => "",
    // ID of the extensions, must be unique within Kimai (as example used for translation)
    'id'            => "demo_ext",
    // the initialization file for this extension (default: init.php)
    'init_file'     => 'init.php',
    // whether customers can access this tab (default: false)
    'customer'      => true,
    // permission to check if the user can access this extension
    'permission'    => 'demo_ext-access',
    // position within the menu (TABs) - default menu positions are 10,20,30,40,50,60,70
    'position'      => 80,
    // stylesheets to include when kimai page is loading
    'stylesheets'   => array(
        'css/demo.css'
    ),
    // javascripts file to include when kimai page is loading
    'javascripts'   => array(
        'js/func.js'
    )
);
