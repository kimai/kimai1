{literal}
<script type="text/javascript"> 
        $(document).ready(function() {
            $('#addPct').ajaxForm(function() { 

                if ($('#pct_grps').val() == null) {
                  alert("{/literal}{$kga.lang.atLeastOneGroup}{literal}");
                  return;
                }

                floaterClose();
                hook_chgPct();
                hook_chgEvt();
            });
             $('#floater_innerwrap').tabs({ selected: 0 });
             // uniform will mess up cloning select elements, which already are "uniformed"
             // maybe the issue is the same? https://github.com/pixelmatrix/uniform/pull/138
//          	 $("select, input:checkbox, input:radio, input:file").uniform();
             var optionsToRemove = new Array();
             $('select.events').each(function(index) {
	                 if($(this).val() != '') {
	                	 $(this).children('[value=""]').remove();
		   				 optionsToRemove.push($(this).val());
	                 }
             });
             var len = 0;
             for(var i=0, len=optionsToRemove.length; i<len; i++) {
            	 $('.events option[value="'+optionsToRemove[i]+'"]').not(':selected').remove();
             }
             var previousValue;
             var previousText;
          	 $('.events').live('focus', function() {
           		previousValue = this.value;
                previousText = $(this).children('[value="'+previousValue+'"]').text();
          	 }).live('change', function() {
      			if(previousValue != '') {
          			// the value we "deselected" has to be added to all other dropdowns to select it again
     	             $('.events').each(function(index) {
         	             if($(this).children('[value="'+previousValue+'"]').length == 0) {
      	            		$(this).append('<option label="'+previousText+'" value="'+previousValue+'">'+previousText+'</option>');
         	             }
                   });
				} 
				// add a new one if the value is in the last field, the value is not empty and there are more options to choose from
                if($(this).val() != '' && $(this).closest('tr').next().length <= 0 && $(this).children().length > 2) {
          			var label = $(this).val();
                  	$(this).children('[value=""]').remove();
	               	var tr = $(this).closest('tr');
	 				var newSelect = tr.clone();
	 				newSelect.find('select').prepend('<option value=""></option>');
	 				newSelect.find('select').val('');
	 				newSelect.find('option[value="'+label+'"]').remove();
	 				tr.after(newSelect);
                    }
 				return true;
          	 });
//          	 $("#pct_grps").sexyselect({title: '{/literal}{$kga.lang.groups}{literal}',
//  					allowCollapse: false,
//  					allowDelete: false,
//  					selectionMode : 'multiple',
//  					nooptionstext : '',
//  					autoSort : true
//  					});
        }); 
    </script>
{/literal}

<div id="floater_innerwrap">

<div id="floater_handle"><span id="floater_title">{if
$id}{$kga.lang.edit}: {$kga.lang.pct}{else}{$kga.lang.new_pct}{/if}</span>
<div class="right"><a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
</div>
</div>

<div class="menuBackground">

<ul class="menu tabSelection">
	<li class="tab norm"><a href="#general"> <span class="aa">&nbsp;</span>
	<span class="bb">{$kga.lang.general}</span> <span class="cc">&nbsp;</span>
	</a></li>
	<li class="tab norm"><a href="#money"> <span class="aa">&nbsp;</span> <span
		class="bb">{$kga.lang.budget}</span> <span class="cc">&nbsp;</span> </a></li>
	<li class="tab norm"><a href="#evts"> <span class="aa">&nbsp;</span> <span
		class="bb">{$kga.lang.evts}</span> <span class="cc">&nbsp;</span> </a></li>
	{if $sel_grp_IDs|@count gt 1}
	<li class="tab norm"><a href="#groups"> <span class="aa">&nbsp;</span>
	<span class="bb">{$kga.lang.groups}</span> <span class="cc">&nbsp;</span>
	</a></li>
	{/if}
	<li class="tab norm"><a href="#comment"> <span class="aa">&nbsp;</span>
	<span class="bb">{$kga.lang.comment}</span> <span class="cc">&nbsp;</span>
	</a></li>
</ul>
</div>

<form id="addPct" action="processor.php" method="post"><input
	name="pct_filter" type="hidden" value="0" /> <input name="axAction"
	type="hidden" value="add_edit_KndPctEvt" /> <input name="axValue"
	type="hidden" value="pct" /> <input name="id" type="hidden"
	value="{$id}" />


<div id="floater_tabs" class="floater_content">

<fieldset id="general">

<ul>

	<li><label for="pct_name">{$kga.lang.pct}:</label> <input type="text"
		name="pct_name" class="focussed" value="{$pct_name|escape:'html'}" /></li>

	<li><label for="pct_kndID">{$kga.lang.knd}:</label> <select
		class="formfield" name="pct_kndID">
		{html_options values=$sel_knd_IDs output=$sel_knd_names
		selected=$knd_selection}
	</select></li>

	<li><label for="pct_visible">{$kga.lang.visibility}:</label> <input
		name="pct_visible" type="checkbox" value='1'
		{if $pct_visible || !$id}checked="checked" {/if} /></li>

	<li><label for="pct_internal">{$kga.lang.internalProject}:</label> <input
		type="checkbox" name="pct_internal" value='1'
		{if $pct_internal}checked="checked" {/if} /></li>
</ul>
</fieldset>

<fieldset id="money">

<ul>
	<li><label for="pct_default_rate">{$kga.lang.default_rate}:</label> <input
		type="text" name="pct_default_rate"
		value="{$pct_default_rate|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
	</li>

	<li><label for="pct_my_rate">{$kga.lang.my_rate}:</label> <input
		type="text" name="pct_my_rate"
		value="{$pct_my_rate|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
	</li>

	<li><label for="pct_fixed_rate">{$kga.lang.fixed_rate}:</label> <input
		type="text" name="pct_fixed_rate"
		value="{$pct_fixed_rate|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
	</li>

	<li><label for="pct_budget">{$kga.lang.budget}:</label> <input
		type='text' name='pct_budget' cols='30' rows='5'
		value="{$pct_budget|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
	</li>

	<li><label for="pct_effort">{$kga.lang.effort}:</label> <input
		type='text' name='pct_effort' cols='30' rows='5'
		value="{$pct_effort|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
	</li>

	<li><label for="pct_approved">{$kga.lang.approved}:</label> <input
		type='text' name='pct_approved' cols='30' rows='5'
		value="{$pct_approved|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
	</li>
</ul>
</fieldset>

<fieldset id="evts"><!--            change after upgrade to smarty 3 -->
<table class="eventsTable">
	<tr>
		<td><label for="pct_evts" style="text-align: left;">{$kga.lang.evts}:</label>
		</td>
		<td><label for="pct_budget" style="text-align: left;">{$kga.lang.budget}:</label>
		</td>
		<td><label for="pct_effort" style="text-align: left;">{$kga.lang.effort}:</label>
		</td>
		<td><label for="pct_approved" style="text-align: left;">{$kga.lang.approved}:</label>
		</td>
	</tr>
	{if $evt_selection != false && $evt_selection|@count < $assignableTasks|@count} 
	{php} 
	$this->append("evt_selection", array('evt_ID' => '')); 
	{/php} 
	{/if}
	{foreach from=$evt_selection item=evt_assigned}
	<tr>
		<td>
		<ul>
			<li><select class="events" class="formfield" name="pct_evt[]"
				style="width: 200px">
				<option value=""></option>
				{html_options options=$assignableTasks selected=$evt_assigned.evt_ID}
			</select></li>
		</ul>
		</td>
		<td><input type="text" name="budget[]" value="{$evt_assigned.evt_budget}"
			style="width: 100px"> </input></td>
		<td><input type="text" name="effort[]" value="{$evt_assigned.evt_effort}"
			style="width: 100px"> </input></td>
		<td><input type="text" name="approved[]" value="{$evt_assigned.evt_approved}"
			style="width: 100px"> </input></td>
	</tr>
	{/foreach}
</table>
</fieldset>

{if $sel_grp_IDs|@count gt 1}
<fieldset id="groups">
<ul>
	<li><!--                        <label for="pct_grp" >{$kga.lang.groups}:</label>-->
	<select class="formfield" id="pct_grps" name="pct_grp[]" multiple
		size='5' style="width: 255px">
		{html_options values=$sel_grp_IDs output=$sel_grp_names
		selected=$grp_selection}
	</select></li>
</ul>
</fieldset>
{else} <input id="pct_grps" name="pct_grp[]" type="hidden"
	value="{$grp_selection.0|escape:'html'}" /> {/if}

<fieldset id="comment">
<ul>
	<li><label for="pct_comment">{$kga.lang.comment}:</label> <textarea
		class='comment' name='pct_comment' cols='30' rows='5'>{$pct_comment|escape:'html'}</textarea>
	</li>
</ul>
</fieldset>

</div>

<div id="formbuttons"><input class='btn_norm' type='button'
	value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' /> <input
	class='btn_ok' type='submit' value='{$kga.lang.submit}' /></div>
</form>
</div>
