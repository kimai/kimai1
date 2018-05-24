<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->translate('vat') ?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->translate('close') ?></a>
        </div>
    </div>
    <div class="floater_content">
        <form id="invoice_extension_editVat" action="../extensions/ki_invoice/processor.php" method="post">
            <input type="hidden" name="axAction" value="editVat"/>
            <fieldset>
                <label for="vat"><?php echo $this->translate('vat') ?></label>
                <input type="number" name="vat" id="vat" value="<?php echo $this->escape(str_replace('.', $this->kga['conf']['decimalSeparator'], $this->kga->getDefaultVat())); ?>" style="width: 50px;"/>
                %
                <div id="formbuttons">
	                <button type="button" class="btn_norm" onclick="floaterClose();"><?php echo $this->translate('cancel') ?></button>
	                <input type="submit" class="btn_ok" value="<?php echo $this->translate('submit') ?>"/>
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