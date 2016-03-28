<form>
    <input type="text" id="newgroup" class="formfield" />
    <input class='btn_ok' type=submit value="<?php echo $this->kga['lang']['addgroup'] ?>" onclick="adminPanel_extension_newGroup(); return false;">
</form>
<br/>
<table>
    <thead>
    <tr class='headerrow'>
        <th width="80px"><?php echo $this->kga['lang']['options'] ?></th>
        <th width="25%"><?php echo $this->kga['lang']['group'] ?></th>
        <th><?php echo $this->kga['lang']['members'] ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if (!isset($this->groups) || $this->groups == '0' || count($this->groups) == 0) {
        ?>
        <tr>
            <td nowrap colspan='3'>
                <?php echo $this->error(); ?>
            </td>
        </tr>
        <?php
    } else {
        foreach ($this->groups as $grouparray) {
            ?>
            <tr class='<?php echo $this->cycle(array("odd", "even"))->next() ?>'>
                <td class="option">
                    <a href="#" onclick="adminPanel_extension_editGroup('<?php echo $grouparray['groupID'] ?>'); $(this).blur(); return false;">
                        <img src="<?php echo $this->skin('grfx/edit2.gif'); ?>" title="<?php echo $this->kga['lang']['editGroup'] ?>" width="13" height="13" alt="<?php echo $this->kga['lang']['editGroup'] ?>" border="0"></a>
                    &nbsp;
                    <?php if ($grouparray['count_users'] == 0): ?>
                        <a href="#" onclick="adminPanel_extension_deleteGroup(<?php echo $grouparray['groupID'] ?>)"><img
                                src="<?php echo $this->skin('grfx/button_trashcan.png'); ?>"
                                title="<?php echo $this->kga['lang']['delete_group'] ?>" width="13" height="13" alt="<?php echo $this->kga['lang']['delete_group'] ?>" border="0"></a>
                    <?php else: ?>
                        <img src="<?php echo $this->skin('grfx/button_trashcan_.png'); ?>" title="<?php echo $this->kga['lang']['delete_group'] ?>" width="13" height="13" alt="<?php echo $this->kga['lang']['delete_group'] ?>" border="0">
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($grouparray['groupID'] == 1): ?>
                        <span style="color:red"><?php echo $this->escape($grouparray['name']); ?></span>
                    <?php else: ?>
                        <?php echo $this->escape($grouparray['name']); ?>
                    <?php endif; ?>
                </td>
                <td><?php echo $grouparray['count_users'] ?></td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>
