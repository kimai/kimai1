<?php /* Smarty version 2.6.20, created on 2011-12-07 17:39:07
         compiled from core/selector.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'core/selector.tpl', 5, false),)), $this); ?>
<div class="preselection">
    
    <strong><?php echo $this->_tpl_vars['kga']['lang']['selectR']; ?>
</strong><br />

    <strong class="short"><?php echo $this->_tpl_vars['kga']['lang']['selectKND']; ?>
</strong><span class="selection" id="sel_knd"><?php echo ((is_array($_tmp=$this->_tpl_vars['knd_data']['knd_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</span><br/>
    <strong class="short"><?php echo $this->_tpl_vars['kga']['lang']['selectPCT']; ?>
</strong><span class="selection" id="sel_pct"><?php echo ((is_array($_tmp=$this->_tpl_vars['pct_data']['pct_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</span><br/>    
    <strong class="short"><?php echo $this->_tpl_vars['kga']['lang']['selectEVT']; ?>
</strong><span class="selection" id="sel_evt"><?php echo ((is_array($_tmp=$this->_tpl_vars['evt_data']['evt_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</span><br/>    
</div>