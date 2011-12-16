<?php /* Smarty version 2.6.20, created on 2011-12-08 15:19:00
         compiled from status.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'status.tpl', 17, false),array('modifier', 'escape', 'status.tpl', 20, false),)), $this); ?>
<form>
    <input type=text id="newstatus" class="formfield"></input>
    <input class='btn_ok' type=submit value="<?php echo $this->_tpl_vars['kga']['lang']['new_status']; ?>
" onclick="ap_ext_newStatus(); return false;">
</form>
<br />
<table>
    <thead>
        <tr class='headerrow'>
            <th><?php echo $this->_tpl_vars['kga']['lang']['status']; ?>
</th>
            <th><?php echo $this->_tpl_vars['kga']['lang']['options']; ?>
</th>
        </tr>
    </thead>
    <tbody>


<?php unset($this->_sections['statusarray']);
$this->_sections['statusarray']['name'] = 'statusarray';
$this->_sections['statusarray']['loop'] = is_array($_loop=$this->_tpl_vars['arr_status']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['statusarray']['show'] = true;
$this->_sections['statusarray']['max'] = $this->_sections['statusarray']['loop'];
$this->_sections['statusarray']['step'] = 1;
$this->_sections['statusarray']['start'] = $this->_sections['statusarray']['step'] > 0 ? 0 : $this->_sections['statusarray']['loop']-1;
if ($this->_sections['statusarray']['show']) {
    $this->_sections['statusarray']['total'] = $this->_sections['statusarray']['loop'];
    if ($this->_sections['statusarray']['total'] == 0)
        $this->_sections['statusarray']['show'] = false;
} else
    $this->_sections['statusarray']['total'] = 0;
if ($this->_sections['statusarray']['show']):

            for ($this->_sections['statusarray']['index'] = $this->_sections['statusarray']['start'], $this->_sections['statusarray']['iteration'] = 1;
                 $this->_sections['statusarray']['iteration'] <= $this->_sections['statusarray']['total'];
                 $this->_sections['statusarray']['index'] += $this->_sections['statusarray']['step'], $this->_sections['statusarray']['iteration']++):
$this->_sections['statusarray']['rownum'] = $this->_sections['statusarray']['iteration'];
$this->_sections['statusarray']['index_prev'] = $this->_sections['statusarray']['index'] - $this->_sections['statusarray']['step'];
$this->_sections['statusarray']['index_next'] = $this->_sections['statusarray']['index'] + $this->_sections['statusarray']['step'];
$this->_sections['statusarray']['first']      = ($this->_sections['statusarray']['iteration'] == 1);
$this->_sections['statusarray']['last']       = ($this->_sections['statusarray']['iteration'] == $this->_sections['statusarray']['total']);
?>
    <tr class='<?php echo smarty_function_cycle(array('values' => "even,odd"), $this);?>
'>

        <td>
            <?php echo ((is_array($_tmp=$this->_tpl_vars['arr_status'][$this->_sections['statusarray']['index']]['status'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

        </td>

        <td><?php echo '<a href="#" onClick="ap_ext_editStatus(\''; ?><?php echo $this->_tpl_vars['arr_status'][$this->_sections['statusarray']['index']]['status_id']; ?><?php echo '\'); $(this).blur(); return false;"><img src="../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/edit2.gif" title="'; ?><?php echo $this->_tpl_vars['kga']['lang']['editstatus']; ?><?php echo '" width="13" height="13" alt="'; ?><?php echo $this->_tpl_vars['kga']['lang']['editstatus']; ?><?php echo '" border="0"></a>&nbsp;'; ?><?php if ($this->_tpl_vars['arr_status'][$this->_sections['statusarray']['index']]['count_zef'] == 0): ?><?php echo '<a href="#" onClick="ap_ext_deleteStatus('; ?><?php echo $this->_tpl_vars['arr_status'][$this->_sections['statusarray']['index']]['status_id']; ?><?php echo ')"><img src="../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/button_trashcan.png" title="'; ?><?php echo $this->_tpl_vars['kga']['lang']['delstatus']; ?><?php echo '" width="13" height="13" alt="'; ?><?php echo $this->_tpl_vars['kga']['lang']['delstatus']; ?><?php echo '" border="0"></a>'; ?><?php else: ?><?php echo '<img src="../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/button_trashcan_.png" title="'; ?><?php echo $this->_tpl_vars['kga']['lang']['delstatus']; ?><?php echo '" width="13" height="13" alt="'; ?><?php echo $this->_tpl_vars['kga']['lang']['delstatus']; ?><?php echo '" border="0">'; ?><?php endif; ?><?php echo ''; ?>
</td>
    </tr>
<?php endfor; endif; ?>
</tbody>
</table>
