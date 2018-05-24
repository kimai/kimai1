<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->translate('export_extension:print') ?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->translate('close') ?></a>
        </div>
    </div>
    <div id="help">
        <div class="content"></div>
    </div>
    <div class="floater_content">
        <form id="export_extension_form_print" action="../extensions/ki_export/processor.php" method="post" target="_blank">
            <input type="hidden" name="axAction" value="export_html"/>
            <input type="hidden" name="axValue" id="axValue" value=""/>
            <input type="hidden" name="first_day" id="first_day" value=""/>
            <input type="hidden" name="last_day" id="last_day" value=""/>
            <input type="hidden" name="axColumns" id="axColumns" value=""/>
            <input type="hidden" name="timeformat" id="timeformat" value=""/>
            <input type="hidden" name="dateformat" id="dateformat" value=""/>
            <input type="hidden" name="default_location" id="default_location" value=""/>
            <input type="hidden" name="filter_cleared" id="filter_cleared" value=""/>
            <input type="hidden" name="filter_refundable" id="filter_refundable" value=""/>
            <input type="hidden" name="filter_type" id="filter_type" value=""/>
            <fieldset>
                <ul>
                    <li>
                        <?php echo $this->translate('export_extension:print_hint') ?>
                    </li>
                    <li>
                        <label for="print_summary"><?php echo $this->translate('export_extension:print_summary') ?>:</label>
                        <input type="checkbox" value="true" name="print_summary" id="print_summary" <?php if ($this->prefs['print_summary']): ?> checked="checked" <?php endif; ?>>
                    </li>
                    <li>
                        <label for="reverse_order"><?php echo $this->translate('export_extension:reverse_order') ?>:</label>
                        <input type="checkbox" value="true" name="reverse_order" id="reverse_order" <?php if ($this->prefs['reverse_order']): ?> checked="checked" <?php endif; ?>/>
                    </li>
                </ul>
                <div id="formbuttons">
	                <button type="button" class="btn_norm" onclick="floaterClose();"><?php echo $this->translate('cancel') ?></button>
	                <input type="submit" class="btn_ok" value="<?php echo $this->translate('submit') ?>" onclick="floaterClose();"/>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#help').hide();
        $('#floater input#timeformat').prop('value', $('#export_extension_timeformat').prop('value'));
        $('#floater input#dateformat').prop('value', $('#export_extension_dateformat').prop('value'));
        $('#floater input#default_location').prop('value', $('#default_location').prop('value'));
        $('#floater input#axValue').prop('value', filterUsers.join(":") + '|' + filterCustomers.join(":") + '|' + filterProjects.join(":") + '|' + filterActivities.join(":"));
        $('#floater input#filter_cleared').prop('value', $('#export_extension_tab_filter_cleared').prop('value'));
        $('#floater input#filter_refundable').prop('value', $('#export_extension_tab_filter_refundable').prop('value'));
        $('#floater input#filter_type').prop('value', $('#export_extension_tab_filter_type').prop('value'));
        $('#floater input#axColumns').prop('value', export_enabled_columns());
        $('.floater_content fieldset label').css('width', '200px');

        $('#floater input#first_day').prop('value', new Date($('#pick_in').val()).getTime() / 1000);
        $('#floater input#last_day').prop('value', new Date($('#pick_out').val()).getTime() / 1000);
    });
</script>