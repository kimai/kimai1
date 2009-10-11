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
 * along with Kimai; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 */

// ================
// = EX PROCESSOR =
// ================

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
    $axAction == 'export_html' ||
    $axAction == 'export_xls'  ||
    $axAction == 'reload') {

  $axColumns = explode('|',$_REQUEST['axColumns']);
  $columns = array();
  foreach ($axColumns as $column)
    $columns[$column] = true;

  $timeformat = strip_tags($_REQUEST['timeformat']);
  $timeformat = preg_replace('/([A-Za-z])/','%$1',$timeformat);

  $dateformat = strip_tags($_REQUEST['dateformat']);
  $dateformat = preg_replace('/([A-Za-z])/','%$1',$dateformat);

  $default_location = strip_tags($_REQUEST['default_location']);
  
  $filter_cleared = $_REQUEST['filter_cleared'];

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
      // $axValue: 1 = cleared, 0 = not cleared
      $id = isset($_REQUEST['id']) ? strip_tags($_REQUEST['id']) : null;
      $success = false;

      if (strncmp($id,"zef",3) == 0)
        $success = xp_zef_set_cleared(substr($id,3),$axValue==1);
      else if (strncmp($id,"exp",3) == 0)
        $success = xp_exp_set_cleared(substr($id,3),$axValue==1);

      echo $success?1:0;
    break;

    // ===========================
    // = Load data and return it =
    // ===========================
    case 'reload':

        $arr_data = xp_get_arr($in,$out,$filterUsr,$filterKnd,$filterPct,1,$default_location,$filter_cleared);
        $tpl->assign('arr_data', count($arr_data)>0?$arr_data:0);

        $tpl->assign('total', intervallApos(get_zef_time($in,$out,$filterUsr,$filterKnd,$filterPct)));

        $ann = xp_get_arr_usr($in,$out,$filterUsr,$filterKnd,$filterPct);
        $ann_new = intervallApos($ann);
        $tpl->assign('usr_ann',$ann_new);
        
        $ann = xp_get_arr_knd($in,$out,$filterUsr,$filterKnd,$filterPct);
        $ann_new = intervallApos($ann);
        $tpl->assign('knd_ann',$ann_new);

        $ann = xp_get_arr_pct($in,$out,$filterUsr,$filterKnd,$filterPct);
        $ann_new = intervallApos($ann);
        $tpl->assign('pct_ann',$ann_new);

        $ann = xp_get_arr_evt($in,$out,$filterUsr,$filterKnd,$filterPct);
        $ann_new = intervallApos($ann);
        $tpl->assign('evt_ann',$ann_new);

        $tpl->assign('custom_timeformat',$timeformat);
        $tpl->assign('custom_dateformat',$dateformat);
        $tpl->assign('custom_filter',$filter);
        $tpl->display("table.tpl");
    break;


    case 'export_html':       
       
        $arr_data = xp_get_arr($in,$out,$filterUsr,$filterKnd,$filterPct,1,$default_location,$filter_cleared);
        $tpl->assign('arr_data', count($arr_data)>0?$arr_data:0);

        $tpl->assign('columns',$columns);
        $tpl->assign('custom_timeformat',$timeformat);
        $tpl->assign('custom_dateformat',$dateformat);
        $tpl->assign('custom_filter',$filter);

        header("Content-Type: text/html");
        $tpl->display("formats/html.tpl");
    break;


    case 'export_xls':        
       
        $arr_data = xp_get_arr($in,$out,$filterUsr,$filterKnd,$filterPct,1,$default_location,$filter_cleared);
        $tpl->assign('arr_data', count($arr_data)>0?$arr_data:0);

        $tpl->assign('columns',$columns);
        $tpl->assign('custom_timeformat',$timeformat);
        $tpl->assign('custom_dateformat',$dateformat);
        $tpl->assign('custom_filter',$filter);

        header("Content-Disposition:attachment;filename=export.xls");
        header("Content-Type: application/vnd.ms-excel");
        $tpl->display("formats/excel.tpl");
    break;


    case 'export_csv':        
       
        $arr_data = xp_get_arr($in,$out,$filterUsr,$filterKnd,$filterPct,1,$default_location,$filter_cleared);
        $column_delimiter = $_REQUEST['column_delimiter'];
        $quote_char = $_REQUEST['quote_char'];
        /*$tpl->assign('arr_data', count($arr_data)>0?$arr_data:0);

        $tpl->assign('columns',$columns);
        $tpl->assign('custom_timeformat',$timeformat);
        $tpl->assign('custom_dateformat',$dateformat);
        $tpl->assign('custom_filter',$filter);*/

        header("Content-Disposition:attachment;filename=export.csv");
        header("Content-Type: text/csv ");

        $row = array();
        
        // output of headers
        if ($columns['date'])
          $row[] = csv_prepare_field($kga['lang']['datum'],$column_delimiter,$quote_char);
        if ($columns['from'])
          $row[] = csv_prepare_field($kga['lang']['in'],$column_delimiter,$quote_char);            
        if ($columns['to'])
          $row[] = csv_prepare_field($kga['lang']['out'],$column_delimiter,$quote_char);           
        if ($columns['time'])
          $row[] = csv_prepare_field($kga['lang']['time'],$column_delimiter,$quote_char);          
        if ($columns['dec_time'])
          $row[] = csv_prepare_field($kga['lang']['timelabel'],$column_delimiter,$quote_char);     
        if ($columns['rate'])
          $row[] = csv_prepare_field($kga['lang']['rate'],$column_delimiter,$quote_char);          
        if ($columns['wage'])
          $row[] = csv_prepare_field('Euro',$column_delimiter,$quote_char);                      
        if ($columns['knd'])
          $row[] = csv_prepare_field($kga['lang']['knd'],$column_delimiter,$quote_char);           
        if ($columns['pct'])
          $row[] = csv_prepare_field($kga['lang']['pct'],$column_delimiter,$quote_char);           
        if ($columns['action'])
          $row[] = csv_prepare_field($kga['lang']['evt'],$column_delimiter,$quote_char);           
        if ($columns['comment'])
          $row[] = csv_prepare_field($kga['lang']['comment'],$column_delimiter,$quote_char);       
        if ($columns['location'])
          $row[] = csv_prepare_field($kga['lang']['zlocation'],$column_delimiter,$quote_char);      
        if ($columns['trackingnr'])
          $row[] = csv_prepare_field($kga['lang']['trackingnr'],$column_delimiter,$quote_char);    
        if ($columns['user'])
          $row[] = csv_prepare_field($kga['lang']['username'],$column_delimiter,$quote_char);          
        if ($columns['cleared'])
          $row[] = csv_prepare_field($kga['lang']['cleared'],$column_delimiter,$quote_char);  

        echo implode($column_delimiter,$row);
        echo "\n";

        // output of data
        foreach ($arr_data as $data) {
          $row = array();
          if ($columns['date'])
            $row[] = csv_prepare_field(strftime($dateformat,$data['time_in']),$column_delimiter,$quote_char);
          if ($columns['from'])
            $row[] = csv_prepare_field(strftime($timeformat,$data['time_in']),$column_delimiter,$quote_char);            
          if ($columns['to'])
            $row[] = csv_prepare_field(strftime($timeformat,$data['time_out']),$column_delimiter,$quote_char);           
          if ($columns['time'])
            $row[] = csv_prepare_field($data['zef_apos'],$column_delimiter,$quote_char);          
          if ($columns['dec_time'])
            $row[] = csv_prepare_field($data['dec_zef_time'],$column_delimiter,$quote_char);     
          if ($columns['rate'])
            $row[] = csv_prepare_field($data['zef_rate'],$column_delimiter,$quote_char);          
          if ($columns['wage'])
            $row[] = csv_prepare_field($data['wage'],$column_delimiter,$quote_char);                      
          if ($columns['knd'])
            $row[] = csv_prepare_field($data['knd_name'],$column_delimiter,$quote_char);           
          if ($columns['pct'])
            $row[] = csv_prepare_field($data['pct_name'],$column_delimiter,$quote_char);           
          if ($columns['action'])
            $row[] = csv_prepare_field($data['evt_name'],$column_delimiter,$quote_char);           
          if ($columns['comment'])
            $row[] = csv_prepare_field($data['comment'],$column_delimiter,$quote_char);       
          if ($columns['location'])
            $row[] = csv_prepare_field($data['location'],$column_delimiter,$quote_char);      
          if ($columns['trackingnr'])
            $row[] = csv_prepare_field($data['trackingnr'],$column_delimiter,$quote_char);    
          if ($columns['user'])
            $row[] = csv_prepare_field($data['username'],$column_delimiter,$quote_char);          
          if ($columns['cleared'])
            $row[] = csv_prepare_field($data['cleared'],$column_delimiter,$quote_char);  

        echo implode($column_delimiter,$row);
        echo "\n";
        }     
    break;



    case 'export_pdf':
       
      $arr_data = xp_get_arr($in,$out,$filterUsr,$filterKnd,$filterPct,1,$default_location,$filter_cleared);

      $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

      $pdf->print_time = time();
      $pdf->SetDisplayMode('default', 'continuous'); //PDF-Seitenanzeige fortlaufend

      $pdf->SetCreator(PDF_CREATOR);
      $pdf->SetTitle('Aufstellung zu Aufwänden und Auslagen');
      $pdf->setPrintHeader(false); 
      $pdf->AddPage();

      if (isset($_REQUEST['create_bookmarks']))
        $pdf->Bookmark('Aufstellung', 0, 0);

      //$pdf->ImageEps('kimai-logo.ai', 0, 10, 60, 0, "http://www.kimai.org", true, 'T', 'R'); //Firmenlogo einbinden



      $pdf->WriteHtml("<h1>Aufstellung zu Aufwänden und Auslagen</h1>");
      $pdf->ln();
      $customers = array();
      foreach ($filterKnd as $knd_id) {
        $customer_info = knd_get_data($knd_id);
        $customers[] = $customer_info['knd_name'];
      }
      if (count($customers)>0) {
        $pdf->cell(20,6,'Kunde:');
        $pdf->cell(20,6,implode(',',$customers));
        $pdf->ln();
      }

      $projects = array();
      foreach ($filterPct as $pct_id) {
        $project_info = pct_get_data($pct_id);
        $projects[] = $project_info['pct_name'];
      }
      if (count($projects)>0) {
        $pdf->cell(20,6,'Projekt:');
        $pdf->cell(20,6,implode(',',$projects));
        $pdf->ln();
      }
      $pdf->ln();
      $pdf->ln();
      $pdf->ColoredTable(array("Datum","Tätigkeit","Dauer","Kosten"),$arr_data);


      if (isset($_REQUEST['print_summary'])) {
        
        //Zeiteinheiten zusammenfassen:
        $zef_summary = array();
        $exp_summary = array();
        foreach ($arr_data as $one_entry) {
          if ($one_entry['type'] == 'zef') {
            if (isset($zef_summary[$one_entry['zef_evtID']])) {
              $zef_summary[$one_entry['zef_evtID']]['time']   += $one_entry['dec_zef_time']; //Sekunden
              $zef_summary[$one_entry['zef_evtID']]['wage']   += $one_entry['wage']; //Euro
            }
            else {
              $zef_summary[$one_entry['zef_evtID']]['name']         = $one_entry['evt_name'];
              $zef_summary[$one_entry['zef_evtID']]['time']         = $one_entry['dec_zef_time'];
              $zef_summary[$one_entry['zef_evtID']]['wage']         = $one_entry['wage'];
            }
          }
          else {
            $exp_info['name']   = 'Auslage: '.$one_entry['evt_name'];
            $exp_info['time']   = -1;
            $exp_info['wage'] = $one_entry['wage'];
            
            $exp_summary[] = $exp_info;
          }
        }
        
        $summary = array_merge($zef_summary,$exp_summary);
        
        $pdf->AddPage();
        
        if (isset($_REQUEST['create_bookmarks']))
          $pdf->Bookmark('Zusammenfassung', 0, 0);
        
        $pdf->WriteHtml("<h1>Zusammenfassung</h1>");
        $pdf->ln();
        $pdf->ln();
        $pdf->ColoredTable_result(array("Tätigkeit","Dauer","Kosten"), $summary);
        
      }

      $pdf->Output('invoice_'.date('Y-m-d_H-i-s', $pdf->print_time).'.pdf', ( (isset($_REQUEST['download_pdf'])) ? 'D' : 'I' ) ); // D=Download I=Eingebunden
    break;

}

?>
