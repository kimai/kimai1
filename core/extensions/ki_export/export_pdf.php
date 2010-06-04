<?php
include('../../libraries/tcpdf/tcpdf.php');

class MYPDF extends TCPDF { 

  var $w = array(); 
  var $print_time;

  var $columns = array();

  public function dateformat($number) {
      return date("d.m.Y",$number);
  } 

  public function timespan($number) {
    global $kga;
    if ($number == -1)
      return "-------";
    else
      return str_replace(".",",",sprintf("%01.2f",$number))." ".$kga['lang']['xp_ext']['duration_unit'];
  } 

  public function money($number) {
    global $kga;
    if ($kga['conf']['currency_first'])
      return $kga['currency_sign']." ".str_replace(".",",",sprintf("%01.2f",$number));
    else
      return str_replace(".",",",sprintf("%01.2f",$number)). " ".$kga['currency_sign'];
  }
  

  // Page footer 
  public function Footer() { 
        global $kga,$knd_data, $pct_data;
        
        // Position at 1.5 cm from bottom 
        $this->SetY(-15);
         
        //Kundendaten
        //$this->SetFont('helvetica', '', 8); // Set font
        //$this->Cell(80, 10, $knd_data['knd_name'].' ('.$pct_data['pct_name'].')', 0, 0, 'L');
        
        // Page number 
        $this->SetFont('helvetica', 'I', 8); // Set font 
        $this->Cell(30, 10, $kga['lang']['xp_ext']['page'].' '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
        
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

    for($i = 0; $i < count($w); $i++) 
          $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1); 
    $this->Ln(); 
  }

  public function ColoredTable($header,$data) {
        global $kga;
        if ($this->columns['wage']) {
          $w = array(30, $this->getPageWidth()-$this->pagedim[$this->page]['lm']-$this->pagedim[$this->page]['rm']-3*30,30,30); 
        }
        else {
          $w = array(30, $this->getPageWidth()-$this->pagedim[$this->page]['lm']-$this->pagedim[$this->page]['rm']-2*30,30); 
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
        foreach($data as $row) {
            if (isset($_POST['hide_cleared_entries']) && $row['cleared'])
              continue;

            $show_comment = !empty($row['comment']) && isset($_REQUEST['print_comments']);
            // check if page break is nessessary
            if ($this->getPageHeight()-$this->pagedim[$this->page]['bm']-($this->getY()+20+($show_comment?6:0)) < 0) {
              $this->Cell(array_sum($w), 0, '', 'T');
              if ($this->columns['wage']) { 
                $this->Ln();  
                $this->Cell($w[0]+$w[1]+$w[2], 6, $kga['lang']['xp_ext']['subtotal'].':', '', 0, 'R', false); 
                $this->Cell($w[3], 6, $this->money($sum), '', 0, 'R', true); //'LR' entfernt (border) 
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
            $this->Cell($w[1], 6, htmlspecialchars_decode($row['evt_name']), 'LR', 0, 'L', $fill);    
            
            $this->Cell($w[2], 6, $this->timespan(isset($row['dec_zef_time'])?$row['dec_zef_time']:0), 'LR', 0, 'R', $fill); 
            if ($this->columns['wage']) {
              $this->Cell($w[3], 6, $this->money($row['wage']), 'LR', 0, 'R', $fill); 
            }
            $this->Ln(); 
            
            
            //Kommentar anzeigen:
            if ( $show_comment ) {
	      $this->Cell($w[0], 6, '', 'L', 0, 'C', $fill); 
              $this->SetFont('', 'I'); 
	      $this->Cell($w[1], 6, $kga['lang']['comment'].': '.nl2br(addEllipsis($row['comment'],40)), 'LR', 0, 'L', $fill);
	      $this->SetFont(''); 
	      $this->Cell($w[2], 6, '', 'LR', 0, 'R', $fill); 

        if ($this->columns['wage']) {
          $this->Cell($w[3], 6, '', 'LR', 0, 'R', $fill); 
        }
	      $this->Ln(); 
            }
            $fill=!$fill; 
            $sum+=$row['wage'];
            
        } 
        $this->Cell(array_sum($w), 0, '', 'T'); 
        $this->Ln();

        if ($this->columns['wage']) {
          $this->Cell($w[0]+$w[1]+$w[2], 6, $kga['lang']['xp_ext']['finalamount'].':', '', 0, 'R', false); 
          $this->SetFont('', 'B'); 
          $this->Cell($w[3], 6, $this->money($sum), '', 0, 'R', true); 
        }
    }
    
    public function ColoredTable_result($header,$data) {
        global $kga;
        if ($this->columns['wage']) {
          $w = array($this->getPageWidth()-$this->pagedim[$this->page]['lm']-$this->pagedim[$this->page]['rm']-2*30,30,30); 
        }
        else {
          $w = array($this->getPageWidth()-$this->pagedim[$this->page]['lm']-$this->pagedim[$this->page]['rm']-30,30);         
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
              $this->Cell($w[1], 6, $this->timespan($sum_time), 'R', 0, 'R', true);
              if ($this->columns['wage'])
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
            if ($this->columns['wage']) {
              $this->Cell($w[2], 6, $this->money($row['wage']), 'LR', 0, 'R', $fill); 
            }
            $this->Ln(); 
            $fill=!$fill; 
            $sum+=$row['wage'];
            $sum_time += $row['time']==-1?0:$row['time']; 
            
        }
        $this->Cell(array_sum($w), 0, '', 'T'); 
        $this->Ln();

        $this->Cell($w[0], 6, $kga['lang']['xp_ext']['finalamount'].':', '', 0, 'R', false); 
        $this->SetFont('', 'B'); 
        $this->Cell($w[1], 6, $this->timespan($sum_time), '', 0, 'R', true);
        if ($this->columns['wage']) {
          $this->Cell($w[2], 6, $this->money($sum), 'L', 0, 'R', true); 
        }
    }

}

 
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->columns = $columns;
$pdf->print_time = time();
$pdf->SetDisplayMode('default', 'continuous'); //PDF-Seitenanzeige fortlaufend

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle($kga['lang']['xp_ext']['pdf_headline']);
$pdf->setPrintHeader(false); 
$pdf->AddPage();

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

$customers = array();
foreach ($filterKnd as $knd_id) {
  $customer_info = knd_get_data($knd_id);
  $customers[] = $customer_info['knd_name'];
}
if (count($customers)>0) {
  $pdf->cell(20,6,$kga['lang']['knd'].':');
  $pdf->cell(20,6,implode(',',$customers));
  $pdf->ln();
}

$projects = array();
foreach ($filterPct as $pct_id) {
  $project_info = pct_get_data($pct_id);
  $projects[] = $project_info['pct_name'];
}
if (count($projects)>0) {
  $pdf->cell(20,6,$kga['lang']['pct'].':');
  $pdf->cell(20,6,implode(',',$projects));
  $pdf->ln();
}
$pdf->ln();
$pdf->ln();
$pdf->ColoredTable(array($kga['lang']['datum'],$kga['lang']['evt'],$kga['lang']['xp_ext']['duration'],$kga['lang']['xp_ext']['costs']),$arr_data);


if (isset($_REQUEST['print_summary'])) {
  
  //Zeiteinheiten zusammenfassen:
  $zef_summary = array();
  $exp_summary = array();
  foreach ($arr_data as $one_entry) {

    if (isset($_POST['hide_cleared_entries']) && $one_entry['cleared'])
      continue;

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
      $exp_info['name']   = $kga['lang']['xp_ext']['expense'].': '.$one_entry['evt_name'];
      $exp_info['time']   = -1;
      $exp_info['wage'] = $one_entry['wage'];
      
      $exp_summary[] = $exp_info;
    }
  }
  
  $summary = array_merge($zef_summary,$exp_summary);
  
  $pdf->AddPage();
  
  if (isset($_REQUEST['create_bookmarks']))
    $pdf->Bookmark($kga['lang']['xp_ext']['summary'], 0, 0);
  
  $pdf->WriteHtml('<h1>'.$kga['lang']['xp_ext']['summary'].'</h1>');
  $pdf->ln();
  $pdf->ln();
  $pdf->ColoredTable_result(array($kga['lang']['evt'],$kga['lang']['xp_ext']['duration'],$kga['lang']['xp_ext']['costs']), $summary);
  
}

$pdf->Output('invoice_'.date('Y-m-d_H-i-s', $pdf->print_time).'.pdf', ( (isset($_REQUEST['download_pdf'])) ? 'D' : 'I' ) ); // D=Download I=Eingebunden
?>