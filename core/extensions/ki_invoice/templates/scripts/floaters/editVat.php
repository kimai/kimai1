    <script type="text/javascript"> 
       
        $(document).ready(function() {

            $('#invoice_extension_editVat').ajaxForm(function() { 
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
            $('#invoice_extension_editVat').ajaxForm(options);

        });
        
    </script>

<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->kga['lang']['vat']?></span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();"><?php echo $this->kga['lang']['close']?></a>
        </div>  
    </div>

    <div class="floater_content">

        
        <form id="invoice_extension_editVat" action="../extensions/ki_invoice/processor.php" method="post">
        <input name="id" type="hidden" value="0" />
        <input name="axAction" type="hidden" value="editVat" />
            <fieldset>   

                
                <label for="vat"><?php echo $this->kga['lang']['vat']?></label>
                <input size="4" name="vat" id="vat" type="text" value="<?php echo $this->escape(str_replace('.',$this->kga['conf']['decimalSeparator'], $this->kga['conf']['defaultVat'])); ?>"/> % 

                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='<?php echo $this->kga['lang']['cancel']?>' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['submit'] ?>'/>

            </fieldset>
	</form>
    </div>
</div>