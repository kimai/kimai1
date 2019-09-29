<form>
    <input type="text" id="newGlobalRole" class="formfield" />
    <input class='btn_ok' type=submit value="<?php echo $this->translate('addGlobalRole') ?>" onclick="adminPanel_extension_newGlobalRole(); return false;">
</form>
<br/>
<table>
    <thead>
    <tr class='headerrow'>
        <th width="80px"><?php echo $this->translate('options') ?></th>
        <th width="25%"><?php echo $this->translate('globalRole') ?></th>
        <th><?php echo $this->translate('users') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if (count($this->globalRoles) == 0) {
        ?>
        <tr>
            <td nowrap colspan="3">
                <?php echo $this->error(); ?>
            </td>
        </tr>
        <?php
    } else {
        foreach ($this->globalRoles as $globalRole) {
            ?>
            <tr class='<?php echo $this->cycle(["odd", "even"])->next() ?>'>
                <td class="option">
                    <a href="#" onclick="adminPanel_extension_editGlobalRole('<?php echo $globalRole['globalRoleID'] ?>'); $(this).blur(); return false;">
                        <img src="<?php echo $this->skin('grfx/edit2.gif'); ?>" title="<?php echo $this->translate('editGlobalRole') ?>" width="13" height="13" alt="<?php echo $this->translate('editGlobalRole') ?>" border="0"></a>
                    &nbsp;
                    <?php if ($globalRole['count_users'] == 0): ?>
                        <a href="#" onclick="adminPanel_extension_deleteGlobalRole(<?php echo $globalRole['globalRoleID'] ?>)"><img
                                src="<?php echo $this->skin('grfx/button_trashcan.png'); ?>"
                                title="<?php echo $this->translate('deleteGlobalRole') ?>" width="13" height="13" alt="<?php echo $this->translate('deleteGlobalRole') ?>" border="0"></a>
                    <?php else: ?>
                        <img src="<?php echo $this->skin('grfx/button_trashcan_.png'); ?>" title="<?php echo $this->translate('deleteGlobalRole') ?>" width="13" height="13" alt="<?php echo $this->translate('deleteGlobalRole') ?>" border="0">
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo $this->escape($globalRole['name']); ?>
                </td>
                <td><?php echo $globalRole['count_users'] ?></td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>
