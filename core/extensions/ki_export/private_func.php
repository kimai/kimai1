<?php

$all_column_headers = array('date','from','to','time','dec_time','rate','wage','knd','pct','evt','comment','location','trackingnr','user','cleared');


/**
 * returns expenses for specific user as multidimensional array
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return array
 * @author th 
 */

$expense_ext_available = false;
if (file_exists('../ki_expenses/private_db_layer_'.$kga['server_conn'].'.php')) {
  include('../ki_expenses/private_db_layer_'.$kga['server_conn'].'.php');
  $expense_ext_available = true;
}
include('private_db_layer_'.$kga['server_conn'].'.php');

function xp_get_arr($start,$end,$users = null,$customers = null,$projects = null,$events = null,$limit=false,$reverse_order=false,$default_location='',$filter_cleared=-1,$filter_type=-1,$limitCommentSize=true) {
  global $expense_ext_available;

    $zef_arr = array();
    $exp_arr = array();
    
    if ($filter_type != 1)
      $zef_arr = get_arr_zef($start,$end,$users,$customers,$projects,$events,$limit,$reverse_order);
    
    if ($filter_type != 0 && $expense_ext_available)
      $exp_arr = get_arr_exp($start,$end,$users,$customers,$projects,$limit,$reverse_order);

    $result_arr = array();

    $zef_arr_index = 0;
    $exp_arr_index = 0;
    while ($zef_arr_index < count($zef_arr) && $exp_arr_index < count($exp_arr)) {
      $arr = array();
      if ( (!$reverse_order && ($zef_arr[$zef_arr_index]['zef_in'] > $exp_arr[$exp_arr_index]['exp_timestamp']) ) ||
           ( $reverse_order && ($zef_arr[$zef_arr_index]['zef_in'] < $exp_arr[$exp_arr_index]['exp_timestamp']) ) ) {
        $arr['type']           = 'zef';
        $arr['id']             = $zef_arr[$zef_arr_index]['zef_ID'];
        $arr['time_in']        = $zef_arr[$zef_arr_index]['zef_in'];
        $arr['time_out']       = $zef_arr[$zef_arr_index]['zef_out'];
        $arr['zef_time']       = $zef_arr[$zef_arr_index]['zef_time'];
        $arr['zef_apos']       = $zef_arr[$zef_arr_index]['zef_apos'];
        $arr['zef_coln']       = $zef_arr[$zef_arr_index]['zef_coln'];
        $arr['dec_zef_time']   = sprintf("%01.2f",$zef_arr[$zef_arr_index]['zef_time']/3600);
        $arr['zef_rate']       = $zef_arr[$zef_arr_index]['zef_rate'];
        $arr['wage']           = $zef_arr[$zef_arr_index]['wage'];
        $arr['pct_kndID']      = $zef_arr[$zef_arr_index]['pct_kndID'];
        $arr['knd_name']       = $zef_arr[$zef_arr_index]['knd_name'];
        $arr['pct_ID']         = $zef_arr[$zef_arr_index]['pct_ID'];
        $arr['pct_name']       = $zef_arr[$zef_arr_index]['pct_name'];
        $arr['pct_comment']    = $zef_arr[$zef_arr_index]['pct_comment'];
        $arr['zef_evtID']      = $zef_arr[$zef_arr_index]['zef_evtID'];
        $arr['evt_name']       = $zef_arr[$zef_arr_index]['evt_name'];
        if ($limitCommentSize)
          $arr['comment']      = addEllipsis($zef_arr[$zef_arr_index]['zef_comment'], 150);
        else
          $arr['comment']      = $zef_arr[$zef_arr_index]['zef_comment'];
        $arr['comment_type']   = $zef_arr[$zef_arr_index]['zef_comment_type'];
        $arr['location']       = $zef_arr[$zef_arr_index]['zef_location'];
        if (empty($arr['location']))
          $arr['location']     = $default_location;
        $arr['trackingnr']     = $zef_arr[$zef_arr_index]['zef_trackingnr'];
        $arr['username']       = $zef_arr[$zef_arr_index]['usr_name'];
        $arr['cleared']        = $zef_arr[$zef_arr_index]['zef_cleared'];
        $zef_arr_index++;
      }
      else {
        $arr['type']           = 'exp';
        $arr['id']             = $exp_arr[$exp_arr_index]['exp_ID'];
        $arr['time_in']        = $exp_arr[$exp_arr_index]['exp_timestamp'];
        $arr['time_out']       = $exp_arr[$exp_arr_index]['exp_timestamp'];
        $arr['zef_time']       = null;
        $arr['zef_apos']       = null;
        $arr['zef_coln']       = null;
        $arr['dec_zef_time']   = null;
        $arr['zef_rate']       = null;
        $arr['wage']           = $exp_arr[$exp_arr_index]['exp_value'];
        $arr['pct_kndID']      = $exp_arr[$exp_arr_index]['pct_kndID'];
        $arr['knd_name']       = $exp_arr[$exp_arr_index]['knd_name'];
        $arr['pct_ID']         = $exp_arr[$exp_arr_index]['pct_ID'];
        $arr['pct_name']       = $exp_arr[$exp_arr_index]['pct_name'];
        $arr['pct_comment']    = $zef_arr[$zef_arr_index]['pct_comment'];
        if ($limitCommentSize)
          $arr['comment']      = addEllipsis($exp_arr[$exp_arr_index]['exp_comment'],150);
        else
          $arr['comment']      = $exp_arr[$exp_arr_index]['exp_comment'];
        $arr['evt_name']       = $exp_arr[$exp_arr_index]['exp_designation'];
        $arr['comment']        = $exp_arr[$exp_arr_index]['exp_comment'];
        $arr['comment_type']   = $exp_arr[$exp_arr_index]['exp_comment_type'];
	$arr['location']       = $default_location;
        $arr['trackingnr']     = null;
        $arr['username']       = $exp_arr[$exp_arr_index]['usr_name'];
        $arr['cleared']        = $exp_arr[$exp_arr_index]['exp_cleared'];
        $exp_arr_index++;
      }
      if ($arr['cleared']==$filter_cleared)
        continue;
      $result_arr[] = $arr;
    }
    while ($zef_arr_index < count($zef_arr)) {
      $arr = array();
      $arr['type']           = 'zef';
      $arr['id']             = $zef_arr[$zef_arr_index]['zef_ID'];
      $arr['time_in']        = $zef_arr[$zef_arr_index]['zef_in'];
      $arr['time_out']       = $zef_arr[$zef_arr_index]['zef_out'];
      $arr['zef_time']       = $zef_arr[$zef_arr_index]['zef_time'];
      $arr['zef_apos']       = $zef_arr[$zef_arr_index]['zef_apos'];
      $arr['zef_coln']       = $zef_arr[$zef_arr_index]['zef_coln'];
      $arr['dec_zef_time']   = sprintf("%01.2f",$zef_arr[$zef_arr_index]['zef_time']/3600);
      $arr['zef_rate']       = $zef_arr[$zef_arr_index]['zef_rate'];
      $arr['wage']           = $zef_arr[$zef_arr_index]['wage'];
      $arr['pct_kndID']      = $zef_arr[$zef_arr_index]['pct_kndID'];
      $arr['knd_name']       = $zef_arr[$zef_arr_index]['knd_name'];
      $arr['pct_ID']         = $zef_arr[$zef_arr_index]['pct_ID'];
      $arr['pct_name']       = $zef_arr[$zef_arr_index]['pct_name'];
      $arr['pct_comment']    = $zef_arr[$zef_arr_index]['pct_comment'];
      $arr['zef_evtID']      = $zef_arr[$zef_arr_index]['zef_evtID'];
      $arr['evt_name']       = $zef_arr[$zef_arr_index]['evt_name'];
      if ($limitCommentSize)
        $arr['comment']      = addEllipsis($zef_arr[$zef_arr_index]['zef_comment'], 150);
      else
        $arr['comment']      = $zef_arr[$zef_arr_index]['zef_comment'];
      $arr['comment_type']   = $zef_arr[$zef_arr_index]['zef_comment_type'];
      $arr['location']       = $zef_arr[$zef_arr_index]['zef_location'];
        if (empty($arr['location']))
          $arr['location']     = $default_location;
      $arr['trackingnr']     = $zef_arr[$zef_arr_index]['zef_trackingnr'];
      $arr['username']       = $zef_arr[$zef_arr_index]['usr_name'];
      $arr['cleared']        = $zef_arr[$zef_arr_index]['zef_cleared'];
      $zef_arr_index++;
      if ($arr['cleared']==$filter_cleared)
        continue;
      $result_arr[] = $arr;
    }
    while ($exp_arr_index < count($exp_arr)) {
      $arr = array();
      $arr['type']           = 'exp';
      $arr['id']             = $exp_arr[$exp_arr_index]['exp_ID'];
      $arr['time_in']        = $exp_arr[$exp_arr_index]['exp_timestamp'];
      $arr['time_out']       = $exp_arr[$exp_arr_index]['exp_timestamp'];
      $arr['wage']           = $exp_arr[$exp_arr_index]['exp_value'];
      $arr['pct_kndID']      = $exp_arr[$exp_arr_index]['pct_kndID'];
      $arr['knd_name']       = $exp_arr[$exp_arr_index]['knd_name'];
      $arr['pct_ID']         = $exp_arr[$exp_arr_index]['pct_ID'];
      $arr['pct_name']       = $exp_arr[$exp_arr_index]['pct_name'];
      if ($limitCommentSize)
        $arr['comment']      = addEllipsis($exp_arr[$exp_arr_index]['exp_comment'],150);
      else
        $arr['comment']      = $exp_arr[$exp_arr_index]['exp_comment'];
      $arr['evt_name']       = $exp_arr[$exp_arr_index]['exp_designation'];
      $arr['comment']        = $exp_arr[$exp_arr_index]['exp_comment'];
      $arr['comment_type']   = $exp_arr[$exp_arr_index]['exp_comment_type'];
      $arr['username']       = $exp_arr[$exp_arr_index]['usr_name'];
      $arr['cleared']        = $exp_arr[$exp_arr_index]['exp_cleared'];
      $exp_arr_index++;
      if ($arr['cleared']==$filter_cleared)
        continue;
      $result_arr[] = $arr;
    }
    return $result_arr;
}


function xp_get_arr_usr($start,$end,$users = null,$customers = null,$projects = null,$events = null) {
    $arr = get_arr_time_usr($start,$end,$users,$customers,$projects,$events);
    return $arr;
}


function xp_get_arr_knd($start,$end,$users = null,$customers = null,$projects = null,$events = null) {
    $arr = get_arr_time_knd($start,$end,$users,$customers,$projects,$events);
    return $arr;
}

function xp_get_arr_pct($start,$end,$users = null,$customers = null,$projects = null,$events = null) {
    $arr = get_arr_time_pct($start,$end,$users,$customers,$projects,$events);
    return $arr;
}

function xp_get_arr_evt($start,$end,$users = null,$customers = null,$projects = null,$events = null) {
    $arr = get_arr_time_evt($start,$end,$users,$customers,$projects,$events);
    return $arr;
}


function csv_prepare_field($field,$column_delimiter,$quote_char) {
  if (strpos($field,$column_delimiter) === false &&
      strpos($field,$quote_char) === false &&
      strpos($field,"\n") === false)
    return $field;

  $field = str_replace($quote_char,$quote_char.$quote_char,$field);
  $field = $quote_char.$field.$quote_char;

  return $field;
}

?>