<div id="timeSheet_head">
    <div class="left">
        <?php if (isset($this->kga['user'])): ?>
            <a href="#" onclick="floaterShow('../extensions/ki_timesheets/floaters.php','add_edit_timeSheetEntry',selected_project+'|'+selected_activity,0,650); $(this).blur(); return false;"><?php echo $this->kga['lang']['add'] ?></a>
        <?php endif; ?>
    </div>
    <table>
        <colgroup>
            <col class="options"/>
            <col class="date"/>
            <col class="from"/>
            <col class="to"/>
            <col class="time"/>
            <?php if ($this->showRates): ?>
                <col class="wage"/>
            <?php endif; ?>
            <col class="customer"/>
            <col class="project"/>
            <col class="activity"/>
            <?php if ($this->showTrackingNumber) { ?>
                <col class="description"/>
                <col class="trackingnumber"/>
            <?php } ?>
            <col class="username"/>
        </colgroup>
        <tbody>
        <tr>
            <td class="option">&nbsp;</td>
            <td class="date"><?php echo $this->kga['lang']['datum'] ?></td>
            <td class="from"><?php echo $this->kga['lang']['in'] ?></td>
            <td class="to"><?php echo $this->kga['lang']['out'] ?></td>
            <td class="time"><?php echo $this->kga['lang']['time'] ?></td>
            <?php if ($this->showRates): ?>
                <td class="wage"><?php echo $this->kga['lang']['wage'] ?></td>
            <?php endif; ?>
            <td class="customer"><?php echo $this->kga['lang']['customer'] ?></td>
            <td class="project"><?php echo $this->kga['lang']['project'] ?></td>
            <td class="activity"><?php echo $this->kga['lang']['activity'] ?></td>
            <?php if ($this->showTrackingNumber) { ?>
                <td class="description"><?php echo $this->kga['lang']['description'] ?></td>
                <td class="trackingnumber"><?php echo $this->kga['lang']['trackingNumber'] ?></td>
            <?php } ?>
            <td class="username"><?php echo $this->kga['lang']['username'] ?></td>
        </tr>
        </tbody>
    </table>
</div>
<div id="timeSheet"><?php echo $this->timeSheet_display ?> </div>
<script type="text/javascript">
    $(document).ready(function () {
        ts_ext_onload();
    });
</script>