{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            xp_ext_onload();
        }); 
    </script>
{/literal}

<div id="xp_head">
    <div class="right">
	        <a href="#" onClick="alert('INVERT!'); return false;">invert</a>
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
          <!-- <col class="cleared" /> -->
        </colgroup>
        <tbody>
            <tr>
                <td class="date"><a onClick="xp_toggle_column('date');">{$kga.lang.datum}</a></td>
                <td class="from"><a onClick="xp_toggle_column('from');">{$kga.lang.in}</a></td>
                <td class="to"><a onClick="xp_toggle_column('to');">{$kga.lang.out}</a></td>
                <td class="time"><a onClick="xp_toggle_column('time');">{$kga.lang.time}</a></td>
                <td class="dec_time"><a onClick="xp_toggle_column('dec_time');">{$kga.lang.timelabel}</a></td>
                <td class="rate"><a onClick="xp_toggle_column('rate');">{$kga.lang.rate}</a></td>
                <td class="wage"><a onClick="xp_toggle_column('wage');">Euro</a></td>
                <td class="knd"><a onClick="xp_toggle_column('knd');">{$kga.lang.knd}</a></td>
                <td class="pct"><a onClick="xp_toggle_column('pct');">{$kga.lang.pct}</a></td>
                <td class="evt"><a onClick="xp_toggle_column('evt');">{$kga.lang.evt}</a></td>
                <td class="comment"><a onClick="xp_toggle_column('comment');">{$kga.lang.comment}</a></td>
                <td class="location"><a onClick="xp_toggle_column('location');">{$kga.lang.zlocation}</a></td>
                <td class="trackingnr"><a onClick="xp_toggle_column('trackingnr');">{$kga.lang.trackingnr}</a></td>
                <td class="user"><a onClick="xp_toggle_column('user');">{$kga.lang.username}</a></td>
                <!-- <td class="evt">cleared</td> -->
            </tr>
        </tbody>
    </table>
</div>

<div id="xp">{$table_display} </div>
