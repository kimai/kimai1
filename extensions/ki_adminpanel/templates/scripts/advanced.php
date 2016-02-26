    <script type="text/javascript">
        function cb(result) {
            if (result.errors.length == 0) {
              window.location.reload();
              return;
            }
            $("#adminPanel_extension_form_editadv_submit").blur();
            $("#adminPanel_extension_output").width($(".adminPanel_extension_panel_header").width()-22);
            $("#adminPanel_extension_output").fadeIn(fading_enabled?500:0,function(){
                $("#adminPanel_extension_output").fadeOut(fading_enabled?4000:0);
            });
        }
        $(document).ready(function() {
    
            $('.disableInput').click(function(){
              var input = $(this);
              if (input.is (':checked')) {
                input.parent().removeClass("disabled");
                input.siblings().prop("disabled", false);
              }
              else {
                input.parent().addClass("disabled");
                input.siblings().prop("disabled", true);
              }
            });

            $('#adminPanel_extension_form_editadv').ajaxForm({target:'#adminPanel_extension_output',success:cb}); 
        }); 
    </script>

<div class="content">
    
    <div id="adminPanel_extension_output"></div>
        
    <form id="adminPanel_extension_form_editadv" action="../extensions/ki_adminpanel/processor.php" method="post">
        
        <fieldset class="adminPanel_extension_advanced">
            <div>
                <input type="text" name="adminmail" size="20" value="<?php echo $this->escape($this->kga['conf']['adminmail']) ?>" class="formfield"> <?php echo $this->kga['lang']['adminmail']?>
            </div>
            <div>
                <input type="text" name="logintries" size="2" value="<?php echo $this->escape($this->kga['conf']['loginTries']) ?>" class="formfield"> <?php echo $this->kga['lang']['logintries']?>
            </div>
            <div>
                <input type="text" name="loginbantime" size="4" value="<?php echo $this->escape($this->kga['conf']['loginBanTime']) ?>" class="formfield"> <?php echo $this->kga['lang']['bantime']?>
            </div>

            <div id="adminPanel_extension_checkupdate">
                <a href="javascript:adminPanel_extension_checkupdate();"><?php echo $this->kga['lang']['checkupdate']?></a>
            </div>

            <div>
                <?php echo $this->kga['lang']['lang']?>:
                <?php echo $this->formSelect('language', $this->kga['conf']['language'], array('class' => 'formfield'), array_combine($this->languages, $this->languages)); ?>
            </div>

            <div>
               <input type="checkbox" name="show_sensible_data" <?php if ($this->kga['show_sensible_data']): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->kga['lang']['show_sensible_data']?>
            </div>

            <div>
               <input type="checkbox" name="show_update_warn" <?php if ($this->kga['show_update_warn']): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->kga['lang']['show_update_warn']?>
            </div>

            <div>
               <input type="checkbox" name="check_at_startup" <?php if ($this->kga['check_at_startup']): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->kga['lang']['check_at_startup']?>
            </div>

            <div>
               <input type="checkbox" name="show_daySeperatorLines" <?php if ($this->kga['show_daySeperatorLines']): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->kga['lang']['show_daySeperatorLines']?>
            </div>

            <div>
               <input type="checkbox" name="show_gabBreaks" <?php if ($this->kga['show_gabBreaks']): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->kga['lang']['show_gabBreaks']?>
            </div>

            <div>
               <input type="checkbox" name="show_RecordAgain" <?php if ($this->kga['show_RecordAgain']): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->kga['lang']['show_RecordAgain']?>
            </div>

            <div>
               <input type="checkbox" name="show_TrackingNr" <?php if ($this->kga['show_TrackingNr']): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->kga['lang']['show_TrackingNr']?>
            </div>

            <div>
               <input type="text" name="currency_name" size="8" value="<?php echo $this->escape($this->kga['currency_name']) ?>" class="formfield"> <?php echo $this->kga['lang']['currency_name']?>
            </div>

            <div>
               <input type="text" name="currency_sign" size="2" value="<?php echo $this->escape($this->kga['currency_sign']) ?>" class="formfield"> <?php echo $this->kga['lang']['currency_sign']?>
            </div>

            <div>
               <input type="checkbox" name="currency_first" <?php if ($this->kga['conf']['currency_first']): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->kga['lang']['currency_first']?>
            </div>

            <div>
               <input type="text" name="date_format_2" size="15" value="<?php echo $this->escape($this->kga['date_format'][2]) ?>" class="formfield"> <?php echo $this->kga['lang']['display_date_format']?>
            </div>

            <div>
               <input type="text" name="date_format_0" size="15" value="<?php echo $this->escape($this->kga['date_format'][0]) ?>" class="formfield"> <?php echo $this->kga['lang']['display_currentDate_format']?>
            </div>

            <div>
               <input type="text" name="date_format_1" size="15" value="<?php echo $this->escape($this->kga['date_format'][1]) ?>" class="formfield"> <?php echo $this->kga['lang']['table_date_format']?>
            </div>

            <div>
               <input type="checkbox" name="durationWithSeconds" <?php if ($this->kga['conf']['durationWithSeconds']): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->kga['lang']['durationWithSeconds']?>
            </div>

            <div>
               <?php echo $this->kga['lang']['round_time']?> <select name="roundPrecision" class="formfield">
                 <option value="0" <?php if ($this->kga['conf']['roundPrecision']==0): ?> selected="selected" <?php endif; ?>>-</option>
                 <option value="1" <?php if ($this->kga['conf']['roundPrecision']==1): ?> selected="selected" <?php endif; ?>>1</option>
                 <option value="5" <?php if ($this->kga['conf']['roundPrecision']==5): ?> selected="selected" <?php endif; ?>>5</option>
                 <option value="10" <?php if ($this->kga['conf']['roundPrecision']==10): ?> selected="selected" <?php endif; ?>>10</option>
                 <option value="15" <?php if ($this->kga['conf']['roundPrecision']==15): ?> selected="selected" <?php endif; ?>>15</option>
                 <option value="15" <?php if ($this->kga['conf']['roundPrecision']==20): ?> selected="selected" <?php endif; ?>>20</option>
                 <option value="30" <?php if ($this->kga['conf']['roundPrecision']==30): ?> selected="selected" <?php endif; ?>>30</option>
               </select> <?php echo $this->kga['lang']['round_time_minute']?> <input type="checkbox" name="allowRoundDown" <?php if($this->kga['conf']['allowRoundDown']): ?> checked="checked" <?php endif; ?> value="1" class="formfiled"> <?php echo $this->kga['lang']['allowRoundDown'];?>
            </div>

            <div>
               <?php echo $this->kga['lang']['decimal_separator']?>: <input type="text" name="decimalSeparator" size="1" value="<?php echo $this->escape($this->kga['conf']['decimalSeparator']) ?>" class="formfield">
            </div>

            <div>
               <?php echo $this->formSelect('defaultTimezone', $this->kga['defaultTimezone'], null, array_combine($this->timezones, $this->timezones)); 
                     echo $this->kga['lang']['defaultTimezone']?>
            </div>

            <div>
               <input type="checkbox" name="exactSums" <?php if ($this->kga['conf']['exactSums']): ?> checked="checked" <?php endif; ?> value="1" class="formfield"> <?php echo $this->kga['lang']['exactSums']?>
            </div>

            <div <?php if (!$this->editLimitEnabled): ?> class="disabled" <?php endif; ?>>
              <input type="checkbox" name="editLimitEnabled" value="1" <?php if ($this->editLimitEnabled): ?> checked="checked" <?php endif; ?> class="formfield, disableInput"> <?php echo $this->kga['lang']['editLimitPart1']?>
              <input type="text" name="editLimitDays" size="3" class="formfield" value="<?php echo $this->editLimitDays?>" <?php if (!$this->editLimitEnabled): ?> disabled="disabled" <?php endif; ?>> <?php echo $this->kga['lang']['editLimitPart2']?>
              <input type="text" name="editLimitHours" size="3" class="formfield" value="<?php echo $this->editLimitHours?>" <?php if (!$this->editLimitEnabled): ?> disabled="disabled" <?php endif; ?>> <?php echo $this->kga['lang']['editLimitPart3']?>
            </div>

            <?php /* FIXME make status field editable */ ?>

<!--        -->
<!--            <div>-->
<!--               <select name="status[]" multiple="multiple">-->
<!--                    {html_options values=$status output=$status_names selected=$this->kga.conf.status}-->
<!--                </select> <?php echo $this->kga['lang']['status']?>-->
<!--            </div>-->
<!--            -->
<!--            <div>-->
<!--               <input type="text" name="new_status" class="formfield"> <?php echo $this->kga['lang']['new_status']?>-->
<!--            </div>-->
            
            <div <?php if (!$this->roundTimesheetEntries): ?> class="disabled" <?php endif; ?>>
              <input type="checkbox" name="roundTimesheetEntries" value="1" <?php if ($this->roundTimesheetEntries): ?> checked="checked" <?php endif; ?> class="formfield, disableInput"> <?php echo $this->kga['lang']['roundTimesheetEntries']?>
              <input type="text" name="roundMinutes" size="3" class="formfield" value="<?php echo $this->roundMinutes?>" <?php if (!$this->roundTimesheetEntries): ?> disabled="disabled" <?php endif; ?>> <?php echo $this->kga['lang']['minutes']?> <?php echo $this->kga['lang']['and']?>
              <input type="text" name="roundSeconds" size="3" class="formfield" value="<?php echo $this->roundSeconds?>" <?php if (!$this->roundTimesheetEntries): ?> disabled="disabled" <?php endif; ?>> <?php echo $this->kga['lang']['seconds']?>
            </div>
            <input name="axAction" type="hidden" value="sendEditAdvanced" />
        
            <div id="formbuttons">
                <input id="adminPanel_extension_form_editadv_submit" class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['save']?>' />
            </div>
            
        
        </fieldset>
        
    </form>

</div>
