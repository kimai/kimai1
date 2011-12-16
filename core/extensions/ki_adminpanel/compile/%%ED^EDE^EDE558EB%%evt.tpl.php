<?php /* Smarty version 2.6.20, created on 2011-12-08 15:19:00
         compiled from evt.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'evt.tpl', 2, false),array('modifier', 'truncate', 'evt.tpl', 10, false),array('function', 'cycle', 'evt.tpl', 15, false),)), $this); ?>

<a href="#" onClick="floaterShow('floaters.php','add_edit_evt',0,0,500,200); $(this).blur(); return false;"><img src="../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/add.png" width="22" height="16" alt="<?php echo $this->_tpl_vars['kga']['lang']['new_evt']; ?>
"></a> <?php echo $this->_tpl_vars['kga']['lang']['new_evt']; ?>


&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['kga']['lang']['view_filter']; ?>
:
        <select size="1" id="evt_pct_filter" onchange="ap_ext_refreshSubtab('evt');">
          <option value="-2" <?php if ($this->_tpl_vars['selected_evt_filter'] == -2): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['kga']['lang']['unassigned']; ?>
</option>
          <option value="-1" <?php if ($this->_tpl_vars['selected_evt_filter'] == -1): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['kga']['lang']['all_events']; ?>
</option>
          <?php unset($this->_sections['row']);
$this->_sections['row']['name'] = 'row';
$this->_sections['row']['loop'] = is_array($_loop=$this->_tpl_vars['arr_pct']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
          <option value="<?php echo $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_ID']; ?>
"
             <?php if ($this->_tpl_vars['selected_evt_filter'] == $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_ID']): ?>selected="selected"<?php endif; ?>><?php echo ((is_array($_tmp=$this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 (<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['knd_name'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 30, "...") : smarty_modifier_truncate($_tmp, 30, "...")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
)</option>
          <?php endfor; endif; ?>
        </select>
<br/><br/>

<?php echo smarty_function_cycle(array('values' => "odd,even",'reset' => true,'print' => false), $this);?>

          <table>
              
              <thead>
                  <tr class='headerrow'>
                      <th><?php echo $this->_tpl_vars['kga']['lang']['options']; ?>
</th>
                      <th><?php echo $this->_tpl_vars['kga']['lang']['evts']; ?>
</th>
                      <th><?php echo $this->_tpl_vars['kga']['lang']['groups']; ?>
</th>
                  </tr>
              </thead>
              
    
            <tbody>

<?php $_from = $this->_tpl_vars['arr_evt']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['evt']):
?>
<?php if ($this->_tpl_vars['evt']['evt_visible'] || $this->_tpl_vars['evt']['zeit'] != "0:00"): ?>
            
                <tr class="<?php echo smarty_function_cycle(array('values' => "odd,even"), $this);?>
">

                    <td class="option">
                        <a href ="#" onClick="editSubject('evt',<?php echo $this->_tpl_vars['evt']['evt_ID']; ?>
); $(this).blur(); return false;">
                            <img src='../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/edit2.gif' width='13' height='13' alt='<?php echo $this->_tpl_vars['kga']['lang']['edit']; ?>
' title='<?php echo $this->_tpl_vars['kga']['lang']['edit']; ?>
' border='0' />
                        </a>
                        
                        &nbsp;
                        
                        <a href="#" id="delete_evt<?php echo $this->_tpl_vars['evt']['evt_ID']; ?>
" onClick="ap_ext_deleteEvent(<?php echo $this->_tpl_vars['evt']['evt_ID']; ?>
)">
                          <img src="../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/button_trashcan.png" title="<?php echo $this->_tpl_vars['kga']['lang']['delevt']; ?>
" width="13" height="13" alt="<?php echo $this->_tpl_vars['kga']['lang']['delevt']; ?>
" border="0">
                        </a>
                    </td>

                    <td class="events">
                        <?php if ($this->_tpl_vars['evt']['evt_visible'] != 1): ?><span style="color:#bbb"><?php endif; ?>
                        <?php echo ((is_array($_tmp=$this->_tpl_vars['evt']['evt_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

                        <?php if ($this->_tpl_vars['evt']['evt_visible'] != 1): ?></span><?php endif; ?>
                    </td>
                    
                    <td>
                        <?php echo ((is_array($_tmp=$this->_tpl_vars['evt']['groups'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

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