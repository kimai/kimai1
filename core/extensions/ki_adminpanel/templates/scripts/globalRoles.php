<form>
    <input type=text id="newGlobalRole" class="formfield"></input>
    <input class='btn_ok' type=submit value="<?php echo $this->kga['lang']['addGlobalRole']?>" onclick="adminPanel_extension_newGlobalRole(); return false;">
</form>
<br />
<table>
    <thead>
        <tr class='headerrow'>
            <th><?php echo $this->kga['lang']['options']?></th>
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
                    <?php echo $this->escape($globalRole['name']); ?>
                </td>

                <td>
                    <a href="#" onClick="adminPanel_extension_editGlobalRole('<?php echo $globalRole['globalRoleID']?>'); $(this).blur(); return false;">
                        <img src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/edit2.gif" title="<?php echo $this->kga['lang']['editGlobalRole']?>" width="13" height="13" alt="<?php echo $this->kga['lang']['editGlobalRole']?>" border="0"></a>

                    &nbsp;

                    <?php if ($globalRole['count_users'] == 0): ?>
                        <a href="#" onClick="adminPanel_extension_deleteGlobalRole(<?php echo $globalRole['globalRoleID']?>)"><img
                                src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/button_trashcan.png"
                                title="<?php echo $this->kga['lang']['deleteGlobalRole']?>" width="13" height="13" alt="<?php echo $this->kga['lang']['deleteGlobalRole']?>" border="0"></a>
                    <?php else: ?>
                        <img src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/button_trashcan_.png" title="<?php echo $this->kga['lang']['deleteGlobalRole']?>" width="13" height="13" alt="<?php echo $this->kga['lang']['deleteGlobalRole']?>" border="0">
                    <?php endif; ?>

                </td>

                <td><?php echo $globalRole['count_users']?></td>
            </tr>
            <?php
        }
    }
?>

</tbody>
</table>
