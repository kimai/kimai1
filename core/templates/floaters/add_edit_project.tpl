{literal}
<script type="text/javascript"> 
        $(document).ready(function() {
            $('#addProject').ajaxForm(function() { 

                if ($('#projectGroups').val() == null) {
                  alert("{/literal}{$kga.lang.atLeastOneGroup}{literal}");
                  return;
                }

                floaterClose();
                hook_projects_changed();
                hook_activities_changed();
            });
             $('#floater_innerwrap').tabs({ selected: 0 });
             // uniform will mess up cloning select elements, which already are "uniformed"
             // maybe the issue is the same? https://github.com/pixelmatrix/uniform/pull/138
//          	 $("select, input:checkbox, input:radio, input:file").uniform();
             var optionsToRemove = new Array();
             $('select.activities').each(function(index) {
	                 if($(this).val() != '') {
	                	 $(this).children('[value=""]').remove();
		   				 optionsToRemove.push($(this).val());
	                 }
             });
             var len = 0;
             for(var i=0, len=optionsToRemove.length; i<len; i++) {
            	 $('.activities option[value="'+optionsToRemove[i]+'"]').not(':selected').remove();
             }
             var previousValue;
             var previousText;
          	 $('.activities').live('focus', function() {
           		previousValue = this.value;
                previousText = $(this).children('[value="'+previousValue+'"]').text();
          	 }).live('change', function() {
      			if(previousValue != '') {
          			// the value we "deselected" has to be added to all other dropdowns to select it again
     	             $('.activities').each(function(index) {
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
//          	 $("#projectGroups").sexyselect({title: '{/literal}{$kga.lang.groups}{literal}',
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
$id}{$kga.lang.edit}: {$kga.lang.project}{else}{$kga.lang.new_project}{/if}</span>
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
	<li class="tab norm"><a href="#activities"> <span class="aa">&nbsp;</span> <span
		class="bb">{$kga.lang.activities}</span> <span class="cc">&nbsp;</span> </a></li>
	{if $groupIDs|@count gt 1}
	<li class="tab norm"><a href="#groups"> <span class="aa">&nbsp;</span>
	<span class="bb">{$kga.lang.groups}</span> <span class="cc">&nbsp;</span>
	</a></li>
	{/if}
	<li class="tab norm"><a href="#comment"> <span class="aa">&nbsp;</span>
	<span class="bb">{$kga.lang.comment}</span> <span class="cc">&nbsp;</span>
	</a></li>
</ul>
</div>

<form id="addProject" action="processor.php" method="post"><input
	name="project_filter" type="hidden" value="0" /> <input name="axAction"
	type="hidden" value="add_edit_CustomerProjectActivity" /> <input name="axValue"
	type="hidden" value="project" /> <input name="id" type="hidden"
	value="{$id}" />


<div id="floater_tabs" class="floater_content">

<fieldset id="general">

<ul>

	<li><label for="name">{$kga.lang.project}:</label> <input type="text"
		name="name" id="focus" value="{$name|escape:'html'}" /></li>

	<li><label for="customerID">{$kga.lang.customer}:</label> <select
		class="formfield" name="customerID">
		{html_options values=$customerIDs output=$customerNames
		selected=$selectedCustomer}
	</select></li>

	<li><label for="visible">{$kga.lang.visibility}:</label> <input
		name="visible" type="checkbox" value='1'
		{if $visible || !$id}checked="checked" {/if} /></li>

	<li><label for="internal">{$kga.lang.internalProject}:</label> <input
		type="checkbox" name="internal" value='1'
		{if $internal}checked="checked" {/if} /></li>
</ul>
</fieldset>

<fieldset id="money">

<ul>
	<li><label for="defaultRate">{$kga.lang.default_rate}:</label> <input
		type="text" name="defaultRate"
		value="{$defaultRate|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
	</li>

	<li><label for="myRate">{$kga.lang.my_rate}:</label> <input
		type="text" name="myRate"
		value="{$myRate|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
	</li>

	<li><label for="fixedRate">{$kga.lang.fixedRate}:</label> <input
		type="text" name="fixedRate"
		value="{$fixedRate|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
	</li>

	<li><label for="budget">{$kga.lang.budget}:</label> <input
		type='text' name='budget' cols='30' rows='5'
		value="{$budget|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
	</li>

	<li><label for="effort">{$kga.lang.effort}:</label> <input
		type='text' name='effort' cols='30' rows='5'
		value="{$effort|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
	</li>

	<li><label for="approved">{$kga.lang.approved}:</label> <input
		type='text' name='approved' cols='30' rows='5'
		value="{$approved|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
	</li>
</ul>
</fieldset>

<fieldset id="activities"><!--            change after upgrade to smarty 3 -->
<table class="activitiesTable">
	<tr>
		<td><label for="activities" style="text-align: left;">{$kga.lang.activities}:</label>
		</td>
		<td><label for="budget" style="text-align: left;">{$kga.lang.budget}:</label>
		</td>
		<td><label for="effort" style="text-align: left;">{$kga.lang.effort}:</label>
		</td>
		<td><label for="approved" style="text-align: left;">{$kga.lang.approved}:</label>
		</td>
	</tr>
	{if $selectedActivities != false && $selectedActivities|@count < $assignableTasks|@count} 
	{php} 
	$this->append("selectedActivities", array('activityID' => '')); 
	{/php} 
	{/if}
	{foreach from=$selectedActivities item=selectedActivity}
	<tr>
		<td>
		<ul>
			<li><select class="activities" class="formfield" name="assignedActivities[]"
				style="width: 200px">
				<option value=""></option>
				{html_options options=$assignableTasks selected=$selectedActivity.activityID}
			</select></li>
		</ul>
		</td>
		<td><input type="text" name="budget[]" value="{$selectedActivity.budget}"
			style="width: 100px"> </input></td>
		<td><input type="text" name="effort[]" value="{$selectedActivity.effort}"
			style="width: 100px"> </input></td>
		<td><input type="text" name="approved[]" value="{$selectedActivity.approved}"
			style="width: 100px"> </input></td>
	</tr>
	{/foreach}
</table>
</fieldset>

{if $groupIDs|@count gt 1}
<fieldset id="groups">
<ul>
	<li><!--                        <label for="pct_grp" >{$kga.lang.groups}:</label>-->
	<select class="formfield" id="projectGroups" name="projectGroups[]" multiple
		size='5' style="width: 255px">
		{html_options values=$groupIDs output=$groupNames
		selected=$selectedGroups}
	</select></li>
</ul>
</fieldset>
{else} <input id="groups" name="groups[]" type="hidden"
	value="{$selectedGroup.0|escape:'html'}" /> {/if}

<fieldset id="comment">
<ul>
	<li><label for="projectComment">{$kga.lang.comment}:</label> <textarea
		class='comment' name='projectComment' cols='30' rows='5'>{$projectComment|escape:'html'}</textarea>
	</li>
</ul>
</fieldset>

</div>

<div id="formbuttons"><input class='btn_norm' type='button'
	value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' /> <input
	class='btn_ok' type='submit' value='{$kga.lang.submit}' /></div>
</form>
</div>
