<div class="content">
    <div id="adminPanel_extension_output"></div>
    <form id="adminPanel_extension_form_editadv" action="../extensions/ki_adminpanel/processor.php" method="post">
        <input name="axAction" type="hidden" value="sendEditAdvanced" />
        <fieldset class="adminPanel_extension_advanced">
            <div>
                <input type="text" name="adminmail" size="20" value="<?php echo $this->escape($this->kga->getAdminEmail()) ?>" class="formfield"> <?php echo $this->translate('adminmail')?>
            </div>
            <div>
                <input type="text" name="logintries" size="2" value="<?php echo $this->escape($this->kga->getLoginTriesBeforeBan()) ?>" class="formfield"> <?php echo $this->translate('logintries')?>
            </div>
            <div>
                <input type="text" name="loginbantime" size="4" value="<?php echo $this->escape($this->kga->getLoginBanTime()) ?>" class="formfield"> <?php echo $this->translate('bantime')?>
            </div>
            <div id="adminPanel_extension_checkupdate">
                <a href="javascript:adminPanel_extension_checkupdate();"><?php echo $this->translate('checkupdate')?></a>
            </div>
            <div>
                <?php echo $this->translate('lang')?>:
                <?php echo $this->formSelect('language', $this->kga->getLanguage(true), ['class' => 'formfield'], array_combine($this->languages, $this->languages)); ?>
            </div>
            <div>
                <input type="checkbox" name="show_update_warn" <?php if ($this->kga['show_update_warn']): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->translate('show_update_warn')?>
            </div>
            <div>
                <input type="checkbox" name="check_at_startup" <?php if ($this->kga['check_at_startup']): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->translate('check_at_startup')?>
            </div>
            <div>
                <input type="checkbox" name="show_daySeperatorLines" <?php if ($this->kga->isShowDaySeperatorLines()): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->translate('show_daySeperatorLines')?>
            </div>
            <div>
                <input type="checkbox" name="show_gabBreaks" <?php if ($this->kga->isShowGabBreaks()): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->translate('show_gabBreaks')?>
            </div>
            <div>
                <input type="checkbox" name="show_RecordAgain" <?php if ($this->kga->isShowRecordAgain()): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->translate('show_RecordAgain')?>
            </div>
            <div>
                <input type="checkbox" name="show_TrackingNr" <?php if ($this->kga->isTrackingNumberEnabled()): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->translate('show_TrackingNr')?>
            </div>
            <div>
                <input type="text" name="currency_name" size="8" value="<?php echo $this->escape($this->kga->getCurrencyName()) ?>" class="formfield"> <?php echo $this->translate('currency_name')?>
            </div>
            <div>
                <input type="text" name="currency_sign" size="2" value="<?php echo $this->escape($this->kga->getCurrencySign()) ?>" class="formfield"> <?php echo $this->translate('currency_sign')?>
            </div>
            <div>
                <input type="checkbox" name="currency_first" <?php if ($this->kga->isDisplayCurrencyFirst()): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->translate('currency_first')?>
            </div>
            <div>
                <input type="text" name="date_format_2" size="15" value="<?php echo $this->escape($this->kga->getDateFormat(2)) ?>" class="formfield"> <?php echo $this->translate('display_date_format')?>
            </div>
            <div>
                <input type="text" name="date_format_0" size="15" value="<?php echo $this->escape($this->kga->getDateFormat(0)) ?>" class="formfield"> <?php echo $this->translate('date_format_0')?>
            </div>
            <div>
                <input type="text" name="date_format_3" size="15" value="<?php echo $this->escape($this->kga->getDateFormat(3)) ?>" class="formfield"> <?php echo $this->translate('date_format_3')?>
            </div>
            <div>
                <input type="text" name="date_format_1" size="15" value="<?php echo $this->escape($this->kga->getDateFormat(1)) ?>" class="formfield"> <?php echo $this->translate('table_date_format')?>
            </div>
            <div>
                <input type="text" name="table_time_format" size="15" value="<?php echo $this->escape($this->kga->getTableTimeFormat()) ?>" class="formfield"> <?php echo $this->translate('table_time_format')?>
            </div>
            <div>
                <input type="checkbox" name="durationWithSeconds" <?php if ($this->kga['conf']['durationWithSeconds']): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->translate('durationWithSeconds')?>
            </div>
            <div>
                <?php echo $this->translate('round_time')?> <select name="roundPrecision" class="formfield">
                    <option value="0" <?php if ($this->kga->getRoundPrecisionRecorderTimes() == 0): ?> selected="selected" <?php endif; ?>>-</option>
                    <option value="1" <?php if ($this->kga->getRoundPrecisionRecorderTimes() == 1): ?> selected="selected" <?php endif; ?>>1</option>
                    <option value="5" <?php if ($this->kga->getRoundPrecisionRecorderTimes() == 5): ?> selected="selected" <?php endif; ?>>5</option>
                    <option value="10" <?php if ($this->kga->getRoundPrecisionRecorderTimes() == 10): ?> selected="selected" <?php endif; ?>>10</option>
                    <option value="15" <?php if ($this->kga->getRoundPrecisionRecorderTimes() == 15): ?> selected="selected" <?php endif; ?>>15</option>
                    <option value="15" <?php if ($this->kga->getRoundPrecisionRecorderTimes() == 20): ?> selected="selected" <?php endif; ?>>20</option>
                    <option value="30" <?php if ($this->kga->getRoundPrecisionRecorderTimes() == 30): ?> selected="selected" <?php endif; ?>>30</option>
                </select> <?php echo $this->translate('round_time_minute')?> <input type="checkbox" name="allowRoundDown" <?php if($this->kga->isRoundDownRecorderTimes()): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->translate('allowRoundDown');?>
            </div>
            <div>
                <?php echo $this->translate('decimal_separator')?>: <input type="text" name="decimalSeparator" size="1" value="<?php echo $this->escape($this->kga['conf']['decimalSeparator']) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->formSelect('defaultTimezone', $this->kga['defaultTimezone'], null, array_combine($this->timezones, $this->timezones));
                echo $this->translate('defaultTimezone')?>
            </div>
            <div>
                <input type="checkbox" name="exactSums" <?php if ($this->kga->isUseExactSums()): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->translate('exactSums')?>
            </div>
            <div <?php if (!$this->editLimitEnabled): ?> class="disabled" <?php endif; ?>>
                <input type="checkbox" name="editLimitEnabled" value="1" <?php if ($this->editLimitEnabled): ?> checked="checked" <?php endif; ?> class="formfield, disableInput"> <?php echo $this->translate('editLimitPart1')?>
                <input type="text" name="editLimitDays" size="3" class="formfield" value="<?php echo $this->editLimitDays?>" <?php if (!$this->editLimitEnabled): ?> disabled="disabled" <?php endif; ?>> <?php echo $this->translate('editLimitPart2')?>
                <input type="text" name="editLimitHours" size="3" class="formfield" value="<?php echo $this->editLimitHours?>" <?php if (!$this->editLimitEnabled): ?> disabled="disabled" <?php endif; ?>> <?php echo $this->translate('editLimitPart3')?>
            </div>
            <div <?php if (!$this->roundTimesheetEntries): ?> class="disabled" <?php endif; ?>>
                <input type="checkbox" name="roundTimesheetEntries" value="1" <?php if ($this->roundTimesheetEntries): ?> checked="checked" <?php endif; ?> class="formfield, disableInput"> <?php echo $this->translate('roundTimesheetEntries')?>
                <input type="text" name="roundMinutes" size="3" class="formfield" value="<?php echo $this->roundMinutes?>" <?php if (!$this->roundTimesheetEntries): ?> disabled="disabled" <?php endif; ?>> <?php echo $this->translate('minutes')?> <?php echo $this->translate('and')?>
                <input type="text" name="roundSeconds" size="3" class="formfield" value="<?php echo $this->roundSeconds?>" <?php if (!$this->roundTimesheetEntries): ?> disabled="disabled" <?php endif; ?>> <?php echo $this->translate('seconds')?>
            </div>
            <div id="formbuttons">
                <input id="adminPanel_extension_form_editadv_submit" class="btn_ok" type="submit" value="<?php echo $this->translate('save')?>" />
            </div>
        </fieldset>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.disableInput').click(function(){
            var input = $(this);
            if (input.is (':checked')) {
                input.parent().removeClass('disabled');
                input.siblings().prop('disabled', false);
            } else {
                input.parent().addClass('disabled');
                input.siblings().prop('disabled', true);
            }
        });

        $('#adminPanel_extension_form_editadv').ajaxForm({
	        success: function (result) {
                if (result.errors.length > 0) {
	                /* FIXME: output json error messages as html
                	$('#adminPanel_extension_form_editadv_submit').blur();

	                var $adminPanel_extension_output = $('#adminPanel_extension_output');
	                $adminPanel_extension_output.width($('.adminPanel_extension_panel_header').width() - 22);
	                $adminPanel_extension_output.show();
	                */
                } else {
	                window.location.reload();
                }
            }
        });
    });
</script>