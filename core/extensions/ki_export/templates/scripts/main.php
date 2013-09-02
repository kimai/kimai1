    <script type="text/javascript">
        $(document).ready(function() {
            export_extension_onload();
        });
    </script>
    <?php

    $colgroup = array();
    $columns = array(
        'date' => 'datum',
        'from' => 'in',
        'to' => 'out',
        'time' => 'time',
        'dec_time' => 'timelabel',
        'rate' => 'rate_short',
        'wage' => 'total',
        'budget' => 'budget',
        'approved' => 'approved',
        'status' => 'status',
        'billable' => 'billable',
        'customer' => 'customer',
        'project' => 'project',
        'activity' => 'activity',
        'description' => 'description',
        'comment' => 'comment',
        'location' => 'location',
        'trackingNumber' => 'trackingNumber',
        'user' => 'username',
        'cleared' => null
    );

    foreach($columns as $colName => $colTitle) {
        $colgroup[$colName] = array(
            'title' => ($colTitle === null ? '' : '<a onClick="export_toggle_column(\''.$colName.'\');">'.$this->translate($colTitle).'</a>'),
            'class' => (isset($this->disabled_columns[$colName]) ? 'disabled' : '')
        );
    }

    // attention - same config is in table.php as well !!!!
    $dataTable = array(
        'header_id'             => 'export_head',
        'header_button'         => '<a href="#" onClick="$(\'#xptable td.cleared>a\').click(); return false;">invert</a>',
        'header_button_class'   => 'right',
        'colgroup'              => $colgroup,
        'data_id'               => 'xp',
        'table_id'              => 'xptable'
    );

    echo $this->dataTable($dataTable)->renderHeader();
    echo $this->table_display;
    echo $this->dataTable()->renderFooter();

    ?>