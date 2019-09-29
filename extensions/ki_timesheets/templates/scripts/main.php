<div id="timeSheet_head">
    <div class="left">
        <?php if (isset($this->kga['user'])): ?>
            <a href="#" onclick="floaterShow('../extensions/ki_timesheets/floaters.php','add_edit_timeSheetEntry',selected_project+'|'+selected_activity,0,650); $(this).blur(); return false;"><?php echo $this->translate('add') ?></a>
        <?php endif; ?>
    </div>
    <table>
        <colgroup>
            <col class="options"/>
            <col class="date"/>
            <col class="from"/>
            <col class="to"/>
            <col class="time"/>
            <?php if ($this->showBillability): ?>
                <col class="billability" />
                <col class="billableTime" />
            <?php endif; ?>
            <?php if ($this->showRates): ?>
                <col class="wage"/>
            <?php endif; ?>
            <col class="customer"/>
            <col class="project"/>
            <col class="activity"/>
            <col class="description"/>
            <?php if ($this->showTrackingNumber): ?>
                <col class="trackingnumber"/>
            <?php endif; ?>
            <col class="username"/>
        </colgroup>
        <tbody>
        <tr>
            <td class="option">&nbsp;</td>
            <td class="date"><?php echo $this->translate('datum'); ?></td>
            <td class="from"><?php echo $this->translate('in'); ?></td>
            <td class="to"><?php echo $this->translate('out'); ?></td>
            <td class="time"><?php echo $this->translate('time'); ?></td>
            <?php if ($this->showBillability): ?>
                <td class="billable"><?php echo $this->translate('billable'); ?></td>
                <td class="time_billable"><?php echo $this->translate('time_billable'); ?></td>
            <?php endif; ?>
            <?php if ($this->showRates): ?>
                <td class="wage"><?php echo $this->translate('wage'); ?></td>
            <?php endif; ?>
            <td class="customer"><?php echo $this->escape($this->translate('customer')); ?></td>
            <td class="project"><?php echo $this->escape($this->translate('project')); ?></td>
            <td class="activity"><?php echo $this->escape($this->translate('activity')); ?></td>
            <td class="description"><?php echo $this->escape($this->translate('description')); ?></td>
            <?php if ($this->showTrackingNumber) { ?>
                <td class="trackingnumber"><?php echo $this->escape($this->translate('trackingNumber')); ?></td>
            <?php } ?>
            <td class="username"><?php echo $this->escape($this->translate('username')); ?></td>
        </tr>
        </tbody>
    </table>
</div>
<div id="timeSheet"><?php echo $this->timeSheet_display; ?></div>
<script type="text/javascript">
    $(document).ready(function () {
        ts_ext_onload();
    });
</script>
