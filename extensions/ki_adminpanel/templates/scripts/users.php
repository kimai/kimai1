<form>
    <input type="text" id="newuser" class="formfield"/>
    <input class='btn_ok' type="submit" value="<?php echo $this->kga['lang']['adduser'] ?>" onclick="adminPanel_extension_newUser(); return false;">
    <?php if ($this->showDeletedUsers): ?>
        <input class='btn_ok' type="button" value="<?php echo $this->kga['lang']['hidedeletedusers'] ?>" onclick="adminPanel_extension_hideDeletedUsers(); return false;">
    <?php else: ?>
        <input class='btn_ok' type="button" value="<?php echo $this->kga['lang']['showdeletedusers'] ?>" onclick="adminPanel_extension_showDeletedUsers(); return false;">
    <?php endif; ?>
</form>
<br/>
<table>
    <thead>
    <tr>
        <th width="80px"><?php echo $this->kga['lang']['options'] ?></th>
        <th width="25%"><?php echo $this->kga['lang']['username'] ?></th>
        <th><?php echo $this->kga['lang']['status'] ?></th>
        <th><?php echo $this->kga['lang']['globalRole'] ?></th>
        <th width="25%"><?php echo $this->kga['lang']['groups']?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if (!isset($this->users) || $this->users == '0' || count($this->users) == 0) {
        ?>
        <tr>
            <td nowrap colspan='4'>
                <?php echo $this->error(); ?>
            </td>
        </tr>
        <?php
    } else {
        foreach ($this->users as $userarray) {
            ?>
            <tr class='<?php echo $this->cycle(array("odd", "even"))->next() ?>'>
                <?php /* ########## Option cells ########## */ ?>
                <td class="option">
                    <a href="#" onclick="adminPanel_extension_editUser('<?php echo $userarray['userID'] ?>'); $(this).blur(); return false;">
                        <img src="<?php echo $this->skin('grfx/edit2.gif'); ?>" title="<?php echo $this->kga['lang']['editUser'] ?>" width="13" height="13" alt="<?php echo $this->kga['lang']['editUser'] ?>" border="0"></a>
                    &nbsp;
                    <?php if ($userarray['mail']): ?>
                        <a href="mailto:<?php echo $this->escape($userarray['mail']); ?>"><img
                                src="<?php echo $this->skin('grfx/button_mail.gif'); ?>"
                                title="<?php echo $this->kga['lang']['mailUser'] ?>" width="12" height="13" alt="<?php echo $this->kga['lang']['mailUser'] ?>" border="0"></a>
                    <?php else: ?>
                        <img src="<?php echo $this->skin('grfx/button_mail_.gif'); ?>" title="<?php echo $this->kga['lang']['mailUser'] ?>" width="12" height="13" alt="<?php echo $this->kga['lang']['mailUser'] ?>" border="0">
                    <?php endif; ?>
                    &nbsp;
                    <?php if ($this->curr_user != $userarray['name']) { ?>
                        <a href="#" id="deleteUser<?php echo $userarray['userID'] ?>" onclick="adminPanel_extension_deleteUser(<?php echo $userarray['userID'] ?>, <?php echo($userarray['trash'] ? "2" : "1"); ?>)"><img
                                src="<?php echo $this->skin('grfx/button_trashcan.png'); ?>" title="<?php echo $this->kga['lang']['deleteUser'] ?>"
                                width="13" height="13" alt="<?php echo $this->kga['lang']['deleteUser'] ?>" border="0"></a>
                    <?php } else { ?>
                        <img src="<?php echo $this->skin('grfx/button_trashcan_.png'); ?>" title="<?php echo $this->kga['lang']['deleteUser'] ?>" width="13" height="13" alt="<?php echo $this->kga['lang']['deleteUser'] ?>" border="0">
                    <?php } ?>
                </td>

                <?php /* ########## User Name ########## */ ?>
                <td>
                    <?php if ($this->curr_user == $userarray['name']): ?>
                        <strong style="color:#00E600"><?php echo $this->escape($userarray['name']) ?></strong>
                    <?php else: ?>
                        <?php if ($userarray['trash']): ?><span style="color:#999"><?php endif; ?>
                        <?php echo $this->escape($userarray['name']); ?>
                        <?php if ($userarray['trash']): ?></span><?php endif; ?>
                    <?php endif; ?>
                </td>

                <?php /* ########## Status ########## */ ?>
                <td>
                    <?php if ($userarray['active'] == 1): ?>
                        <?php if ($this->curr_user != $userarray['name']): ?>
                            <a href="#" id="ban<?php echo $userarray['userID'] ?>" onclick="adminPanel_extension_banUser('<?php echo $userarray['userID'] ?>'); return false;">
                                <img src="<?php echo $this->skin('grfx/jipp.gif'); ?>" alt='<?php echo $this->kga['lang']['activeAccount'] ?>' title='<?php echo $this->kga['lang']['activeAccount'] ?>' border="0" width="16" height="16"/></a>
                        <?php else: ?>
                            <img src="<?php echo $this->skin('grfx/jipp_.gif'); ?>" alt='<?php echo $this->kga['lang']['activeAccount'] ?>' title='<?php echo $this->kga['lang']['activeAccount'] ?>' border="0" width="16" height="16"/>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($userarray['active'] == 0): ?>
                        <a href="#" id="ban<?php echo $userarray['userID'] ?>" onclick="adminPanel_extension_unbanUser('<?php echo $userarray['userID'] ?>'); return false;">
                            <img src="<?php echo $this->skin('grfx/lock.png'); ?>" alt='<?php echo $this->kga['lang']['bannedUser'] ?>' title='<?php echo $this->kga['lang']['bannedUser'] ?>' border="0" width="16" height="16"/></a>
                    <?php endif; ?>
                    &nbsp;
                    <?php if ($userarray['passwordSet'] == "no"): ?>
                        <a href="#" onclick="adminPanel_extension_editUser('<?php echo $userarray['userID'] ?>'); $(this).blur(); return false;">
                            <img src="<?php echo $this->skin('grfx/caution_mini.png'); ?>" width="16" height="16" title='<?php echo $this->kga['lang']['nopasswordset'] ?>' border="0"></a>
                    <?php endif; ?>
                    &nbsp;
                    <?php if ($userarray['trash']): ?>
                        <a href="#" id="deleteUser<?php echo $userarray['userID'] ?>" onclick="adminPanel_extension_deleteUser(<?php echo $userarray['userID'] ?>, 0)"><img
                                src="<?php echo $this->skin('grfx/button_trashcan.png'); ?>" title="<?php echo $this->kga['lang']['restoreAccount'] ?>"
                                width="13" height="13" alt="<?php echo $this->kga['lang']['restoreAccount'] ?>" border="0"></a>
                    <?php endif; ?>
                </td>
                <!-- ########## /Status cells ########## -->
                <!-- ########## Global Group ########## -->
                <td>
                    <?php echo $userarray['globalRoleName']; ?>
                </td>
                <!-- ########## Group cells ########## -->
                <!-- ########## Group cells ########## -->
                <td>
                    <?php echo implode(', ', $userarray['groups']); ?>
                </td>
                <!-- ########## Group cells ########## -->
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>
<p>
    <strong><?php echo $this->kga['lang']['hint'] ?></strong> <?php echo $this->kga['lang']['rename_caution_before_username'] ?>
    '<?php echo $this->escape($this->curr_user) ?>' <?php echo $this->kga['lang']['rename_caution_after_username'] ?>
</p>
