<?php

function budget_plot_data($projects) {

$wages = array(); // pct_ID => array(costs of evt1,costs of evt2,...)

$zef_arr = get_arr_zef(0,time());

$events = get_arr_evt("all");

// sum up wages for every project and every event
foreach ($zef_arr as $zef) {

  if ($zef['wage_decimal'] == 0.00)
    continue;

  if (!isset($wages[$zef['zef_pctID']])) {
    // project doesn't exists.

    $wages[$zef['zef_pctID']] = array();
    foreach ($events as $event) {
      $wages[$zef['zef_pctID']][$event['evt_ID']] = 0;
    }

  }

  $wages[$zef['zef_pctID']][$zef['zef_evtID']] += $zef['wage_decimal'];
}

// convert array to javascript array
$plot_data = array();
foreach ($wages as $project_id => $wage_array) {
  logfile(serialize($wage_array));
  $plot_data[$project_id] = '['.implode(',',$wage_array).']';
}
return $plot_data;
}


?>