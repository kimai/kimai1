<?php /* Smarty version 2.6.20, created on 2011-12-07 22:26:56
         compiled from timespan.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'timespan.tpl', 1, false),array('modifier', 'escape', 'timespan.tpl', 1, false),)), $this); ?>
<?php echo $this->_tpl_vars['kga']['lang']['ext_invoice']['invoiceTimePeriod']; ?>
  <b><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['in'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['kga']['date_format']['2']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['kga']['date_format']['2'])))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 - <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['out'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['kga']['date_format']['2']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['kga']['date_format']['2'])))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</b>