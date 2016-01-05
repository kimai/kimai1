<div id="invoice_extension_header">
    <strong><?php echo $this->kga['lang']['ext_invoice']['invoiceTitle'] ?></strong>
</div>
<div id="invoice_extension_wrap">
    <div id="invoice_extension">
        <form id="invoice_extension_form" method="post" action="../extensions/ki_invoice/print.php" target="_blank">
            <div id="invoice_extension_advanced">
                <div>
                    <?php echo $this->kga['lang']['ext_invoice']['invoiceCustomer'] ?>:
                    <?php echo $this->formSelect('customerID', $this->preselected_customer, array(
                        'id' => 'invoice_customerID', 
                        'class' => 'formfield'), $this->customers); ?>
                </div>
                <div>
                    <?php echo $this->kga['lang']['ext_invoice']['invoiceProject'] ?>:
                    <?php echo $this->formSelect('projectID[]', $this->preselected_project, array(
                        'id' => 'invoice_projectID', 
                        'class' => 'formfield', 
                        'multiple' => 'multiple'), $this->projects); ?>
                </div>
                <div id="invoice_timespan">
                    <?php echo $this->kga['lang']['ext_invoice']['invoiceTimePeriod'] ?>:
                    <b><?php echo $this->escape(strftime($this->kga['date_format'][2], $this->timeframe[0])) . ' - ' .
                            $this->escape(strftime($this->kga['date_format'][2], $this->timeframe[1])) ?></b>
                </div>
                <!--Work in Progress: Select box for form type-->
                <?php echo $this->kga['lang']['ext_invoice']['invoiceTemplate'] ?>:
                <?php echo $this->formSelect('ivform_file', null, array(
                    'id' => 'invoice_form_docs', 
                    'class' => 'formfield'), $this->sel_form_files); ?>
                <br/><br/>
                <!-- Some boxes below are checked by default. Delete "checked" to set default to unchecked condition -->
                <?php echo $this->kga['lang']['ext_invoice']['defaultVat'] ?>:
                <span id="defaultVat"><?php echo $this->escape(str_replace('.', $this->kga['conf']['decimalSeparator'], $this->kga['conf']['defaultVat'])) ?></span>
                % (<a id="editVatLink" href="#"><?php echo $this->kga['lang']['change'] ?></a>)<br/>
                <input type="checkbox" name="short" checked="checked"> <?php echo $this->kga['lang']['ext_invoice']['invoiceOptionShort'] ?>
                <br/>
                <input type="checkbox" name="round" checked="checked"> <?php echo $this->kga['lang']['ext_invoice']['invoiceOptionRound'] ?>
                <?php echo $this->formSelect('roundValue', null, array(
                    'id' => 'invoice_round_ID', 
                    'class' => 'formfield'), $this->roundingOptions); ?>
                <br/>
                <select name="filter_cleared" name="cleared">
                    <option value="-1" <?php if (!$this->kga['conf']['hideClearedEntries']): ?> selected="selected" <?php endif; ?>><?php echo $this->kga['lang']['export_extension']['cleared_all'] ?></option>
                    <option value="1"><?php echo $this->kga['lang']['export_extension']['cleared_cleared'] ?></option>
                    <option value="0" <?php if ($this->kga['conf']['hideClearedEntries']): ?> selected="selected" <?php endif; ?>><?php echo $this->kga['lang']['export_extension']['cleared_open'] ?></option>
                </select>
                <div id="invoice_button">
                    <input type="submit" class="btn_ok" value="<?php echo $this->kga['lang']['ext_invoice']['invoiceButton'] ?>">
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        invoice_extension_onload();
        $('#editVatLink').click(function () {
            this.blur();
            floaterShow(invoice_extension_path + "floaters.php", "editVat", 0, 0, 250, function () {
                $('#vat').focus();
            });
            return false;
        });
        $('#invoice_customerID').change(function () {
            $.ajax({
                url: invoice_extension_path + 'processor.php',
                data: {
                    'axAction': 'projects',
                    'customerID': $(this).val()
                }
            }).done(function (data) {
                $('#invoice_projectID').empty();
                for (var projectID in data) {
                    $('#invoice_projectID').append($('<option>', {
                        value: projectID,
                        text: data[projectID]
                    }));
                }
            });
        });
    });
</script>