
    <script type="text/javascript">
        $(document).ready(function() {
            ts_ext_onload();
        }); 
    </script>

<?php

$addRecord = '';
if (isset($this->kga['user'])) {
    $addRecord = '<a href="#" onClick="floaterShow(\'../extensions/ki_timesheets/floaters.php\',\'add_edit_timeSheetEntry\',selected_project+\'|\'+selected_activity,0,650); $(this).blur(); return false;">'.$this->kga['lang']['add'].'</a>';
}

$colgroup = array(
    'option' => '&nbsp;',
    'date' => $this->kga['lang']['datum'],
    'from' => $this->kga['lang']['in'],
    'to' => $this->kga['lang']['out'],
    'time' => $this->kga['lang']['time']
);

if ($this->showRates) {
    $colgroup['wage'] = $this->kga['lang']['wage'];
}

$colgroup['customer'] = $this->kga['lang']['customer'];
$colgroup['project'] = $this->kga['lang']['project'];
$colgroup['activity'] = $this->kga['lang']['activity'];

if ($this->showTrackingNumber) {
    $colgroup['trackingnumber'] = $this->kga['lang']['trackingNumber'];
}
$colgroup['username'] = $this->kga['lang']['username'];

// attention - same config is in timeSheet.php as well !!!!
$dataTable = array(
    'header_id'     => 'timeSheet_head',
    'header_button' => $addRecord,
    'colgroup'      => $colgroup,
    'data_id'       => 'timeSheet'
);

echo $this->dataTable($dataTable)->renderHeader();
echo $this->timeSheet_display;
echo $this->dataTable()->renderFooter();

?>