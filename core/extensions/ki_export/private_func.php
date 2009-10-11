<?php

/**
 * returns expenses for specific user as multidimensional array
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return array
 * @author th 
 */

include('../ki_expenses/private_db_layer_'.$kga['server_conn'].'.php');
include('private_db_layer_'.$kga['server_conn'].'.php');
include('../../libraries/tcpdf/tcpdf.php');

function xp_get_arr($start,$end,$users = null,$customers = null,$projects = null,$limit,$default_location='',$filter_cleared=-1) {
    $zef_arr = get_arr_zef($start,$end,$users,$customers,$projects,$limit);
    $exp_arr = get_arr_exp($start,$end,$users,$customers,$projects,$limit);
    $result_arr = array();

    $zef_arr_index = 0;
    $exp_arr_index = 0;
    while ($zef_arr_index < count($zef_arr) && $exp_arr_index < count($exp_arr)) {
      $arr = array();
      if ($zef_arr[$zef_arr_index]['zef_in'] > $exp_arr[$exp_arr_index]['exp_timestamp']) {
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
        $arr['comment']        = addEllipsis($zef_arr[$zef_arr_index]['zef_comment'], 150);
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
        $arr['wage']           = $exp_arr[$exp_arr_index]['exp_value'];
        $arr['pct_kndID']      = $exp_arr[$exp_arr_index]['pct_kndID'];
        $arr['knd_name']       = $exp_arr[$exp_arr_index]['knd_name'];
        $arr['pct_ID']         = $exp_arr[$exp_arr_index]['pct_ID'];
        $arr['pct_name']       = $exp_arr[$exp_arr_index]['pct_name'];
        //$arr['pct_comment'] = $exp_arr[$exp_arr_index]['pct_comment'];
        $arr['evt_name']       = $exp_arr[$exp_arr_index]['exp_designation'];
        $arr['comment']        = $exp_arr[$exp_arr_index]['exp_comment'];
        $arr['comment_type']   = $exp_arr[$exp_arr_index]['exp_comment_type'];
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
	    $arr['comment']        = addEllipsis($zef_arr[$zef_arr_index]['zef_comment'], 150);
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
      $arr['id']             = $zef_arr[$zef_arr_index]['exp_ID'];
      $arr['time_in']        = $exp_arr[$exp_arr_index]['exp_timestamp'];
      $arr['time_out']       = $exp_arr[$exp_arr_index]['exp_timestamp'];
      $arr['wage']           = $exp_arr[$exp_arr_index]['exp_value'];
      $arr['pct_kndID']      = $exp_arr[$exp_arr_index]['pct_kndID'];
      $arr['knd_name']       = $exp_arr[$exp_arr_index]['knd_name'];
      $arr['pct_ID']         = $exp_arr[$exp_arr_index]['pct_ID'];
      $arr['pct_name']       = $exp_arr[$exp_arr_index]['pct_name'];
      //$arr['pct_comment'] = $exp_arr[$exp_arr_index]['pct_comment'];
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


function xp_get_arr_usr($start,$end,$users = null,$customers = null,$projects = null) {
    $arr = get_arr_time_usr($start,$end,$users,$customers,$projects);
    return $arr;
}


function xp_get_arr_knd($start,$end,$users = null,$customers = null,$projects = null) {
    $arr = get_arr_time_knd($start,$end,$users,$customers,$projects);
    return $arr;
}

function xp_get_arr_pct($start,$end,$users = null,$customers = null,$projects = null) {
    $arr = get_arr_time_pct($start,$end,$users,$customers,$projects);
    return $arr;
}

function xp_get_arr_evt($start,$end,$users = null,$customers = null,$projects = null) {
    $arr = get_arr_time_evt($start,$end,$users,$customers,$projects);
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

/*
**  PDF Export
*/
class MYPDF extends TCPDF { 

  var $w = array(); 
  var $print_time;

  public function dateformat($number) {
      return date("d.m.Y",$number);
  } 

  public function timespan($number) {
    if ($number == -1)
      return "-------";
    else
      return str_replace(".",",",sprintf("%01.2f",$number))." Std.";
  } 

  public function money($number) {
    return str_replace(".",",",sprintf("%01.2f",$number)). " €";
  }
  
  public function comment_type($type) {
    //array('Comment', 'Help', 'Insert', 'Key', 'NewParagraph', 'Note', 'Paragraph')
    //Kommentar 0   Notiz 1   Achtung 2
    
    return ($type == 0) ? 'Comment' : 'Note';
  }
  
  public function comment_color($type) {
    return ($type == 2) ? array(255,0,0) : array(255,255,0);
  }
  
  public function comment_title($type) {
    switch ($type) {
      case 0:  return 'Kommentar:';
      case 1:  return 'Notiz:';
      case 2:  return 'Achtung:';
      default: return '';
    }
  }
  

  // Page footer 
  public function Footer() { 
        global $knd_data, $pct_data;
        
        // Position at 1.5 cm from bottom 
        $this->SetY(-15);
         
        //Kundendaten
        //$this->SetFont('helvetica', '', 8); // Set font
        //$this->Cell(80, 10, $knd_data['knd_name'].' ('.$pct_data['pct_name'].')', 0, 0, 'L');
        
        // Page number 
        $this->SetFont('helvetica', 'I', 8); // Set font 
        $this->Cell(30, 10, 'Seite '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
        
        //Datum
        $this->SetFont('helvetica', '', 8); // Set font
        $this->Cell(0, 10, date('d.m.Y H:i:s', $this->print_time), 0, 0, 'R');
  } 

  public function printHeader($w,$header) {

        // Colors, line width and bold font 
        $this->SetFillColor(240, 240, 240); 
        $this->SetTextColor(0); 
        $this->SetDrawColor(0,0,0); 
        $this->SetLineWidth(0.3); 
        $this->SetFont('', 'B'); 

    for($i = 0; $i < count($header); $i++) 
          $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1); 
    $this->Ln(); 
  }

  public function ColoredTable($header,$data) {
        $w = array(30, $this->getPageWidth()-$this->pagedim[$this->page]['lm']-$this->pagedim[$this->page]['rm']-3*30,30,30); 
    
        // Header 
        $this->printHeader($w,$header);

        // Color and font restoration 
        $this->SetFillColor(224, 235, 255); 
        $this->SetTextColor(0); 
        $this->SetFont(''); 
        // Data 
        $fill = 0; 
        $sum = 0;
        foreach($data as $row) { 
            // check if page break is nessessary
            if ($this->getPageHeight()-$this->pagedim[$this->page]['bm']-($this->getY()+20) < 0) {
              $this->Cell(array_sum($w), 0, '', 'T'); 
              $this->Ln();  
              $this->Cell($w[0]+$w[1]+$w[2], 6, "Zwischensumme:", '', 0, 'R', false); 
              $this->Cell($w[3], 6, $this->money($sum), '', 0, 'R', true); //'LR' entfernt (border) 
              $this->Ln();  
              $this->AddPage();
              $this->printHeader($w,$header);

              // Color and font restoration 
              $this->SetFillColor(224, 235, 255); 
              $this->SetTextColor(0); 
              $this->SetFont(''); 
            }
            $this->Cell($w[0], 6, $this->dateformat($row['time_in']), 'LR', 0, 'C', $fill); 
            $this->Cell($w[1], 6, $row['evt_name'], 'LR', 0, 'L', $fill);    
            
            
            //Kommentar anzeigen:
            if ( (!empty($row['comment'])) && (isset($_REQUEST['print_comments'])) ) {
            $this->Annotation($w[0]+$w[1]+2.5,$this->getY()+0.45,10,10,
                               $row['comment'], //Text
                               array(
                                 'Subtype'=>'Text', 
                                 'name' => $this->comment_type($row['comment_type']), //Symbol  
                                 'T' => $this->comment_title($row['comment_type']), //Titel 
                                 'Subj' => '', 
                                 'C' => $this->comment_color($row['comment_type']) //Farbe
                               )
                             ); 
            }
            
            $this->Cell($w[2], 6, $this->timespan(isset($row['dec_zef_time'])?$row['dec_zef_time']:0), 'LR', 0, 'R', $fill); 
            $this->Cell($w[3], 6, $this->money($row['wage']), 'LR', 0, 'R', $fill); 
            $this->Ln(); 
            $fill=!$fill; 
            $sum+=$row['wage'];
            
        } 
        $this->Cell(array_sum($w), 0, '', 'T'); 
        $this->Ln();

        $this->Cell($w[0]+$w[1]+$w[2], 6, "Endbetrag:", '', 0, 'R', false); 
        $this->SetFont('', 'B'); 
        $this->Cell($w[3], 6, $this->money($sum), '', 0, 'R', true); 
    }
    
    public function ColoredTable_result($header,$data) {
        $w = array($this->getPageWidth()-$this->pagedim[$this->page]['lm']-$this->pagedim[$this->page]['rm']-2*30,30,30); 
        
        // Header 
        $this->printHeader($w,$header);

        // Color and font restoration 
        $this->SetFillColor(224, 235, 255); 
        $this->SetTextColor(0); 
        $this->SetFont(''); 
        // Data 
        $fill = 0; 
        $sum = 0;
        $sum_time = 0;
        foreach($data as $row) { 
            // check if page break is nessessary
            if ($this->getPageHeight()-$this->pagedim[$this->page]['bm']-($this->getY()+20) < 0) {
              $this->Cell(array_sum($w), 0, '', 'T'); 
              $this->Ln();  
              $this->Cell($w[0], 6, "Zwischensummen:", '', 0, 'R', false); 
              $this->Cell($w[1], 6, $this->timespan($sum_time), 'R', 0, 'R', true);
              $this->Cell($w[2], 6, $this->money($sum), 'L', 0, 'R', true); 
              $this->Ln();  
              $this->AddPage();
              $this->printHeader($w,$header);

              // Color and font restoration 
              $this->SetFillColor(224, 235, 255); 
              $this->SetTextColor(0); 
              $this->SetFont(''); 
            }
            $this->Cell($w[0], 6, $row['name'], 'LR', 0, 'L', $fill);    
            $this->Cell($w[1], 6, $this->timespan($row['time']), 'LR', 0, 'R', $fill); 
            $this->Cell($w[2], 6, $this->money($row['wage']), 'LR', 0, 'R', $fill); 
            $this->Ln(); 
            $fill=!$fill; 
            $sum+=$row['wage'];
            $sum_time += $row['time']==-1?0:$row['time']; 
            
        }
        $this->Cell(array_sum($w), 0, '', 'T'); 
        $this->Ln();

        $this->Cell($w[0], 6, "Endsummen:", '', 0, 'R', false); 
        $this->SetFont('', 'B'); 
        $this->Cell($w[1], 6, $this->timespan($sum_time), 'R', 0, 'R', true);
        $this->Cell($w[2], 6, $this->money($sum), 'L', 0, 'R', true); 
    }

}

?>