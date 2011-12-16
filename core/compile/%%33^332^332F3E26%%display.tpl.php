<?php /* Smarty version 2.6.20, created on 2011-12-07 17:39:07
         compiled from core/display.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'core/display.tpl', 36, false),)), $this); ?>
<?php echo '
<script type="text/javascript" charset="utf-8">
    $(function()
    {
        $(\'.date-pick\').datepicker(
          {dateFormat:\'mm/dd/yy\',
          onSelect: function(dateText, instance) {
            if (this == $(\'#pick_in\')[0]) {
              setTimespace(new Date(dateText),undefined);
            }
            if (this == $(\'#pick_out\')[0]) {
              setTimespace(undefined,new Date(dateText));
            }
          }
        });
        
        setTimespaceStart(new Date('; ?>
<?php echo $this->_tpl_vars['timespace_in']*1000; ?>
<?php echo '));
        setTimespaceEnd(new Date('; ?>
<?php echo $this->_tpl_vars['timespace_out']*1000; ?>
<?php echo '));
        updateTimespaceWarning();
             
    });
</script>
'; ?>



<div id="dates">
        <input type="hidden" id="pick_in" class="date-pick"/>
        <a href="#" id="ts_in" onClick="$('#pick_in').datepicker('show');return false"></a> - 
        <input type="hidden" id="pick_out" class="date-pick"/>
        <a href="#" id="ts_out" onClick="$('#pick_out').datepicker('show');return false"></a>
</div>


<div id="infos">
    <span id="n_date"></span> &nbsp; 
    <img src="../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/g3_display_smallclock.png" width="13" height="13" alt="Display Smallclock" />
    <span id="n_uhr">00:00</span> &nbsp; 
    <img src="../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/g3_display_eye.png" width="15" height="12" alt="Display Eye" /> 
    <strong id="display_total"><?php echo $this->_tpl_vars['total']; ?>
</strong> 
</div>