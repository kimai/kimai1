<form>
    <input type=text id="newgroup" class="formfield"></input>
    <input class='btn_ok' type=submit value="<?php echo $this->kga['lang']['addgroup']?>" onclick="adminPanel_extension_newGroup(); return false;">
</form>
<br />
<table>
    <thead>
        <tr class='headerrow'>
            <th class="admin_options"><?php echo $this->kga['lang']['options']?></th>
            <th><?php echo $this->kga['lang']['group']?></th>
            <th><?php echo $this->kga['lang']['members']?></th>
        </tr>
    </thead>
    <tbody>
    <?php
    if (!isset($this->groups) || $this->groups == '0' || count($this->groups) == 0)
    {
        ?>
    <tr>
        <td nowrap colspan='3'>
            <?php echo $this->error(); ?>
        </td>
    </tr>
        <?php
    }
    else
    {
        foreach ($this->groups as $grouparray)
        {
            ?>
            <tr class='<?php echo $this->cycle(array("odd","even"))->next()?>'>

                <td>
                    <a href="#" onClick="adminPanel_extension_editGroup('<?php echo $grouparray['groupID']?>'); $(this).blur(); return false;">
                        <?php echo $this->icons('edit', array('title' => $this->kga['lang']['editGroup'])); ?></a>
                    &nbsp;
                    <?php if ($grouparray['count_users'] == 0): ?>
                        <a href="#" onClick="adminPanel_extension_deleteGroup(<?php echo $grouparray['groupID']?>)">
                            <?php echo $this->icons('delete', array('title' => $this->kga['lang']['delete_group'])); ?></a>
                    <?php else: ?>
                        <?php echo $this->icons('delete', array('title' => $this->kga['lang']['delete_group'], 'disabled' => true)); ?>
                    <?php endif; ?>

                </td>

                <td>
                    <?php if ($grouparray['groupID'] == 1): ?>
                        <span style="color:red"><?php echo $this->escape($grouparray['name']); ?></span>
                    <?php else: ?>
                        <?php echo $this->escape($grouparray['name']); ?>
                    <?php endif; ?>
                </td>

                <td><?php echo $grouparray['count_users']?></td>
            </tr>
            <?php
        }
    }
?>

</tbody>
</table>
