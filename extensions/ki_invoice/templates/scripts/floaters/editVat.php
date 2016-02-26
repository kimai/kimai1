<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->kga['lang']['vat'] ?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->kga['lang']['close'] ?></a>
        </div>
    </div>
    <div class="floater_content">
        <form id="invoice_extension_editVat" action="../extensions/ki_invoice/processor.php" method="post">
            <input type="hidden" name="axAction" value="editVat"/>
            <fieldset>
                <label for="vat"><?php echo $this->kga['lang']['vat'] ?></label>
                <input type="number" name="vat" id="vat" value="<?php echo $this->escape(str_replace('.', $this->kga['conf']['decimalSeparator'], $this->kga['conf']['defaultVat'])); ?>" style="width: 50px;"/>
                %
                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='<?php echo $this->kga['lang']['cancel'] ?>' onclick='floaterClose();return false;'/>
                    <input class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['submit'] ?>'/>
            </fieldset>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var $invoice_extension_editVat = $('#invoice_extension_editVat');
        $invoice_extension_editVat.ajaxForm({
            success: function (response) {
                if (response == 1) {
                    $('#defaultVat').html($('#vat').val());
                }
                floaterClose();
            }
        });
    });
</script>