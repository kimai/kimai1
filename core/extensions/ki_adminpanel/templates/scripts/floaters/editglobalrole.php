<script type="text/javascript">
    $(document).ready(function () {
        $('#adminPanel_extension_form_editRole').ajaxForm({
            'beforeSubmit': function () {
                clearFloaterErrorMessages();

                if ($('#adminPanel_extension_form_editRole').attr('submitting')) {
                    return false;
                }
                else {
                    $('#adminPanel_extension_form_editRole').attr('submitting', true);
                    return true;
                }
            },
            'success': function (result) {
                $('#adminPanel_extension_form_editRole').removeAttr('submitting');
                for (var fieldName in result.errors)
                    setFloaterErrorMessage(fieldName, result.errors[fieldName]);

                if (result.errors.length == 0) {
                    floaterClose();
                    adminPanel_extension_refreshSubtab('<?php echo $this->jsEscape($this->reloadSubtab); ?>');
                }
            },
            'error': function () {
                $('#adminPanel_extension_form_editRole').removeAttr('submitting');
            }});
        $('#floater_innerwrap').tabs({ selected: 0 });
    });
</script>
<?php

$extensions = array();
$keyHierarchy = array();
$this->hierarchy()->parse($this->permissions, $extensions, $keyHierarchy);

$this->floater()
    ->setTitle($this->title)
    ->setFormAction('../extensions/ki_adminpanel/processor.php')
    ->setFormId('adminPanel_extension_form_editRole')
    ->addTab('general', $this->translate('general'));

foreach ($keyHierarchy as $key => $subKeys)
{
    if (count($subKeys) == 1 && array_key_exists('access', $subKeys)) continue;

    $name = $key;
    if (isset($this->kga['lang']['extensions'][$name])) {
        $name = $this->kga['lang']['extensions'][$name];
    }
    $this->floater()->addTab($key, $name);
}

echo $this->floater()->floaterBegin();

?>

<input name="id" type="hidden" value="<?php echo $this->id ?>"/>
<input name="axAction" type="hidden" value="<?php echo $this->action; ?>"/>

<?php echo $this->floater()->tabContentBegin('general'); ?>
        <ul>
            <li>
                <label for="name"><?php echo $this->kga['lang']['rolename'] ?>:</label>
                <input class="formfield" type="text" name="name"
                       value="<?php echo $this->escape($this->name) ?>"/>
            </li>
        </ul>

        <fieldset class="floatingTabLayout">
            <?php if (count($extensions) > 0): ?>
                <legend><?php echo $this->kga['lang']['extensionsTitle']; ?></legend>
            <?php
            endif;
            foreach ($extensions as $key => $value):
                $name = $key;
                if (isset($this->kga['lang']['extensions'][$name]))
                    $name = $this->kga['lang']['extensions'][$name];
                ?>
                <span class="permission"><input type="checkbox" value="1"
                                                name="<?php echo $key ?>-access" <?php if ($value == 1): ?> checked="checked" <?php endif; ?> /><?php echo $name ?></span>
            <?php endforeach; ?>
        </fieldset>
    <?php echo $this->floater()->tabContentEnd(); ?>

    <?php $this->hierarchy()->render($this->kga, $keyHierarchy); ?>

<?php echo $this->floater()->floaterEnd(); ?>