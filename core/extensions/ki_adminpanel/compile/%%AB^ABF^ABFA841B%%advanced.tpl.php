<?php /* Smarty version 2.6.20, created on 2011-12-08 15:19:00
         compiled from advanced.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'advanced.tpl', 41, false),array('function', 'html_options', 'advanced.tpl', 56, false),)), $this); ?>
<?php echo '    
    <script type="text/javascript">
        function cb(data) {
            if (data=="ok") {
              window.location.reload();
              return;
            }
            $("#ap_ext_form_editadv_submit").blur();
            $("#ap_ext_output").width($(".ap_ext_panel_header").width()-22);
            $("#ap_ext_output").fadeIn(fading_enabled?500:0,function(){
                $("#ap_ext_output").fadeOut(fading_enabled?4000:0);
            });
        }
        $(document).ready(function() {
    
            $(\'.disableInput\').click(function(){
              var input = $(this);
              if (input.is (\':checked\')) {
                input.parent().removeClass("disabled");
                input.siblings().attr("disabled","");
              }
              else {
                input.parent().addClass("disabled");
                input.siblings().attr("disabled","disabled");
              }
            });

            $(\'#ap_ext_form_editadv\').ajaxForm({target:\'#ap_ext_output\',success:cb}); 
        }); 
    </script>
'; ?>


<div class="content">
    
    <div id="ap_ext_output"></div>
        
    <form id="ap_ext_form_editadv" action="../extensions/ki_adminpanel/processor.php" method="post">
        
        <fieldset class="ap_ext_advanced">
            <div>
                <input type="text" name="adminmail" size="20" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['adminmail'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['adminmail']; ?>

            </div>
            <div>
                <input type="text" name="logintries" size="2" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['loginTries'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['logintries']; ?>

            </div>
            <div>
                <input type="text" name="loginbantime" size="4" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['loginBanTime'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['bantime']; ?>

            </div>

            <div id="ap_ext_checkupdate">
                <a href="javascript:ap_ext_checkupdate();"><?php echo $this->_tpl_vars['kga']['lang']['checkupdate']; ?>
</a>
            </div>

            <div>
                <?php echo $this->_tpl_vars['kga']['lang']['lang']; ?>
: <select name="language" class="formfield">
                    <?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['languages'],'output' => $this->_tpl_vars['languages'],'selected' => $this->_tpl_vars['kga']['conf']['language']), $this);?>

                </select>
            </div>

            <div>
               <input type="checkbox" name="show_sensible_data" <?php if ($this->_tpl_vars['kga']['show_sensible_data']): ?>checked="checked"<?php endif; ?> value="1" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['show_sensible_data']; ?>

            </div>

            <div>
               <input type="checkbox" name="show_update_warn" <?php if ($this->_tpl_vars['kga']['show_update_warn']): ?>checked="checked"<?php endif; ?> value="1" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['show_update_warn']; ?>

            </div>

            <div>
               <input type="checkbox" name="check_at_startup" <?php if ($this->_tpl_vars['kga']['check_at_startup']): ?>checked="checked"<?php endif; ?> value="1" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['check_at_startup']; ?>

            </div>

            <div>
               <input type="checkbox" name="show_daySeperatorLines" <?php if ($this->_tpl_vars['kga']['show_daySeperatorLines']): ?>checked="checked"<?php endif; ?> value="1" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['show_daySeperatorLines']; ?>

            </div>

            <div>
               <input type="checkbox" name="show_gabBreaks" <?php if ($this->_tpl_vars['kga']['show_gabBreaks']): ?>checked="checked"<?php endif; ?> value="1" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['show_gabBreaks']; ?>

            </div>

            <div>
               <input type="checkbox" name="show_RecordAgain" <?php if ($this->_tpl_vars['kga']['show_RecordAgain']): ?>checked="checked"<?php endif; ?> value="1" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['show_RecordAgain']; ?>

            </div>

            <div>
               <input type="checkbox" name="show_TrackingNr" <?php if ($this->_tpl_vars['kga']['show_TrackingNr']): ?>checked="checked"<?php endif; ?> value="1" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['show_TrackingNr']; ?>

            </div>

            <div>
               <input type="text" name="currency_name" size="8" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['currency_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['currency_name']; ?>

            </div>

            <div>
               <input type="text" name="currency_sign" size="2" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['currency_sign'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['currency_sign']; ?>

            </div>

            <div>
               <input type="checkbox" name="currency_first" <?php if ($this->_tpl_vars['kga']['conf']['currency_first']): ?>checked="checked"<?php endif; ?> value="1" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['currency_first']; ?>

            </div>

            <div>
               <input type="text" name="date_format_2" size="8" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['date_format']['2'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['display_date_format']; ?>

            </div>

            <div>
               <input type="text" name="date_format_0" size="8" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['date_format']['0'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['display_currentDate_format']; ?>

            </div>

            <div>
               <input type="text" name="date_format_1" size="8" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['date_format']['1'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['table_date_format']; ?>

            </div>

            <div>
               <input type="checkbox" name="durationWithSeconds" <?php if ($this->_tpl_vars['kga']['conf']['durationWithSeconds']): ?>checked="checked"<?php endif; ?> value="1" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['durationWithSeconds']; ?>

            </div>

            <div>
               <?php echo $this->_tpl_vars['kga']['lang']['round_time']; ?>
 <select name="roundPrecision" class="formfield">
                 <option value="0" <?php if ($this->_tpl_vars['kga']['conf']['roundPrecision'] == 0): ?>selected="selected"<?php endif; ?>>-</option>
                 <option value="1" <?php if ($this->_tpl_vars['kga']['conf']['roundPrecision'] == 1): ?>selected="selected"<?php endif; ?>>1</option>
                 <option value="5" <?php if ($this->_tpl_vars['kga']['conf']['roundPrecision'] == 5): ?>selected="selected"<?php endif; ?>>5</option>
                 <option value="10" <?php if ($this->_tpl_vars['kga']['conf']['roundPrecision'] == 10): ?>selected="selected"<?php endif; ?>>10</option>
                 <option value="15" <?php if ($this->_tpl_vars['kga']['conf']['roundPrecision'] == 15): ?>selected="selected"<?php endif; ?>>15</option>
                 <option value="15" <?php if ($this->_tpl_vars['kga']['conf']['roundPrecision'] == 20): ?>selected="selected"<?php endif; ?>>20</option>
                 <option value="30" <?php if ($this->_tpl_vars['kga']['conf']['roundPrecision'] == 30): ?>selected="selected"<?php endif; ?>>30</option>
               </select> <?php echo $this->_tpl_vars['kga']['lang']['round_time_minute']; ?>

            </div>

            <div>
               <?php echo $this->_tpl_vars['kga']['lang']['decimal_separator']; ?>
: <input type="text" name="decimalSeparator" size="1" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['decimalSeparator'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" class="formfield">
            </div>

            <div>
               <select name="defaultTimezone">
                    <?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['timezones'],'output' => $this->_tpl_vars['timezones'],'selected' => $this->_tpl_vars['kga']['conf']['defaultTimezone']), $this);?>

                </select> <?php echo $this->_tpl_vars['kga']['lang']['defaultTimezone']; ?>

            </div>

            <div>
               <input type="checkbox" name="exactSums" <?php if ($this->_tpl_vars['kga']['conf']['exactSums']): ?>checked="checked"<?php endif; ?> value="1" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['exactSums']; ?>

            </div>

            <div <?php if (! $this->_tpl_vars['editLimitEnabled']): ?>class="disabled"<?php endif; ?>>
              <input type="checkbox" name="editLimitEnabled" value="1" <?php if ($this->_tpl_vars['editLimitEnabled']): ?>checked="checked"<?php endif; ?> class="formfield, disableInput"> <?php echo $this->_tpl_vars['kga']['lang']['editLimitPart1']; ?>

              <input type="text" name="editLimitDays" size="3" class="formfield" value="<?php echo $this->_tpl_vars['editLimitDays']; ?>
" <?php if (! $this->_tpl_vars['editLimitEnabled']): ?>disabled="disabled"<?php endif; ?>> <?php echo $this->_tpl_vars['kga']['lang']['editLimitPart2']; ?>

              <input type="text" name="editLimitHours" size="3" class="formfield" value="<?php echo $this->_tpl_vars['editLimitHours']; ?>
" <?php if (! $this->_tpl_vars['editLimitEnabled']): ?>disabled="disabled"<?php endif; ?>> <?php echo $this->_tpl_vars['kga']['lang']['editLimitPart3']; ?>

            </div>

<!--        -->
<!--            <div>-->
<!--               <select name="status[]" multiple="multiple">-->
<!--                    <?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['status'],'output' => $this->_tpl_vars['status_names'],'selected' => $this->_tpl_vars['kga']['conf']['status']), $this);?>
-->
<!--                </select> <?php echo $this->_tpl_vars['kga']['lang']['status']; ?>
-->
<!--            </div>-->
<!--            -->
<!--            <div>-->
<!--               <input type="text" name="new_status" class="formfield"> <?php echo $this->_tpl_vars['kga']['lang']['new_status']; ?>
-->
<!--            </div>-->
            
            <div <?php if (! $this->_tpl_vars['roundTimesheetEntries']): ?>class="disabled"<?php endif; ?>>
              <input type="checkbox" name="roundTimesheetEntries" value="1" <?php if ($this->_tpl_vars['roundTimesheetEntries']): ?>checked="checked"<?php endif; ?> class="formfield, disableInput"> <?php echo $this->_tpl_vars['kga']['lang']['roundTimesheetEntries']; ?>

              <input type="text" name="roundMinutes" size="3" class="formfield" value="<?php echo $this->_tpl_vars['roundMinutes']; ?>
" <?php if (! $this->_tpl_vars['roundTimesheetEntries']): ?>disabled="disabled"<?php endif; ?>> <?php echo $this->_tpl_vars['kga']['lang']['minutes']; ?>
 <?php echo $this->_tpl_vars['kga']['lang']['and']; ?>

              <input type="text" name="roundSeconds" size="3" class="formfield" value="<?php echo $this->_tpl_vars['roundSeconds']; ?>
" <?php if (! $this->_tpl_vars['roundTimesheetEntries']): ?>disabled="disabled"<?php endif; ?>> <?php echo $this->_tpl_vars['kga']['lang']['seconds']; ?>

            </div>
            <input name="axAction" type="hidden" value="sendEditAdvanced" />
        
            <div id="formbuttons">
                <input id="ap_ext_form_editadv_submit" class='btn_ok' type='submit' value='<?php echo $this->_tpl_vars['kga']['lang']['save']; ?>
' />
            </div>
            
        
        </fieldset>
        
    </form>

</div>