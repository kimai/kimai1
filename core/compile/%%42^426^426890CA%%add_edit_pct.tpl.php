<?php /* Smarty version 2.6.20, created on 2011-12-08 15:19:02
         compiled from add_edit_pct.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'add_edit_pct.tpl', 86, false),array('modifier', 'escape', 'add_edit_pct.tpl', 111, false),array('modifier', 'replace', 'add_edit_pct.tpl', 134, false),array('function', 'html_options', 'add_edit_pct.tpl', 115, false),)), $this); ?>
<?php echo '
<script type="text/javascript"> 
        $(document).ready(function() {
            $(\'#addPct\').ajaxForm(function() { 

                if ($(\'#pct_grps\').val() == null) {
                  alert("'; ?>
<?php echo $this->_tpl_vars['kga']['lang']['atLeastOneGroup']; ?>
<?php echo '");
                  return;
                }

                floaterClose();
                hook_chgPct();
                hook_chgEvt();
            });
             $(\'#floater_innerwrap\').tabs({ selected: 0 });
             // uniform will mess up cloning select elements, which already are "uniformed"
             // maybe the issue is the same? https://github.com/pixelmatrix/uniform/pull/138
//          	 $("select, input:checkbox, input:radio, input:file").uniform();
             var optionsToRemove = new Array();
             $(\'select.events\').each(function(index) {
	                 if($(this).val() != \'\') {
	                	 $(this).children(\'[value=""]\').remove();
		   				 optionsToRemove.push($(this).val());
	                 }
             });
             var len = 0;
             for(var i=0, len=optionsToRemove.length; i<len; i++) {
            	 $(\'.events option[value="\'+optionsToRemove[i]+\'"]\').not(\':selected\').remove();
             }
             var previousValue;
             var previousText;
          	 $(\'.events\').live(\'focus\', function() {
           		previousValue = this.value;
                previousText = $(this).children(\'[value="\'+previousValue+\'"]\').text();
          	 }).live(\'change\', function() {
      			if(previousValue != \'\') {
          			// the value we "deselected" has to be added to all other dropdowns to select it again
     	             $(\'.events\').each(function(index) {
         	             if($(this).children(\'[value="\'+previousValue+\'"]\').length == 0) {
      	            		$(this).append(\'<option label="\'+previousText+\'" value="\'+previousValue+\'">\'+previousText+\'</option>\');
         	             }
                   });
				} 
				// add a new one if the value is in the last field, the value is not empty and there are more options to choose from
                if($(this).val() != \'\' && $(this).closest(\'tr\').next().length <= 0 && $(this).children().length > 2) {
          			var label = $(this).val();
                  	$(this).children(\'[value=""]\').remove();
	               	var tr = $(this).closest(\'tr\');
	 				var newSelect = tr.clone();
	 				newSelect.find(\'select\').prepend(\'<option value=""></option>\');
	 				newSelect.find(\'select\').val(\'\');
	 				newSelect.find(\'option[value="\'+label+\'"]\').remove();
	 				tr.after(newSelect);
                    }
 				return true;
          	 });
//          	 $("#pct_grps").sexyselect({title: \''; ?>
<?php echo $this->_tpl_vars['kga']['lang']['groups']; ?>
<?php echo '\',
//  					allowCollapse: false,
//  					allowDelete: false,
//  					selectionMode : \'multiple\',
//  					nooptionstext : \'\',
//  					autoSort : true
//  					});
        }); 
    </script>
'; ?>


<div id="floater_innerwrap">

<div id="floater_handle"><span id="floater_title"><?php if ($this->_tpl_vars['id']): ?><?php echo $this->_tpl_vars['kga']['lang']['edit']; ?>
: <?php echo $this->_tpl_vars['kga']['lang']['pct']; ?>
<?php else: ?><?php echo $this->_tpl_vars['kga']['lang']['new_pct']; ?>
<?php endif; ?></span>
<div class="right"><a href="#" class="close" onClick="floaterClose();"><?php echo $this->_tpl_vars['kga']['lang']['close']; ?>
</a>
</div>
</div>

<div class="menuBackground">

<ul class="menu tabSelection">
	<li class="tab norm"><a href="#general"> <span class="aa">&nbsp;</span>
	<span class="bb"><?php echo $this->_tpl_vars['kga']['lang']['general']; ?>
</span> <span class="cc">&nbsp;</span>
	</a></li>
	<li class="tab norm"><a href="#money"> <span class="aa">&nbsp;</span> <span
		class="bb"><?php echo $this->_tpl_vars['kga']['lang']['budget']; ?>
</span> <span class="cc">&nbsp;</span> </a></li>
	<li class="tab norm"><a href="#evts"> <span class="aa">&nbsp;</span> <span
		class="bb"><?php echo $this->_tpl_vars['kga']['lang']['evts']; ?>
</span> <span class="cc">&nbsp;</span> </a></li>
	<?php if (count($this->_tpl_vars['sel_grp_IDs']) > 1): ?>
	<li class="tab norm"><a href="#groups"> <span class="aa">&nbsp;</span>
	<span class="bb"><?php echo $this->_tpl_vars['kga']['lang']['groups']; ?>
</span> <span class="cc">&nbsp;</span>
	</a></li>
	<?php endif; ?>
	<li class="tab norm"><a href="#comment"> <span class="aa">&nbsp;</span>
	<span class="bb"><?php echo $this->_tpl_vars['kga']['lang']['comment']; ?>
</span> <span class="cc">&nbsp;</span>
	</a></li>
</ul>
</div>

<form id="addPct" action="processor.php" method="post"><input
	name="pct_filter" type="hidden" value="0" /> <input name="axAction"
	type="hidden" value="add_edit_KndPctEvt" /> <input name="axValue"
	type="hidden" value="pct" /> <input name="id" type="hidden"
	value="<?php echo $this->_tpl_vars['id']; ?>
" />


<div id="floater_tabs" class="floater_content">

<fieldset id="general">

<ul>

	<li><label for="pct_name"><?php echo $this->_tpl_vars['kga']['lang']['pct']; ?>
:</label> <input type="text"
		name="pct_name" id="focus" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['pct_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" /></li>

	<li><label for="pct_kndID"><?php echo $this->_tpl_vars['kga']['lang']['knd']; ?>
:</label> <select
		class="formfield" name="pct_kndID">
		<?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['sel_knd_IDs'],'output' => $this->_tpl_vars['sel_knd_names'],'selected' => $this->_tpl_vars['knd_selection']), $this);?>

	</select></li>

	<li><label for="pct_visible"><?php echo $this->_tpl_vars['kga']['lang']['visibility']; ?>
:</label> <input
		name="pct_visible" type="checkbox" value='1'
		<?php if ($this->_tpl_vars['pct_visible'] || ! $this->_tpl_vars['id']): ?>checked="checked" <?php endif; ?> /></li>

	<li><label for="pct_internal"><?php echo $this->_tpl_vars['kga']['lang']['internalProject']; ?>
:</label> <input
		type="checkbox" name="pct_internal" value='1'
		<?php if ($this->_tpl_vars['pct_internal']): ?>checked="checked" <?php endif; ?> /></li>
</ul>
</fieldset>

<fieldset id="money">

<ul>
	<li><label for="pct_default_rate"><?php echo $this->_tpl_vars['kga']['lang']['default_rate']; ?>
:</label> <input
		type="text" name="pct_default_rate"
		value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['pct_default_rate'])) ? $this->_run_mod_handler('replace', true, $_tmp, '.', $this->_tpl_vars['kga']['conf']['decimalSeparator']) : smarty_modifier_replace($_tmp, '.', $this->_tpl_vars['kga']['conf']['decimalSeparator'])))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
	</li>

	<li><label for="pct_my_rate"><?php echo $this->_tpl_vars['kga']['lang']['my_rate']; ?>
:</label> <input
		type="text" name="pct_my_rate"
		value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['pct_my_rate'])) ? $this->_run_mod_handler('replace', true, $_tmp, '.', $this->_tpl_vars['kga']['conf']['decimalSeparator']) : smarty_modifier_replace($_tmp, '.', $this->_tpl_vars['kga']['conf']['decimalSeparator'])))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
	</li>

	<li><label for="pct_fixed_rate"><?php echo $this->_tpl_vars['kga']['lang']['fixed_rate']; ?>
:</label> <input
		type="text" name="pct_fixed_rate"
		value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['pct_fixed_rate'])) ? $this->_run_mod_handler('replace', true, $_tmp, '.', $this->_tpl_vars['kga']['conf']['decimalSeparator']) : smarty_modifier_replace($_tmp, '.', $this->_tpl_vars['kga']['conf']['decimalSeparator'])))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
	</li>

	<li><label for="pct_budget"><?php echo $this->_tpl_vars['kga']['lang']['budget']; ?>
:</label> <input
		type='text' name='pct_budget' cols='30' rows='5'
		value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['pct_budget'])) ? $this->_run_mod_handler('replace', true, $_tmp, '.', $this->_tpl_vars['kga']['conf']['decimalSeparator']) : smarty_modifier_replace($_tmp, '.', $this->_tpl_vars['kga']['conf']['decimalSeparator'])))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
	</li>

	<li><label for="pct_effort"><?php echo $this->_tpl_vars['kga']['lang']['effort']; ?>
:</label> <input
		type='text' name='pct_effort' cols='30' rows='5'
		value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['pct_effort'])) ? $this->_run_mod_handler('replace', true, $_tmp, '.', $this->_tpl_vars['kga']['conf']['decimalSeparator']) : smarty_modifier_replace($_tmp, '.', $this->_tpl_vars['kga']['conf']['decimalSeparator'])))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
	</li>

	<li><label for="pct_approved"><?php echo $this->_tpl_vars['kga']['lang']['approved']; ?>
:</label> <input
		type='text' name='pct_approved' cols='30' rows='5'
		value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['pct_approved'])) ? $this->_run_mod_handler('replace', true, $_tmp, '.', $this->_tpl_vars['kga']['conf']['decimalSeparator']) : smarty_modifier_replace($_tmp, '.', $this->_tpl_vars['kga']['conf']['decimalSeparator'])))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
	</li>
</ul>
</fieldset>

<fieldset id="evts"><!--            change after upgrade to smarty 3 -->
<table class="eventsTable">
	<tr>
		<td><label for="pct_evts" style="text-align: left;"><?php echo $this->_tpl_vars['kga']['lang']['evts']; ?>
:</label>
		</td>
		<td><label for="pct_budget" style="text-align: left;"><?php echo $this->_tpl_vars['kga']['lang']['budget']; ?>
:</label>
		</td>
		<td><label for="pct_effort" style="text-align: left;"><?php echo $this->_tpl_vars['kga']['lang']['effort']; ?>
:</label>
		</td>
		<td><label for="pct_approved" style="text-align: left;"><?php echo $this->_tpl_vars['kga']['lang']['approved']; ?>
:</label>
		</td>
	</tr>
	<?php if ($this->_tpl_vars['evt_selection'] != false && count($this->_tpl_vars['evt_selection']) < count($this->_tpl_vars['assignableTasks'])): ?> 
	<?php  
	$this->append("evt_selection", array('evt_ID' => '')); 
	 ?> 
	<?php endif; ?>
	<?php $_from = $this->_tpl_vars['evt_selection']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['evt_assigned']):
?>
	<tr>
		<td>
		<ul>
			<li><select class="events" class="formfield" name="pct_evt[]"
				style="width: 200px">
				<option value=""></option>
				<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['assignableTasks'],'selected' => $this->_tpl_vars['evt_assigned']['evt_ID']), $this);?>

			</select></li>
		</ul>
		</td>
		<td><input type="text" name="budget[]" value="<?php echo $this->_tpl_vars['evt_assigned']['evt_budget']; ?>
"
			style="width: 100px"> </input></td>
		<td><input type="text" name="effort[]" value="<?php echo $this->_tpl_vars['evt_assigned']['evt_effort']; ?>
"
			style="width: 100px"> </input></td>
		<td><input type="text" name="approved[]" value="<?php echo $this->_tpl_vars['evt_assigned']['evt_approved']; ?>
"
			style="width: 100px"> </input></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>
</fieldset>

<?php if (count($this->_tpl_vars['sel_grp_IDs']) > 1): ?>
<fieldset id="groups">
<ul>
	<li><!--                        <label for="pct_grp" ><?php echo $this->_tpl_vars['kga']['lang']['groups']; ?>
:</label>-->
	<select class="formfield" id="pct_grps" name="pct_grp[]" multiple
		size='5' style="width: 255px">
		<?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['sel_grp_IDs'],'output' => $this->_tpl_vars['sel_grp_names'],'selected' => $this->_tpl_vars['grp_selection']), $this);?>

	</select></li>
</ul>
</fieldset>
<?php else: ?> <input id="pct_grps" name="pct_grp[]" type="hidden"
	value="<?php echo ((is_array($_tmp=$this->_tpl_vars['grp_selection']['0'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" /> <?php endif; ?>

<fieldset id="comment">
<ul>
	<li><label for="pct_comment"><?php echo $this->_tpl_vars['kga']['lang']['comment']; ?>
:</label> <textarea
		class='comment' name='pct_comment' cols='30' rows='5'><?php echo ((is_array($_tmp=$this->_tpl_vars['pct_comment'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</textarea>
	</li>
</ul>
</fieldset>

</div>

<div id="formbuttons"><input class='btn_norm' type='button'
	value='<?php echo $this->_tpl_vars['kga']['lang']['cancel']; ?>
' onClick='floaterClose(); return false;' /> <input
	class='btn_ok' type='submit' value='<?php echo $this->_tpl_vars['kga']['lang']['submit']; ?>
' /></div>
</form>
</div>