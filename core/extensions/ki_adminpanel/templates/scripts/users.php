<form>
    <input type="text" id="newuser" class="formfield" />
    <input class='btn_ok' type="submit" value="<?php echo $this->kga['lang']['adduser']?>" onclick="adminPanel_extension_newUser(); return false;">
    <?php if ($this->showDeletedUsers): ?>
    <input class='btn_ok' type="button" value="<?php echo $this->kga['lang']['hidedeletedusers']?>" onclick="adminPanel_extension_hideDeletedUsers(); return false;">
    <?php else: ?>
    <input class='btn_ok' type="button" value="<?php echo $this->kga['lang']['showdeletedusers']?>" onclick="adminPanel_extension_showDeletedUsers(); return false;">
    <?php endif; ?>
</form>

<br />

<table>

    <thead>
      <tr class='headerrow'>
          <th class="admin_options"><?php echo $this->kga['lang']['options']?></th>
          <th><?php echo $this->kga['lang']['username']?></th>
          <th><?php echo $this->kga['lang']['status']?></th>
          <th><?php echo $this->kga['lang']['group']?></th>
      </tr>
    </thead>

    <tbody>
    <?php
    if (!isset($this->users) || $this->users == '0' || count($this->users) == 0)
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
        foreach ($this->users as $userarray)
        {
            ?>
            <tr class='<?php echo $this->cycle(array("odd","even"))->next()?>'>


                <!-- ########## Option cells ########## -->
                <td>
                    <a href="#" onClick="adminPanel_extension_editUser('<?php echo $userarray['userID'] ?>'); $(this).blur(); return false;">
                        <?php echo $this->icons('edit', array('title' => $this->kga['lang']['editUser'])); ?></a>
                    &nbsp;
                    <?php if ($userarray['mail']): ?>
                        <a href="mailto:<?php echo $this->escape($userarray['mail']);?>">
                            <?php echo $this->icons('email', array('title' => $this->kga['lang']['mailUser'])); ?></a>
                    <?php else: ?>
                        <?php echo $this->icons('email', array('title' => $this->kga['lang']['mailUser'], 'disabled' => true)); ?>

                    <?php endif; ?>

                    &nbsp;

                    <?php if ($this->curr_user != $userarray['name']) { ?>
                        <a href="#" id="deleteUser<?php echo $userarray['userID'] ?>" onClick="adminPanel_extension_deleteUser(<?php echo $userarray['userID'] ?>, <?php echo ($userarray['trash'] ? "false" : "true"); ?>)">
                            <?php echo $this->icons('delete', array('title' => $this->kga['lang']['deleteUser'])); ?></a>
                    <?php } else { ?>
                        <?php echo $this->icons('delete', array('title' => $this->kga['lang']['deleteUser'], 'disabled' => true)); ?>
                    <?php } ?>
                </td>
                <!-- ########## /Option cells ########## -->

                <!-- ########## USER NAME ########## -->
                <td>
                    <?php if ($this->curr_user == $userarray['name']): ?>
                        <span class="admin"><?php echo $this->escape($userarray['name'])?></span>
                    <?php else: ?>
                        <?php if ($userarray['trash']):?><span style="color:#999"><?php endif; ?>
                            <?php echo $this->escape($userarray['name']);?>
                        <?php if ($userarray['trash']):?></span><?php endif; ?>
                    <?php endif; ?>
                </td>
                <!-- ########## /USER NAME ########## -->

                <td>
                    <?php if ($userarray['active'] == 1): ?>
                        <?php if ($this->curr_user != $userarray['name']): ?>
                                <a href="#" id="ban<?php echo $userarray['userID'] ?>" onClick="adminPanel_extension_banUser('<?php echo $userarray['userID'] ?>'); return false;">
                                    <?php echo $this->icons('unlocked', array('title' => $this->kga['lang']['activeAccount'])); ?></a>
                        <?php else: ?>
                            <?php echo $this->icons('unlocked', array('title' => $this->kga['lang']['activeAccount'], 'disabled' => true)); ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($userarray['active'] == 0): ?>
                        <a href="#" id="ban<?php echo $userarray['userID'] ?>" onClick="adminPanel_extension_unbanUser('<?php echo $userarray['userID'] ?>'); return false;">
                            <?php echo $this->icons('locked', array('title' => $this->kga['lang']['bannedUser'])); ?></a>
                    <?php endif; ?>

                        &nbsp;

                    <?php if ($userarray['passwordSet'] == "no"): ?>
                        <a href="#" onClick="adminPanel_extension_editUser('<?php echo $userarray['userID'] ?>'); $(this).blur(); return false;">
                            <?php echo $this->icons('warning', array('title' => $this->kga['lang']['nopasswordset'])); ?></a>
                    <?php endif; ?>

                        &nbsp;

                    <?php if ($userarray['trash']): ?>
                        <strong style="color:red">X</strong>
                    <?php endif; ?>
                </td>
                <!-- ########## /Status cells ########## -->

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

<p><strong><?php echo $this->kga['lang']['hint']?></strong> <?php echo $this->kga['lang']['rename_caution_before_username']?> '<?php echo $this->escape($this->curr_user) ?>' <?php echo $this->kga['lang']['rename_caution_after_username']?></p>