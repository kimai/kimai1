{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            xp_ext_onload();
        }); 
    </script>
{/literal}

<div id="xp_head">
    <div class="left">
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
            </tr>
        </tbody>
    </table>
</div>

<div id="xp">{$table_display} </div>
