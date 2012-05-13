{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            export_extension_onload();
        }); 
    </script>
{/literal}


<div id="export_head">
    <div class="right">
	        <a href="#" onClick="$('#xptable td.cleared>a').click(); return false;">invert</a>
    </div>

    <table>
        <colgroup>
          <col class="date" />
          <col class="from" />
          <col class="to" />
          <col class="time" />
          <col class="dec_time" />
          <col class="rate" />
          <col class="wage" />
          <col class="budget" />
          <col class="approved" />
          <col class="status" />
          <col class="billable" />
          <col class="customer" />
          <col class="project" />
          <col class="activity" />
          <col class="description" />
          <col class="comment" />
          <col class="location" />
          <col class="trackingNumber" />
          <col class="user" />
        </colgroup>
        <tbody>
            <tr>
                <td class="date {if $disabled_columns.date}disabled{/if}"><a onClick="export_toggle_column('date');">{$kga.lang.datum}</a></td>
                <td class="from {if $disabled_columns.from}disabled{/if}"><a onClick="export_toggle_column('from');">{$kga.lang.in}</a></td>
                <td class="to {if $disabled_columns.to}disabled{/if}"><a onClick="export_toggle_column('to');">{$kga.lang.out}</a></td>
                <td class="time {if $disabled_columns.time}disabled{/if}"><a onClick="export_toggle_column('time');">{$kga.lang.time}</a></td>
                <td class="dec_time {if $disabled_columns.dec_time}disabled{/if}"><a onClick="export_toggle_column('dec_time');">{$kga.lang.timelabel}</a></td>
                <td class="rate"><a class="rate {if $disabled_columns.rate}disabled{/if}" onClick="export_toggle_column('rate');">{$kga.lang.rate_short}</a></td>
                <td class="wage"><a class="wage {if $disabled_columns.wage}disabled{/if}" onClick="export_toggle_column('wage');">{$kga.lang.total}</a></td>
                <td class="budget {if $disabled_columns.budget}disabled{/if}"><a onClick="export_toggle_column('budget');">{$kga.lang.budget}</a></td>
                <td class="approved {if $disabled_columns.approved}disabled{/if}"><a onClick="export_toggle_column('approved');">{$kga.lang.approved}</a></td>
                <td class="status {if $disabled_columns.status}disabled{/if}"><a onClick="export_toggle_column('status);">{$kga.lang.status}</a></td>
                <td class="billable {if $disabled_columns.billable}disabled{/if}"><a onClick="export_toggle_column('billable');">{$kga.lang.billable}</a></td>
                <td class="customer {if $disabled_columns.customer}disabled{/if}"><a onClick="export_toggle_column('customer');">{$kga.lang.customer}</a></td>
                <td class="project {if $disabled_columns.project}disabled{/if}"><a onClick="export_toggle_column('project');">{$kga.lang.project}</a></td>
                <td class="activity {if $disabled_columns.activity}disabled{/if}"><a onClick="export_toggle_column('activity');">{$kga.lang.activity}</a></td>
                <td class="description {if $disabled_columns.description}disabled{/if}"><a onClick="export_toggle_column('description');">{$kga.lang.description}</a></td>

                <td class="moreinfo" colspan="3">
					<a class="comment {if $disabled_columns.comment}disabled{/if}" onClick="export_toggle_column('comment');">{$kga.lang.comment}</a>,
	                <a class="location {if $disabled_columns.location}disabled{/if}" onClick="export_toggle_column('location');">{$kga.lang.location}</a>,
	                <a class="trackingNumber {if $disabled_columns.trackingNumber}disabled{/if}" onClick="export_toggle_column('trackingNumber');">{$kga.lang.trackingNumber}</a>
				</td>
				
                <td class="user {if $disabled_columns.user}disabled{/if}"><a onClick="export_toggle_column('user');">{$kga.lang.username}</a></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="xp">{$table_display} </div>
