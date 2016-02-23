<div id="expenses_head">
    <div class="left">
        <?php if (isset($this->kga['user'])): ?>
            <a href="#" onclick="floaterShow('../extensions/ki_expenses/floaters.php','add_edit_record',0,0,600); $(this).blur(); return false;"><?php echo $this->kga['lang']['add'] ?></a>
        <?php endif; ?>
    </div>
    <table>
        <colgroup>
            <col class="options"/>
            <col class="date"/>
            <col class="time"/>
            <col class="value"/>
            <col class="refundable"/>
            <col class="customer"/>
            <col class="project"/>
            <col class="designation"/>
            <col class="username"/>
        </colgroup>
        <tbody>
        <tr>
            <td class="option">&nbsp;</td>
            <td class="date"><?php echo $this->kga['lang']['datum'] ?></td>
            <td class="time"><?php echo $this->kga['lang']['timelabel'] ?></td>
            <td class="value"><?php echo $this->kga['lang']['expense'] ?></td>
            <td class="refundable"><?php echo $this->kga['lang']['refundable'] ?></td>
            <td class="customer"><?php echo $this->kga['lang']['customer'] ?></td>
            <td class="project"><?php echo $this->kga['lang']['project'] ?></td>
            <td class="designation"><?php echo $this->kga['lang']['designation'] ?></td>
            <td class="username"><?php echo $this->kga['lang']['username'] ?></td>
        </tr>
        </tbody>
    </table>
</div>
<div id="expenses"><?php echo $this->expenses_display ?></div>
<script type="text/javascript">
    $(document).ready(function () {
        expense_extension_onload();
    });
</script>