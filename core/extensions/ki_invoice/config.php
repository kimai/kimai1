<?php

return array(
    'name'          => "Invoice",
    'id'            => "ki_invoice",
    'permission'    => 'ki_invoice-access',
    'position'      => 60,
    'stylesheets'   => array(
        'css/styles.css.php'
    ),
    'javascripts'   => array(
        'js/invoice_init.js',
        'js/invoice_func.js'
    )
    // FIXME extensions [2]
    // TAB_CHANGE_TRIGGER       = "invoice_extension_tab_changed();"
    // TIMEFRAME_CHANGE_TRIGGER = "invoice_extension_timeframe_changed();"
);
