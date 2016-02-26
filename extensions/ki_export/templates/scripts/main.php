<div id="export_head">
    <div class="right">
        <a href="#" onclick="$('#xptable td.cleared>a').click(); return false;">invert</a>
    </div>
    <table>
        <colgroup>
            <col class="date"/>
            <col class="from"/>
            <col class="to"/>
            <col class="time"/>
            <col class="dec_time"/>
            <col class="rate"/>
            <col class="wage"/>
            <col class="budget"/>
            <col class="approved"/>
            <col class="status"/>
            <col class="billable"/>
            <col class="customer"/>
            <col class="project"/>
            <col class="activity"/>
            <col class="description"/>
            <col class="comment"/>
            <col class="location"/>
            <col class="trackingNumber"/>
            <col class="user"/>
        </colgroup>
        <tbody>
        <tr>
            <td class="date <?php if (isset($this->disabled_columns['date'])): ?> disabled <?php endif; ?>">
                <a onclick="export_toggle_column('date');"><?php echo $this->kga['lang']['datum'] ?></a></td>
            <td class="from <?php if (isset($this->disabled_columns['from'])): ?> disabled <?php endif; ?>">
                <a onclick="export_toggle_column('from');"><?php echo $this->kga['lang']['in'] ?></a></td>
            <td class="to <?php if (isset($this->disabled_columns['to'])): ?> disabled <?php endif; ?>">
                <a onclick="export_toggle_column('to');"><?php echo $this->kga['lang']['out'] ?></a></td>
            <td class="time"><a class="time <?php if (isset($this->disabled_columns['time'])):?> disabled <?php endif; ?>" onClick="export_toggle_column('time');"><?php echo $this->kga['lang']['time']?></a></td>
            <td class="dec_time"><a class="dec_time <?php if (isset($this->disabled_columns['dec_time'])):?> disabled <?php endif; ?>" onClick="export_toggle_column('dec_time');"><?php echo $this->kga['lang']['timelabel']?></a></td>
            <td class="rate">
                <a class="rate <?php if (isset($this->disabled_columns['rate'])): ?> disabled <?php endif; ?>" onclick="export_toggle_column('rate');"><?php echo $this->kga['lang']['rate_short'] ?></a>
            </td>
            <td class="wage">
                <a class="wage <?php if (isset($this->disabled_columns['wage'])): ?> disabled <?php endif; ?>" onclick="export_toggle_column('wage');"><?php echo $this->kga['lang']['total'] ?></a>
            </td>
            <td class="budget <?php if (isset($this->disabled_columns['budget'])): ?> disabled <?php endif; ?>">
                <a onclick="export_toggle_column('budget');"><?php echo $this->kga['lang']['budget'] ?></a></td>
            <td class="approved <?php if (isset($this->disabled_columns['approved'])): ?> disabled <?php endif; ?>">
                <a onclick="export_toggle_column('approved');"><?php echo $this->kga['lang']['approved'] ?></a></td>
            <td class="status <?php if (isset($this->disabled_columns['status'])): ?> disabled <?php endif; ?>">
                <a onclick="export_toggle_column('status');"><?php echo $this->kga['lang']['status'] ?></a></td>
            <td class="billable <?php if (isset($this->disabled_columns['billable'])): ?> disabled <?php endif; ?>">
                <a onclick="export_toggle_column('billable');"><?php echo $this->kga['lang']['billable'] ?></a></td>
            <td class="customer <?php if (isset($this->disabled_columns['customer'])): ?> disabled <?php endif; ?>">
                <a onclick="export_toggle_column('customer');"><?php echo $this->kga['lang']['customer'] ?></a></td>
            <td class="project <?php if (isset($this->disabled_columns['project'])): ?> disabled <?php endif; ?>">
                <a onclick="export_toggle_column('project');"><?php echo $this->kga['lang']['project'] ?></a></td>
            <td class="activity <?php if (isset($this->disabled_columns['activity'])): ?> disabled <?php endif; ?>">
                <a onclick="export_toggle_column('activity');"><?php echo $this->kga['lang']['activity'] ?></a></td>
            <td class="description <?php if (isset($this->disabled_columns['description'])): ?> disabled <?php endif; ?>">
                <a onclick="export_toggle_column('description');"><?php echo $this->kga['lang']['description'] ?></a>
            </td>
            <td class="moreinfo" colspan="3">
                <a class="comment <?php if (isset($this->disabled_columns['comment'])): ?> disabled <?php endif; ?>" onclick="export_toggle_column('comment');"><?php echo $this->kga['lang']['comment'] ?></a>,
                <a class="location <?php if (isset($this->disabled_columns['location'])): ?> disabled <?php endif; ?>" onclick="export_toggle_column('location');"><?php echo $this->kga['lang']['location'] ?></a>,
                <a class="trackingNumber <?php if (isset($this->disabled_columns['trackingNumber'])): ?> disabled <?php endif; ?>" onclick="export_toggle_column('trackingNumber');"><?php echo $this->kga['lang']['trackingNumber'] ?></a>
            </td>
            <td class="user <?php if (isset($this->disabled_columns['user'])): ?> disabled <?php endif; ?>">
                <a onclick="export_toggle_column('user');"><?php echo $this->kga['lang']['username'] ?></a></td>
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