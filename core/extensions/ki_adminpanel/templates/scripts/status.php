<form>
    <input type="text" id="newstatus" class="formfield" />
    <input class='btn_ok' type=submit value="<?php echo $this->kga['lang']['new_status']?>" onclick="adminPanel_extension_newStatus(); return false;">
</form>
<br />
<table>
    <thead>
        <tr class='headerrow'>
            <th><?php echo $this->kga['lang']['status']?></th>
            <th><?php echo $this->kga['lang']['default']?></th>
            <th class="admin_options"><?php echo $this->kga['lang']['options']?></th>
        </tr>
    </thead>
    <tbody>
    <?php
    if (!isset($this->arr_status) || $this->arr_status == '0' || count($this->arr_status) == 0)
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
        foreach ($this->arr_status as $statusarray)
        {
            ?>
            <tr class='<?php echo $this->cycle(array("odd","even"))->next()?>'>

                <td>
                    <?php echo $this->escape($statusarray['status'])?>
                </td>

                <td>
                    <?php echo $statusarray['statusID'] == $this->kga['conf']['defaultStatusID'] ? $this->kga['lang']['default'] : '' ?>
                </td>

                <td>
                    <a href="#" onClick="adminPanel_extension_editStatus('<?php echo $statusarray['statusID']?>'); $(this).blur(); return false;">
                        <?php echo $this->icons('edit', array('title' => $this->kga['lang']['editstatus'])); ?></a>
                    &nbsp;
                    <?php if ($statusarray['timeSheetEntryCount'] == 0): ?>
                        <a href="#" onClick="adminPanel_extension_deleteStatus(<?php echo $statusarray['statusID']?>)">
                            <?php echo $this->icons('delete', array('title' => $this->kga['lang']['delete_status'])); ?></a>
                    <?php else: ?>
                        <?php echo $this->icons('delete', array('title' => $this->kga['lang']['delete_status'], 'disabled' => true)); ?>
                    <?php endif; ?>

                </td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>