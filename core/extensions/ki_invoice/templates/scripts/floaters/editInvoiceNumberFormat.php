<div id="floater_innerwrap">
	<div id="floater_handle">
		<span id="floater_title"><?php echo $this->kga['lang']['invoiceNumberFormat']?></span>
		<div class="right">
			<a href="#" class="close" onclick="floaterClose();"><?php echo $this->kga['lang']['close']?></a>
		</div>
	</div>
	<div class="floater_content">
		<form id="invoice_extension_editInvoiceNumberFormat" action="../extensions/ki_invoice/processor.php" method="post">
			<input name="axAction" type="hidden" value="editInvoiceNumberFormat" />
			<fieldset>
				<label for="invoiceNumberFormat"><?php echo $this->kga['lang']['format']?></label>
				<input size="8" name="invoiceNumberFormat" id="invoiceNumberFormat" type="text" value="<?php echo $this->escape($this->kga['conf']['invoiceNumberFormat']); ?>"/>
				<div id="formbuttons">
					<input class='btn_norm' type='button' value='<?php echo $this->kga['lang']['cancel']?>' onclick='floaterClose(); return false;' />
					<input class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['submit'] ?>'/>
				</div>
			</fieldset>
		</form>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#invoice_extension_editInvoiceNumberFormat').ajaxForm(function() {
			floaterClose();
		});
		// prepare Options Object
		var options = {
			success: function(response) {
				if (response == 1) {
					$('#defaultInvoiceNumberFormat').html($('#invoiceNumberFormat').val());
				}
				floaterClose();
			}
		};
		// pass options to ajaxForm
		$('#invoice_extension_editInvoiceNumberFormat').ajaxForm(options);
	});
</script>