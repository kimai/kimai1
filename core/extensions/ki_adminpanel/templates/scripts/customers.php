<a onClick="floaterShow('floaters.php','add_edit_customer',0,0,450); $(this).blur(); return false;"
   href="#" ><?php echo $this->icons('add', array('title' => $this->kga['lang']['new_customer'])); ?></a>
<?php echo $this->kga['lang']['new_customer']?>
<br/><br/>

<table>

    <thead>
      <tr class='headerrow'>
          <th class="admin_options"><?php echo $this->kga['lang']['options']?></th>
          <th><?php echo $this->kga['lang']['customers']?></th>
          <th><?php echo $this->kga['lang']['contactPerson']?></th>
          <th><?php echo $this->kga['lang']['groups']?></th>
      </tr>
    </thead>

    <tbody>
    <?php
    if (!isset($this->customers) || $this->customers == '0' || count($this->customers) == 0)
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
        foreach ($this->customers as $row)
        {
            ?>
            <tr class="<?php echo $this->cycle(array("odd","even"))->next()?>">

                <td class="option">
                    <a href ="#" onClick="editSubject('customer',<?php echo $row['customerID']?>); $(this).blur(); return false;">
                        <?php echo $this->icons('edit'); ?></a>

                    &nbsp;

                    <a href="#" id="delete_customer<?php echo $row['customerID']?>" onClick="adminPanel_extension_deleteCustomer(<?php echo $row['customerID']?>)">
                        <?php echo $this->icons('delete', array('title' => $this->kga['lang']['delete_customer'])); ?></a>
                </td>

                <td class="clients">
                    <?php if ($row['visible'] != 1):?><span style="color:#bbb"><?php endif; ?>
                    <?php echo $this->escape($row['name']);?>
                    <?php if ($row['visible'] != 1):?></span><?php endif; ?>
                </td>

                <td>
                  <?php echo $this->escape($row['contact']);?>
                </td>

                <td>
                    <?php echo $this->escape($row['groups'])?>
                </td>

            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>