<?php /* Smarty version 2.6.20, created on 2011-12-08 15:19:00
         compiled from groups.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'groups.tpl', 19, false),array('modifier', 'escape', 'groups.tpl', 23, false),)), $this); ?>
<form>
    <input type=text id="newgroup" class="formfield"></input>
    <input class='btn_ok' type=submit value="<?php echo $this->_tpl_vars['kga']['lang']['addgroup']; ?>
" onclick="ap_ext_newGroup(); return false;">
</form>
<br />
<table>
    <thead>
        <tr class='headerrow'>
            <th><?php echo $this->_tpl_vars['kga']['lang']['group']; ?>
</th>
            <th><?php echo $this->_tpl_vars['kga']['lang']['options']; ?>
</th>
            <th><?php echo $this->_tpl_vars['kga']['lang']['members']; ?>
</th>
            <th><?php echo $this->_tpl_vars['kga']['lang']['groupleader']; ?>
</th>
        </tr>
    </thead>
    <tbody>


<?php unset($this->_sections['grouparray']);
$this->_sections['grouparray']['name'] = 'grouparray';
$this->_sections['grouparray']['loop'] = is_array($_loop=$this->_tpl_vars['arr_grp']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['grouparray']['show'] = true;
$this->_sections['grouparray']['max'] = $this->_sections['grouparray']['loop'];
$this->_sections['grouparray']['step'] = 1;
$this->_sections['grouparray']['start'] = $this->_sections['grouparray']['step'] > 0 ? 0 : $this->_sections['grouparray']['loop']-1;
if ($this->_sections['grouparray']['show']) {
    $this->_sections['grouparray']['total'] = $this->_sections['grouparray']['loop'];
    if ($this->_sections['grouparray']['total'] == 0)
        $this->_sections['grouparray']['show'] = false;
} else
    $this->_sections['grouparray']['total'] = 0;
if ($this->_sections['grouparray']['show']):

            for ($this->_sections['grouparray']['index'] = $this->_sections['grouparray']['start'], $this->_sections['grouparray']['iteration'] = 1;
                 $this->_sections['grouparray']['iteration'] <= $this->_sections['grouparray']['total'];
                 $this->_sections['grouparray']['index'] += $this->_sections['grouparray']['step'], $this->_sections['grouparray']['iteration']++):
$this->_sections['grouparray']['rownum'] = $this->_sections['grouparray']['iteration'];
$this->_sections['grouparray']['index_prev'] = $this->_sections['grouparray']['index'] - $this->_sections['grouparray']['step'];
$this->_sections['grouparray']['index_next'] = $this->_sections['grouparray']['index'] + $this->_sections['grouparray']['step'];
$this->_sections['grouparray']['first']      = ($this->_sections['grouparray']['iteration'] == 1);
$this->_sections['grouparray']['last']       = ($this->_sections['grouparray']['iteration'] == $this->_sections['grouparray']['total']);
?>
    <tr class='<?php echo smarty_function_cycle(array('values' => "even,odd"), $this);?>
'>

        <td>
<?php if ($this->_tpl_vars['arr_grp'][$this->_sections['grouparray']['index']]['grp_ID'] == 1): ?>            
            <span style="color:red"><?php echo ((is_array($_tmp=$this->_tpl_vars['arr_grp'][$this->_sections['grouparray']['index']]['grp_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</span>
<?php else: ?>
            <?php echo ((is_array($_tmp=$this->_tpl_vars['arr_grp'][$this->_sections['grouparray']['index']]['grp_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

<?php endif; ?>            
        </td>


        
        <td><?php echo '<a href="#" onClick="ap_ext_editGroup(\''; ?><?php echo $this->_tpl_vars['arr_grp'][$this->_sections['grouparray']['index']]['grp_ID']; ?><?php echo '\'); $(this).blur(); return false;"><img src="../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/edit2.gif" title="'; ?><?php echo $this->_tpl_vars['kga']['lang']['editgrp']; ?><?php echo '" width="13" height="13" alt="'; ?><?php echo $this->_tpl_vars['kga']['lang']['editgrp']; ?><?php echo '" border="0"></a>&nbsp;'; ?><?php echo ''; ?><?php if ($this->_tpl_vars['arr_grp'][$this->_sections['grouparray']['index']]['count_users'] == 0): ?><?php echo '<a href="#" onClick="ap_ext_deleteGroup('; ?><?php echo $this->_tpl_vars['arr_grp'][$this->_sections['grouparray']['index']]['grp_ID']; ?><?php echo ')"><img src="../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/button_trashcan.png" title="'; ?><?php echo $this->_tpl_vars['kga']['lang']['delgrp']; ?><?php echo '" width="13" height="13" alt="'; ?><?php echo $this->_tpl_vars['kga']['lang']['delgrp']; ?><?php echo '" border="0"></a>'; ?><?php else: ?><?php echo '<img src="../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/button_trashcan_.png" title="'; ?><?php echo $this->_tpl_vars['kga']['lang']['delgrp']; ?><?php echo '" width="13" height="13" alt="'; ?><?php echo $this->_tpl_vars['kga']['lang']['delgrp']; ?><?php echo '" border="0">'; ?><?php endif; ?><?php echo ''; ?>
</td>
        
        <td><?php echo $this->_tpl_vars['arr_grp'][$this->_sections['grouparray']['index']]['count_users']; ?>
</td>
        
        
    

        <td>
            <?php $_from = $this->_tpl_vars['arr_grp'][$this->_sections['grouparray']['index']]['leader_name']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['leader']):
?>
            <?php echo ((is_array($_tmp=$this->_tpl_vars['leader'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

            <?php endforeach; endif; unset($_from); ?>
        </td>




    </tr>
<?php endfor; endif; ?>
</tbody>
</table>
