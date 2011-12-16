<?php /* Smarty version 2.6.20, created on 2011-12-07 17:39:07
         compiled from lists/pct.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'lists/pct.tpl', 1, false),array('modifier', 'escape', 'lists/pct.tpl', 14, false),array('modifier', 'replace', 'lists/pct.tpl', 18, false),array('modifier', 'truncate', 'lists/pct.tpl', 24, false),)), $this); ?>
<?php echo smarty_function_cycle(array('values' => "odd,even",'reset' => true,'print' => false), $this);?>

          <table>

            <tbody>
    
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
<?php if ($this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_visible']): ?>
                <tr id="row_pct<?php echo $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_ID']; ?>
" class="pct knd<?php echo $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['knd_ID']; ?>
 <?php echo smarty_function_cycle(array('values' => "odd,even"), $this);?>
" >
                    
                    
                    <td nowrap class="option">

<?php if ($this->_tpl_vars['kga']['usr'] && $this->_tpl_vars['kga']['usr']['usr_sts'] != 2): ?>
                        <a href ="#" onClick="editSubject('pct',<?php echo $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_ID']; ?>
); $(this).blur(); return false;"><img src='../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/edit2.gif' width='13' height='13' alt='<?php echo $this->_tpl_vars['kga']['lang']['edit']; ?>
' title='<?php echo $this->_tpl_vars['kga']['lang']['edit']; ?>
 (ID:<?php echo $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_ID']; ?>
)' border='0' /></a>
<?php endif; ?>
                        <a href ="#" onClick="lists_update_filter('pct',<?php echo $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_ID']; ?>
); $(this).blur(); return false;"><img src='../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/filter.png' width='13' height='13' alt='<?php echo $this->_tpl_vars['kga']['lang']['filter']; ?>
' title='<?php echo $this->_tpl_vars['kga']['lang']['filter']; ?>
' border='0' /></a>

                        <a href ="#" class="preselect" onClick="buzzer_preselect('pct',<?php echo $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_ID']; ?>
,'<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_name'])) ? $this->_run_mod_handler('replace', true, $_tmp, "'", "\\'") : smarty_modifier_replace($_tmp, "'", "\\'")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
',<?php echo $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['knd_ID']; ?>
,'<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['knd_name'])) ? $this->_run_mod_handler('replace', true, $_tmp, "'", "\\'") : smarty_modifier_replace($_tmp, "'", "\\'")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
'); lists_reload('evt'); return false;" id="ps<?php echo $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_ID']; ?>
"><img src='../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/preselect_off.png' width='13' height='13' alt='<?php echo $this->_tpl_vars['kga']['lang']['select']; ?>
' title='<?php echo $this->_tpl_vars['kga']['lang']['select']; ?>
 (ID:<?php echo $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_ID']; ?>
)' border='0' /></a>
                    </td>

                    <td width="100%" class="projects" onmouseover="lists_change_color(this,true);" onmouseout="lists_change_color(this,false);" onClick="buzzer_preselect('pct',<?php echo $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_ID']; ?>
,'<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_name'])) ? $this->_run_mod_handler('replace', true, $_tmp, "'", "\\'") : smarty_modifier_replace($_tmp, "'", "\\'")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
',<?php echo $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['knd_ID']; ?>
,'<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['knd_name'])) ? $this->_run_mod_handler('replace', true, $_tmp, "'", "\\'") : smarty_modifier_replace($_tmp, "'", "\\'")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
'); lists_reload('evt'); return false;">
                        <?php if ($this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_visible'] != 1): ?><span style="color:#bbb"><?php endif; ?>
                        <?php if ($this->_tpl_vars['kga']['conf']['flip_pct_display']): ?>    
                            <?php if ($this->_tpl_vars['kga']['conf']['showIDs'] == 1): ?><span class="ids"><?php echo $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_ID']; ?>
</span> <?php endif; ?><span class="lighter"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['knd_name'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 30, "...") : smarty_modifier_truncate($_tmp, 30, "...")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
:</span> <?php echo ((is_array($_tmp=$this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

                        <?php else: ?>
                            <?php if ($this->_tpl_vars['kga']['conf']['pct_comment_flag'] == 1): ?>
                                <?php if ($this->_tpl_vars['kga']['conf']['showIDs'] == 1): ?><span class="ids"><?php echo $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_ID']; ?>
</span> <?php endif; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 <span class="lighter"><?php if ($this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_comment']): ?>(<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_comment'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 30, "...") : smarty_modifier_truncate($_tmp, 30, "...")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
)<?php else: ?><span class="lighter">(<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['knd_name'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 30, "...") : smarty_modifier_truncate($_tmp, 30, "...")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
)</span><?php endif; ?></span>
                            <?php else: ?>
                                <?php if ($this->_tpl_vars['kga']['conf']['showIDs'] == 1): ?><span class="ids"><?php echo $this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_ID']; ?>
</span> <?php endif; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 <span class="lighter">(<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['knd_name'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 30, "...") : smarty_modifier_truncate($_tmp, 30, "...")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
)</span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['pct_visible'] != 1): ?></span><?php endif; ?>
                    </td>


                    <td class="annotation">
                        <?php echo ((is_array($_tmp=$this->_tpl_vars['arr_pct'][$this->_sections['row']['index']]['zeit'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

                    </td>

                </tr>
<?php endif; ?>            
<?php endfor; endif; ?>

<?php if ($this->_tpl_vars['arr_pct'] == '0'): ?>
                <tr>
                    <td nowrap colspan='3'>
                        <strong style="color:red"><?php echo $this->_tpl_vars['kga']['lang']['noItems']; ?>
</strong>
                    </td>
                </tr>
<?php endif; ?>


            </tbody>  
        </table>  