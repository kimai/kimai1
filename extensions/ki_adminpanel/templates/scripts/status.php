<form>
    <input type="text" id="newstatus" class="formfield"/>
    <input class='btn_ok' type=submit value="<?php echo $this->kga['lang']['new_status'] ?>" onclick="adminPanel_extension_newStatus(); return false;">
</form>
<br/>
<table>
    <thead>
    <tr class='headerrow'>
        <th width="80px"><?php echo $this->kga['lang']['options'] ?></th>
        <th width="25%"><?php echo $this->kga['lang']['status'] ?></th>
        <th><?php echo $this->kga['lang']['default'] ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if (!isset($this->statuses) || $this->statuses == '0' || count($this->statuses) == 0) {
        ?>
        <tr>
            <td nowrap colspan='3'>
                <?php echo $this->error(); ?>
            </td>
        </tr>
        <?php
    } else {
        foreach ($this->statuses as $statusarray) {
            ?>
            <tr class='<?php echo $this->cycle(array("odd", "even"))->next() ?>'>
                <td class="option">
                    <a href="#" onclick="adminPanel_extension_editStatus('<?php echo $statusarray['statusID'] ?>'); $(this).blur(); return false;"><img
                            src="<?php echo $this->skin('grfx/edit2.gif'); ?>" title="<?php echo $this->kga['lang']['editstatus'] ?>"
                            width="13" height="13" alt="<?php echo $this->kga['lang']['editstatus'] ?>" border="0"></a>
                    &nbsp;
                    <?php if ($statusarray['timeSheetEntryCount'] == 0): ?>
                        <a href="#" onclick="adminPanel_extension_deleteStatus(<?php echo $statusarray['statusID'] ?>)"><img
                                src="<?php echo $this->skin('grfx/button_trashcan.png'); ?>" title="<?php echo $this->kga['lang']['delete_status'] ?>"
                                width="13" height="13" alt="<?php echo $this->kga['lang']['delete_status'] ?>" border="0"></a>
                    <?php else: ?>
                        <img src="<?php echo $this->skin('grfx/button_trashcan_.png'); ?>" title="<?php echo $this->kga['lang']['delete_status'] ?>" width="13" height="13" alt="<?php echo $this->kga['lang']['delete_status'] ?>" border="0">
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo $this->escape($statusarray['status']) ?>
                </td>
                <td>
                    <?php echo $statusarray['statusID'] == $this->kga['conf']['defaultStatusID'] ? $this->kga['lang']['default'] : '' ?>
                </td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>
