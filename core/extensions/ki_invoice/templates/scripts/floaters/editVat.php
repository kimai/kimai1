<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->kga['lang']['vat']?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->kga['lang']['close']?></a>
        </div>  
    </div>

    <div class="floater_content">

        
        <form id="invoice_extension_editVat" action="../extensions/ki_invoice/processor.php" method="post">
        <input type="hidden" name="id" value="0" />
        <input type="hidden" name="axAction" value="editVat" />
            <fieldset>   

                
                <label for="vat"><?php echo $this->kga['lang']['vat']?></label>
                <input size="4" name="vat" id="vat" type="text" value="<?php echo $this->escape(str_replace('.',$this->kga['conf']['decimalSeparator'], $this->kga['conf']['defaultVat'])); ?>"/> % 

                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='<?php echo $this->kga['lang']['cancel']?>' onclick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['submit'] ?>'/>

            </fieldset>
	</form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#invoice_extension_editVat').ajaxForm(function() {
            floaterClose();
        });
        var options = {
            success:    function(response) {
                if (response == 1) {
                    $('#defaultVat').html($('#vat').val());
                }
                floaterClose();
            }
        };
        $('#invoice_extension_editVat').ajaxForm(options);
    });
</script>