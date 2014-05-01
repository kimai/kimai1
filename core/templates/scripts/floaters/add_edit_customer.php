<script type="text/javascript">
    $(document).ready(function() {

        $('.disableInput').click(function(){
          var input = $(this);
          if (input.is (':checked'))
            input.siblings().prop("disabled", true);
          else
            input.siblings().prop("disabled", false);
        });

        $('#add_edit_customer').ajaxForm({
          'beforeSubmit': function() {
            clearFloaterErrorMessages();

            if ($('#add_edit_customer').attr('submitting')) {
              return false;
            }
            else {
              $('#add_edit_customer').attr('submitting', true);
              return true;
            }
          },
          'success': function(result) {
            $('#add_edit_customer').removeAttr('submitting');

            for (var fieldName in result.errors)
              setFloaterErrorMessage(fieldName,result.errors[fieldName]);


            if (result.errors.length == 0) {
              floaterClose();
              hook_customers_changed();
            }
          },
          'error' : function() {
              $('#add_edit_customer').removeAttr('submitting');
          }});

         $('#floater_innerwrap').tabs({ selected: 0 });
    });
</script>


<?php
    $title = (isset($this->id)) ? $this->kga['lang']['edit'].': '.$this->kga['lang']['customer'] : $this->kga['lang']['new_customer'];

    $this->floater()
        ->setTitle($title)
        ->setFormAction('processor.php')
        ->setFormId('add_edit_customer')
        ->addTab('general', $this->translate('general'))
        ->addTab('address', $this->translate('address'))
        ->addTab('contact', $this->translate('contact'));

    if (count($this->groups) > 1) {
        $this->floater()->addTab('groups', $this->translate('groups'));
    }

    $this->floater()->addTab('commenttab', $this->translate('comment'));

    echo $this->floater()->floaterBegin();
?>

    <input name="customerFilter"   type="hidden" value="0" />
    <input name="axAction"     type="hidden" value="add_edit_CustomerProjectActivity" />
    <input name="axValue"      type="hidden" value="customer" />
    <input name="id"           type="hidden" value="<?php echo $this->id?>" />

    <?php echo $this->floater()->tabContentBegin('general'); ?>
        <ul>
            <li>
                <label for="name" ><?php echo $this->kga['lang']['customer']?>:</label>
                <?php echo $this->formText('name', $this->name);?>
            </li>
            <li>
                <label for="vat" ><?php echo $this->kga['lang']['vat']?>:</label>
                <?php echo $this->formText('vat', $this->vat);?>
            </li>
            <li>
                 <label for="visible"><?php echo $this->kga['lang']['visibility']?>:</label>
                 <?php echo $this->formCheckbox('visible', '1',array('checked' => $this->visible || !$this->id));?>
            </li>
            <li>
              <label for="password"><?php echo $this->kga['lang']['password']?>:</label>
              <div class="multiFields">
                <?php echo $this->formPassword('password', '', array(
                            'cols' => 30,
                            'rows' => 3,
                            'disabled' => (!$this->password)?'disabled':''
                    ));?><br/>
                <?php echo $this->formCheckbox('no_password', '1',array('class' => 'disableInput', 'checked' => !$this->password)); echo $this->kga['lang']['nopassword']?>
              </div>
            </li>
            <li>
                <label for="timezone"><?php echo $this->kga['lang']['timezone']?>:</label>
                <?php echo $this->timeZoneSelect('timezone', $this->timezone); ?>
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
                    <label for="customerGroups" ><?php echo $this->kga['lang']['groups']?>:</label>
                    <?php echo $this->formSelect('customerGroups[]', $this->selectedGroups, array(
                        'class' => 'formfield',
                        'id' => 'customerGroups',
                        'multiple' => 'multiple',
                        'size' => 3,
                        'style' => 'width:255px'), $this->groups); ?>
                </li>
            </ul>
        <?php
        echo $this->floater()->tabContentEnd();
    }
    else
    {
        echo $this->formHidden('customerGroups[]', $this->selectedGroups[0], null ,array('id' => 'customerGroups'));
    }

    ?>

    <?php echo $this->floater()->tabContentBegin('address'); ?>
        <ul>
            <li>
                <label for="company" ><?php echo $this->kga['lang']['company']?>:</label>
                <?php echo $this->formText('company', $this->company);?>
            </li>
            <li>
                <label for="contactPerson" ><?php echo $this->kga['lang']['contactPerson']?>:</label>
                <?php echo $this->formText('contactPerson', $this->contact);?>
            </li>
            <li>
                <label for="street" ><?php echo $this->kga['lang']['street']?>:</label>
                <?php echo $this->formText('street', $this->street);?>
            </li>
            <li>
                <label for="zipcode" ><?php echo $this->kga['lang']['zipcode']?>:</label>
                <?php echo $this->formText('zipcode', $this->zipcode);?>
            </li>
            <li>
                <label for="city" ><?php echo $this->kga['lang']['city']?>:</label>
                <?php echo $this->formText('city', $this->city);?>
            </li>
        </ul>
    <?php echo $this->floater()->tabContentEnd(); ?>

    <?php echo $this->floater()->tabContentBegin('contact'); ?>
        <ul>
            <li>
                <label for="phone" ><?php echo $this->kga['lang']['telephon']?>:</label>
                <?php echo $this->formText('phone', $this->phone);?>
            </li>
            <li>
                <label for="fax" ><?php echo $this->kga['lang']['fax']?>:</label>
                <?php echo $this->formText('fax', $this->fax);?>
            </li>
            <li>
                <label for="mobile" ><?php echo $this->kga['lang']['mobilephone']?>:</label>
                <?php echo $this->formText('mobile', $this->mobile);?>
            </li>
            <li>
                <label for="mail" ><?php echo $this->kga['lang']['mail']?>:</label>
                <?php echo $this->formText('mail', $this->mail);?>
            </li>
            <li>
                <label for="homepage" ><?php echo $this->kga['lang']['homepage']?>:</label>
                <?php echo $this->formText('homepage', $this->homepage);?>
            </li>
        </ul>
    <?php echo $this->floater()->tabContentEnd(); ?>

<?php echo $this->floater()->floaterEnd(); ?>