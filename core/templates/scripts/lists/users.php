<table>

  <tbody>

    <?php foreach ($this->users as $user): ?>
  
    <tr id="row_user<?php echo $user['userID']?>" class="<?php echo $this->cycle(array('odd','even'))->next()?>">    
      <!--  option cell -->
      <td nowrap class="option">
        <a href ="#" onClick="lists_update_filter('user',<?php echo $user['userID']?>); $(this).blur(); return false;">
          <img src='../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/filter.png' width='13' height='13' alt='<?php echo $this->kga['lang']['filter']?>' title='<?php echo $this->kga['lang']['filter']?>' border='0' />
        </a>
      </td>

      <!-- name cell -->
      <td width="100%" class="clients">
        <?php echo $this->escape($user['name']) ?>
      </td>

      <!-- annotation cell -->
      <td nowrap class="annotation"></td>
    </tr>

    <?php endforeach; ?>

    <?php if ($this->users == '0'): ?>
      <tr>
          <td colspan='3'>
              <strong style="color:red"><?php echo $this->kga['lang']['noItems']?></strong>
          </td>
      </tr>
    <?php  endif; ?>
  </tbody>  
</table>  