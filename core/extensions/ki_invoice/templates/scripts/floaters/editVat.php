<script type="text/javascript">
    $(document).ready(function () {

        $('#invoice_extension_editVat').ajaxForm(function () {
            floaterClose();
        });

        // prepare Options Object
        var options = {
            success: function (response) {
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

<?php
echo $this->floater()
    ->setTitle($this->translate('vat'))
    ->setFormAction('../extensions/ki_invoice/processor.php')
    ->setFormId('invoice_extension_editVat')
    ->floaterBegin();
?>

    <input name="id" type="hidden" value="0"/>
    <input name="axAction" type="hidden" value="editVat"/>
    <ul>
        <li>
        <label for="vat"><?php echo $this->kga['lang']['vat'] ?></label>
        <input size="4" name="vat" id="vat" type="text"
               value="<?php echo $this->escape(str_replace('.', $this->kga['conf']['decimalSeparator'], $this->kga['conf']['defaultVat'])); ?>"/>
        %
        </li>
    </ul>

<?php echo $this->floater()->floaterEnd(); ?>