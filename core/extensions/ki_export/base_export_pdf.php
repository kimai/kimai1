<?php


class BasePDF extends TCPDF { 

  public $print_time;
  public $date_format;
  public $time_format;

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
    return Format::formatCurrency($number,false);
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
    * Print the table containing the summarized information.
    * @param array $header String with the column headers.
    * @param array $data Data to print.
    */
  public function printSummary($header,$data) {
    global $kga;

    $summarizedData = $this->summarize($data);

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
    foreach($summarizedData as $row) { 
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
    $this->SetFont(''); 
  }

  /**
   * Create the summary data array.
   */
  function summarize($pdf_arr_data) {
    global $kga;
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
      
      return array_merge($zef_summary,$exp_summary);
  }

}

?>