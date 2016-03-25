<div id="export_head">
    <table>
        <tbody>
        <tr>
            <td class="date <?php if (isset($this->disabled_columns['date'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('date');" title="<?php echo $this->kga['lang']['datum'] ?>"><?php echo $this->ellipsis($this->kga['lang']['datum'], 5) ?></a>
            </td>
            <td class="from <?php if (isset($this->disabled_columns['from'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('from');" title="<?php echo $this->kga['lang']['in'] ?>"><?php echo $this->ellipsis($this->kga['lang']['in'], 5) ?></a>
            </td>
            <td class="to <?php if (isset($this->disabled_columns['to'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('to');" title="<?php echo $this->kga['lang']['out'] ?>"><?php echo $this->ellipsis($this->kga['lang']['out'], 5) ?></a>
            </td>
            <td class="time <?php if (isset($this->disabled_columns['time'])):?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('time');" title="<?php echo $this->kga['lang']['time'] ?>"><?php echo $this->ellipsis($this->kga['lang']['time'], 4) ?></a>
            </td>
            <td class="dec_time <?php if (isset($this->disabled_columns['dec_time'])):?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('dec_time');" title="<?php echo $this->kga['lang']['timelabel'] ?>"><?php echo $this->ellipsis($this->kga['lang']['timelabel'], 4) ?></a>
            </td>
            <td class="rate <?php if (isset($this->disabled_columns['rate'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('rate');" title="<?php echo $this->kga['lang']['rate_short'] ?>"><?php echo $this->ellipsis($this->kga['lang']['rate_short'], 5) ?></a>
            </td>
            <td class="wage <?php if (isset($this->disabled_columns['wage'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('wage');" title="<?php echo $this->kga['lang']['total'] ?>"><?php echo $this->ellipsis($this->kga['lang']['total'], 5) ?></a>
            </td>
            <td class="budget <?php if (isset($this->disabled_columns['budget'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('budget');" title="<?php echo $this->kga['lang']['budget'] ?>"><?php echo $this->ellipsis($this->kga['lang']['budget'], 5) ?></a>
            </td>
            <td class="approved <?php if (isset($this->disabled_columns['approved'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('approved');" title="<?php echo $this->kga['lang']['approved'] ?>"><?php echo $this->ellipsis($this->kga['lang']['approved'], 4) ?></a>
            </td>
            <td class="status <?php if (isset($this->disabled_columns['status'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('status');" title="<?php echo $this->kga['lang']['status'] ?>"><?php echo $this->ellipsis($this->kga['lang']['status'], 4) ?></a>
            </td>
            <td class="billable <?php if (isset($this->disabled_columns['billable'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('billable');" title="<?php echo $this->kga['lang']['billable'] ?>"><?php echo $this->ellipsis($this->kga['lang']['billable'], 3) ?></a>
            </td>
            <td class="customer <?php if (isset($this->disabled_columns['customer'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('customer');" title="<?php echo $this->kga['lang']['customer'] ?>"><?php echo $this->ellipsis($this->kga['lang']['customer'], 12) ?></a>
            </td>
            <td class="project <?php if (isset($this->disabled_columns['project'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('project');" title="<?php echo $this->kga['lang']['project'] ?>"><?php echo $this->ellipsis($this->kga['lang']['project'], 8) ?></a>
            </td>
            <td class="activity <?php if (isset($this->disabled_columns['activity'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('activity');" title="<?php echo $this->kga['lang']['activity'] ?>"><?php echo $this->ellipsis($this->kga['lang']['activity'], 21) ?></a>
            </td>
            <td class="description <?php if (isset($this->disabled_columns['description'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('description');" title="<?php echo $this->kga['lang']['description'] ?>"><?php echo $this->ellipsis($this->kga['lang']['description'], 13) ?></a>
            </td>
            <td class="comment <?php if (isset($this->disabled_columns['comment'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('comment');" title="<?php echo $this->kga['lang']['comment'] ?>"><?php echo $this->ellipsis($this->kga['lang']['comment'], 3) ?></a>
            </td>
            <td class="location <?php if (isset($this->disabled_columns['location'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('location');" title="<?php echo $this->kga['lang']['location'] ?>"><?php echo $this->ellipsis($this->kga['lang']['location'], 3) ?></a>
            </td>
            <td class="trackingNumber <?php if (isset($this->disabled_columns['trackingNumber'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('trackingNumber');" title="<?php echo $this->kga['lang']['trackingNumber'] ?>"><?php echo $this->ellipsis($this->kga['lang']['trackingNumber'], 3) ?></a>
            </td>
            <td class="user <?php if (isset($this->disabled_columns['user'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('user');" title="<?php echo $this->kga['lang']['username'] ?>"><?php echo $this->ellipsis($this->kga['lang']['username'], 4) ?></a>
            </td>
            <td class="cleared">
                <a onclick="$('#xptable td.cleared>a').click(); return false;">invert</a>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div id="xp"><?php echo $this->table_display ?> </div>
<script type="text/javascript">
    $(document).ready(function () {
        export_extension_onload();
    });
</script>