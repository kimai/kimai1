<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2009 Kimai-Development-Team
 *
 * Kimai is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; Version 3, 29 June 2007
 *
 * Kimai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimai; If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Export Processor.
 */

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require("../../includes/kspi.php");

require("private_func.php");


// ============================
// = parse general parameters =
// ============================

if ($axAction == 'export_csv'  ||
    $axAction == 'export_pdf'  ||
    $axAction == 'export_pdf2' ||
    $axAction == 'export_html' ||
    $axAction == 'export_xls'  ||
    $axAction == 'reload') {

  if (isset($_REQUEST['axColumns'])) {
    $axColumns = explode('|',$_REQUEST['axColumns']);
    $columns = array();
    foreach ($axColumns as $column)
      $columns[$column] = true;
  }

  $timeformat = strip_tags($_REQUEST['timeformat']);
  $timeformat = preg_replace('/([A-Za-z])/','%$1',$timeformat);

  $dateformat = strip_tags($_REQUEST['dateformat']);
  $dateformat = preg_replace('/([A-Za-z])/','%$1',$dateformat);

  $default_location = strip_tags($_REQUEST['default_location']);

  $reverse_order = isset($_REQUEST['reverse_order']);
  
  $filter_cleared     = $_REQUEST['filter_cleared'];
  $filter_refundable  = $_REQUEST['filter_refundable'];
  $filter_type        = $_REQUEST['filter_type'];
  
  $filters = explode('|',$axValue);

  if ($filters[0] == "")
    $filterUsr = array();
  else
    $filterUsr = explode(':',$filters[0]);

  if ($filters[1] == "")
    $filterKnd = array();
  else
    $filterKnd = explode(':',$filters[1]);

  if ($filters[2] == "")
    $filterPct = array();
  else
    $filterPct = explode(':',$filters[2]);

  if ($filters[3] == "")
    $filterEvt = array();
  else
    $filterEvt = explode(':',$filters[3]);

  // if no userfilter is set, set it to current user
  if (isset($kga['usr']) && count($filterUsr) == 0)
    array_push($filterUsr,$kga['usr']['usr_ID']);
    
  if (isset($kga['customer']))
    $filterKnd = array($kga['customer']['knd_ID']);
}





// ==================
// = handle request =
// ==================
switch ($axAction) {   
    

    // ======================
    // = set status cleared =
    // ======================
    case 'set_cleared':
      if (isset($kga['customer'])) {
        echo 0;
        break;
      }
      // $axValue: 1 = cleared, 0 = not cleared
      $id = isset($_REQUEST['id']) ? strip_tags($_REQUEST['id']) : null;
      $success = false;

      if (strncmp($id,"zef",3) == 0)
        $success = xp_zef_set_cleared(substr($id,3),$axValue==1);
      else if (strncmp($id,"exp",3) == 0)
        $success = xp_exp_set_cleared(substr($id,3),$axValue==1);

      echo $success?1:0;
    break;
    

    // =========================
    // = save selected columns =
    // =========================
    case 'toggle_header':
      // $axValue: header name
      $success = xp_toggle_header($axValue);
      echo $success?1:0;
    break;

    // ===========================
    // = Load data and return it =
    // ===========================
    case 'reload':
    	$arr_data = xp_get_arr($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt,false,$reverse_order,$default_location,$filter_cleared,$filter_type,false,$filter_refundable);
        $tpl->assign('arr_data', count($arr_data)>0?$arr_data:0);

        $tpl->assign('total', formatDuration(get_zef_time($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt,$filter_cleared)));

        $ann = xp_get_arr_usr($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        formatAnnotations($ann);
        $tpl->assign('usr_ann',$ann);
        
        $ann = xp_get_arr_knd($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        formatAnnotations($ann);
        $tpl->assign('knd_ann',$ann);

        $ann = xp_get_arr_pct($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        formatAnnotations($ann);
        $tpl->assign('pct_ann',$ann);

        $ann = xp_get_arr_evt($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt);
        formatAnnotations($ann);
        $tpl->assign('evt_ann',$ann);

        $tpl->assign('timeformat',$timeformat);
        $tpl->assign('dateformat',$dateformat);
        if (isset($kga['usr']))
          $tpl->assign('disabled_columns',xp_get_disabled_headers($kga['usr']['usr_ID']));
        $tpl->display("table.tpl");
    break;


    /**
     * Exort as html file.
     */
    case 'export_html':   

        usr_set_preferences(array(
          'print_summary' => isset($_REQUEST['print_summary'])?1:0,
          'reverse_order' => isset($_REQUEST['reverse_order'])?1:0),
          'ki_export.print.');
          
       
        $arr_data = xp_get_arr($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt,false,$reverse_order,$default_location,$filter_cleared,$filter_type,false,$filter_refundable);
        $timeSum = 0;
        $wageSum = 0;
        foreach ($arr_data as $data) {
          $timeSum += $data['dec_zef_time'];
          $wageSum += $data['wage'];
        }
        
        $tpl->assign('timespan',strftime($kga['date_format']['2'],$in).' - '.strftime($kga['date_format']['2'],$out) );

        if (isset($_REQUEST['print_summary'])) {
          //Create the summary. Same as in PDF export
          $zef_summary = array();
          $exp_summary = array();
          foreach ($arr_data as $one_entry) {

            if ($one_entry['type'] == 'zef') {
              if (isset($zef_summary[$one_entry['zef_evtID']])) {
                $zef_summary[$one_entry['zef_evtID']]['time']   += $one_entry['dec_zef_time']; //Sekunden
                $zef_summary[$one_entry['zef_evtID']]['wage']   += $one_entry['wage']; //Euro
              }
              else {
                $zef_summary[$one_entry['zef_evtID']]['name']         = html_entity_decode($one_entry['evt_name']);
                $zef_summary[$one_entry['zef_evtID']]['time']         = $one_entry['dec_zef_time'];
                $zef_summary[$one_entry['zef_evtID']]['wage']         = $one_entry['wage'];
              }
            }
            else {
              $exp_info['name']   = $kga['lang']['xp_ext']['expense'].': '.$one_entry['evt_name'];
              $exp_info['time']   = -1;
              $exp_info['wage'] = $one_entry['wage'];
              
              $exp_summary[] = $exp_info;
            }
          }
          
          $summary = array_merge($zef_summary,$exp_summary);
          $tpl->assign('summary',$summary);
        }
        else
          $tpl->assign('summary',0);


        // Create filter descirption, Same is in PDF export
        $customers = array();
        foreach ($filterKnd as $knd_id) {
          $customer_info = knd_get_data($knd_id);
          $customers[] = $customer_info['knd_name'];
        }
        $tpl->assign('customersFilter',implode(', ',$customers));

        $projects = array();
        foreach ($filterPct as $pct_id) {
          $project_info = pct_get_data($pct_id);
          $projects[] = $project_info['pct_name'];
        }
        $tpl->assign('projectsFilter',implode(', ',$projects));

        $tpl->assign('arr_data', count($arr_data)>0?$arr_data:0);

        $tpl->assign('columns',$columns);
        $tpl->assign('custom_timeformat',$timeformat);
        $tpl->assign('custom_dateformat',$dateformat);
        $tpl->assign('timeSum',$timeSum);
        $tpl->assign('wageSum',$wageSum);

        header("Content-Type: text/html");
        $tpl->display("formats/html.tpl");
    break;


    /**
     * Exort as excel file.
     */
    case 'export_xls':

        usr_set_preferences(array(
          'decimal_separator' => $_REQUEST['decimal_separator'],
          'reverse_order' => isset($_REQUEST['reverse_order'])?1:0),
          'ki_export.xls.');      
       
        $arr_data = xp_get_arr($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt,false,$reverse_order,$default_location,$filter_cleared,$filter_type,false,$filter_refundable);
        for ($i=0;$i<count($arr_data);$i++) {
          $arr_data[$i]['dec_zef_time'] = str_replace(".",$_REQUEST['decimal_separator'],$arr_data[$i]['dec_zef_time']);
          $arr_data[$i]['zef_rate'] = str_replace(".",$_REQUEST['decimal_separator'],$arr_data[$i]['zef_rate']);
          $arr_data[$i]['wage'] = str_replace(".",$_REQUEST['decimal_separator'],$arr_data[$i]['wage']);
        }
        $tpl->assign('arr_data', count($arr_data)>0?$arr_data:0);

        $tpl->assign('columns',$columns);
        $tpl->assign('custom_timeformat',$timeformat);
        $tpl->assign('custom_dateformat',$dateformat);

        header("Content-Disposition:attachment;filename=export.xls");
        header("Content-Type: application/vnd.ms-excel");
        $tpl->display("formats/excel.tpl");
    break;


    /**
     * Exort as csv file.
     */
    case 'export_csv':

        usr_set_preferences(array(
          'column_delimiter' => $_REQUEST['column_delimiter'],
          'quote_char' => $_REQUEST['quote_char'],
          'reverse_order' => isset($_REQUEST['reverse_order'])?1:0),
          'ki_export.csv.');      
       
        $arr_data = xp_get_arr($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt,false,$reverse_order,$default_location,$filter_cleared,$filter_type,false,$filter_refundable);
        $column_delimiter = $_REQUEST['column_delimiter'];
        $quote_char = $_REQUEST['quote_char'];

        header("Content-Disposition:attachment;filename=export.csv");
        header("Content-Type: text/csv ");

        $row = array();
        
        // output of headers
        if (isset($columns['date']))
          $row[] = csv_prepare_field($kga['lang']['datum'],$column_delimiter,$quote_char);
        if (isset($columns['from']))
          $row[] = csv_prepare_field($kga['lang']['in'],$column_delimiter,$quote_char);            
        if (isset($columns['to']))
          $row[] = csv_prepare_field($kga['lang']['out'],$column_delimiter,$quote_char);           
        if (isset($columns['time']))
          $row[] = csv_prepare_field($kga['lang']['time'],$column_delimiter,$quote_char);          
        if (isset($columns['dec_time']))
          $row[] = csv_prepare_field($kga['lang']['timelabel'],$column_delimiter,$quote_char);     
        if (isset($columns['rate']))
          $row[] = csv_prepare_field($kga['lang']['rate'],$column_delimiter,$quote_char);          
        if (isset($columns['wage']))
          $row[] = csv_prepare_field($kga['currency_name'],$column_delimiter,$quote_char);                      
        if (isset($columns['knd']))
          $row[] = csv_prepare_field($kga['lang']['knd'],$column_delimiter,$quote_char);           
        if (isset($columns['pct']))
          $row[] = csv_prepare_field($kga['lang']['pct'],$column_delimiter,$quote_char);           
        if (isset($columns['action']))
          $row[] = csv_prepare_field($kga['lang']['evt'],$column_delimiter,$quote_char);           
        if (isset($columns['comment']))
          $row[] = csv_prepare_field($kga['lang']['comment'],$column_delimiter,$quote_char);       
        if (isset($columns['location']))
          $row[] = csv_prepare_field($kga['lang']['zlocation'],$column_delimiter,$quote_char);      
        if (isset($columns['trackingnr']))
          $row[] = csv_prepare_field($kga['lang']['trackingnr'],$column_delimiter,$quote_char);    
        if (isset($columns['user']))
          $row[] = csv_prepare_field($kga['lang']['username'],$column_delimiter,$quote_char);          
        if (isset($columns['cleared']))
          $row[] = csv_prepare_field($kga['lang']['cleared'],$column_delimiter,$quote_char);  

        echo implode($column_delimiter,$row);
        echo "\n";

        // output of data
        foreach ($arr_data as $data) {
          $row = array();
          if (isset($columns['date']))
            $row[] = csv_prepare_field(strftime($dateformat,$data['time_in']),$column_delimiter,$quote_char);
          if (isset($columns['from']))
            $row[] = csv_prepare_field(strftime($timeformat,$data['time_in']),$column_delimiter,$quote_char);            
          if (isset($columns['to']))
            $row[] = csv_prepare_field(strftime($timeformat,$data['time_out']),$column_delimiter,$quote_char);           
          if (isset($columns['time']))
            $row[] = csv_prepare_field($data['zef_duration'],$column_delimiter,$quote_char);          
          if (isset($columns['dec_time']))
            $row[] = csv_prepare_field($data['dec_zef_time'],$column_delimiter,$quote_char);     
          if (isset($columns['rate']))
            $row[] = csv_prepare_field($data['zef_rate'],$column_delimiter,$quote_char);          
          if (isset($columns['wage']))
            $row[] = csv_prepare_field($data['wage'],$column_delimiter,$quote_char);                      
          if (isset($columns['knd']))
            $row[] = csv_prepare_field($data['knd_name'],$column_delimiter,$quote_char);           
          if (isset($columns['pct']))
            $row[] = csv_prepare_field($data['pct_name'],$column_delimiter,$quote_char);           
          if (isset($columns['action']))
            $row[] = csv_prepare_field($data['evt_name'],$column_delimiter,$quote_char);           
          if (isset($columns['comment']))
            $row[] = csv_prepare_field($data['comment'],$column_delimiter,$quote_char);       
          if (isset($columns['location']))
            $row[] = csv_prepare_field($data['location'],$column_delimiter,$quote_char);      
          if (isset($columns['trackingnr']))
            $row[] = csv_prepare_field($data['trackingnr'],$column_delimiter,$quote_char);    
          if (isset($columns['user']))
            $row[] = csv_prepare_field($data['username'],$column_delimiter,$quote_char);          
          if (isset($columns['cleared']))
            $row[] = csv_prepare_field($data['cleared'],$column_delimiter,$quote_char);  

        echo implode($column_delimiter,$row);
        echo "\n";
        }     
    break;



    /**
     * Export as tabular PDF document.
     */
    case 'export_pdf':

        usr_set_preferences(array(
          'print_comments'=>isset($_REQUEST['print_comments'])?1:0,
          'print_summary'=>isset($_REQUEST['print_summary'])?1:0,
          'create_bookmarks'=>isset($_REQUEST['create_bookmarks'])?1:0, 
          'download_pdf'=>isset($_REQUEST['download_pdf'])?1:0,
          'customer_new_page'=>isset($_REQUEST['customer_new_page'])?1:0, 
          'reverse_order'=>isset($_REQUEST['reverse_order'])?1:0,
          'pdf_format'=>'export_pdf'),
          'ki_export.pdf.');    

      $arr_data = xp_get_arr($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt,false,$reverse_order,$default_location,$filter_cleared,$filter_type,false,$filter_refundable);

      $knd_arr_data = array();
      if (isset($_REQUEST['customer_new_page'])) {
        foreach ($arr_data as $row) {
          $knd_id = $row['pct_kndID'];

          // create key for customer, if not present
          if (!array_key_exists($knd_id,$knd_arr_data))
            $knd_arr_data[$knd_id] = array();

          // add row
          $knd_arr_data[$knd_id][] = $row;

        }
      }

      require('export_pdf.php');
    break;



    /**
     * Export as a PDF document in a list format.
     */
    case 'export_pdf2':

        usr_set_preferences(array(
          'print_comments'=>isset($_REQUEST['print_comments'])?1:0,
          'print_summary'=>isset($_REQUEST['print_summary'])?1:0,
          'create_bookmarks'=>isset($_REQUEST['create_bookmarks'])?1:0, 
          'download_pdf'=>isset($_REQUEST['download_pdf'])?1:0,
          'customer_new_page'=>isset($_REQUEST['customer_new_page'])?1:0, 
          'reverse_order'=>isset($_REQUEST['reverse_order'])?1:0,
          'pdf_format'=>'export_pdf2'),
          'ki_export.pdf.');    
       
      $arr_data = xp_get_arr($in,$out,$filterUsr,$filterKnd,$filterPct,$filterEvt,false,$reverse_order,$default_location,$filter_cleared,$filter_type,false,$filter_refundable);

      // sort data into new array, where first dimension is customer and second dimension is project
      $pdf_arr_data = array();
      foreach ($arr_data as $row) {
        $knd_id = $row['pct_kndID'];
        $pct_id = $row['pct_ID'];

        // create key for customer, if not present
        if (!array_key_exists($knd_id,$pdf_arr_data))
          $pdf_arr_data[$knd_id] = array();

        // create key for project, if not present
        if (!array_key_exists($pct_id,$pdf_arr_data[$knd_id]))
          $pdf_arr_data[$knd_id][$pct_id] = array();

        // add row
        $pdf_arr_data[$knd_id][$pct_id][] = $row;

      }
      require('export_pdf2.php');
      break;

}

?>
