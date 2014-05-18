<script type="text/javascript"> 
	$(document).ready(function() {
        $('#invoice_extension').invoice({
            noProject: "<?php echo $this->translate('noProject', 'ext_invoice'); ?>",
            path: '../extensions/ki_invoice/'
        });
	});
</script>

<?php
    echo $this->extensionScreen(
        array(
            'title'     => $this->translate('invoiceTitle', 'ext_invoice'),
            'id'        => 'invoice_extension_header',
            'level'     => array('invoice_extension_wrap', 'invoice_extension'),
            'styles'    => true
        )
    )->getHeader();
?>

    <form id="invoice_extension_form" method="post" action="../extensions/ki_invoice/print.php" target="_blank">
        <div id="invoice_extension_advanced">
            <table>
                <tr>
                    <td>
                        <?php echo $this->translate('customer') ?>:
                    </td>
                    <td>
                        <?php echo $this->formSelect('customerID', $this->preselected_customer, array('id' => 'invoice_customerID', 'class'=>'formfield'), $this->customers); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $this->translate('project'); ?>:
                    </td>
                    <td>
                        <?php echo $this->formSelect('projectID[]', $this->preselected_project, array('id' => 'invoice_projectID', 'class'=>'formfield', 'multiple' => 'multiple'), $this->projects); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $this->translate('invoiceTimePeriod', 'ext_invoice'); ?>
                    </td>
                    <td>
                        <div id="invoice_timespan">
                            <?php echo $this->timespan_display; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $this->translate('invoiceTemplate', 'ext_invoice'); ?>
                    </td>
                    <td>
                        <!--Work in Progress: Select box for form type-->
                        <?php echo $this->formSelect('ivform_file', null, array('id' => 'invoice_form_docs', 'class' => 'formfield'), $this->sel_form_files); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $this->translate('defaultVat', 'ext_invoice'); ?>:
                    </td>
                    <td>
                        <span id="defaultVat"><?php echo $this->escape(str_replace('.',$this->kga['conf']['decimalSeparator'], $this->kga['conf']['defaultVat']))?></span> %
                        <a id="editVatLink" href="#">(<?php echo $this->translate('change'); ?>)</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $this->translate('invoiceOptionShort', 'ext_invoice'); ?>:
                    </td>
                    <td>
                        <input type="checkbox" name="short" checked="checked">
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $this->translate('invoiceOptionRound', 'ext_invoice'); ?>:
                    </td>
                    <td>
                        <input type="checkbox" name="round" checked="checked">
                        <?php echo $this->formSelect('roundValue', null, array('id' => 'invoice_round_ID', 'class' => 'formfield'), $this->roundingOptions); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $this->translate('view_filter'); ?>:
                    </td>
                    <td>
                        <select name="filter_cleared" name="cleared">
                            <option value="-1" <?php if (!$this->kga['conf']['hideClearedEntries']):?> selected="selected" <?php endif; ?>><?php echo $this->translate('cleared_all', 'export_extension'); ?></option>
                            <option value="1"><?php echo $this->translate('cleared_cleared', 'export_extension'); ?></option>
                            <option value="0" <?php if ($this->kga['conf']['hideClearedEntries']):?> selected="selected" <?php endif; ?>><?php echo $this->translate('cleared_open', 'export_extension'); ?></option>
                        </select>
                    </td>
                </tr>
            </table>

            <div id="invoice_button">
                <input type="submit" class="btn_ok" value="<?php echo $this->translate('invoiceButton', 'ext_invoice'); ?>">
            </div>
        </div>
    </form>

<?php echo $this->extensionScreen()->getFooter(); ?>