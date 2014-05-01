<script type="text/javascript"> 
$(document).ready(function() {

  $('#add_edit_activity').ajaxForm({
    'beforeSubmit': function() {
      clearFloaterErrorMessages();

      if ($('#add_edit_activity').attr('submitting')) {
        return false;
      }
      else {
        $('#add_edit_activity').attr('submitting', true);
        return true;
      }
    },
    'success': function(result) {
      $('#add_edit_activity').removeAttr('submitting');

      for (var fieldName in result.errors)
        setFloaterErrorMessage(fieldName,result.errors[fieldName]);

      if (result.errors.length == 0) {
         floaterClose();
         hook_activities_changed();
      }
     },
      'error' : function() {
        $('#add_edit_activity').removeAttr('submitting');
      }});

     $('#floater_innerwrap').tabs({ selected: 0 });
 }); 
</script>

<?php
    $title = isset($this->id) ? $this->kga['lang']['edit'].': '.$this->kga['lang']['activity'] : $this->kga['lang']['new_activity'];

    $this->floater()
        ->setTitle($title)
        ->setFormAction('processor.php')
        ->setFormId('add_edit_activity')
        ->addTab('general', $this->translate('general'))
        ->addTab('projectstab', $this->translate('projects'));

    if (count($this->groups) > 1) {
        $this->floater()->addTab('groups', $this->translate('groups'));
    }

    $this->floater()->addTab('commenttab', $this->translate('comment'));

    echo $this->floater()->floaterBegin();
?>

    <input name="activityFilter"   type="hidden" value="0" />
    <input name="axAction" type="hidden" value="add_edit_CustomerProjectActivity" />
    <input name="axValue" type="hidden" value="activity" />
    <input name="id" type="hidden" value="<?php echo $this->id; ?>" />

    <?php echo $this->floater()->tabContentBegin('general'); ?>
        <ul>
            <li>
                <label for="name" ><?php echo $this->kga['lang']['activity']?>:</label>
                <?php echo $this->formText('name', $this->name);?>
            </li>
            <li>
                <label for="defaultRate" ><?php echo $this->kga['lang']['default_rate']?>:</label>
                <?php echo $this->formText('defaultRate', str_replace('.',$this->kga['conf']['decimalSeparator'],$this->defaultRate)); ?>
            </li>
            <li>
                <label for="myRate" ><?php echo $this->kga['lang']['my_rate']?>:</label>
                <?php echo $this->formText('myRate', str_replace('.',$this->kga['conf']['decimalSeparator'],$this->myRate)); ?>
            </li>
            <li>
                <label for="fixedRate" ><?php echo $this->kga['lang']['fixedRate']?>:</label>
                <?php echo $this->formText('fixedRate', str_replace('.',$this->kga['conf']['decimalSeparator'],$this->fixedRate)); ?>
            </li>
            <li>
                 <label for="visible"><?php echo $this->kga['lang']['visibility']?>:</label>
                <?php echo $this->formCheckbox('visible', '1',array('checked' => $this->visible || !$this->id));?>
            </li>
        </ul>
    <?php echo $this->floater()->tabContentEnd(); ?>

    <?php echo $this->floater()->tabContentBegin('commenttab'); ?>
        <ul>
            <li>
                 <label for="comment"><?php echo $this->kga['lang']['comment']?>:</label>
                 <?php echo $this->formTextarea('comment', $this->comment,array(
                    'cols' => 30,
                    'rows' => 5,
                    'class' => 'comment'
                    ));?>
            </li>
        </ul>
    <?php echo $this->floater()->tabContentEnd(); ?>

    <?php
    if (count($this->groups) > 1)
    {
        echo $this->floater()->tabContentBegin('groups');
        ?>
        <ul>
            <li>
                <label for="activityGroups"><?php echo $this->kga['lang']['groups']?>:</label>
                <?php echo $this->formSelect('activityGroups[]', $this->selectedGroups, array(
                    'class' => 'formfield',
                    'id' => 'activityGroups',
                    'multiple' => 'multiple',
                    'size' => 3,
                    'style' => 'width:255px'), $this->groups); ?>
            </li>
        </ul>
        <?php
        echo $this->floater()->tabContentEnd();
    } else {
        echo $this->formHidden('activityGroups[]', $this->selectedGroups[0], array('id' => 'activityGroups'));
    }
    ?>

    <?php echo $this->floater()->tabContentBegin('projectstab'); ?>
        <ul>
            <li>
                <label for="activityProjects"><?php echo $this->kga['lang']['projects']?>:</label>
                <?php echo $this->formSelect('projects[]', $this->selectedProjects, array(
                    'class' => 'formfield',
                    'id' => 'activityProjects',
                    'multiple' => 'multiple',
                    'size' => 5,
                    'style' => 'width:255px'), $this->projects); ?>
            </li>
        </ul>
    <?php echo $this->floater()->tabContentEnd(); ?>

<?php echo $this->floater()->floaterEnd(); ?>