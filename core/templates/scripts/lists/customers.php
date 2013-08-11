<?php
// remove hidden entries from list
$customers = $this->filterListEntries($this->customers);
?>
<table>
  <tbody>
    <?php
    if (count($customers) == 0)
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
        foreach ($customers as  $customer)
        {
            ?>

            <tr id="row_customer" data-id="<?php echo $customer['customerID']?>" class="customer customer<?php echo $customer['customerID']?> <?php echo $this->cycle(array('odd','even'))->next()?>">

              <!-- option cell -->
              <td nowrap class="option">
                <?php if ($this->show_customer_edit_button): ?>
                <a href ="#" onClick="editSubject('customer',<?php echo $customer['customerID']?>); $(this).blur(); return false;">
                    <?php echo $this->icons('edit'); ?>
                </a>
                <?php endif; ?>
                <a href ="#" onClick="lists_update_filter('customer',<?php echo $customer['customerID']?>); $(this).blur(); return false;">
                    <?php echo $this->icons('filter'); ?>
                </a>
              </td>

              <!-- name cell -->
              <td width="100%" class="clients" onmouseover="lists_change_color(this,true);" onmouseout="lists_change_color(this,false);" onClick="lists_customer_highlight(<?php echo $customer['customerID']?>); $(this).blur(); return false;">
                  <?php if ($customer['visible'] != 1): ?><span style="color:#bbb"><?php endif; ?>
                  <?php if ($this->kga['conf']['showIDs'] == 1): ?><span class="ids"><?php echo $customer['customerID']?></span> <?php endif; echo $this->escape($customer['name'])?>
                  <?php if ($customer['visible'] != 1): ?></span><?php endif; ?>
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
