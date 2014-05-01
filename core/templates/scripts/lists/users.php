<table>
  <tbody>
    <?php
    if (count($this->users) == 0)
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
        foreach ($this->users as $user)
        {
            ?>
            <tr id="row_user" data-id="<?php echo $user['userID']?>" class="<?php echo $this->cycle(array('odd','even'))->next()?>">
              <!--  option cell -->
              <td nowrap class="option">
                <a href ="#" onClick="lists_update_filter('user',<?php echo $user['userID']?>); $(this).blur(); return false;">
                    <?php echo $this->icons('filter'); ?>
                </a>
              </td>

              <!-- name cell -->
              <td width="100%" class="clients">
                <?php echo $this->escape($user['name']) ?>
              </td>

              <!-- annotation cell -->
              <td nowrap class="annotation"></td>
            </tr>
            <?php
        }
    }
  ?>
  </tbody>
</table>  