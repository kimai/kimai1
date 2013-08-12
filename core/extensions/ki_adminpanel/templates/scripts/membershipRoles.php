<form>
    <input type=text id="newMembershipRole" class="formfield"></input>
    <input class='btn_ok' type=submit value="<?php echo $this->kga['lang']['addMembershipRole']?>" onclick="adminPanel_extension_newMembershipRole(); return false;">
</form>
<br />
<table>
    <thead>
        <tr class='headerrow'>
            <th class="admin_options"><?php echo $this->kga['lang']['options']?></th>
            <th><?php echo $this->kga['lang']['membershipRole']?></th>
            <th><?php echo $this->kga['lang']['users']?></th>
        </tr>
    </thead>
    <tbody>
    <?php
    if (count($this->membershipRoles) == 0)
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
        foreach ($this->membershipRoles as $membershipRole)
        {
            ?>
            <tr class='<?php echo $this->cycle(array("odd","even"))->next()?>'>

                <td>
                    <a href="#" onClick="adminPanel_extension_editMembershipRole('<?php echo $membershipRole['membershipRoleID']?>'); $(this).blur(); return false;">
                        <?php echo $this->icons('edit', array('title' => $this->kga['lang']['editMembershipRole'])); ?></a>
                    &nbsp;

                    <?php if ($membershipRole['count_users'] == 0): ?>
                        <a href="#" onClick="adminPanel_extension_deleteMembershipRole(<?php echo $membershipRole['membershipRoleID']?>)">
                            <?php echo $this->icons('delete', array('title' => $this->kga['lang']['deleteMembershipRole'])); ?></a>
                    <?php else: ?>
                        <?php echo $this->icons('delete', array('title' => $this->kga['lang']['deleteMembershipRole'], 'disabled' => true)); ?>
                    <?php endif; ?>

                </td>

                <td>
                    <?php echo $this->escape($membershipRole['name']); ?>
                </td>

                <td><?php echo $membershipRole['count_users']?></td>
            </tr>
            <?php
        }
    }
?>

</tbody>
</table>
