<?php
include('../../libraries/tcpdf/tcpdf.php');

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
?>