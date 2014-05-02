<?php

return array(
    'name'          => "Budget",
    'id'            => "ki_budget",
    'customer'      => true,
    'permission'    => 'ki_budget-access',
    'position'      => 30,
    'stylesheets'   => array(
        'css/styles.css'
    ),
    'javascripts'   => array(
        'js/budget_func.js',
        'js/budget_init.js'
    )
    // FIXME extensions [2]
    // LIST_FILTER_TRIGGER      = "budget_extension_reload();"
);
