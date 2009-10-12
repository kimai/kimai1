{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            iv_ext_onload();
        }); 
    </script>
{/literal}

<div id="iv_ext_header">
     <strong>{$kga.langInvoice.invoiceTitle}</strong> 
</div>

<div id="iv_ext_wrap">
    <div id ="iv_ext">
    	
	<form id="iv_ext_form" method="post" action="../extensions/ki_invoice/print.php" target="_blank">
		<div id="iv_ext_advanced">
			<div id="iv_ext_form">
			{$kga.langInvoice.invoiceProject} 
			<select id="iv_pct_ID" name="pct_ID" class="formfield">
			  {html_options values=$sel_pct_IDs output=$sel_pct_names selected=$pres_pct}
			</select>
			</div>
			<div id="iv_timespan">
				{$timespan_display}
			</div>
			
    		<input type=checkbox name="vat"> {$kga.langInvoice.invoiceOptionVat}<BR>
    		<input type=checkbox name="short"> {$kga.langInvoice.invoiceOptionShort}<BR>
    		<input type=checkbox name="round"> {$kga.langInvoice.invoiceOptionRound}
    		<select id="iv_round_ID" name="pct_round" class="formfield">
			  {html_options values=$sel_round_IDs output=$sel_round_names selected=$pres_round}
			</select>
     
	    	<div id="iv_button">	
				<input type="submit" class="btn_ok" value={$kga.langInvoice.invoiceButton}>
	    	</div>
		</div>
	</form>

    </div>
</div>