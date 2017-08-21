<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->kga['lang']['export_extension']['exportInddXML'] ?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->kga['lang']['close'] ?></a>
        </div>
    </div>
    <div id="help">
        <div class="content"></div>
    </div>
    <div class="floater_content">
        <form id="export_extension_form_export_INDDXML" action="../extensions/ki_export/processor.php" method="post">
            <input type="hidden" name="axAction" value="export_INDDXML"/>
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
                        <label for="reverse_order"><?php echo $this->kga['lang']['export_extension']['reverse_order'] ?>:</label>
                        <input type="checkbox" value="true" name="reverse_order" id="reverse_order" <?php if ($this->prefs['reverse_order']): ?> checked="checked" <?php endif; ?>/>
                    </li>
                    <li>
                        <label for="decimal_separator"><?php echo $this->kga['lang']['decimal_separator'] ?>:</label>
                        <input type="text" value="<?php echo $this->prefs['decimal_separator'] ?>" name="decimal_separator" id="decimal_separator" size="1"/>
                    </li>
                    <li>
                        <?php echo $this->kga['lang']['export_extension']['dl_hint'] ?>
                    </li>
                </ul>
                <div id="formbuttons">
                    <input class="btn_norm" type="button" value="<?php echo $this->kga['lang']['cancel'] ?>" onclick="floaterClose();return false;"/>
                    <input class="btn_ok" type="submit" value="<?php echo $this->kga['lang']['submit'] ?>" onclick="floaterClose();"/>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#help').hide();
        var $floater = $('#floater');
        $floater.find('#timeformat').prop('value', $('#export_extension_timeformat').prop('value'));
        $floater.find('#dateformat').prop('value', $('#export_extension_dateformat').prop('value'));
        $floater.find('#default_location').prop('value', $('#default_location').prop('value'));
        $floater.find('#axValue').prop('value', filterUsers.join(":") + '|' + filterCustomers.join(":") + '|' + filterProjects.join(":") + '|' + filterActivities.join(":"));
        $floater.find('#filter_cleared').prop('value', $('#export_extension_tab_filter_cleared').prop('value'));
        $floater.find('#filter_refundable').prop('value', $('#export_extension_tab_filter_refundable').prop('value'));
        $floater.find('#filter_type').prop('value', $('#export_extension_tab_filter_type').prop('value'));
        $floater.find('#axColumns').prop('value', export_enabled_columns());
        $floater.find('#first_day').prop('value', new Date($('#pick_in').val()).getTime() / 1000);
        $floater.find('#last_day').prop('value', new Date($('#pick_out').val()).getTime() / 1000);
        
        $floater.find('.floater_content fieldset label').css('width', '200px');
    });
</script>