<?php
// remove hidden entries from list
$customers = $this->filterListEntries($this->customers);
?>
<table>
    <tbody>
    <?php
    if (count($customers) > 0) {
        foreach ($customers as $customer) {
            ?>
            <tr id="row_customer" data-id="<?php echo $customer['customerID'] ?>"
                class="customer customer<?php echo $customer['customerID'] ?> <?php echo $this->cycle([
                    'odd',
                    'even'
                ])->next() ?>">
                <td nowrap class="option">
                    <?php if ($this->show_customer_edit_button): ?>
                        <a href="#"
                           onclick="editSubject('customer',<?php echo $customer['customerID'] ?>); $(this).blur(); return false;">
                            <img src="<?php echo $this->skin('grfx/edit2.gif'); ?>" width="13" height="13"
                                 alt='<?php echo $this->translate('edit') ?>'
                                 title='<?php echo $this->translate('edit') ?>' border="0"/>
                        </a>
                    <?php endif; ?>
                    <a href="#"
                       onclick="lists_update_filter('customer',<?php echo $customer['customerID'] ?>); $(this).blur(); return false;">
                        <img src="<?php echo $this->skin('grfx/filter.png'); ?>" width='13' height='13'
                             alt='<?php echo $this->translate('filter') ?>'
                             title='<?php echo $this->translate('filter') ?>' border="0"/>
                    </a>
                </td>
                <td width="100%" class="clients" onmouseover="lists_change_color(this,true);"
                    onmouseout="lists_change_color(this,false);"
                    onclick="lists_customer_highlight(<?php echo $customer['customerID'] ?>); $(this).blur(); return false;">
                    <?php if ($this->kga->getSettings()->isShowIds()): ?><span
                            class="ids"><?php echo $customer['customerID'] ?></span> <?php endif;
                    echo $this->escape($customer['name']) ?>
                </td>
                <td nowrap class="annotation"></td>
            </tr>
            <?php
        }
    } else {
        ?>
        <tr>
            <td nowrap colspan="3">
                <?php echo $this->error(); ?>
            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
