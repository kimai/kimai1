<script type="text/javascript">
    $(document).ready(function() {
        $('#addProject').ajaxForm({
          'beforeSubmit': function() {
            clearFloaterErrorMessages();

            if ($('#addProject').attr('submitting')) {
              return false;
            }
            else {
              $('#addProject').attr('submitting', true);
              return true;
            }
          },
          'success': function(result) {
            $('#addProject').removeAttr('submitting');

            for (var fieldName in result.errors)
              setFloaterErrorMessage(fieldName,result.errors[fieldName]);

            if (result.errors.length == 0) {
              floaterClose();
              hook_projects_changed();
              hook_activities_changed();
            }
        },
        'error' : function() {
            $('#addProject').removeAttr('submitting');
        }});


        function deleteButtonClicked() {
          var row = $(this).parent().parent()[0];
          var id = $('#assignedActivities', row).val();
          var text = $('td', row).text().trim();
          $('#newActivity').append('<option label = "' + text + '" value = "' + id + '">' + text + '</option>');
          $(row).remove();

          if ($('#newActivity option').length > 1)
            $('#activitiestab .addRow').show();
        }

        $('#activitiestab .deleteButton').click(deleteButtonClicked);

        $('#newActivity').change(function() {
          if ($(this).val() == -1) return;

          var row = $('<tr>' +
            '<td>' + $('option:selected', this).text() + '<input type="hidden" name="assignedActivities[]" value="' + $(this).val() + '"/></td>' +
            '<td><input type="text" name="budget[]"/></td>' +
            '<td><input type="text" name="effort[]"/></td>' +
            '<td><input type="text" name="approved[]"/></td>' +
            '<td> <a class="deleteButton">' +
            '<img src="../skins/' + skin + '/grfx/close.png" width="22" height="16" />' +
            '</a> </td>' +
            '</tr>');
          $('#activitiestab .activitiesTable tr.addRow').before(row);
          $('.deleteButton', row).click(deleteButtonClicked);

          $('option:selected', this).remove();

          $(this).val(-1);

          if ($('option', this).length <= 1)
            $('#activitiestab .addRow').hide();
        });

         $('#floater_innerwrap').tabs({ selected: 0 });

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
         $('.activities').on('focus', function() {
            previousValue = this.value;
            previousText = $(this).children('[value="'+previousValue+'"]').text();
         }).on('change', function() {
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

<?php
    $title = (isset($this->id)) ? $this->kga['lang']['edit'].': '.$this->kga['lang']['project'] : $this->kga['lang']['new_project'];

    $this->floater()
        ->setTitle($title)
        ->setFormAction('processor.php')
        ->setFormId('addProject')
        ->addTab('general', $this->translate('general'))
        ->addTab('money', $this->translate('budget'))
        ->addTab('activitiestab', $this->translate('activities'));

    if (count($this->groups) > 1) {
        $this->floater()->addTab('groups', $this->translate('groups'));
    }

    $this->floater()->addTab('comment', $this->translate('comment'));

    echo $this->floater()->floaterBegin();
?>

    <input name="projectFilter" type="hidden" value="0" />
    <input name="axAction" type="hidden" value="add_edit_CustomerProjectActivity" />
    <input name="axValue" type="hidden" value="project" />
    <input name="id" type="hidden" value="<?php echo $this->id?>" />

    <?php echo $this->floater()->tabContentBegin('general'); ?>
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
    <?php echo $this->floater()->tabContentEnd(); ?>

    <?php echo $this->floater()->tabContentBegin('money'); ?>
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

            <li><label for="project_budget"><?php echo $this->kga['lang']['budget']?>:</label>
                    <?php echo $this->formText('project_budget', str_replace('.',$this->kga['conf']['decimalSeparator'],$this->budget)); ?>
            </li>

            <li><label for="project_effort"><?php echo $this->kga['lang']['effort']?>:</label>
                    <?php echo $this->formText('project_effort', str_replace('.',$this->kga['conf']['decimalSeparator'],$this->effort)); ?>
            </li>

            <li><label for="project_approved"><?php echo $this->kga['lang']['approved']?>:</label>
                    <?php echo $this->formText('project_approved', str_replace('.',$this->kga['conf']['decimalSeparator'],$this->approved)); ?>
            </li>
        </ul>
    <?php echo $this->floater()->tabContentEnd(); ?>

    <?php echo $this->floater()->tabContentBegin('activitiestab'); ?>
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
            $assignedActivities = array();
            if (isset($this->selectedActivities) && is_array($this->selectedActivities))
            {
                foreach ($this->selectedActivities as $selectedActivity) {
                $assignedActivities[] = $selectedActivity['activityID'];
                ?>
                <tr>
                    <td>
                        <?php echo $this->escape($selectedActivity['name']), $this->formHidden('assignedActivities[]', $selectedActivity['activityID']); ?>
                    </td>
                    <td>
                        <?php echo $this->formText('budget[]', $selectedActivity['budget']);?>
                    </td>
                    <td>
                        <?php echo $this->formText('effort[]', $selectedActivity['effort']);?>
                    </td>
                    <td>
                        <?php echo $this->formText('approved[]', $selectedActivity['approved']);?>
                    </td>
                    <td>
                      <a class="deleteButton">
                        <img src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/close.png" width="22" height="16" />
                      </a>
                    </td>
                </tr>
                <?php
                }
            }

            $selectArray = array(-1 => '');
            foreach ($this->allActivities as $activity) {
              if (array_search($activity['activityID'], $assignedActivities) === false)
                $selectArray[$activity['activityID']] = $activity['name'];
            }
            ?>
            <tr class="addRow" <?php if (count($selectArray) <= 1):?> style="display:none" <?php endif; ?> >
              <td> <?php
               echo $this->formSelect('newActivity',null,null,$selectArray); ?> </td>
            </tr>
        </table>
    <?php echo $this->floater()->tabContentEnd(); ?>

    <?php
    if (count($this->groups) > 1)
    {
        echo $this->floater()->tabContentBegin('groups');
        ?>
            <ul>
                <li>
                    <label for="projectGroups"><?php echo $this->kga['lang']['groups']?>:</label>
                    <?php echo $this->formSelect('projectGroups[]', $this->selectedGroups, array(
                        'class' => 'formfield',
                        'id' => 'projectGroups',
                        'multiple' => 'multiple',
                        'size' => 3,
                        'style' => 'width:255px'), $this->groups); ?>
                </li>
            </ul>
        <?php
        echo $this->floater()->tabContentEnd();
    } else {
        echo $this->formHidden('projectGroups[]', $this->selectedGroups[0], array('id' => 'projectGroups'));
    }
    ?>

    <?php echo $this->floater()->tabContentBegin('comment'); ?>
        <ul>
            <li><label for="projectComment"><?php echo $this->kga['lang']['comment']?>:</label>
                    <?php
                    echo $this->formTextarea('projectComment', $this->comment,
                        array('cols' => 30, 'rows' => 5, 'class' => 'comment')
                    ); ?>
            </li>
        </ul>
    <?php echo $this->floater()->tabContentEnd(); ?>

<?php echo $this->floater()->floaterEnd(); ?>