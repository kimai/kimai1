<?php echo $this->kga['lang']['ext_invoice']['invoiceTimePeriod'] ?>:
<b><?php echo $this->escape(strftime($this->kga['date_format'][2], $this->timeframe[0])) . ' - ' .
        $this->escape(strftime($this->kga['date_format'][2], $this->timeframe[1])) ?></b>