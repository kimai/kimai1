<?php

return array(
    'name'          => "Expenses",
    'id'            => "ki_expenses",
    'customer'      => true,
    'permission'    => 'ki_expenses-access',
    'position'      => 40,
    'stylesheets'   => array(
        'css/styles.css'
    ),
    'javascripts'   => array(
        'js/exp_func.js',
        'js/exp_init.js'
    )
    // FIXME extensions [2]
    // TAB_CHANGE_TRIGGER       = "expense_extension_triggerchange();"
    // TIMEFRAME_CHANGE_TRIGGER = "expense_extension_timeframe_changed();"
    // LIST_FILTER_TRIGGER      = "expense_extension_reload();"
    // RESIZE_TRIGGER           = "expense_extension_resize();"
);
