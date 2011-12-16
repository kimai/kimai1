<?php /* Smarty version 2.6.20, created on 2011-12-07 17:39:07
         compiled from lists/usr.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'lists/usr.tpl', 1, false),array('modifier', 'escape', 'lists/usr.tpl', 15, false),)), $this); ?>
<?php echo smarty_function_cycle(array('values' => "odd,even",'reset' => true,'print' => false), $this);?>

          <table>
    
            <tbody>
    
<?php unset($this->_sections['row']);
$this->_sections['row']['name'] = 'row';
$this->_sections['row']['loop'] = is_array($_loop=$this->_tpl_vars['arr_usr']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['row']['show'] = true;
$this->_sections['row']['max'] = $this->_sections['row']['loop'];
$this->_sections['row']['step'] = 1;
$this->_sections['row']['start'] = $this->_sections['row']['step'] > 0 ? 0 : $this->_sections['row']['loop']-1;
if ($this->_sections['row']['show']) {
    $this->_sections['row']['total'] = $this->_sections['row']['loop'];
    if ($this->_sections['row']['total'] == 0)
        $this->_sections['row']['show'] = false;
} else
    $this->_sections['row']['total'] = 0;
if ($this->_sections['row']['show']):

            for ($this->_sections['row']['index'] = $this->_sections['row']['start'], $this->_sections['row']['iteration'] = 1;
                 $this->_sections['row']['iteration'] <= $this->_sections['row']['total'];
                 $this->_sections['row']['index'] += $this->_sections['row']['step'], $this->_sections['row']['iteration']++):
$this->_sections['row']['rownum'] = $this->_sections['row']['iteration'];
$this->_sections['row']['index_prev'] = $this->_sections['row']['index'] - $this->_sections['row']['step'];
$this->_sections['row']['index_next'] = $this->_sections['row']['index'] + $this->_sections['row']['step'];
$this->_sections['row']['first']      = ($this->_sections['row']['iteration'] == 1);
$this->_sections['row']['last']       = ($this->_sections['row']['iteration'] == $this->_sections['row']['total']);
?>
            
                <tr id="row_usr<?php echo $this->_tpl_vars['arr_usr'][$this->_sections['row']['index']]['usr_ID']; ?>
" class="<?php echo smarty_function_cycle(array('values' => "odd,even"), $this);?>
">
                    



                    <td nowrap class="option">
                      <a href ="#" onClick="lists_update_filter('usr',<?php echo $this->_tpl_vars['arr_usr'][$this->_sections['row']['index']]['usr_ID']; ?>
); $(this).blur(); return false;"><img src='../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/filter.png' width='13' height='13' alt='<?php echo $this->_tpl_vars['kga']['lang']['filter']; ?>
' title='<?php echo $this->_tpl_vars['kga']['lang']['filter']; ?>
' border='0' /></a>

                    </td>

                    <td width="100%" class="clients">
                            <?php echo ((is_array($_tmp=$this->_tpl_vars['arr_usr'][$this->_sections['row']['index']]['usr_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

                    </td>


                    <td nowrap class="annotation">

                    </td>

                </tr>
         
<?php endfor; endif; ?>

<?php if ($this->_tpl_vars['arr_usr'] == '0'): ?>
                <tr>
                    <td colspan='3'>
                        <strong style="color:red"><?php echo $this->_tpl_vars['kga']['lang']['noItems']; ?>
</strong>
                    </td>
                </tr>
<?php endif; ?>


            </tbody>  
        </table>  