<?php /* Smarty version 2.6.20, created on 2011-12-08 15:19:00
         compiled from database.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'database.tpl', 1, false),)), $this); ?>
<?php echo $this->_tpl_vars['kga']['lang']['DBname']; ?>
: <?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['server_database'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

<br />
<br />
<a href="../db_restore.php">Database Backup Utility</a>
