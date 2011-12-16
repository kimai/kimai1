<?php /* Smarty version 2.6.20, created on 2011-12-08 15:19:12
         compiled from ../lists/evt.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', '../lists/evt.tpl', 1, false),array('modifier', 'escape', '../lists/evt.tpl', 13, false),array('modifier', 'replace', '../lists/evt.tpl', 17, false),)), $this); ?>
<?php echo smarty_function_cycle(array('values' => "odd,even",'reset' => true,'print' => false), $this);?>

          <table>
    
            <tbody>

<?php $_from = $this->_tpl_vars['arr_evt']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['evt']):
?>
<?php if ($this->_tpl_vars['evt']['evt_visible']): ?>
            
                <tr id="row_evt<?php echo $this->_tpl_vars['evt']['evt_ID']; ?>
" class="<?php echo smarty_function_cycle(array('values' => "odd,even"), $this);?>
" >

                    <td nowrap class="option">
<?php if ($this->_tpl_vars['kga']['usr'] && $this->_tpl_vars['kga']['usr']['usr_sts'] != 2): ?>
                        <a href ="#" onClick="editSubject('evt',<?php echo $this->_tpl_vars['evt']['evt_ID']; ?>
); $(this).blur(); return false;"><img src='../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/edit2.gif' width='13' height='13' alt='<?php echo $this->_tpl_vars['kga']['lang']['edit']; ?>
' title='<?php echo $this->_tpl_vars['kga']['lang']['edit']; ?>
 (ID:<?php echo $this->_tpl_vars['evt']['evt_ID']; ?>
)' border='0' /></a>
<?php endif; ?>
                        <a href ="#" onClick="lists_update_filter('evt',<?php echo $this->_tpl_vars['evt']['evt_ID']; ?>
); $(this).blur(); return false;"><img src='../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/filter.png' width='13' height='13' alt='<?php echo $this->_tpl_vars['kga']['lang']['filter']; ?>
' title='<?php echo $this->_tpl_vars['kga']['lang']['filter']; ?>
' border='0' /></a>

                        <a href ="#" class="preselect" onClick="buzzer_preselect('evt',<?php echo $this->_tpl_vars['evt']['evt_ID']; ?>
,'<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['evt']['evt_name'])) ? $this->_run_mod_handler('replace', true, $_tmp, "'", "\\'") : smarty_modifier_replace($_tmp, "'", "\\'")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
',0,0); return false;" id="ps<?php echo $this->_tpl_vars['evt']['evt_ID']; ?>
"><img src='../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/preselect_off.png' width='13' height='13' alt='<?php echo $this->_tpl_vars['kga']['lang']['select']; ?>
' title='<?php echo $this->_tpl_vars['kga']['lang']['select']; ?>
 (ID:<?php echo $this->_tpl_vars['evt']['evt_ID']; ?>
)' border='0' /></a>
                    </td>

                    <td width="100%" class="events" onClick="buzzer_preselect('evt',<?php echo $this->_tpl_vars['evt']['evt_ID']; ?>
,'<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['evt']['evt_name'])) ? $this->_run_mod_handler('replace', true, $_tmp, "'", "\\'") : smarty_modifier_replace($_tmp, "'", "\\'")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
',0,0); return false;" onmouseover="lists_change_color(this,true);" onmouseout="lists_change_color(this,false);">
                        <?php if ($this->_tpl_vars['evt']['evt_visible'] != 1): ?><span style="color:#bbb"><?php endif; ?>
                        <?php if ($this->_tpl_vars['kga']['conf']['showIDs'] == 1): ?><span class="ids"><?php echo $this->_tpl_vars['evt']['evt_ID']; ?>
</span> <?php endif; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['evt']['evt_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

                        <?php if ($this->_tpl_vars['evt']['evt_visible'] != 1): ?></span><?php endif; ?>
                    </td>

                    <td nowrap class="annotation">
                        <?php echo ((is_array($_tmp=$this->_tpl_vars['evt']['zeit'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

                    </td>

                </tr>
            
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>




<?php if ($this->_tpl_vars['arr_evt'] == '0'): ?>
                <tr>
                    <td colspan='3'>
                        <strong style="color:red"><?php echo $this->_tpl_vars['kga']['lang']['noItems']; ?>
</strong>
                    </td>
                </tr>
<?php endif; ?>


            </tbody>  
        </table>  