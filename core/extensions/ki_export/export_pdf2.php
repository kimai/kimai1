<?php
include('../../libraries/tcpdf/tcpdf.php');

class MYPDF extends TCPDF { 

  var $w = array(); 
  var $print_time;
  var $date_format;
  var $time_format;

  var $sum;

  // format date
  public function date($number) {
      return strftime($this->date_format,$number);
  } 

  // format time
  public function time($number) {
    if ($number == -1)
      return "-------";
    else
      return strftime($this->time_format,$number);
  } 

  // format wage
  public function money($number) {
    global $kga;
    if ($kga['conf']['currency_first'])
      return $kga['currency_sign']." ".str_replace(".",",",sprintf("%01.2f",$number));
    else
      return str_replace(".",",",sprintf("%01.2f",$number)). " ".$kga['currency_sign'];
  }

  public function columnWidths($max_time_width,$max_money_width) {
    return array($max_time_width,
        $this->getPageWidth()-$this->pagedim[$this->page]['lm']-$this->pagedim[$this->page]['rm']-$max_time_width-$max_money_width,
        $max_money_width); 
  }


  // split the string in lines and check if a line would overflow and cause more lines
  public function getHtmlStringLines($string,$line_width) {
    $htmlLines = explode("<br />",$string);
    $lineCount = count($htmlLines);
    foreach ($htmlLines as $line) {
      $lineCount += ceil($this->GetStringWidth($line)/$line_width);
    }
    return $lineCount;
  }

  
  // print page footer 
  public function Footer() { 
        global $kga,$knd_data, $pct_data;
        
        // Position at 1.5 cm from bottom 
        $this->SetY(-15);
         
        //Kundendaten
        /*$this->SetFont('helvetica', '', 8); // Set font
        $this->Cell(80, 10, $knd_data['knd_name'].' ('.$pct_data['pct_name'].')', 0, 0, 'L');*/
        
        // Page number 
        $this->SetFont('helvetica', 'I', 8); // Set font 
        $this->Cell(30, 10, $kga['lang']['xp_ext']['page'].' '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
        
        //Datum
        $this->SetFont('helvetica', '', 8); // Set font
        $this->Cell(0, 10, date('d.m.Y H:i:s', $this->print_time), 0, 0, 'R');
  } 


  public function printRows($columns,$data,$widths) {

    $this->sum = 0;
    foreach($data as $row) {
      if (isset($_POST['hide_cleared_entries']) && $row['cleared'])
        continue; 
      if ($row['type'] == "exp") {
        $this->printExpenseRow($columns,$widths,$row);
        $this->sum+=$row['wage'];
      }
      else {
        $this->printTimeRow($columns,$widths,$row);
        $this->sum+=$row['wage'];            
      }
    }
  }  

  function printExpenseRow($columns,$w,$row) {
    global $kga;
    $date_string = '';
    if ($columns['date'])
      $date_string = $this->date($row['time_in']);
    if ($columns['from'])
      $date_string .= ' '.$this->time($row['time_in']);


    $event_string = ($columns['action'] && !empty($row['evt_name'])) ?
        $kga['lang']['xp_ext']['expense'].': <i>'.$row['evt_name'].'</i>' : '';
    $user_string = ($columns['user'] && !empty($row['username'])) ?
        $kga['lang']['xp_ext']['by'].': <i>'.$row['username'].'</i>' : '';
    $comment_string = ($columns['comment'] && !empty($row['comment'])) ?
        $kga['lang']['comment'].': <i>'.nl2br($row['comment']).'</i>' : '';
    $wage_string = '<b>'.$this->money($row['wage']).'</b>';
    
    $event_fills_row = empty($user_string) || ($this->GetStringWidth($event_string)+$this->GetStringWidth($user_string) > $w[1]);

    $field_rows = 2; // number of rows in block of values
    
    if (!empty($event_string) && !empty($user_string) && $event_fills_row)
      $field_rows++;

    if (empty($event_string) && empty($user_string))
      $field_rows--;

    if (empty($comment_string))
      $field_rows--;

    $probable_comment_lines = $this->GetHtmlStringLines($comment_string,$w[1]);

    // check if page break is nessessary
    if ($this->getPageHeight()-$this->pagedim[$this->page]['bm']-($this->getY()+($field_rows+$probable_comment_lines+2)*6) < 0) {
      if ($columns['wage']) {
        $this->ln();
        $this->WriteHtmlCell($w[0]+$w[1], 6, $this->getX(),$this->getY(),$kga['lang']['xp_ext']['subtotal'].':', '',0,0,true,'R');
        $this->WriteHtmlCell($w[2], 6, $this->getX(),$this->getY(),$this->money($this->sum),'',0,0,true,'R');
      }
      $this->AddPage();      
    }

    $this->ln();    
    $this->Cell($w[0], 6, $date_string, '', 0, 'R');

    for ($i=0;$i<3;$i++) {
      $handled_row = false;

      switch ($i) {
        case 0: // row with event or event and user
          if ($event_fills_row && !empty($event_string)) {
            $this->WriteHtmlCell($w[1], 6, $this->getX(),$this->getY(),$event_string, 'L'); 
            $handled_row = true;
          }
          else if (!empty($event_string) && !empty($user_string)) {
            $this->WriteHtmlCell($w[1]/2, 6, $this->getX(),$this->getY(),$event_string, 'L');
            $this->WriteHtmlCell($w[1]/2, 6, $this->getX(),$this->getY(),$user_string, '');  
            $handled_row = true;
          }
          else if (!empty($user_string)) {
            $this->WriteHtmlCell($w[1], 6, $this->getX(),$this->getY(),$user_string, 'L');    
            $handled_row = true;
          }
        break;

        case 1: // row with user
          if ($event_fills_row && !empty($user_string)) {
              $this->WriteHtmlCell($w[1], 6, $this->getX(),$this->getY(),$user_string, 'L');   
              $handled_row = true;
          }
        break;

        case 2: // row with comment
          if (!empty($comment_string)) {
              $this->WriteHtmlCell($w[1], 6, $this->getX(),$this->getY(),$comment_string, 'L');   
              $handled_row = true;
          }
        break;

      }

      if ($handled_row) {
        $field_rows--;

        if ($field_rows == 0) { // if this is the last row
          $this->ln($this->getLastH());
          $this->Cell($w[0], 6, ''); 
          $this->Cell($w[1], 6, '','T'); 
          if ($columns['wage']) {
            $this->WriteHtmlCell($w[2], 6, $this->getX(),$this->getY()-$this->getLastH(),$wage_string, '',0,0,true,'R');
          }
          $this->ln();
          //$this->ln();
          break; // leave for loop
        }
        else {
          $this->ln(); 
          $this->Cell($w[0],6,'');
        }

      }

    }
  }
        
      function printTimeRow($columns,$w,$row) {
    global $kga;
              $from_date_string = '';
              if ($columns['date'])
                $from_date_string = $this->date($row['time_in']);
              if ($columns['from'])
                $from_date_string .= ' '.$this->time($row['time_in']);

              $to_date_string = '';
              if ($columns['to']) {
                if ($columns['date'])
                  $to_date_string = $this->date($row['time_out']);
                $to_date_string .= ' '.$this->time($row['time_out']);
              }

               
              if ($columns['action'] && !empty($row['evt_name']))
                $event_string =  $kga['lang']['evt'].': <i>'.$row['evt_name'].'</i>';
              else
                $event_string = '';

              if ($columns['user'] && !empty($row['username']))
                $user_string =  $kga['lang']['xp_ext']['done_by'].': <i>'.$row['username'].'</i>';
              else
                $user_string = '';

              if ($columns['location'] && !empty($row['location']))
                $location_string =  $kga['lang']['location'].': <i>'.$row['location'].'</i>';
              else
                $location_string = '';

              if ($columns['trackingnr'] && !empty($row['trackingnr']))
                $trackingnr_string = $kga['lang']['trackingnr'].': <i>'.$row['trackingnr'].'</i>';
              else
                $trackingnr_string = '';

              if ($columns['comment'] && !empty($row['comment']))
                $comment_string = $kga['lang']['comment'].': <i>'.nl2br($row['comment']).'</i>';
              else
                $comment_string = '';

              if ($columns['time'] && !empty($row['zef_apos']))
                $time_string = $kga['lang']['xp_ext']['duration'].': <i>'.$row['zef_apos'].' '.$kga['lang']['xp_ext']['duration_unit'].'</i>';
              else
                $time_string = '';

              if ($columns['rate'] && !empty($row['zef_rate']))
                $rate_string = $kga['lang']['rate'].': <i>'.$row['zef_rate'].'</i>';
              else
                $rate_string = '';

              if ($columns['wage'] && !empty($row['wage']))
                $wage_string = '<b>'.$this->money($row['wage']).'</b>';
              else
                $wage_string = '';
    
    $event_fills_row = empty($user_string) || ($this->GetStringWidth($event_string)+$this->GetStringWidth($user_string) > $w[1]);

    $field_rows = 4; // number of rows in block of values
    
    if (!empty($event_string) && !empty($user_string) && $event_fills_row)
      $field_rows++;

    if (empty($event_string) && empty($user_string))
      $field_rows--;

    if (empty($location_string) && empty($trackingnr_string))
      $field_rows--;

    if (empty($comment_string))
      $field_rows--;

    if (empty($time_string) && empty($rate_string))
      $field_rows--;

    $probable_comment_lines = $this->getHtmlStringLines($comment_string,$w[1]);

    // check if page break is nessessary
    if ($this->getPageHeight()-$this->pagedim[$this->page]['bm']-($this->getY()+($field_rows+$probable_comment_lines+2)*6) < 0) {
      if ($columns['wage']) {
        $this->ln();    
        $this->WriteHtmlCell($w[0]+$w[1], 6, $this->getX(),$this->getY(),$kga['lang']['xp_ext']['subtotal'].':', '',0,0,true,'R');
        $this->WriteHtmlCell($w[2], 6, $this->getX(),$this->getY(),$this->money($this->sum), '',0,0,true,'R');
      }
      $this->AddPage();
    }

    $this->ln();    
    $this->Cell($w[0], 6, $from_date_string, '', 0, 'R');


              
    for ($i=0;$i<5;$i++) {
      $handled_row = false;

      switch ($i) {
        case 0: // row with event or event and user
          if ($event_fills_row && !empty($event_string)) {
            $this->WriteHtmlCell($w[1], 6, $this->getX(),$this->getY(),$event_string, 'L'); 
            $handled_row = true;
          }
          else if (!empty($event_string) && !empty($user_string)) {
            $this->WriteHtmlCell($w[1]/2, 6, $this->getX(),$this->getY(),$event_string, 'L');
            $this->WriteHtmlCell($w[1]/2, 6, $this->getX(),$this->getY(),$user_string, '');  
            $handled_row = true;
          }
          else if (!empty($user_string)) {
            $this->WriteHtmlCell($w[1], 6, $this->getX(),$this->getY(),$user_string, 'L');    
            $handled_row = true;
          }
        break;

        case 1: // row with user
          if ($event_fills_row && !empty($user_string)) {
              $this->WriteHtmlCell($w[1], 6, $this->getX(),$this->getY(),$user_string, 'L');   
              $handled_row = true;
          }
        break;

        case 2: // row with location and/or tracking number
          if (!empty($location_string) && empty($trackingnr_string)) {
              $this->WriteHtmlCell($w[1], 6, $this->getX(),$this->getY(),$location_string, 'L');   
              $handled_row = true;
          }
          else if (empty($location_string) && !empty($trackingnr_string)) {
              $this->WriteHtmlCell($w[1], 6, $this->getX(),$this->getY(),$trackingnr_string, 'L');   
              $handled_row = true;
          }
          else if (!empty($location_string) && !empty($trackingnr_string)) {
              $this->WriteHtmlCell($w[1]/2, 6, $this->getX(),$this->getY(),$location_string, 'L');
              $this->WriteHtmlCell($w[1]/2, 6, $this->getX(),$this->getY(),$trackingnr_string, '');      
              $handled_row = true;
          }        
        break;

        case 3: // row with comment
          if (!empty($comment_string)) {
              $this->WriteHtmlCell($w[1], 6, $this->getX(),$this->getY(),$comment_string, 'L');   
              $handled_row = true;
          }
        break;

        case 4: // row with time and/or rate
          if (!empty($time_string) && empty($rate_string)) {
              $this->WriteHtmlCell($w[1], 6, $this->getX(),$this->getY(),$time_string, 'L');   
              $handled_row = true;
          }
          else if (empty($time_string) && !empty($rate_string)) {
              $this->WriteHtmlCell($w[1], 6, $this->getX(),$this->getY(),$rate_string, 'L');   
              $handled_row = true;
          }
          else if (!empty($time_string) && !empty($rate_string)) {
              $this->WriteHtmlCell($w[1]/2, 6, $this->getX(),$this->getY(),$time_string, 'L');
              $this->WriteHtmlCell($w[1]/2, 6, $this->getX(),$this->getY(),$rate_string, '');      
              $handled_row = true;
          }    
        break;

      }

      if ($handled_row) {
        $field_rows--;

        if ($field_rows == 0) { // if this is the last row
          $this->ln($this->getLastH());
          $this->Cell($w[0], 6, ''); 
          $this->Cell($w[1], 6, '','T'); 
          $this->WriteHtmlCell($w[2], 6, $this->getX(),$this->getY()-$this->getLastH(),$wage_string, '',0,0,true,'R');
          $this->ln();
          //$this->ln();
          break; // leave for loop
        }
        else {
          $this->ln(); 
          $this->Cell($w[0],6,'');
        }

      }

    }

           
  }




  public function timespan($number) {
    global $kga;
    if ($number == -1)
      return "-------";
    else
      return str_replace(".",",",sprintf("%01.2f",$number))." ".$kga['lang']['xp_ext']['duration_unit'];
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
  
    public function ColoredTable_result($header,$data) {
        global $kga;
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
              $this->Cell($w[0], 6, $kga['lang']['xp_ext']['subtotal'].':', '', 0, 'R', false); 
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

        $this->Cell($w[0], 6, $kga['lang']['xp_ext']['finalamount'].':', '', 0, 'R', false); 
        $this->SetFont('', 'B'); 
        $this->Cell($w[1], 6, $this->timespan($sum_time), 'R', 0, 'R', false);
        $this->Cell($w[2], 6, $this->money($sum), 'L', 0, 'R', false); 
        $this->SetFont(''); 
    }

}

 
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->date_format = $dateformat;
$pdf->time_format = $timeformat;
$pdf->print_time = time();
$pdf->SetDisplayMode('default', 'continuous'); //PDF-Seitenanzeige fortlaufend

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle($kga['lang']['xp_ext']['pdf_headline']);
$pdf->setPrintHeader(false); 
$pdf->AddPage();

$pdf->setFont('helvetica');

if (isset($_REQUEST['create_bookmarks']))
  $pdf->Bookmark($kga['lang']['xp_ext']['pdf_headline'], 0, 0);

//$pdf->ImageEps('kimai-logo.ai', 0, 10, 60, 0, "http://www.kimai.org", true, 'T', 'R'); //Firmenlogo einbinden



$pdf->WriteHtml('<h1>'.$kga['lang']['xp_ext']['pdf_headline'].'</h1>');
$pdf->ln();

$pdf->WriteHtml('<b>'.$kga['lang']['xp_ext']['time_period'].':</b> '.
strftime($kga['date_format']['2'],$in).' - '.strftime($kga['date_format']['2'],$out) );

if (!empty($_REQUEST['document_comment'])) {
  $pdf->ln();
  $pdf->WriteHtml($_REQUEST['document_comment']);
}
$pdf->ln();

$firstRun = true;


// arrays for keeping track to print summary
$zef_summary = array();
$exp_summary = array();

foreach ($pdf_arr_data as $customer) {

  if ($firstRun)
    $firstRun = false;
  else if (isset($_REQUEST['customer_new_page']))
    $pdf->AddPage();

  // process each customer in first dimension

  $project_ids = array_keys($customer);

  // get customer name from first row of first project
  $customer_name = $customer[$project_ids[0]][0]['knd_name'];

  $pdf->ln(); 
  $pdf->WriteHtml("<h2>$customer_name</h2>");

  foreach ($project_ids as $project_id) {
    // process each project in second dimension

    $project_name = $customer[$project_id][0]['pct_name'];

    $pdf->ln(); 
    $pdf->WriteHtml("<h4>$project_name</h4>");

    $max_money_width = 0;
    $max_time_width = 0;
    // calculate maximum width for time and money
    // and add to summary array
    foreach ($customer[$project_id] as $row) {

      if (isset($_POST['hide_cleared_entries']) && $row['cleared'])
        continue;


      // maximum width calculation
      $max_money_width = max($max_money_width,$pdf->GetStringWidth($pdf->money($row['wage'])));

      $time_width = 0;
      if ($columns['date'])
        $time_width += $pdf->GetStringWidth(strftime($dateformat,$row['time_in']));
      if ($columns['from'] && $columns['to'])
        $time_width += max($pdf->GetStringWidth(strftime($timeformat,$row['time_in'])),
          $pdf->GetStringWidth(strftime($timeformat,$row['time_out'])));
      else if ($columns['from'])
        $time_width += $pdf->GetStringWidth(strftime($timeformat,$row['time_in']));
      else
         $time_width += $pdf->GetStringWidth(strftime($timeformat,$row['time_out']));


      $max_time_width = max($max_time_width,$time_width);


      // summary aggregation
      if ($row['type'] == 'zef') {
        if (isset($zef_summary[$row['zef_evtID']])) {
          $zef_summary[$row['zef_evtID']]['time']   += $row['dec_zef_time']; //Sekunden
          $zef_summary[$row['zef_evtID']]['wage']   += $row['wage']; //Euro
        }
        else {
          $zef_summary[$row['zef_evtID']]['name']         = $row['evt_name'];
          $zef_summary[$row['zef_evtID']]['time']         = $row['dec_zef_time'];
          $zef_summary[$row['zef_evtID']]['wage']         = $row['wage'];
        }
      }
      else {
        $exp_info['name']   = $kga['lang']['xp_ext']['expense'].': '.$row['evt_name'];
        $exp_info['time']   = -1;
        $exp_info['wage'] = $row['wage'];
        
        $exp_summary[] = $exp_info;
      }
    }
   $max_time_width+=10;
   $max_money_width+=10;
   $widths = $pdf->columnWidths($max_time_width,$max_money_width);

    $pdf->printRows($columns,$customer[$project_id],$widths);

    
    if ($columns['wage']) {
      $pdf->ln();    
      $pdf->WriteHtmlCell($widths[0]+$widths[1], 6, $pdf->getX(),$pdf->getY(),$kga['lang']['xp_ext']['finalamount'].':', '',0,0,true,'R');
      $pdf->WriteHtmlCell($widths[2], 6, $pdf->getX(),$pdf->getY(),$pdf->money($pdf->sum), '',0,0,true,'R');
    }

  }


}



if (isset($_REQUEST['print_summary'])) {
  
  $summary = array_merge($zef_summary,$exp_summary);
  
  $pdf->AddPage();
  
  
  if (isset($_REQUEST['create_bookmarks']))
    $pdf->Bookmark($kga['lang']['xp_ext']['summary'], 0, 0);
  


  $pdf->WriteHtml('<h4>'.$kga['lang']['xp_ext']['summary'].'</h4>');
  $pdf->ln();
  $pdf->ColoredTable_result(array($kga['lang']['evt'],$kga['lang']['xp_ext']['duration'],$kga['lang']['xp_ext']['costs']), $summary);

}  



$pdf->Output('invoice_'.date('Y-m-d_H-i-s', $pdf->print_time).'.pdf', ( (isset($_REQUEST['download_pdf'])) ? 'D' : 'I' ) ); // D=Download I=Eingebunden
?>