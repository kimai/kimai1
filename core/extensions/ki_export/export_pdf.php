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

  /**
   * Widths of all columns
   */
  var $w = array(); 
  var $print_time;
  var $date_format;

  var $columns = array();

  /**
   * Format a Unix timestamp to a date.
   * @param int $number unix timestamp
   * @return string formatted as date
   */
  public function dateformat($number) {
      return strftime($this->date_format,$number);
  } 

  /**
   * Format the duration.
   * @param int $number number to format (usually in hours)
   * @return string formatted string
   */
  public function timespan($number) {
    global $kga;
    if ($number == -1)
      return "-------";
    else
      return str_replace(".",$kga['conf']['decimalSeparator'],sprintf("%01.2f",$number))." ".$kga['lang']['xp_ext']['duration_unit'];
  } 

  /**
   * Format a number as a money value.
   * @param int $number amount of money
   * @return string formatted string
   */
  public function money($number) {
    return formatCurrency($number,false);
  }
  

  /**
   * Print a footer on every page.
   */
  public function Footer() { 
        global $kga,$knd_data, $pct_data;
        
        // Position at 1.5 cm from bottom 
        $this->SetY(-15);
         
        // customer data
        //$this->SetFont('helvetica', '', 8); // Set font
        //$this->Cell(80, 10, $knd_data['knd_name'].' ('.$pct_data['pct_name'].')', 0, 0, 'L');
        
        // Page number 
        $this->SetFont('helvetica', 'I', 8); // Set font 
        $this->Cell(30, 10, $kga['lang']['xp_ext']['page'].' '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
        
        //Date
        $this->SetFont('helvetica', '', 8); // Set font
        $this->Cell(0, 10, date('d.m.Y H:i:s', $this->print_time), 0, 0, 'R');
  } 

  /**
   * Print the header of the table.
   * @param array $w widths of the table columns
   * @param array $header string with the column headers
   */
  public function printHeader($w,$header) {

        // Colors, line width and bold font 
        $this->SetFillColor(240, 240, 240); 
        $this->SetTextColor(0); 
        $this->SetDrawColor(0,0,0); 
        $this->SetLineWidth(0.3); 
        $this->SetFont('', 'B'); 

    for($i = 0; $i < count($w); $i++) {
          if ($w[$i] <= 0) continue;
          $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1); 
     }
    $this->Ln(); 
  }

  /**
   * Print the table containing the detailed information.
   * @param array $header String with the column headers.
   * @param array $data Data to print.
   */
  public function ColoredTable($header,$data) {
    global $kga;
    $dateWidth = max(
    $this->GetStringWidth($header[0]),
    $this->GetStringWidth($this->dateformat(mktime(0,0,0,12,31,2000)))
    );
    $dateWidth += 4;
    $w = array($dateWidth, $this->getPageWidth()-$this->pagedim[$this->page]['lm']-$this->pagedim[$this->page]['rm']-$dateWidth,0,0);
    if (isset($this->columns['wage'])) {
      $w[3] = 30;
      $w[1] -= 30; 
    }
    if (isset($this->columns['dec_time'])) {
      $w[2] = 30;
      $w[1] -= 30; 
    }

    // Header 
    $this->printHeader($w,$header);

    // Color and font restoration 
    $this->SetFillColor(224, 235, 255); 
    $this->SetTextColor(0); 
    $this->SetFont(''); 
    // Data 
    $fill = 0; 
    $moneySum = 0;
    $timeSum = 0;
    foreach($data as $row) {

      $show_comment = !empty($row['comment']) && isset($_REQUEST['print_comments']);
      // check if page break is nessessary
      if ($this->getPageHeight()-$this->pagedim[$this->page]['bm']-($this->getY()+20+($show_comment?6:0)) < 0) {
        $this->Cell(array_sum($w), 0, '', 'T');
        if (isset($this->columns['wage']) || isset($this->columns['dec_time'])) { 
          $this->Ln();  
          $this->Cell($w[0]+$w[1], 6, $kga['lang']['xp_ext']['subtotal'].':', '', 0, 'R', false); 
          if (isset($this->columns['dec_time']))
            $this->Cell($w[2], 6, $this->timespan($timeSum), isset($this->columns['wage'])?'R':'', 0, 'R', true); 
          if (isset($this->columns['wage']))
            $this->Cell($w[3], 6, $this->money($moneySum), 'L', 0, 'R', true);
        }
        $this->Ln();  
        $this->AddPage();
        $this->printHeader($w,$header);

        // Color and font restoration 
        $this->SetFillColor(224, 235, 255); 
        $this->SetTextColor(0); 
        $this->SetFont(''); 
      }
      $this->Cell($w[0], 6, $this->dateformat($row['time_in']), 'LR', 0, 'C', $fill); 
      $this->Cell($w[1], 6, $row['knd_name'] . ' - ' . $row['evt_name'], 'LR', 0, 'L', $fill);    
      
      if (isset($this->columns['dec_time']))
        $this->Cell($w[2], 6, $this->timespan(isset($row['dec_zef_time'])?$row['dec_zef_time']:0), 'LR', 0, 'R', $fill); 
      if (isset($this->columns['wage']))
        $this->Cell($w[3], 6, $this->money($row['wage']), 'LR', 0, 'R', $fill); 
      $this->Ln(); 
        
        
      //Kommentar anzeigen:
      if ( $show_comment ) {
             // comment line width
             $comment_line_width = 58;
             // split comment in lines
             $comment_lines = explode("\n", wordwrap(stripslashes($row['comment']), $comment_line_width, "\n", true));
       // loop through all comment lines an add a cell for each line
             if (is_array($comment_lines)) {
               // determine font sizes to work with
               $current_font_size = $this->getFontSizePt();
               if ($current_font_size <= 0) {
                 $current_font_size = 12;
               }
               $comment_font_size = $current_font_size - 2;
               foreach ($comment_lines as $comment_line) {
                 $this->Cell($w[0], 6, '', 'L', 0, 'C', $fill); 
           $this->SetFont('', 'I', $comment_font_size); 
           //$this->Cell($w[1], 6, $kga['lang']['comment'].': '.nl2br(addEllipsis($row['comment'],40)), 'LR', 0, 'L', $fill);
           $this->Cell($w[1], 6, $comment_line, 'LR', 0, 'L', $fill);
           $this->SetFont('', '', $current_font_size); 
            if (isset($this->columns['dec_time']))
              $this->Cell($w[2], 6, '', 'LR', 0, 'R', $fill); 
            if (isset($this->columns['wage']))
              $this->Cell($w[3], 6, '', 'LR', 0, 'R', $fill); 
           $this->Ln();
               }
             }
     }
     $fill=!$fill; 
      $moneySum+=$row['wage'];
      $timeSum += $row['dec_zef_time']==-1?0:$row['dec_zef_time']; 
        
    } 
    $this->Cell(array_sum($w), 0, '', 'T'); 
    $this->Ln();

    if (isset($this->columns['wage']) || isset($this->columns['dec_time'])) {
      $this->Cell($w[0]+$w[1], 6, $kga['lang']['xp_ext']['finalamount'].':', '', 0, 'R', false); 
      $this->SetFont('', 'B'); 
      if (isset($this->columns['dec_time']))
        $this->Cell($w[2], 6, $this->timespan($timeSum), isset($this->columns['wage'])?'R':'', 0, 'R', true); 
      if (isset($this->columns['wage']))
        $this->Cell($w[3], 6, $this->money($moneySum), 'L', 0, 'R', true); 
    }
  }

  /**
    * Print the table containing the summarized information.
    * @param array $header String with the column headers.
    * @param array $data Data to print.
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
          $this->Cell($w[1], 6, $this->timespan($sum_time), 'R', 0, 'R', true);
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
      $this->Cell($w[1], 6, $this->timespan($sum_time), '', 0, 'R', true);
    if (isset($this->columns['wage']))
      $this->Cell($w[2], 6, $this->money($sum), 'L', 0, 'R', true); 
  }

}

 
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->date_format = $dateformat;
$pdf->columns = $columns;
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

if (isset($_REQUEST['create_bookmarks']))
  $pdf->Bookmark($pdf_title, 0, 0);

//$pdf->ImageEps('kimai-logo.ai', 0, 10, 60, 0, "http://www.kimai.org", true, 'T', 'R'); // include company logo



$pdf->WriteHtml('<h1>' . $pdf_title . '</h1>');
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

if (isset($_REQUEST['print_summary'])) {
  
  //Create the summary.
  $zef_summary = array();
  $exp_summary = array();
  foreach ($arr_data as $one_entry) {

    if ($one_entry['type'] == 'zef') {
      if (isset($zef_summary[$one_entry['zef_evtID']])) {
        $zef_summary[$one_entry['zef_evtID']]['time']   += ($kga['conf']['exactSums'] == 1)?$one_entry['zef_time']/3600:$one_entry['dec_zef_time']; //Sekunden
        $zef_summary[$one_entry['zef_evtID']]['wage']   += ($kga['conf']['exactSums'] == 1)?$one_entry['wage_decimal']:$one_entry['wage']; //Euro
      }
      else {
        $zef_summary[$one_entry['zef_evtID']]['name']         = html_entity_decode($one_entry['evt_name']);
        $zef_summary[$one_entry['zef_evtID']]['time']         = ($kga['conf']['exactSums'] == 1)?$one_entry['zef_time']/3600:$one_entry['dec_zef_time'];
        $zef_summary[$one_entry['zef_evtID']]['wage']         = ($kga['conf']['exactSums'] == 1)?$one_entry['wage_decimal']:$one_entry['wage'];
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
  
  if (isset($_REQUEST['create_bookmarks']))
    $pdf->Bookmark($kga['lang']['xp_ext']['summary'], 0, 0);
  
  $pdf->ln();
  $pdf->WriteHtml('<h3>'.$kga['lang']['xp_ext']['summary'].'</h3>');
  $pdf->ln();
  $pdf->ColoredTable_result(array($kga['lang']['evt'],$kga['lang']['xp_ext']['duration'],$kga['lang']['xp_ext']['costs']), $summary);
  
  $pdf->AddPage();
  
}

// Write to the PDF document which, if any, customer filters were applied.
$customers = array();
foreach ($filterKnd as $knd_id) {
  $customer_info = knd_get_data($knd_id);
  $customers[] = $customer_info['knd_name'];
}
if (count($customers)>0) {
  $pdf->cell(20,6,$kga['lang']['knd'].':');
  $pdf->cell(20,6,implode(', ',$customers));
  $pdf->ln();
}

// Write to the PDF document which, if any, project filters were applied.
$projects = array();
foreach ($filterPct as $pct_id) {
  $project_info = pct_get_data($pct_id);
  $projects[] = $project_info['pct_name'];
}
if (count($projects)>0) {
  $pdf->cell(20,6,$kga['lang']['pct'].':');
  $pdf->cell(20,6,implode(', ',$projects));
  $pdf->ln();
}


if (isset($_REQUEST['customer_new_page'])) {
  $firstRun = true;
  foreach ($knd_arr_data as $arr_data) {
    if ($firstRun)
      $firstRun = false;
    else
      $pdf->AddPage();
    $pdf->ln();
    $pdf->WriteHtml('<h3>'.$kga['lang']['xp_ext']['full_list'].'</h3>');
    $pdf->ln();
    $pdf->ColoredTable(array($kga['lang']['datum'],$kga['lang']['evt'],$kga['lang']['xp_ext']['duration'],$kga['lang']['xp_ext']['costs']),$arr_data);
  }
}
else {
  $pdf->ln();
  $pdf->WriteHtml('<h3>'.$kga['lang']['xp_ext']['full_list'].'</h3>');
  $pdf->ln();
  $pdf->ColoredTable(array($kga['lang']['datum'],$kga['lang']['evt'],$kga['lang']['xp_ext']['duration'],$kga['lang']['xp_ext']['costs']),$arr_data);
}


$pdf->Output('invoice_'.date('Y-m-d_H-i-s', $pdf->print_time).'.pdf', ( (isset($_REQUEST['download_pdf'])) ? 'D' : 'I' ) ); // D=Download I=Eingebunden
?>