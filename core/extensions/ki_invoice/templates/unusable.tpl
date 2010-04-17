{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            iv_ext_onload();
        }); 
    </script>
{/literal}

<div id="iv_ext_header">
     <strong>{$kga.lang.ext_invoice.invoiceTitle}</strong> 
</div>

<div id="iv_ext_wrap">
    <div id ="iv_ext">
    	
	{if in_array('nopdo',$problems)}
	 {$kga.lang.ext_invoice.noPDOerror}
<br/>
	{/if}
	{if in_array('nozip',$problems)}
	 {$kga.lang.ext_invoice.noZIPerror}
	{/if}

    </div>
</div>