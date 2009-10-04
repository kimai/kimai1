{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            iv_ext_onload();
        }); 
    </script>
{/literal}

<div id="iv_ext_header">
     <strong>Rechnung Erstellen</strong> 
</div>

<div id="iv_ext_wrap">
    <div id ="iv_ext">
	
    <img src="../extensions/ki_invoice/grfx/OpenOffice.png" width="341" height="450" alt="OpenOffice" id="iv_screenshot">	

	<form id="iv_ext_form" method="post" action="../extensions/ki_invoice/print.php" target="_blank">
		<div id="iv_ext_advanced">
			<div id="iv_ext_form">
			Projekt: <select id="iv_pct_ID" name="pct_ID" class="formfield">
			  {html_options values=$sel_pct_IDs output=$sel_pct_names selected=$pres_pct}
			</select>
			</div>
			<div id="iv_timespan">
				{$timespan_display}
			</div>
			
    		<input type=checkbox name="vat"> Abrechnung mit MWST<BR>
    		<input type=checkbox name="short"> Kurze Abrechnung<BR>
    
	    	<div id="iv_button">	
				<input type="submit" class="btn_ok" value="Erstellen"/>
	    	</div>
		</div>
	</form>

    </div>
</div>