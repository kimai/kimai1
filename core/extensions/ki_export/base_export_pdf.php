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
      return str_replace(".",$kga['conf']['decimalSeparator'],sprintf("%01.2f",$number))." ".$kga['lang']['export_extension']['duration_unit'];
  }
  
  /**
   * Appen the time unit.
   * @param string $time
   * @return string $time + time_unit
   */
  public function time_unit($time) {
    global $kga;
    return $time." ".$kga['lang']['export_extension']['duration_unit'];
  }
      
  /**
   * Add to standard time.
   * @param string $time1, time to add to the duration time (usually in standard hours:minutes)
   * @param string $timesum, total duration in standard time
   * @return string added timesum+time in standard time
   */
  public function SumStdTime($time1,$timesum) {
    $times = array($time1,$timesum);
    $seconds = 0;
    foreach ($times as $time){
       list($hour,$minute) = explode (':', $time);
       $seconds += $hour*3600;
       $seconds += $minute*60;
    }
    $hours = floor($seconds/3600);
    $seconds -= $hours*3600;
    $minutes = floor($seconds/60);
    return sprintf('% 2d:%02d', $hours, $minutes);
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
    if (isset($this->columns['dec_time']) || isset($this->columns['time'])) {
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
    $sum_std_time = "0:00";
    foreach($summarizedData as $row) { 
      // check if page break is nessessary
      if ($this->getPageHeight()-$this->pagedim[$this->page]['bm']-($this->getY()+20) < 0) {
        $this->Cell(array_sum($w), 0, '', 'T'); 
        $this->Ln();  
        $this->Cell($w[0], 6, $kga['lang']['export_extension']['subtotal'].':', '', 0, 'R', false); 
        if ($_REQUEST['time_type']=="dec_time"){
           if (isset($this->columns['dec_time']))
              $this->Cell($w[1], 6, $this->timespan($sum_time), 'R', 0, 'R', true);
        } else{
            if (isset($this->columns['time']))
                $this->Cell($w[1], 6, $this->time_unit($row['std_time']), 'R', 0, 'R', true);
        }
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
      if ($_REQUEST['time_type']=="dec_time"){
         if (isset($this->columns['dec_time']))
            $this->Cell($w[1], 6, $this->timespan($row['time']), 'LR', 0, 'R', $fill); 
      } else {   
         if (isset($this->columns['time']))
            $this->Cell($w[1], 6, gmdate('H:i',$row['std_time']), 'LR', 0, 'R', $fill);
      }   
      if (isset($this->columns['wage']))
        $this->Cell($w[2], 6, $this->money($row['wage']), 'LR', 0, 'R', $fill); 
      $this->Ln(); 
      $fill=!$fill; 
      $sum+=$row['wage'];
      $sum_time += $row['time']==-1?0:$row['time']; 
      $sum_std_time += $row['std_time'];
        
    }
    $this->Cell(array_sum($w), 0, '', 'T'); 
    $this->Ln();

    $this->Cell($w[0], 6, $kga['lang']['export_extension']['finalamount'].':', '', 0, 'R', false); 
    $this->SetFont('', 'B');
    if ($_REQUEST['time_type']=="dec_time"){
       if (isset($this->columns['dec_time']))
          $this->Cell($w[1], 6, $this->timespan($sum_time), '', 0, 'R', true);
    } else {   
       if (isset($this->columns['time']))
          $this->Cell($w[1], 6, gmdate('H:i',$sum_std_time), '', 0, 'R', true);
    }   
    if (isset($this->columns['wage']))
      $this->Cell($w[2], 6, $this->money($sum), 'L', 0, 'R', true); 
    $this->SetFont(''); 
  }

  /**
   * Create the summary data array.
   */
  function summarize($orderedExportData) {
    global $kga;
    // arrays for keeping track to print summary
      $timeSheetSummary = array();
      $expenseSummary = array();

      foreach ($orderedExportData as $customer) {
        $project_ids = array_keys($customer);
        foreach ($project_ids as $project_id) {
          foreach ($customer[$project_id] as $row) {
          
            // summary aggregation
            if ($row['type'] == 'timeSheet') {
              if (isset($timeSheetSummary[$row['activityID']])) {
            $timeSheetSummary[$row['activityID']]['time']   += ($kga['conf']['exactSums'] == 1)?$row['duration']/3600:$row['decimalDuration']; //Sekunden
            $timeSheetSummary[$row['activityID']]['std_time'] += $row['duration'];
            $timeSheetSummary[$row['activityID']]['wage']   += ($kga['conf']['exactSums'] == 1)?$row['wage_decimal']:$row['wage']; //Euro
          }
            else {
              $timeSheetSummary[$row['activityID']]['name']         = html_entity_decode($row['activityName']);
              $timeSheetSummary[$row['activityID']]['time']         = ($kga['conf']['exactSums'] == 1)?$row['duration']/3600:$row['decimalDuration'];
              $timeSheetSummary[$row['activityID']]['std_time']     = $row['duration'];
              $timeSheetSummary[$row['activityID']]['wage']         = ($kga['conf']['exactSums'] == 1)?$row['wage_decimal']:$row['wage'];
        }
            }
            else {
              $expenseInfo['name']   = $kga['lang']['export_extension']['expense'].': '.$row['projectName'];
              $expenseInfo['time']   = -1;
              $expenseInfo['std_time']   = -1;
              $expenseInfo['wage'] = $row['wage'];
              $expenseSummary[] = $expenseInfo;
            }
          }
        }
      }
      
      return array_merge($timeSheetSummary,$expenseSummary);
  }

}

?>