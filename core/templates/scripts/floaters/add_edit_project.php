<script type="text/javascript"> 
        $(document).ready(function() {
            $('#addProject').ajaxForm(function() { 

                if ($('#projectGroups').val() == null) {
                  alert("<?php echo $this->kga['lang']['atLeastOneGroup']?>");
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
        }); 
    </script>

<div id="floater_innerwrap">

<div id="floater_handle"><span id="floater_title">
<?php if (isset($id)) echo $this->kga['lang']['edit'], ':', $this->kga['lang']['project']; else echo $this->kga['lang']['new_project']; ?></span>
<div class="right"><a href="#" class="close" onClick="floaterClose();"><?php echo $this->kga['lang']['close']?></a>
</div>
</div>

<div class="menuBackground">

<ul class="menu tabSelection">
	<li class="tab norm"><a href="#general"> <span class="aa">&nbsp;</span>
	<span class="bb"><?php echo $this->kga['lang']['general']?></span> <span class="cc">&nbsp;</span>
	</a></li>
	<li class="tab norm"><a href="#money"> <span class="aa">&nbsp;</span> <span
		class="bb"><?php echo $this->kga['lang']['budget']?></span> <span class="cc">&nbsp;</span> </a></li>
	<li class="tab norm"><a href="#activitiestab"> <span class="aa">&nbsp;</span> <span
		class="bb"><?php echo $this->kga['lang']['activities']?></span> <span class="cc">&nbsp;</span> </a></li>
	<?php if (count($this->groupIDs) > 1): ?>
	<li class="tab norm"><a href="#groups"> <span class="aa">&nbsp;</span>
	<span class="bb"><?php echo $this->kga['lang']['groups']?></span> <span class="cc">&nbsp;</span>
	</a></li>
	<?php endif; ?>
	<li class="tab norm"><a href="#comment"> <span class="aa">&nbsp;</span>
	<span class="bb"><?php echo $this->kga['lang']['comment']?></span> <span class="cc">&nbsp;</span>
	</a></li>
</ul>
</div>

<form id="addProject" action="processor.php" method="post">
  <input name="projectFilter" type="hidden" value="0" />
  <input name="axAction" type="hidden" value="add_edit_CustomerProjectActivity" />
  <input name="axValue" type="hidden" value="project" />
  <input name="id" type="hidden" value="<?php echo $this->id?>" />


<div id="floater_tabs" class="floater_content">

<fieldset id="general">

<ul>

	<li><label for="name"><?php echo $this->kga['lang']['project']?>:</label>
            <?php echo $this->formText('name', $this->name);?> </li>

	<li><label for="customerID"><?php echo $this->kga['lang']['customer']?>:</label> 
		<?php echo $this->formSelect('customerID', $this->selectedCustomer, array('class' => 'formfield'), $this->customers); ?>
        </li>

	<li><label for="visible"><?php echo $this->kga['lang']['visibility']?>:</label>
            <?php echo $this->formCheckbox('visible', '1',array('checked' => $this->visible || !$this->id));?>
	</li>

	<li><label for="internal"><?php echo $this->kga['lang']['internalProject']?>:</label>
            <?php echo $this->formCheckbox('internal', '1',array('checked' => $this->internal));?>
	</li>
</ul>
</fieldset>

<fieldset id="money">

<ul>
	<li><label for="defaultRate"><?php echo $this->kga['lang']['default_rate']?>:</label>
            <?php echo $this->formText('defaultRate', str_replace('.',$this->kga['conf']['decimalSeparator'],$this->defaultRate)); ?>
	</li>

	<li><label for="myRate"><?php echo $this->kga['lang']['my_rate']?>:</label>
            <?php echo $this->formText('myRate', str_replace('.',$this->kga['conf']['decimalSeparator'],$this->myRate)); ?>
	</li>

	<li><label for="fixedRate"><?php echo $this->kga['lang']['fixedRate']?>:</label>
            <?php echo $this->formText('fixedRate', str_replace('.',$this->kga['conf']['decimalSeparator'],$this->fixedRate)); ?>
	</li>

	<li><label for="budget"><?php echo $this->kga['lang']['budget']?>:</label>
            <?php echo $this->formText('budget', str_replace('.',$this->kga['conf']['decimalSeparator'],$this->budget)); ?>
	</li>

	<li><label for="effort"><?php echo $this->kga['lang']['effort']?>:</label>
            <?php echo $this->formText('effort', str_replace('.',$this->kga['conf']['decimalSeparator'],$this->effort)); ?>
	</li>

	<li><label for="approved"><?php echo $this->kga['lang']['approved']?>:</label>
            <?php echo $this->formText('approved', str_replace('.',$this->kga['conf']['decimalSeparator'],$this->approved)); ?>
	</li>
</ul>
</fieldset>

<fieldset id="activitiestab">
    <?php
    if (!isset($this->selectedActivities) || $this->selectedActivities == false) {
        echo '<div class="error">'.$this->kga['lang']['no_project_activities'].'</div>';
    }
    else
    {
        ?>

        <table class="activitiesTable">
            <tr>
                <td><label for="assignedActivities" style="text-align: left;"><?php echo $this->kga['lang']['activities']?>:</label>
                </td>
                <td><label for="budget" style="text-align: left;"><?php echo $this->kga['lang']['budget']?>:</label>
                </td>
                <td><label for="effort" style="text-align: left;"><?php echo $this->kga['lang']['effort']?>:</label>
                </td>
                <td><label for="approved" style="text-align: left;"><?php echo $this->kga['lang']['approved']?>:</label>
                </td>
            </tr>
            <?php
            if (count($this->selectedActivities) < count($this->assignableTasks)) {
                $this->selectedActivities[] = array('activityID' => '');
            }

            foreach ($this->selectedActivities as $selectedActivity): ?>
            <tr>
                <td>
                <ul>
                    <li>
                      <?php echo $this->formSelect('assignedActivities[]', $selectedActivity['activityID'], array('class'=>'activities formfield'), $this->assignableTasks); ?>
                    </li>
                </ul>
                </td>
                <td>
                    <?php echo $this->formText('budget[]', $selectedActivity['budget'], array('style' => 'width: 100px'));?>
                </td>
                <td>
                    <?php echo $this->formText('effort[]', $selectedActivity['effort'], array('style' => 'width: 100px'));?>
                </td>
                <td>
                    <?php echo $this->formText('approved[]', $selectedActivity['approved'], array('style' => 'width: 100px'));?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }
?>
</fieldset>

<?php if (count($this->groupIDs) > 1): ?>
<fieldset id="groups">
    <ul>
        <li>
           <?php echo $this->formSelect('projectGroups[]', $this->selectedGroups, array(
                'class' => 'formfield',
                'id' => 'projectGroups',
                'multiple' => 'multiple',
                'size' => 3,
                'style' => 'width:255px'), $this->groups); ?>
        </li>
    </ul>
</fieldset>
<?php else: 
 echo $this->formHidden('projectGroups[]', $this->selectedGroups[0], array('id' => 'projectGroups'));
endif; ?>

<fieldset id="comment">
<ul>
	<li><label for="projectComment"><?php echo $this->kga['lang']['comment']?>:</label>
            <?php echo $this->formTextarea('projectComment', $this->projectComment,array(
		'cols' => 30,
		'rows' => 5,
		'class' => 'comment'
		));?>
	</li>
</ul>
</fieldset>

</div>

<div id="formbuttons"><input class='btn_norm' type='button'
	value='<?php echo $this->kga['lang']['cancel']?>' onClick='floaterClose(); return false;' /> <input
	class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['submit']?>' /></div>
</form>
</div>
