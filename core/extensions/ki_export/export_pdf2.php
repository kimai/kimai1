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

include('../../libraries/tcpdf/tcpdf.php');

class MYPDF extends TCPDF { 

  var $w = array(); 
  var $print_time;
  var $date_format;
  var $time_format;
  var $columns;

  var $moneySum;
  var $timeSum;

  /**
   * Format a unix timestamp as a date string.
   * @param int $number unix timestamp
   * @return string formatted string
   */
  public function date($number) {
      return strftime($this->date_format,$number);
  } 

  /**
   * Format a unix timestamp as a time string.
   * @param int $number unix timestamp
   * @return string formatted string
   */
  public function time($number) {
    if ($number == -1)
      return "-------";
    else
      return strftime($this->time_format,$number);
  }


  /**
   * Format the duration.
   * @param int $number duration.
   * @return duration nicely formatted
   */
  public function timespan($number) {
    global $kga;
    if ($number == -1)
      return "-------";
    else
      return str_replace(".",$kga['conf']['decimalSeparator'],sprintf("%01.2f",$number))." ".$kga['lang']['xp_ext']['duration_unit'];
  } 

  /**
   * Format an amount of money.
   * @param int $number amount of money
   * @return string formatted string
   */
  public function money($number) {
    return formatCurrency($number,false);
  }

  /**
   * Create the array which hold the column widths. They depends on the maximum with
   * the time column and the money column need.
   * @param int $max_time_width maximum width the time column needs.
   * @param int $max_time_width maximum width the time column needs.
   * @return array containing the widths of the columns
   */
  public function columnWidths($max_time_width,$max_money_width) {
    return array($max_time_width,
        $this->getPageWidth()-$this->pagedim[$this->page]['lm']-$this->pagedim[$this->page]['rm']-$max_time_width-$max_money_width,
        $max_money_width); 
  }


  /**
   * Split the string in lines and check if a line would overflow and cause more lines.
   * @param string $string Text to check.
   * @param int $line_width 
   * @return int Number of lines the text will need.
   */
  public function getHtmlStringLines($string,$line_width) {
    $htmlLines = explode("<br />",$string);
    $lineCount = count($htmlLines);
    foreach ($htmlLines as $line) {
      $lineCount += ceil($this->GetStringWidth($line)/$line_width);
    }
    return $lineCount;
  }

  
  /**
   * Print a footer on every page.
   */
  public function Footer() { 
    global $kga,$knd_data, $pct_data;
    
    // Position at 1.5 cm from bottom 
    $this->SetY(-15);
      
    // customer data
    /*$this->SetFont('helvetica', '', 8); // Set font
    $this->Cell(80, 10, $knd_data['knd_name'].' ('.$pct_data['pct_name'].')', 0, 0, 'L');*/
    
    // Page number 
    $this->SetFont('helvetica', 'I', 8); // Set font 
    $this->Cell(30, 10, $kga['lang']['xp_ext']['page'].' '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
    
    //Datum
    $this->SetFont('helvetica', '', 8); // Set font
    $this->Cell(0, 10, date('d.m.Y H:i:s', $this->print_time), 0, 0, 'R');
  } 

  /**
   * Put a new entry into the PDF document. Decide if it's a time entry or
   * expense entry and call the appropriate functions.
   * @param array $columns array containing all fields which should be printed
   *   (Columns is misleading, but they were columns in the browser.)
   * @param array $data the data of this entry
   * @param array $widths the widths of the columns
   */
  public function printRows($data,$widths) {

    $this->moneySum = 0;
    $this->timeSum = 0;
    foreach($data as $row) {
      if ($row['type'] == "exp") {
        $this->printExpenseRow($widths,$row);
        $this->moneySum+=$row['wage'];
      }
      else {
        $this->printTimeRow($widths,$row);
        $this->moneySum+=$row['wage'];
        $this->timeSum +=$row['dec_zef_time']==-1?0:$row['dec_zef_time'];
      }
    }
  }  

  /**
   * Put a new expense entry into the PDF document.
   * @param array $columns array containing all fields which should be printed
   *   (Columns is misleading, but they were columns in the browser.)
   * @param array $w the widths of the columns
   * @param array $row the data of this entry
   */
  function printExpenseRow($w,$row) {
    global $kga;
    $date_string = '';
    if (isset($this->columns['date']))
      $date_string = $this->date($row['time_in']);
    if (isset($this->columns['from']))
      $date_string .= ' '.$this->time($row['time_in']);


    $event_string   = (isset($this->columns['action']) && !empty($row['evt_name'])) ? $kga['lang']['xp_ext']['expense'].': <i>'.$row['evt_name'].'</i>' : '';
    $user_string    = (isset($this->columns['user']) && !empty($row['username']))   ? $kga['lang']['xp_ext']['by'].': <i>'.$row['username'].'</i>'      : '';
    $comment_string = (isset($this->columns['comment']) && !empty($row['comment'])) ? $kga['lang']['comment'].': <i>'.nl2br($row['comment']).'</i>'     : '';
    $wage_string    = '<b>'.$this->money($row['wage']).'</b>';
    
    $event_fills_row = empty($user_string) || ($this->GetStringWidth($event_string)+$this->GetStringWidth($user_string) > $w[1]);

    // Find out how many rows we use for this entry.
    $field_rows = 2;
    if (!empty($event_string) && !empty($user_string) && $event_fills_row)
      $field_rows++;
    if (empty($event_string) && empty($user_string))
      $field_rows--;
    if (empty($comment_string))
      $field_rows--;

    $probable_comment_lines = $this->GetHtmlStringLines($comment_string,$w[1]);

    // check if page break is nessessary
    if ($this->getPageHeight()-$this->pagedim[$this->page]['bm']-($this->getY()+($field_rows+$probable_comment_lines+4)*6) < 0) {
      if (isset($this->columns['wage']) && isset($this->columns['dec_time'])) {
        $this->ln();
        $this->WriteHtmlCell($w[0]+$w[1]+$w[2], 6, $this->getX(),$this->getY(),$this->timespan($this->timeSum),'',0,0,true,'R');
        $this->ln();
        $this->WriteHtmlCell($w[0]+$w[1], 6, $this->getX(),$this->getY(),$kga['lang']['xp_ext']['subtotal'].':', '',0,0,true,'R');
        $this->WriteHtmlCell($w[2], 6, $this->getX(),$this->getY(),$this->money($this->moneySum),'',0,0,true,'R');
      } else if(isset($this->columns['wage'])) {
        $this->ln();
        $this->WriteHtmlCell($w[0]+$w[1], 6, $this->getX(),$this->getY(),$kga['lang']['xp_ext']['subtotal'].':', '',0,0,true,'R');
        $this->WriteHtmlCell($w[2], 6, $this->getX(),$this->getY(),$this->money($this->moneySum),'',0,0,true,'R');
      } else if(isset($this->columns['dec_time'])) { 
        $this->ln();
        $this->WriteHtmlCell($w[0]+$w[1], 6, $this->getX(),$this->getY(),$kga['lang']['xp_ext']['subtotal'].':', '',0,0,true,'R');
        $this->WriteHtmlCell($w[2], 6, $this->getX(),$this->getY(),$this->timespan($this->timeSum),'',0,0,true,'R');
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
          if (isset($this->columns['wage'])) {
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
   /**
   * Put a new time entry into the PDF document.
   * @param array $columns array containing all fields which should be printed
   *   (Columns is misleading, but they were columns in the browser.)
   * @param array $w the widths of the columns
   * @param array $row the data of this entry
   */
  function printTimeRow($w,$row) {
    global $kga;
    $from_date_string = '';
    if (isset($this->columns['date']))
      $from_date_string = $this->date($row['time_in']);
    if (isset($this->columns['from']))
      $from_date_string .= ' '.$this->time($row['time_in']);

    $to_date_string = '';
    if (isset($this->columns['to'])) {
      if (isset($this->columns['date']))
        $to_date_string = $this->date($row['time_out']);
      $to_date_string .= ' '.$this->time($row['time_out']);
    }

      
    if (isset($this->columns['action']) && !empty($row['evt_name']))
      $event_string =  $kga['lang']['evt'].': <i>'.$row['evt_name'].'</i>';
    else
      $event_string = '';

    if (isset($this->columns['user']) && !empty($row['username']))
      $user_string =  $kga['lang']['xp_ext']['done_by'].': <i>'.$row['username'].'</i>';
    else
      $user_string = '';

    if (isset($this->columns['location']) && !empty($row['location']))
      $location_string =  $kga['lang']['zlocation'].': <i>'.$row['location'].'</i>';
    else
      $location_string = '';

    if (isset($this->columns['trackingnr']) && !empty($row['trackingnr']))
      $trackingnr_string = $kga['lang']['trackingnr'].': <i>'.$row['trackingnr'].'</i>';
    else
      $trackingnr_string = '';

    if (isset($this->columns['comment']) && !empty($row['comment']))
      $comment_string = $kga['lang']['comment'].': <i>'.nl2br($row['comment']).'</i>';
    else
      $comment_string = '';

    if (isset($this->columns['time']) && !empty($row['zef_duration']))
      $time_string = $kga['lang']['xp_ext']['duration'].': <i>'.$row['zef_duration'].' '.$kga['lang']['xp_ext']['duration_unit'].'</i>';
    else
      $time_string = '';

    if (isset($this->columns['rate']) && !empty($row['zef_rate']))
      $rate_string = $kga['lang']['rate'].': <i>'.$row['zef_rate'].'</i>';
    else
      $rate_string = '';

    if (isset($this->columns['wage']) && !empty($row['wage']))
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
    if ($this->getPageHeight()-$this->pagedim[$this->page]['bm']-($this->getY()+($field_rows+$probable_comment_lines+4)*6) < 0) {
      if (isset($this->columns['wage']) && isset($this->columns['dec_time'])) {
        $this->ln();
        $this->WriteHtmlCell($w[0]+$w[1]+$w[2], 6, $this->getX(),$this->getY(),$this->timespan($this->timeSum),'',0,0,true,'R');
        $this->ln();
        $this->WriteHtmlCell($w[0]+$w[1], 6, $this->getX(),$this->getY(),$kga['lang']['xp_ext']['subtotal'].':', '',0,0,true,'R');
        $this->WriteHtmlCell($w[2], 6, $this->getX(),$this->getY(),$this->money($this->moneySum),'',0,0,true,'R');
      } else if(isset($this->columns['wage'])) {
        $this->ln();
        $this->WriteHtmlCell($w[0]+$w[1], 6, $this->getX(),$this->getY(),$kga['lang']['xp_ext']['subtotal'].':', '',0,0,true,'R');
        $this->WriteHtmlCell($w[2], 6, $this->getX(),$this->getY(),$this->money($this->moneySum),'',0,0,true,'R');
      } else if(isset($this->columns['dec_time'])) { 
        $this->ln();
        $this->WriteHtmlCell($w[0]+$w[1], 6, $this->getX(),$this->getY(),$kga['lang']['xp_ext']['subtotal'].':', '',0,0,true,'R');
        $this->WriteHtmlCell($w[2], 6, $this->getX(),$this->getY(),$this->timespan($this->timeSum),'',0,0,true,'R');
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

  /**
   * Print a header of the summarization table.
   * @param array $w widths of the columns
   * @param array $headers name of the column headers
   */
  public function printHeader($w,$header) {

    // Colors, line width and bold font 
    $this->SetFillColor(240, 240, 240); 
    $this->SetTextColor(0); 
    $this->SetDrawColor(0,0,0); 
    $this->SetLineWidth(0.3); 
    $this->SetFont('', 'B'); 

    for($i = 0; $i < count($header); $i++) {
      if ($w[$i] <= 0) continue;
      $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1); 
    }
    $this->Ln(); 
  }

  /**
   * Print the summarization table.
   * @param array $headers name of the column headers
   * @param array $data summarized data to print
   */
  public function ColoredTable_result($header,$data) {
    global $kga;
    $w = array($this->getPageWidth()-$this->pagedim[$this->page]['lm']-$this->pagedim[$this->page]['rm'],0,0);
    if (isset($this->columns['wage'])) {
      $w[2] = 30;
      $w[0] -= 30; 
    }
    if (isset($this->columns['dec_time'])) {
      $w[1] = 30;
      $w[0] -= 30; 
    }
    
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
        if (isset($this->columns['dec_time']))
          $this->Cell($w[1], 6, $this->timespan($sum_time), isset($this->columns['wage'])?'R':'', 0, 'R', true);
        if (isset($this->columns['wage']))
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
      if (isset($this->columns['dec_time']))
        $this->Cell($w[1], 6, $this->timespan($row['time']), 'LR', 0, 'R', $fill); 
      if (isset($this->columns['wage']))
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
    if (isset($this->columns['dec_time']))
      $this->Cell($w[1], 6, $this->timespan($sum_time), isset($this->columns['wage'])?'R':'', 0, 'R', false);
    if (isset($this->columns['wage']))
      $this->Cell($w[2], 6, $this->money($sum), 'L', 0, 'R', false); 
    $this->SetFont(''); 
  }

}

 
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->columns = $columns;
$pdf->date_format = $dateformat;
$pdf->time_format = $timeformat;
$pdf->print_time = time();
$pdf->SetDisplayMode('default', 'continuous'); //PDF-Seitenanzeige fortlaufend

// determine page title
switch ($filter_type) {
 case 0:
   $pdf_title = $kga['lang']['xp_ext']['pdf_headline_only_times'];
   break;
 case 1:
   $pdf_title = $kga['lang']['xp_ext']['pdf_headline_only_expenses'];
   break;
 case -1:
 default:
   $pdf_title = $kga['lang']['xp_ext']['pdf_headline'];
}
// determine filter values
switch ($filter_cleared) {
 case 0:
   $pdf_filter[] = $kga['lang']['xp_ext']['cleared_open'];
   break;
 case 1:
   $pdf_filter[] = $kga['lang']['xp_ext']['cleared_cleared'];
   break;
}

switch ($filter_refundable) {
 case 0:
   $pdf_filter[] = $kga['lang']['xp_ext']['refundable_refundable'];
   break;
 case 1:
   $pdf_filter[] = $kga['lang']['xp_ext']['refundable_not_refundable'];
   break;
}

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle($pdf_title);
$pdf->setPrintHeader(false); 
$pdf->AddPage();

$pdf->setFont('helvetica');

if (isset($_REQUEST['create_bookmarks']))
  $pdf->Bookmark($pdf_title, 0, 0);

//$pdf->ImageEps('kimai-logo.ai', 0, 10, 60, 0, "http://www.kimai.org", true, 'T', 'R'); // include company logo


$pdf->WriteHtml('<h1>'.$pdf_title.'</h1>');
$pdf->ln();

$pdf->WriteHtml('<b>'.$kga['lang']['xp_ext']['time_period'].':</b> '.
strftime($kga['date_format']['2'],$in).' - '.strftime($kga['date_format']['2'],$out) );

if (isset($pdf_filter)) {
  $pdf->ln();
  $pdf->WriteHtml('<b>' . $kga['lang']['xp_ext']['filter'] . ':</b> ' . implode(' | ', $pdf_filter));
}

if (!empty($_REQUEST['document_comment'])) {
  $pdf->ln();
  $pdf->WriteHtml($_REQUEST['document_comment']);
}

$pdf->ln();


if (isset($_REQUEST['print_summary'])) {

  // arrays for keeping track to print summary
  $zef_summary = array();
  $exp_summary = array();

  foreach ($pdf_arr_data as $customer) {
    $project_ids = array_keys($customer);
    foreach ($project_ids as $project_id) {
      foreach ($customer[$project_id] as $row) {
      
	// summary aggregation
	if ($row['type'] == 'zef') {
	  if (isset($zef_summary[$row['zef_evtID']])) {
        $zef_summary[$row['zef_evtID']]['time']   += ($kga['conf']['exactSums'] == 1)?$row['zef_time']/3600:$row['dec_zef_time']; //Sekunden
        $zef_summary[$row['zef_evtID']]['wage']   += ($kga['conf']['exactSums'] == 1)?$row['wage_decimal']:$row['wage']; //Euro
      }
	else {
	  $zef_summary[$row['zef_evtID']]['name']         = html_entity_decode($row['evt_name']);
	  $zef_summary[$row['zef_evtID']]['time']         = ($kga['conf']['exactSums'] == 1)?$row['zef_time']/3600:$row['dec_zef_time'];
	  $zef_summary[$row['zef_evtID']]['wage']         = ($kga['conf']['exactSums'] == 1)?$row['wage_decimal']:$row['wage'];
    }
	}
	else {
	  $exp_info['name']   = $kga['lang']['xp_ext']['expense'].': '.$row['evt_name'];
	  $exp_info['time']   = -1;
	  $exp_info['wage'] = $row['wage'];
	  $exp_summary[] = $exp_info;
	}
      }
    }
  }
  
  $summary = array_merge($zef_summary,$exp_summary);
  
  
  if (isset($_REQUEST['create_bookmarks']))
    $pdf->Bookmark($kga['lang']['xp_ext']['summary'], 0, 0);
  


  $pdf->WriteHtml('<h4>'.$kga['lang']['xp_ext']['summary'].'</h4>');
  $pdf->ln();
  $pdf->ColoredTable_result(array($kga['lang']['evt'],$kga['lang']['xp_ext']['duration'],$kga['lang']['xp_ext']['costs']), $summary);
  
  $pdf->AddPage();

}  

$pdf->WriteHtml('<h4>'.$kga['lang']['xp_ext']['full_list'].'</h4>');
$pdf->ln();


$firstRun = true;


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

      // maximum width calculation
      $max_money_width = max($max_money_width,$pdf->GetStringWidth($pdf->money($row['wage'])));

      $time_width = 0;
      if (isset($columns['date']))
        $time_width += $pdf->GetStringWidth(strftime($dateformat,$row['time_in']));
      if (isset($columns['from']) && isset($columns['to']))
        $time_width += max($pdf->GetStringWidth(strftime($timeformat,$row['time_in'])),
          $pdf->GetStringWidth(strftime($timeformat,$row['time_out'])));
      else if (isset($columns['from']))
        $time_width += $pdf->GetStringWidth(strftime($timeformat,$row['time_in']));
      else
         $time_width += $pdf->GetStringWidth(strftime($timeformat,$row['time_out']));


      $max_time_width = max($max_time_width,$time_width);


      
    }
   $max_time_width+=10;
   $max_money_width+=10;
   $widths = $pdf->columnWidths($max_time_width,$max_money_width);

    $pdf->printRows($customer[$project_id],$widths);

    
    if (isset($columns['wage']) && isset($columns['dec_time'])) {
        $pdf->ln();
        $pdf->WriteHtmlCell($widths[0]+$widths[1]+$widths[2], 6, $pdf->getX(),$pdf->getY(),$pdf->timespan($pdf->timeSum),'',0,0,true,'R');
        $pdf->ln();
        $pdf->WriteHtmlCell($widths[0]+$widths[1], 6, $pdf->getX(),$pdf->getY(),$kga['lang']['xp_ext']['finalamount'].':', '',0,0,true,'R');
        $pdf->WriteHtmlCell($widths[2], 6, $pdf->getX(),$pdf->getY(),$pdf->money($pdf->moneySum),'',0,0,true,'R');
      } else if(isset($columns['wage'])) {
        $pdf->ln();
        $pdf->WriteHtmlCell($widths[0]+$widths[1], 6, $pdf->getX(),$pdf->getY(),$kga['lang']['xp_ext']['finalamount'].':', '',0,0,true,'R');
        $pdf->WriteHtmlCell($widths[2], 6, $pdf->getX(),$pdf->getY(),$pdf->money($pdf->moneySum),'',0,0,true,'R');
      } else if(isset($columns['dec_time'])) { 
        $pdf->ln();
        $pdf->WriteHtmlCell($widths[0]+$widths[1], 6, $pdf->getX(),$pdf->getY(),$kga['lang']['xp_ext']['finalamount'].':', '',0,0,true,'R');
        $pdf->WriteHtmlCell($widths[2], 6, $pdf->getX(),$pdf->getY(),$pdf->timespan($pdf->timeSum),'',0,0,true,'R');
      } 

  }


}




$pdf->Output('invoice_'.date('Y-m-d_H-i-s', $pdf->print_time).'.pdf', ( (isset($_REQUEST['download_pdf'])) ? 'D' : 'I' ) ); // D=Download I=Eingebunden
?>