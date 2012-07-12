{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            invoice_extension_onload();
            $('#editVatLink').click(function () {
              this.blur();
              floaterShow(invoice_extension_path + "floaters.php","editVat",0,0,250,100);
            });
        }); 
    </script>
{/literal}

<div id="invoice_extension_header">
     <strong>{$kga.lang.ext_invoice.invoiceTitle}</strong> 
</div>

<div id="invoice_extension_wrap">
    <div id ="invoice_extension">
    	
	<form id="invoice_extension_form" method="post" action="../extensions/ki_invoice/print.php" target="_blank">
		<div id="invoice_extension_advanced">
			<div id="invoice_extension_form">
			{$kga.lang.ext_invoice.invoiceProject} 
			<select id="invoice_projectID" name="projectID" class="formfield">
			  {html_options options=$projects selected=$preselected_project}
			</select>
			</div>
			<div id="invoice_timespan">
				{$timespan_display}
			</div>
			
<!--Work in Progress: Select box for form type-->
			{$kga.lang.ext_invoice.invoiceTemplate}
			<select id="invoice_form_docs" name="ivform_file" class="formfield">
			 {html_options values=$sel_form_files output=$sel_form_files}
			</select><br/><br/>


<!-- Some boxes below are checked by default. Delete "checked" to set default to unchecked condition -->

   		{$kga.lang.ext_invoice.defaultVat}: <span id="defaultVat">{$kga.conf.defaultVat|replace:'.':$kga.conf.decimalSeparator|escape:'html'}</span> % <a id="editVatLink" href="#">({$kga.lang.change})</a> <br/>
		<input type=checkbox name="short" checked> {$kga.lang.ext_invoice.invoiceOptionShort}<br/>
    		<input type=checkbox name="round" checked> {$kga.lang.ext_invoice.invoiceOptionRound}
    		<select id="invoice_round_ID" name="round" class="formfield">
			  {html_options options=$roundingOptions}
			</select>
     
            <br/>
            
            <select name="filter_cleared" name="cleared">
              <option value="-1" {if !$kga.conf.hideClearedEntries}selected="selected"{/if}>{$kga.lang.export_extension.cleared_all}</option>
              <option value="1">{$kga.lang.export_extension.cleared_cleared}</option>
              <option value="0" {if $kga.conf.hideClearedEntries}selected="selected"{/if}>{$kga.lang.export_extension.cleared_open}</option>
            </select>
     
	    	<div id="invoice_button">	
				<input type="submit" class="btn_ok" value={$kga.lang.ext_invoice.invoiceButton}>
	    	</div>
		</div>
	</form>

    </div>
</div>
