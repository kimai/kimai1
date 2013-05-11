<form>
    <input type=text id="newMembershipRole" class="formfield"></input>
    <input class='btn_ok' type=submit value="<?php echo $this->kga['lang']['addMembershipRole']?>" onclick="adminPanel_extension_newMembershipRole(); return false;">
</form>
<br />
<table>
    <thead>
        <tr class='headerrow'>
            <th><?php echo $this->kga['lang']['options']?></th>
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
                    <?php echo $this->escape($membershipRole['name']); ?>
                </td>

                <td>
                    <a href="#" onClick="adminPanel_extension_editMembershipRole('<?php echo $membershipRole['membershipRoleID']?>'); $(this).blur(); return false;">
                        <img src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/edit2.gif" title="<?php echo $this->kga['lang']['editMembershipRole']?>" width="13" height="13" alt="<?php echo $this->kga['lang']['editMembershipRole']?>" border="0"></a>

                    &nbsp;

                    <?php if ($membershipRole['count_users'] == 0): ?>
                        <a href="#" onClick="adminPanel_extension_deleteMembershipRole(<?php echo $membershipRole['membershipRoleID']?>)"><img
                                src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/button_trashcan.png"
                                title="<?php echo $this->kga['lang']['deleteMembershipRole']?>" width="13" height="13" alt="<?php echo $this->kga['lang']['deleteMembershipRole']?>" border="0"></a>
                    <?php else: ?>
                        <img src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/button_trashcan_.png" title="<?php echo $this->kga['lang']['deleteMembershipRole']?>" width="13" height="13" alt="<?php echo $this->kga['lang']['deleteMembershipRole']?>" border="0">
                    <?php endif; ?>

                </td>

                <td><?php echo $membershipRole['count_users']?></td>
            </tr>
            <?php
        }
    }
?>

</tbody>
</table>
