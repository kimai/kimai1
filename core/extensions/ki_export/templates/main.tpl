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
          <col class="wage" />
          <col class="knd" />
          <col class="pct" />
          <col class="evt" />
          <!-- <col class="cleared" /> -->
        </colgroup>
        <tbody>
            <tr>
                <td class="date">{$kga.lang.datum}</td>
                <td class="from">{$kga.lang.in}</td>
                <td class="to">{$kga.lang.out}</td>
                <td class="time">{$kga.lang.time}</td>
                <td class="wage">{$kga.lang.wage}</td>
                <td class="knd">{$kga.lang.knd}</td>
                <td class="pct">{$kga.lang.pct}</td>
                <td class="evt">{$kga.lang.evt}</td>
                <!-- <td class="evt">cleared</td> -->
            </tr>
        </tbody>
    </table>
</div>

<div id="xp">{$table_display} </div>
