<?php /* Smarty version 2.6.20, created on 2011-12-07 17:39:08
         compiled from zef.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'zef.tpl', 1, false),array('modifier', 'date_format', 'zef.tpl', 43, false),array('modifier', 'escape', 'zef.tpl', 61, false),array('modifier', 'replace', 'zef.tpl', 192, false),array('modifier', 'nl2br', 'zef.tpl', 297, false),)), $this); ?>
<?php echo smarty_function_cycle(array('values' => "odd,even",'reset' => true,'print' => false), $this);?>

<?php if ($this->_tpl_vars['arr_zef']): ?>
        <div id="zeftable">
        
          <table>
              
            <colgroup>
              <col class="option" />
              <col class="date" />
              <col class="from" />
              <col class="to" />
              <col class="time" />
              <col class="wage" />
              <col class="client" />
              <col class="project" />
              <col class="action" />
              <col class="trackingnumber" />
              <col class="username" />
            </colgroup>

            <tbody>

<?php $this->assign('time_buffer', '0'); ?>
<?php $this->assign('day_buffer', '0'); ?>
<?php $this->assign('zef_in_buffer', 0); ?>
                
<?php unset($this->_sections['row']);
$this->_sections['row']['name'] = 'row';
$this->_sections['row']['loop'] = is_array($_loop=$this->_tpl_vars['arr_zef']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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

<?php if ($this->_tpl_vars['time_buffer'] == 0): ?>
<?php $this->assign('time_buffer', $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out']); ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out']): ?>                
                <tr id="zefEntry<?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_ID']; ?>
" class="<?php echo smarty_function_cycle(array('values' => "odd,even"), $this);?>
">
<?php else: ?>                    
                <tr id="zefEntry<?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_ID']; ?>
" class="<?php echo smarty_function_cycle(array('values' => "odd,even"), $this);?>
 active">
<?php endif; ?>
               
                    <td nowrap class="option 
                                            <?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] > $this->_tpl_vars['time_buffer']): ?>
                                                <?php if ($this->_tpl_vars['showOverlapLines']): ?>time_overlap<?php endif; ?>
                                            <?php elseif (((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d") : smarty_modifier_date_format($_tmp, "%d")) != $this->_tpl_vars['day_buffer']): ?>
                                                <?php if ($this->_tpl_vars['kga']['show_daySeperatorLines']): ?>break_day<?php endif; ?>
                                            <?php elseif ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] != $this->_tpl_vars['zef_in_buffer']): ?>
                                                <?php if ($this->_tpl_vars['kga']['show_gabBreaks']): ?>break_gap<?php endif; ?>
                                            <?php endif; ?>
                    ">

<?php if ($this->_tpl_vars['kga']['usr']): ?>

                        
<?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out']): ?>



                        <?php if ($this->_tpl_vars['kga']['show_RecordAgain']): ?><?php echo '<a href =\'#\' class=\'recordAgain\' onClick="ts_ext_recordAgain('; ?><?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_pctID']; ?><?php echo ','; ?><?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_evtID']; ?><?php echo ','; ?><?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_ID']; ?><?php echo '); return false;"><img src=\'../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/button_recordthis.gif\' width=\'13\' height=\'13\' alt=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['recordAgain']; ?><?php echo '\' title=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['recordAgain']; ?><?php echo ' (ID:'; ?><?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_ID']; ?><?php echo ')\' border=\'0\' /></a>'; ?>
<?php endif; ?>


<?php else: ?>


                        <?php echo '<a href =\'#\' class=\'stop\' onClick="ts_ext_stopRecord('; ?><?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_ID']; ?><?php echo '); return false;"><img src=\'../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/button_stopthis.gif\' width=\'13\' height=\'13\' alt=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['stop']; ?><?php echo '\' title=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['stop']; ?><?php echo ' (ID:'; ?><?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_ID']; ?><?php echo ')\' border=\'0\' /></a>'; ?>


<?php endif; ?>

                        
<?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] && ( $this->_tpl_vars['kga']['conf']['editLimit'] == "-" || time ( ) - $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] <= $this->_tpl_vars['kga']['conf']['editLimit'] )): ?>
                        <?php echo '<a href =\'#\' onClick="editRecord('; ?><?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_ID']; ?><?php echo '); $(this).blur(); return false;" title=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['edit']; ?><?php echo '\'><img src=\'../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/edit2.gif\' width=\'13\' height=\'13\' alt=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['edit']; ?><?php echo '\' title=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['edit']; ?><?php echo '\' border=\'0\' /></a>'; ?>

                        

        <?php if ($this->_tpl_vars['kga']['conf']['quickdelete'] > 0): ?>
                        <?php echo '<a href =\'#\' class=\'quickdelete\' onClick="quickdelete('; ?><?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_ID']; ?><?php echo '); return false;"><img src=\'../skins/'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?><?php echo '/grfx/button_trashcan.png\' width=\'13\' height=\'13\' alt=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['quickdelete']; ?><?php echo '\' title=\''; ?><?php echo $this->_tpl_vars['kga']['lang']['quickdelete']; ?><?php echo '\' border=0 /></a>'; ?>

    <?php endif; ?>

<?php endif; ?> 

<?php endif; ?>

                    
                    </td>












                   


                    <td class="date
                      <?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] > $this->_tpl_vars['time_buffer']): ?>
                          <?php if ($this->_tpl_vars['showOverlapLines']): ?>time_overlap<?php endif; ?>
                      <?php elseif (((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d") : smarty_modifier_date_format($_tmp, "%d")) != $this->_tpl_vars['day_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_daySeperatorLines']): ?>break_day<?php endif; ?>
                      <?php elseif ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] != $this->_tpl_vars['zef_in_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_gabBreaks']): ?>break_gap<?php endif; ?>
                      <?php endif; ?>
                    ">
                        <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['kga']['date_format']['1']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['kga']['date_format']['1'])))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

                    </td>


                    <td class="from
                      <?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] > $this->_tpl_vars['time_buffer']): ?>
                          <?php if ($this->_tpl_vars['showOverlapLines']): ?>time_overlap<?php endif; ?>
                      <?php elseif (((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d") : smarty_modifier_date_format($_tmp, "%d")) != $this->_tpl_vars['day_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_daySeperatorLines']): ?>break_day<?php endif; ?>
                      <?php elseif ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] != $this->_tpl_vars['zef_in_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_gabBreaks']): ?>break_gap<?php endif; ?>
                      <?php endif; ?>
                    ">
                        <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

                    </td>


                    <td class="to
                      <?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] > $this->_tpl_vars['time_buffer']): ?>
                          <?php if ($this->_tpl_vars['showOverlapLines']): ?>time_overlap<?php endif; ?>
                      <?php elseif (((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d") : smarty_modifier_date_format($_tmp, "%d")) != $this->_tpl_vars['day_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_daySeperatorLines']): ?>break_day<?php endif; ?>
                      <?php elseif ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] != $this->_tpl_vars['zef_in_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_gabBreaks']): ?>break_gap<?php endif; ?>
                      <?php endif; ?>
                    ">
                    
<?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out']): ?>
                        <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

<?php else: ?>                     
                        &ndash;&ndash;:&ndash;&ndash;
<?php endif; ?>
                    </td>


                    <td class="time
                      <?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] > $this->_tpl_vars['time_buffer']): ?>
                          <?php if ($this->_tpl_vars['showOverlapLines']): ?>time_overlap<?php endif; ?>
                      <?php elseif (((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d") : smarty_modifier_date_format($_tmp, "%d")) != $this->_tpl_vars['day_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_daySeperatorLines']): ?>break_day<?php endif; ?>
                      <?php elseif ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] != $this->_tpl_vars['zef_in_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_gabBreaks']): ?>break_gap<?php endif; ?>
                      <?php endif; ?>
                    ">
                    
<?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_time']): ?>
                    
                        <?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_duration']; ?>

                      
<?php else: ?>  
                        &ndash;:&ndash;&ndash;
<?php endif; ?>
                    </td>


                    <td class="wage
                      <?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] > $this->_tpl_vars['time_buffer']): ?>
                          <?php if ($this->_tpl_vars['showOverlapLines']): ?>time_overlap<?php endif; ?>
                      <?php elseif (((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d") : smarty_modifier_date_format($_tmp, "%d")) != $this->_tpl_vars['day_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_daySeperatorLines']): ?>break_day<?php endif; ?>
                      <?php elseif ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] != $this->_tpl_vars['zef_in_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_gabBreaks']): ?>break_gap<?php endif; ?>
                      <?php endif; ?>
                    ">
                    
<?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['wage']): ?>
                    
                        <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['wage'])) ? $this->_run_mod_handler('replace', true, $_tmp, '.', $this->_tpl_vars['kga']['conf']['decimalSeparator']) : smarty_modifier_replace($_tmp, '.', $this->_tpl_vars['kga']['conf']['decimalSeparator'])))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

                      
<?php else: ?>  
                        &ndash;
<?php endif; ?>
                    </td>


                    <td class="knd
                      <?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] > $this->_tpl_vars['time_buffer']): ?>
                          <?php if ($this->_tpl_vars['showOverlapLines']): ?>time_overlap<?php endif; ?>
                      <?php elseif (((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d") : smarty_modifier_date_format($_tmp, "%d")) != $this->_tpl_vars['day_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_daySeperatorLines']): ?>break_day<?php endif; ?>
                      <?php elseif ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] != $this->_tpl_vars['zef_in_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_gabBreaks']): ?>break_gap<?php endif; ?>
                      <?php endif; ?>
                    ">
                        <?php echo ((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['knd_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

                    </td>


                    <td class="pct
                      <?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] > $this->_tpl_vars['time_buffer']): ?>
                          <?php if ($this->_tpl_vars['showOverlapLines']): ?>time_overlap<?php endif; ?>
                      <?php elseif (((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d") : smarty_modifier_date_format($_tmp, "%d")) != $this->_tpl_vars['day_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_daySeperatorLines']): ?>break_day<?php endif; ?>
                      <?php elseif ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] != $this->_tpl_vars['zef_in_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_gabBreaks']): ?>break_gap<?php endif; ?>
                      <?php endif; ?>
                    ">
                        
                        <a href ="#" class="preselect_lnk" 
                            onClick="buzzer_preselect('pct',<?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['pct_ID']; ?>
,'<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['pct_name'])) ? $this->_run_mod_handler('replace', true, $_tmp, "'", "\\'") : smarty_modifier_replace($_tmp, "'", "\\'")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
',<?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['pct_kndID']; ?>
,'<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['knd_name'])) ? $this->_run_mod_handler('replace', true, $_tmp, "'", "\\'") : smarty_modifier_replace($_tmp, "'", "\\'")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
'); 
                            return false;">
                            <?php echo ((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['pct_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

                            <?php if ($this->_tpl_vars['kga']['conf']['pct_comment_flag'] == 1): ?>
                                <?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['pct_comment']): ?>
                                    <span class="lighter">(<?php echo ((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['pct_comment'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
)</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </a>
                    </td>



                    <td class="evt
                      <?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] > $this->_tpl_vars['time_buffer']): ?>
                          <?php if ($this->_tpl_vars['showOverlapLines']): ?>time_overlap<?php endif; ?>
                      <?php elseif (((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d") : smarty_modifier_date_format($_tmp, "%d")) != $this->_tpl_vars['day_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_daySeperatorLines']): ?>break_day<?php endif; ?>
                      <?php elseif ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] != $this->_tpl_vars['zef_in_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_gabBreaks']): ?>break_gap<?php endif; ?>
                      <?php endif; ?>
                    ">
                        
                        <a href ="#" class="preselect_lnk" 
                            onClick="buzzer_preselect('evt',<?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_evtID']; ?>
,'<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['evt_name'])) ? $this->_run_mod_handler('replace', true, $_tmp, "'", "\\'") : smarty_modifier_replace($_tmp, "'", "\\'")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
',0,0); 
                            return false;">
                            <?php echo ((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['evt_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 
                        </a>
                        
<?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_comment']): ?>
    <?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_comment_type'] == '0'): ?>
                        <a href="#" onClick="ts_comment(<?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_ID']; ?>
); $(this).blur(); return false;"><img src='../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/blase.gif' width="12" height="13" title='<?php echo ((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_comment'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' border="0" /></a>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_comment_type'] == '1'): ?>
                        <a href="#" onClick="ts_comment(<?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_ID']; ?>
); $(this).blur(); return false;"><img src='../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/blase_sys.gif' width="12" height="13" title='<?php echo ((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_comment'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' border="0" /></a>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_comment_type'] == '2'): ?>
                        <a href="#" onClick="ts_comment(<?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_ID']; ?>
); $(this).blur(); return false;"><img src='../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/blase_caution.gif' width="12" height="13" title='<?php echo ((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_comment'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' border="0" /></a>
    <?php endif; ?>
<?php endif; ?>
                    </td>

                    <td class="trackingnumber
                      <?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] > $this->_tpl_vars['time_buffer']): ?>
                          <?php if ($this->_tpl_vars['showOverlapLines']): ?>time_overlap<?php endif; ?>
                      <?php elseif (((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d") : smarty_modifier_date_format($_tmp, "%d")) != $this->_tpl_vars['day_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_daySeperatorLines']): ?>break_day<?php endif; ?>
                      <?php elseif ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] != $this->_tpl_vars['zef_in_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_gabBreaks']): ?>break_gap<?php endif; ?>
                      <?php endif; ?>
                    ">
                    <?php echo ((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_trackingnr'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

                    </td>

                    <td class="username
                      <?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] > $this->_tpl_vars['time_buffer']): ?>
                          <?php if ($this->_tpl_vars['showOverlapLines']): ?>time_overlap<?php endif; ?>
                      <?php elseif (((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d") : smarty_modifier_date_format($_tmp, "%d")) != $this->_tpl_vars['day_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_daySeperatorLines']): ?>break_day<?php endif; ?>
                      <?php elseif ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_out'] != $this->_tpl_vars['zef_in_buffer']): ?>
                          <?php if ($this->_tpl_vars['kga']['show_gabBreaks']): ?>break_gap<?php endif; ?>
                      <?php endif; ?>
                    ">
                    <?php echo ((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['usr_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

                    </td>

                </tr>

<?php if ($this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_comment']): ?>                
                <tr id="c<?php echo $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_ID']; ?>
" class="comm<?php echo ((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_comment_type'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" <?php if ($this->_tpl_vars['hideComments']): ?>style="display:none;"<?php endif; ?>>
                    <td colspan="11"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_comment'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</td>
                </tr>
<?php endif; ?>

<?php $this->assign('day_buffer', ((is_array($_tmp=$this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d") : smarty_modifier_date_format($_tmp, "%d"))); ?>
<?php $this->assign('zef_in_buffer', $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in']); ?>
<?php $this->assign('time_buffer', $this->_tpl_vars['arr_zef'][$this->_sections['row']['index']]['zef_in']); ?>
               
<?php endfor; endif; ?>
                
            </tbody>   
        </table>
    </div>  
<?php else: ?>
<div style='padding:5px;color:#f00'>
    <strong><?php echo $this->_tpl_vars['kga']['lang']['noEntries']; ?>
</strong>
</div>
<?php endif; ?>

<script type="text/javascript"> 
    ts_usr_ann = null;
    ts_knd_ann = null;
    ts_pct_ann = null;
    ts_evt_ann = null;
    ts_total = '<?php echo $this->_tpl_vars['total']; ?>
';

    <?php if ($this->_tpl_vars['usr_ann']): ?>
    ts_usr_ann = new Array();
    <?php $_from = $this->_tpl_vars['usr_ann']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['value']):
?>
      ts_usr_ann[<?php echo $this->_tpl_vars['id']; ?>
] = '<?php echo $this->_tpl_vars['value']; ?>
';
    <?php endforeach; endif; unset($_from); ?>
    <?php endif; ?>

    <?php if ($this->_tpl_vars['knd_ann']): ?>
    ts_knd_ann = new Array();
    <?php $_from = $this->_tpl_vars['knd_ann']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['value']):
?>
      ts_knd_ann[<?php echo $this->_tpl_vars['id']; ?>
] = '<?php echo $this->_tpl_vars['value']; ?>
';
    <?php endforeach; endif; unset($_from); ?>
    <?php endif; ?>

    <?php if ($this->_tpl_vars['pct_ann']): ?>
    ts_pct_ann = new Array();
    <?php $_from = $this->_tpl_vars['pct_ann']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['value']):
?>
      ts_pct_ann[<?php echo $this->_tpl_vars['id']; ?>
] = '<?php echo $this->_tpl_vars['value']; ?>
';
    <?php endforeach; endif; unset($_from); ?>
    <?php endif; ?>

    <?php if ($this->_tpl_vars['evt_ann']): ?>
    ts_evt_ann = new Array();
    <?php $_from = $this->_tpl_vars['evt_ann']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['value']):
?>
      ts_evt_ann[<?php echo $this->_tpl_vars['id']; ?>
] = '<?php echo $this->_tpl_vars['value']; ?>
';
    <?php endforeach; endif; unset($_from); ?>
    <?php endif; ?>
    
    <?php echo '
    lists_update_annotations(parseInt($(\'#gui div.ki_timesheet\').attr(\'id\').substring(7)),ts_usr_ann,ts_knd_ann,ts_pct_ann,ts_evt_ann);
    $(\'#display_total\').html(ts_total);
    '; ?>

    
</script>