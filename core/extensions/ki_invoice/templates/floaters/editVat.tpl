{literal}    
    <script type="text/javascript"> 
       
        $(document).ready(function() {

            $('#iv_ext_editVat').ajaxForm(function() { 
                floaterClose();
            });

            // prepare Options Object 
            var options = { 
                success:    function(response) { 
                    if (response == 1) {
                      $('#defaultVat').html($('#vat').val());
                    }
                    floaterClose();
                } 
            }; 
            
            // pass options to ajaxForm 
            $('#iv_ext_editVat').ajaxForm(options);

        });
        
    </script>
{/literal}

<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title">{$kga.lang.editVat}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
        </div>  
    </div>

    <div class="floater_content">

        
        <form id="iv_ext_editVat" action="../extensions/ki_invoice/processor.php" method="post">
        <input name="id" type="hidden" value="0" />
        <input name="axAction" type="hidden" value="editVat" />
            <fieldset>   

{* -------------------------------------------------------------------- *} 

                
                <label for="vat">{$kga.lang.vat}</label>
                <input size="4" name="vat" id="vat" type="text" value="{$kga.conf.defaultVat|replace:'.':$kga.conf.decimalSeparator|escape:'html'}"/> % 

                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}'/>
                </div>

{* -------------------------------------------------------------------- *} 

            </fieldset>
	</form>
    </div>
</div>