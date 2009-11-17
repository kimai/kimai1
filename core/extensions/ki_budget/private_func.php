<?php

include('../ki_expenses/private_db_layer_'.$kga['server_conn'].'.php');

function budget_plot_data($projects) {

$wages = array(); // pct_ID => array(expenses,costs of evt1,costs of evt2,...)


$events = get_arr_evt("all");


/* create mapping from event id to position in array
 * 0 = expenses
 * 1 = first event
 * ...
 */
$event_id_to_pos_map = array();
$i = 1;
foreach ($events as $event) {
  $event_id_to_pos_map[$event['evt_ID']] = $i++;
}
unset($i);


/*
 * sum up expenses
 */
$exp_arr = get_arr_exp(0,time());
foreach ($exp_arr as $exp) {

  if (!isset($wages[$exp['exp_pctID']])) {
    // project doesn't exists.
    $wages[$exp['exp_pctID']] = array_fill(0,count($events)+1,0);
  }

  $wages[$exp['exp_pctID']][0] += $exp['exp_value'];
}


/*
 * sum up wages for every project and every event
 */
$zef_arr = get_arr_zef(0,time());
foreach ($zef_arr as $zef) {

  if ($zef['wage_decimal'] == 0.00)
    continue;

  if (!isset($wages[$zef['zef_pctID']])) {
    // project doesn't exists.
    $wages[$zef['zef_pctID']] = array_fill(0,count($events)+1,0);
  }

  $wages[$zef['zef_pctID']][$event_id_to_pos_map[$zef['zef_evtID']]] += $zef['wage_decimal'];
}

/* 
 * convert array to javascript array
 */
$plot_data = array();
foreach ($wages as $project_id => $wage_array) {
  $plot_data[$project_id] = '['.implode(',',$wage_array).']';
}
return $plot_data;
}


?>