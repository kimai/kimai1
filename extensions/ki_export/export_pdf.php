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

include('base_export_pdf.php');

class MYPDF extends BasePDF { 

  /**
   * Widths of all columns
   */
  var $w = array(); 

  var $columns = array();

  

  /**
   * Print a footer on every page.
   */
  public function Footer() { 
        global $kga,$customerData, $projectData;
        
        // Position at 1.5 cm from bottom 
        $this->SetY(-15);
         
        // customer data
        //$this->SetFont('helvetica', '', 8); // Set font
        //$this->Cell(80, 10, $customerData['name'].' ('.$projectData['pct_name'].')', 0, 0, 'L');
        
        // Page number 
        $this->SetFont('helvetica', 'I', 8); // Set font 
        $this->Cell(30, 10, $kga['lang']['export_extension']['page'].' '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
        
        //Date
        $this->SetFont('helvetica', '', 8); // Set font
        $this->Cell(0, 10, date('d.m.Y H:i:s', $this->print_time), 0, 0, 'R');
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
   if (isset($_REQUEST['time_type'])){
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
    if ($_REQUEST['time_type']=="dec_time") {
       $timeSum = 0;
    } else {
       $timeSum = "0:00";
    }
    
    foreach($data as $row) {

      $show_comment = !empty($row['comment']) && isset($_REQUEST['print_comments']);
      // check if page break is nessessary
      if ($this->getPageHeight()-$this->pagedim[$this->page]['bm']-($this->getY()+20+($show_comment?6:0)) < 0) {
        $this->Cell(array_sum($w), 0, '', 'T');
        if (isset($this->columns['wage']) || isset($this->columns['dec_time']) || isset($this->columns['time'])) {
           $this->Ln();
           $this->Cell($w[0]+$w[1], 6, $kga['lang']['export_extension']['subtotal'].':', '', 0, 'R', false);
           if ($_REQUEST['time_type']=="dec_time"){
              if (isset($this->columns['dec_time'])){
                 $this->Cell($w[2], 6, $this->timespan($timeSum), isset($this->columns['wage'])?'R':'', 0, 'R', true);
              }
           } else {
              if (isset($this->columns['time'])){
                 $this->Cell($w[2], 6, $this->time_unit($timeSum), isset($this->columns['wage'])?'R':'', 0, 'R', true);
              }
           }
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
      if (isset($this->columns['trackingNumber'])){
         $trackingnumber = " (#".$row['trackingNumber'].") - ";
      } else {
         $trackingnumber = "";
      }
      $this->Cell($w[1], 6, $trackingnumber.$row['customerName'] . ' - ' . $row['activityName'], 'LR', 0, 'L', $fill);    
      
      if ($_REQUEST['time_type']=="dec_time") {
          if (isset($this->columns['dec_time'])){
             $this->Cell($w[2], 6, $this->timespan(isset($row['decimalDuration'])?$row['decimalDuration']:0), 'LR', 0, 'R', $fill);
          }
      } else {
          if (isset($this->columns['time'])){ 
             $this->Cell($w[2], 6, $this->time_unit(isset($row['formattedDuration'])?$row['formattedDuration']:0), 'LR', 0, 'R', $fill);
          }
      }
      
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
           //$this->Cell($w[1], 6, $kga['lang']['comment'].': '.nl2br(Format::addEllipsis($row['comment'],40)), 'LR', 0, 'L', $fill);
           $this->Cell($w[1], 6, $comment_line, 'LR', 0, 'L', $fill);
           $this->SetFont('', '', $current_font_size);
           if ($_REQUEST['time_type']=="dec_time") {
               if (isset($this->columns['dec_time'])){
                  $this->Cell($w[2], 6, '', 'LR', 0, 'R', $fill);
               }
           } else {
               if (isset($this->columns['time'])){
                  $this->Cell($w[2], 6, '', 'LR', 0, 'R', $fill);
               }
           }   
            if (isset($this->columns['wage']))
              $this->Cell($w[3], 6, '', 'LR', 0, 'R', $fill); 
           $this->Ln();
               }
             }
     }
     $fill=!$fill; 
      $moneySum+=$row['wage'];
      if ($_REQUEST['time_type']=="dec_time") {
         $timeSum += $row['decimalDuration']==-1?0:$row['decimalDuration'];
      } else {
         $timeSum = $this->SumStdTime($row['formattedDuration']==-1?0:$row['formattedDuration'],$timeSum);
      }
        
    } 
    $this->Cell(array_sum($w), 0, '', 'T'); 
    $this->Ln();

    if (isset($this->columns['wage']) || isset($this->columns['dec_time'])) {
      $this->Cell($w[0]+$w[1], 6, $kga['lang']['export_extension']['finalamount'].':', '', 0, 'R', false); 
      $this->SetFont('', 'B'); 
      if ($_REQUEST['time_type']=="dec_time") {
        if (isset($this->columns['dec_time'])) {
           $this->Cell($w[2], 6, $this->timespan($timeSum), isset($this->columns['wage'])?'R':'', 0, 'R', true);
        }
      } else {
        if (isset($this->columns['time'])){
           $this->Cell($w[2], 6, $this->time_unit($timeSum), isset($this->columns['wage'])?'R':'', 0, 'R', true);
        }
      } 
      if (isset($this->columns['wage']))
        $this->Cell($w[3], 6, $this->money($moneySum), 'L', 0, 'R', true); 
    }
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
   $pdf_title = $kga['lang']['export_extension']['pdf_headline_only_times'];
   break;
 case 1:
   $pdf_title = $kga['lang']['export_extension']['pdf_headline_only_expenses'];
   break;
 case -1:
 default:
   $pdf_title = $kga['lang']['export_extension']['pdf_headline'];
}
// determine filter values
switch ($filter_cleared) {
 case 0:
   $pdf_filter[] = $kga['lang']['export_extension']['cleared_open'];
   break;
 case 1:
   $pdf_filter[] = $kga['lang']['export_extension']['cleared_cleared'];
   break;
}

switch ($filter_refundable) {
 case 0:
   $pdf_filter[] = $kga['lang']['export_extension']['refundable_refundable'];
   break;
 case 1:
   $pdf_filter[] = $kga['lang']['export_extension']['refundable_not_refundable'];
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

$pdf->WriteHtml('<b>'.$kga['lang']['export_extension']['time_period'].':</b> '.
strftime($kga['date_format']['2'],$in).' - '.strftime($kga['date_format']['2'],$out) );

if (isset($pdf_filter)) {
  $pdf->ln();
  $pdf->WriteHtml('<b>' . $kga['lang']['export_extension']['tab_filter'] . ':</b> ' . implode(' | ', $pdf_filter));
}

if (!empty($_REQUEST['document_comment'])) {
  $pdf->ln();
  $pdf->WriteHtml($_REQUEST['document_comment']);
}

if (isset($_REQUEST['print_summary'])) {
  
  if (isset($_REQUEST['create_bookmarks']))
    $pdf->Bookmark($kga['lang']['export_extension']['summary'], 0, 0);
  
  $pdf->ln();
  $pdf->WriteHtml('<h3>'.$kga['lang']['export_extension']['summary'].'</h3>');
  $pdf->ln();
  $pdf->printSummary(array($kga['lang']['activity'],$kga['lang']['export_extension']['duration'],$kga['lang']['export_extension']['costs']), $orderedExportData);
  
  $pdf->AddPage();
  
}

// Write to the PDF document which, if any, customer filters were applied.
$customers = array();
foreach ($filterCustomers as $customerID) {
  $customer_info = $database->customer_get_data($customerID);
  $customers[] = $customer_info['name'];
}
if (count($customers)>0) {
  $label = $kga['lang']['customer'].': ';
  $labelWidth = $pdf->GetStringWidth($label);
  $pdf->cell($labelWidth,6,$label);
  $pdf->cell($labelWidth,6,implode(', ',$customers));
  $pdf->ln();
}

// Write to the PDF document which, if any, project filters were applied.
$projects = array();
foreach ($filterProjects as $projectID) {
  $project_info = $database->project_get_data($projectID);
  $projects[] = $project_info['name'];
}
if (count($projects)>0) {
  $label = $kga['lang']['project'].': ';
  $labelWidth = $pdf->GetStringWidth($label);
  $pdf->cell($labelWidth,6,$label);
  $pdf->cell($labelWidth,6,implode(', ',$projects));
  $pdf->ln();
}

$firstRun = true;
foreach ($orderedExportData as $customer) {
  // process each customer in first dimension

  if ($firstRun)
    $firstRun = false;
  else if (isset($_REQUEST['customer_new_page']))
    $pdf->AddPage();

  $pdf->ln();
  $pdf->WriteHtml('<h3>'.$kga['lang']['export_extension']['full_list'].'</h3>');
  $pdf->ln();  

  $project_ids = array_keys($customer);

  foreach ($project_ids as $project_id) {
    // process each project in second dimension
    $pdf->ColoredTable(array($kga['lang']['datum'],$kga['lang']['activity'],$kga['lang']['export_extension']['duration'],$kga['lang']['export_extension']['costs']),$customer[$project_id]);
    $pdf->ln();
    $pdf->ln();
    $pdf->ln();
  }
}

$pdf->Output('invoice_'.date('Y-m-d_H-i-s', $pdf->print_time).'.pdf', ( (isset($_REQUEST['download_pdf'])) ? 'D' : 'I' ) ); // D=Download I=Eingebunden
?>
