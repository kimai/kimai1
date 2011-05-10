{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            iv_ext_onload();
            $('#editVatLink').click(function () {
              this.blur();
              floaterShow(iv_ext_path + "floaters.php","editVat",0,0,250,100);
            });
        }); 
    </script>
{/literal}

<div id="iv_ext_header">
     <strong>{$kga.lang.ext_invoice.invoiceTitle}</strong> 
</div>

<div id="iv_ext_wrap">
    <div id ="iv_ext">
    	
	<form id="iv_ext_form" method="post" action="../extensions/ki_invoice/print.php" target="_blank">
		<div id="iv_ext_advanced">
			<div id="iv_ext_form">
			{$kga.lang.ext_invoice.invoiceProject} 
			<select id="iv_pct_ID" name="pct_ID" class="formfield">
			  {html_options values=$sel_pct_IDs output=$sel_pct_names selected=$pres_pct}
			</select>
			</div>
			<div id="iv_timespan">
				{$timespan_display}
			</div>
			
<!--Work in Progress: Select box for form type-->
			Invoice Form:
			<select id="iv_form_docs" name="ivform_file" class="formfield">
			 {html_options values=$sel_form_files output=$sel_form_files selected=$pres_form}
			</select><br/><br/>


<!-- Some boxes below are checked by default. Delete "checked" to set default to unchecked condition -->

   		{$kga.lang.ext_invoice.defaultVat}: <span id="defaultVat">{$kga.conf.defaultVat|replace:'.':$kga.conf.decimalSeparator|escape:'html'}</span> % <a id="editVatLink" href="#">({$kga.lang.change})</a> <br/>
		<input type=checkbox name="short" checked> {$kga.lang.ext_invoice.invoiceOptionShort}<br/>
    		<input type=checkbox name="round" checked> {$kga.lang.ext_invoice.invoiceOptionRound}
    		<select id="iv_round_ID" name="pct_round" class="formfield">
			  {html_options values=$sel_round_IDs output=$sel_round_names selected=$pres_round}
			</select>
     
	    	<div id="iv_button">	
				<input type="submit" class="btn_ok" value={$kga.lang.ext_invoice.invoiceButton}>
	    	</div>
		</div>
	</form>

    </div>
</div>
