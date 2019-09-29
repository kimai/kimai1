<table>
    <tbody>
    <?php
    if (count($this->users)) {
        foreach ($this->users as $user) {
            ?>
            <tr id="row_user" data-id="<?php echo $user['userID'] ?>"
                class="<?php echo $this->cycle(['odd', 'even'])->next() ?>">
                <td nowrap class="option">
                    <a href="#"
                       onclick="lists_update_filter('user',<?php echo $user['userID'] ?>); $(this).blur(); return false;"><img
                                src="<?php echo $this->skin('grfx/filter.png'); ?>" width="13" height="13"
                                alt="<?php echo $this->translate('filter') ?>"
                                title="<?php echo $this->translate('filter') ?>" border="0"/>
                    </a>
                </td>
                <td width="100%" class="clients">
                    <?php echo $this->escape($user['name']) ?>
                </td>
                <td nowrap class="annotation"></td>
            </tr>
            <?php
        }
    } else {
        ?>
        <tr>
            <td nowrap colspan="3"><?php echo $this->error(); ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>