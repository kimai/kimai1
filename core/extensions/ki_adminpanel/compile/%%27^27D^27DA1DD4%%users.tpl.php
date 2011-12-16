<?php /* Smarty version 2.6.20, created on 2011-12-08 15:19:00
         compiled from users.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'users.tpl', 33, false),array('modifier', 'escape', 'users.tpl', 40, false),)), $this); ?>
<form>
    <input type=text id="newuser" class="formfield"></input>
    <input class='btn_ok' type="submit" value="<?php echo $this->_tpl_vars['kga']['lang']['adduser']; ?>
" onclick="ap_ext_newUser(); return false;">
<?php if ($this->_tpl_vars['showDeletedUsers']): ?>    
    <input class='btn_ok' type="button" value="<?php echo $this->_tpl_vars['kga']['lang']['hidedeletedusers']; ?>
" onclick="ap_ext_hideDeletedUsers(); return false;">
<?php else: ?>
    <input class='btn_ok' type="button" value="<?php echo $this->_tpl_vars['kga']['lang']['showdeletedusers']; ?>
" onclick="ap_ext_showDeletedUsers(); return false;">
<?php endif; ?>
</form>



<br />



<table>

    <thead>
      <tr>
          <th><?php echo $this->_tpl_vars['kga']['lang']['username']; ?>
</th>
          <th><?php echo $this->_tpl_vars['kga']['lang']['options']; ?>
</th>
          <th><?php echo $this->_tpl_vars['kga']['lang']['status']; ?>
</th>
          <th><?php echo $this->_tpl_vars['kga']['lang']['group']; ?>
</th>
      </tr>
    </thead>


    <tbody>
<?php unset($this->_sections['userarray']);
$this->_sections['userarray']['name'] = 'userarray';
$this->_sections['userarray']['loop'] = is_array($_loop=$this->_tpl_vars['arr_usr']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['userarray']['show'] = true;
$this->_sections['userarray']['max'] = $this->_sections['userarray']['loop'];
$this->_sections['userarray']['step'] = 1;
$this->_sections['userarray']['start'] = $this->_sections['userarray']['step'] > 0 ? 0 : $this->_sections['userarray']['loop']-1;
if ($this->_sections['userarray']['show']) {
    $this->_sections['userarray']['total'] = $this->_sections['userarray']['loop'];
    if ($this->_sections['userarray']['total'] == 0)
        $this->_sections['userarray']['show'] = false;
} else
    $this->_sections['userarray']['total'] = 0;
if ($this->_sections['userarray']['show']):

            for ($this->_sections['userarray']['index'] = $this->_sections['userarray']['start'], $this->_sections['userarray']['iteration'] = 1;
                 $this->_sections['userarray']['iteration'] <= $this->_sections['userarray']['total'];
                 $this->_sections['userarray']['index'] += $this->_sections['userarray']['step'], $this->_sections['userarray']['iteration']++):
$this->_sections['userarray']['rownum'] = $this->_sections['userarray']['iteration'];
$this->_sections['userarray']['index_prev'] = $this->_sections['userarray']['index'] - $this->_sections['userarray']['step'];
$this->_sections['userarray']['index_next'] = $this->_sections['userarray']['index'] + $this->_sections['userarray']['step'];
$this->_sections['userarray']['first']      = ($this->_sections['userarray']['iteration'] == 1);
$this->_sections['userarray']['last']       = ($this->_sections['userarray']['iteration'] == $this->_sections['userarray']['total']);
?><?php echo '<tr class=\''; ?><?php echo smarty_function_cycle(array('values' => "even,odd"), $this);?><?php echo '\'>'; ?><?php echo '<td>'; ?><?php if ($this->_tpl_vars['curr_user'] == $this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_name']): ?><?php echo '<strong style="color:#00E600">'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '</strong>'; ?><?php else: ?><?php echo ''; ?><?php if ($this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_trash']): ?><?php echo '<span style="color:#999">'; ?><?php endif; ?><?php echo ''; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo ''; ?><?php if ($this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_trash']): ?><?php echo '</span>'; ?><?php endif; ?><?php echo ''; ?><?php endif; ?><?php echo '</td>'; ?><?php echo ''; ?><?php echo '<td><a href="#" onClick="ap_ext_editUser(\''; ?><?php echo $this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_ID']; ?><?php echo '\'); $(this).blur(); return false;"><img src="../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/edit2.gif" title="'; ?><?php echo $this->_tpl_vars['kga']['lang']['editusr']; ?><?php echo '" width="13" height="13" alt="'; ?><?php echo $this->_tpl_vars['kga']['lang']['editusr']; ?><?php echo '" border="0"></a>&nbsp;'; ?><?php echo ''; ?><?php if ($this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_mail']): ?><?php echo '<a href="mailto:'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_mail'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '"><img src="../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/button_mail.gif" title="'; ?><?php echo $this->_tpl_vars['kga']['lang']['mailusr']; ?><?php echo '" width="12" height="13" alt="'; ?><?php echo $this->_tpl_vars['kga']['lang']['mailusr']; ?><?php echo '" border="0"></a>'; ?><?php else: ?><?php echo '<img src="../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/button_mail_.gif" title="'; ?><?php echo $this->_tpl_vars['kga']['lang']['mailusr']; ?><?php echo '" width="12" height="13" alt="'; ?><?php echo $this->_tpl_vars['kga']['lang']['mailusr']; ?><?php echo '" border="0">'; ?><?php endif; ?><?php echo '&nbsp;'; ?><?php if ($this->_tpl_vars['curr_user'] != $this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_name']): ?><?php echo '<a href="#" id="delete_usr'; ?><?php echo $this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_ID']; ?><?php echo '" onClick="ap_ext_deleteUser('; ?><?php echo $this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_ID']; ?><?php echo ')"><img src="../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/button_trashcan.png" title="'; ?><?php echo $this->_tpl_vars['kga']['lang']['delusr']; ?><?php echo '" width="13" height="13" alt="'; ?><?php echo $this->_tpl_vars['kga']['lang']['delusr']; ?><?php echo '" border="0"></a>'; ?><?php else: ?><?php echo '<img src="../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/button_trashcan_.png" title="'; ?><?php echo $this->_tpl_vars['kga']['lang']['delusr']; ?><?php echo '" width="13" height="13" alt="'; ?><?php echo $this->_tpl_vars['kga']['lang']['delusr']; ?><?php echo '" border="0">'; ?><?php endif; ?><?php echo '</td>'; ?><?php echo ''; ?><?php echo '<td>'; ?><?php if ($this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_sts'] == 0): ?><?php echo '<img src=\'../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/crown.png\' alt=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['adminusr']; ?><?php echo '\' title=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['adminusr']; ?><?php echo '\' border="0">'; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_sts'] == 1): ?><?php echo '<img src=\'../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/leader.gif\' alt=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['groupleader']; ?><?php echo '\' title=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['groupleader']; ?><?php echo '\' border="0">'; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_sts'] == 2): ?><?php echo '<img src=\'../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/user.gif\' alt=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['regusr']; ?><?php echo '\' title=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['regusr']; ?><?php echo '\' border="0">'; ?><?php endif; ?><?php echo '&nbsp;'; ?><?php if ($this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_active'] == 1): ?><?php echo ''; ?><?php if ($this->_tpl_vars['curr_user'] != $this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_name']): ?><?php echo '<a href="#" id="ban'; ?><?php echo $this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_ID']; ?><?php echo '" onClick="ap_ext_banUser(\''; ?><?php echo $this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_ID']; ?><?php echo '\'); return false;"><img src=\'../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/jipp.gif\' alt=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['activeusr']; ?><?php echo '\' title=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['activeusr']; ?><?php echo '\' border="0" width="16" height="16" /></a>'; ?><?php else: ?><?php echo '<img src=\'../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/jipp_.gif\' alt=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['activeusr']; ?><?php echo '\' title=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['activeusr']; ?><?php echo '\' border="0" width="16" height="16" />'; ?><?php endif; ?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_active'] == 0): ?><?php echo '<a href="#" id="ban'; ?><?php echo $this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_ID']; ?><?php echo '" onClick="ap_ext_unbanUser(\''; ?><?php echo $this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_ID']; ?><?php echo '\'); return false;"><img src=\'../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/lock.png\' alt=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['bannedusr']; ?><?php echo '\' title=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['bannedusr']; ?><?php echo '\' border="0" width="16" height="16" /></a>'; ?><?php endif; ?><?php echo '&nbsp;'; ?><?php if ($this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_pw'] == 'no'): ?><?php echo '<a href="#" onClick="ap_ext_editUser(\''; ?><?php echo $this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_ID']; ?><?php echo '\'); $(this).blur(); return false;"><img src="../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/caution_mini.png" width="16" height="16" title=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['nopasswordset']; ?><?php echo '\' border="0"></a>'; ?><?php endif; ?><?php echo '&nbsp;'; ?><?php if ($this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['usr_trash']): ?><?php echo '<strong style="color:red">X</strong>'; ?><?php endif; ?><?php echo '</td>'; ?><?php echo ''; ?><?php echo '<td>'; ?><?php unset($this->_sections['group']);
$this->_sections['group']['name'] = 'group';
$this->_sections['group']['loop'] = is_array($_loop=$this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['groups']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['group']['show'] = true;
$this->_sections['group']['max'] = $this->_sections['group']['loop'];
$this->_sections['group']['step'] = 1;
$this->_sections['group']['start'] = $this->_sections['group']['step'] > 0 ? 0 : $this->_sections['group']['loop']-1;
if ($this->_sections['group']['show']) {
    $this->_sections['group']['total'] = $this->_sections['group']['loop'];
    if ($this->_sections['group']['total'] == 0)
        $this->_sections['group']['show'] = false;
} else
    $this->_sections['group']['total'] = 0;
if ($this->_sections['group']['show']):

            for ($this->_sections['group']['index'] = $this->_sections['group']['start'], $this->_sections['group']['iteration'] = 1;
                 $this->_sections['group']['iteration'] <= $this->_sections['group']['total'];
                 $this->_sections['group']['index'] += $this->_sections['group']['step'], $this->_sections['group']['iteration']++):
$this->_sections['group']['rownum'] = $this->_sections['group']['iteration'];
$this->_sections['group']['index_prev'] = $this->_sections['group']['index'] - $this->_sections['group']['step'];
$this->_sections['group']['index_next'] = $this->_sections['group']['index'] + $this->_sections['group']['step'];
$this->_sections['group']['first']      = ($this->_sections['group']['iteration'] == 1);
$this->_sections['group']['last']       = ($this->_sections['group']['iteration'] == $this->_sections['group']['total']);
?><?php echo ''; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['arr_usr'][$this->_sections['userarray']['index']]['groups'][$this->_sections['group']['index']])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo ''; ?><?php if ($this->_sections['group']['last'] == false): ?><?php echo ', '; ?><?php endif; ?><?php echo ''; ?><?php endfor; endif; ?><?php echo '</td>'; ?><?php echo '</tr></tbody>'; ?>
<?php endfor; endif; ?>

</table>

<p><strong><?php echo $this->_tpl_vars['kga']['lang']['hint']; ?>
</strong> <?php echo $this->_tpl_vars['kga']['lang']['usr_caution1']; ?>
 '<?php echo ((is_array($_tmp=$this->_tpl_vars['curr_user'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' <?php echo $this->_tpl_vars['kga']['lang']['usr_caution2']; ?>
</p>