<div id="export_head">
    <table>
        <tbody>
        <tr>
            <td class="date <?php if (isset($this->disabled_columns['date'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('date');" title="<?php echo $this->translate('datum') ?>"><?php echo $this->ellipsis($this->translate('datum'), 5) ?></a>
            </td>
            <td class="from <?php if (isset($this->disabled_columns['from'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('from');" title="<?php echo $this->translate('in') ?>"><?php echo $this->ellipsis($this->translate('in'), 5) ?></a>
            </td>
            <td class="to <?php if (isset($this->disabled_columns['to'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('to');" title="<?php echo $this->translate('out') ?>"><?php echo $this->ellipsis($this->translate('out'), 5) ?></a>
            </td>
            <td class="time <?php if (isset($this->disabled_columns['time'])):?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('time');" title="<?php echo $this->translate('time') ?>"><?php echo $this->ellipsis($this->translate('time'), 4) ?></a>
            </td>
            <td class="dec_time <?php if (isset($this->disabled_columns['dec_time'])):?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('dec_time');" title="<?php echo $this->translate('timelabel') ?>"><?php echo $this->ellipsis($this->translate('timelabel'), 4) ?></a>
            </td>
            <td class="rate <?php if (isset($this->disabled_columns['rate'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('rate');" title="<?php echo $this->translate('rate_short') ?>"><?php echo $this->ellipsis($this->translate('rate_short'), 5) ?></a>
            </td>
            <td class="wage <?php if (isset($this->disabled_columns['wage'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('wage');" title="<?php echo $this->translate('total') ?>"><?php echo $this->ellipsis($this->translate('total'), 5) ?></a>
            </td>
            <td class="budget <?php if (isset($this->disabled_columns['budget'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('budget');" title="<?php echo $this->translate('budget') ?>"><?php echo $this->ellipsis($this->translate('budget'), 5) ?></a>
            </td>
            <td class="approved <?php if (isset($this->disabled_columns['approved'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('approved');" title="<?php echo $this->translate('approved') ?>"><?php echo $this->ellipsis($this->translate('approved'), 4) ?></a>
            </td>
            <td class="status <?php if (isset($this->disabled_columns['status'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('status');" title="<?php echo $this->translate('status') ?>"><?php echo $this->ellipsis($this->translate('status'), 4) ?></a>
            </td>
            <td class="billable <?php if (isset($this->disabled_columns['billable'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('billable');" title="<?php echo $this->translate('billable') ?>"><?php echo $this->ellipsis($this->translate('billable'), 3) ?></a>
            </td>
            <td class="customer <?php if (isset($this->disabled_columns['customer'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('customer');" title="<?php echo $this->translate('customer') ?>"><?php echo $this->ellipsis($this->translate('customer'), 12) ?></a>
            </td>
            <td class="project <?php if (isset($this->disabled_columns['project'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('project');" title="<?php echo $this->translate('project') ?>"><?php echo $this->ellipsis($this->translate('project'), 8) ?></a>
            </td>
            <td class="activity <?php if (isset($this->disabled_columns['activity'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('activity');" title="<?php echo $this->translate('activity') ?>"><?php echo $this->ellipsis($this->translate('activity'), 21) ?></a>
            </td>
            <td class="description <?php if (isset($this->disabled_columns['description'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('description');" title="<?php echo $this->translate('description') ?>"><?php echo $this->ellipsis($this->translate('description'), 13) ?></a>
            </td>
            <td class="comment <?php if (isset($this->disabled_columns['comment'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('comment');" title="<?php echo $this->translate('comment') ?>"><?php echo $this->ellipsis($this->translate('comment'), 3) ?></a>
            </td>
            <td class="location <?php if (isset($this->disabled_columns['location'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('location');" title="<?php echo $this->translate('location') ?>"><?php echo $this->ellipsis($this->translate('location'), 3) ?></a>
            </td>
            <td class="trackingNumber <?php if (isset($this->disabled_columns['trackingNumber'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('trackingNumber');" title="<?php echo $this->translate('trackingNumber') ?>"><?php echo $this->ellipsis($this->translate('trackingNumber'), 3) ?></a>
            </td>
            <td class="user <?php if (isset($this->disabled_columns['user'])): ?>disabled<?php endif; ?>">
                <a onclick="export_toggle_column('user');" title="<?php echo $this->translate('username') ?>"><?php echo $this->ellipsis($this->translate('username'), 4) ?></a>
            </td>
            <td class="cleared">
                <a title="<?php echo $this->translate('export_extension:cleared') ?>" onclick="if (export_toogle_cleared_confirm()) { $('#xptable td.cleared>a').click(); } return false;">invert</a>
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