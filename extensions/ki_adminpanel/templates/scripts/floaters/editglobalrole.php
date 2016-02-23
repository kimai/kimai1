<?php

$extensions = array();
$keyHierarchy = array();

$this->getHelper('ParseHierarchy')->parseHierarchy($this->permissions, $extensions, $keyHierarchy);
?>
<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->title ?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->kga['lang']['close'] ?></a>
        </div>
    </div>
    <div class="menuBackground">
        <ul class="menu tabSelection">
            <li class="tab norm"><a href="#general">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->kga['lang']['general'] ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
            <?php
            foreach ($keyHierarchy as $key => $subKeys):
                if (count($subKeys) == 1 && array_key_exists('access', $subKeys)) continue;

                $name = $key;
                if (isset($this->kga['lang']['extensions'][$name]))
                    $name = $this->kga['lang']['extensions'][$name];
                ?>
                <li class="tab norm"><a href="#<?php echo $key ?>">
                        <span class="aa">&nbsp;</span>
                        <span class="bb"><?php echo $name ?></span>
                        <span class="cc">&nbsp;</span>
                    </a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <form id="adminPanel_extension_form_editRole" action="../extensions/ki_adminpanel/processor.php" method="post">
        <input type="hidden" name="id" value="<?php echo $this->id ?>"/>
        <input type="hidden" name="axAction" value="<?php echo $this->action; ?>"/>
        <div id="floater_tabs" class="floater_content">
            <fieldset id="general">
                <ul>
                    <li>
                        <label for="name"><?php echo $this->kga['lang']['rolename'] ?>:</label>
                        <input class="formfield" type="text" name="name" id="name" value="<?php echo $this->escape($this->name) ?>"/>
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
                        <span class="permission"><input type="checkbox" value="1" name="<?php echo $key ?>-access" <?php if ($value == 1): ?> checked="checked" <?php endif; ?> /><?php echo $name ?></span>
                    <?php endforeach; ?>
                </fieldset>
            </fieldset>
            <?php $this->echoHierarchy($this->kga, $keyHierarchy); ?>
        </div>
        <div id="formbuttons">
            <input class='btn_norm' type='button' value='<?php echo $this->kga['lang']['cancel'] ?>' onclick='floaterClose();return false;'/>
            <input class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['submit'] ?>'/>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#floater_innerwrap').tabs({selected: 0});
        var $adminPanel_extension_form_editRole = $('#adminPanel_extension_form_editRole');
        $adminPanel_extension_form_editRole.ajaxForm({
            'beforeSubmit': function () {
                clearFloaterErrorMessages();
                if ($('#adminPanel_extension_form_editRole').attr('submitting')) {
                    return false;
                }
                else {
                    $adminPanel_extension_form_editRole.attr('submitting', true);
                    return true;
                }
            },
            'success': function (result) {
                $adminPanel_extension_form_editRole.removeAttr('submitting');
                for (var fieldName in result.errors) {
                    setFloaterErrorMessage(fieldName, result.errors[fieldName]);
                }
                if (result.errors.length == 0) {
                    floaterClose();
                    adminPanel_extension_refreshSubtab('<?php echo $this->jsEscape($this->reloadSubtab); ?>');
                }
            },
            'error': function () {
                $adminPanel_extension_form_editRole.removeAttr('submitting');
            }
        });
    });
</script>