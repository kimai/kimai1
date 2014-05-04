<script type="text/javascript">
    $(document).ready(function() {
        expense_extension_onload();
    });
</script>
<?php
$addRecord = '';
if (isset($this->kga['user'])) {
    $addRecord = '<a href="#" onClick="floaterShow(\'../extensions/ki_expenses/floaters.php\',\'add_edit_record\',0,0,600); $(this).blur(); return false;">'.$this->kga['lang']['add'].'</a>';
}

// attention - same config is in expenses.php as well !!!!
$dataTable = array(
    'header_id'     => 'expenses_head',
    'header_button' => $addRecord,
    'colgroup'      => array(
        'option' => '&nbsp;',
        'date' => $this->kga['lang']['datum'],
        'time' => $this->kga['lang']['timelabel'],
        'value' => $this->kga['lang']['expense'],
        'refundable' => $this->kga['lang']['refundable'],
        'customer' => $this->kga['lang']['customer'],
        'project' => $this->kga['lang']['project'],
        'designation' => $this->kga['lang']['designation'],
        'username' => $this->kga['lang']['username']
    ),
    'data_id'       => 'expenses'
);

echo $this->dataTable($dataTable)->renderHeader();
echo $this->expenses_display;
echo $this->dataTable()->renderFooter();

?>