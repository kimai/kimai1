{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            xp_ext_onload();
        }); 
    </script>
{/literal}


<div id="xp_head">
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
          <col class="knd" />
          <col class="pct" />
          <col class="evt" />
          <col class="comment" />
          <col class="location" />
          <col class="trackingnr" />
          <col class="user" />
        </colgroup>
        <tbody>
            <tr>
                <td class="date {if $disabled_columns.date}disabled{/if}"><a onClick="xp_toggle_column('date');">{$kga.lang.datum}</a></td>
                <td class="from {if $disabled_columns.from}disabled{/if}"><a onClick="xp_toggle_column('from');">{$kga.lang.in}</a></td>
                <td class="to {if $disabled_columns.to}disabled{/if}"><a onClick="xp_toggle_column('to');">{$kga.lang.out}</a></td>
                <td class="time {if $disabled_columns.time}disabled{/if}"><a onClick="xp_toggle_column('time');">{$kga.lang.time}</a></td>
                <td class="dec_time {if $disabled_columns.dec_time}disabled{/if}"><a onClick="xp_toggle_column('dec_time');">{$kga.lang.timelabel}</a></td>
                <td class="rate"><a class="rate {if $disabled_columns.rate}disabled{/if}" onClick="xp_toggle_column('rate');">{$kga.lang.rate_short}</a></td>
                <td class="wage"><a class="wage {if $disabled_columns.wage}disabled{/if}" onClick="xp_toggle_column('wage');">{$kga.lang.total}</a></td>
                <td class="knd {if $disabled_columns.knd}disabled{/if}"><a onClick="xp_toggle_column('knd');">{$kga.lang.knd}</a></td>
                <td class="pct {if $disabled_columns.pct}disabled{/if}"><a onClick="xp_toggle_column('pct');">{$kga.lang.pct}</a></td>
                <td class="evt {if $disabled_columns.evt}disabled{/if}"><a onClick="xp_toggle_column('evt');">{$kga.lang.evt}</a></td>

                <td class="moreinfo" colspan="3">
					<a class="comment {if $disabled_columns.comment}disabled{/if}" onClick="xp_toggle_column('comment');">{$kga.lang.comment}</a>,
	                <a class="location {if $disabled_columns.location}disabled{/if}" onClick="xp_toggle_column('location');">{$kga.lang.zlocation}</a>,
	                <a class="trackingnr {if $disabled_columns.trackingnr}disabled{/if}" onClick="xp_toggle_column('trackingnr');">{$kga.lang.trackingnr}</a>
				</td>
				
                <td class="user {if $disabled_columns.user}disabled{/if}"><a onClick="xp_toggle_column('user');">{$kga.lang.username}</a></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="xp">{$table_display} </div>
