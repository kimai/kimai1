<form>
    <input type=text id="newGlobalRole" class="formfield"></input>
    <input class='btn_ok' type=submit value="<?php echo $this->kga['lang']['addGlobalRole']?>" onclick="adminPanel_extension_newGlobalRole(); return false;">
</form>
<br />
<table>
    <thead>
        <tr class='headerrow'>
            <th class="admin_options"><?php echo $this->kga['lang']['options']?></th>
            <th><?php echo $this->kga['lang']['globalRole']?></th>
            <th><?php echo $this->kga['lang']['users']?></th>
        </tr>
    </thead>
    <tbody>
    <?php
    if (count($this->globalRoles) == 0)
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
        foreach ($this->globalRoles as $globalRole)
        {
            ?>
            <tr class='<?php echo $this->cycle(array("odd","even"))->next()?>'>

                <td>
                    <a href="#" onClick="adminPanel_extension_editGlobalRole('<?php echo $globalRole['globalRoleID']?>'); $(this).blur(); return false;">
                        <?php echo $this->icons('edit', array('title' => $this->kga['lang']['editGlobalRole'])); ?></a>

                    &nbsp;

                    <?php if ($globalRole['count_users'] == 0): ?>
                        <a href="#" onClick="adminPanel_extension_deleteGlobalRole(<?php echo $globalRole['globalRoleID']?>)">
                            <?php echo $this->icons('delete', array('title' => $this->kga['lang']['deleteGlobalRole'])); ?></a>
                    <?php else: ?>
                        <?php echo $this->icons('delete', array('title' => $this->kga['lang']['deleteGlobalRole'], 'disabled' => true)); ?>
                    <?php endif; ?>

                </td>

                <td>
                    <?php echo $this->escape($globalRole['name']); ?>
                </td>

                <td><?php echo $globalRole['count_users']?></td>
            </tr>
            <?php
        }
    }
?>

</tbody>
</table>
