{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            ts_ext_onload();
        }); 
    </script>
{/literal}

<div id="zef_head">
    <div class="left">
    {if $kga.usr}
        <a href="#" onClick="floaterShow('../extensions/ki_timesheets/floaters.php','add_edit_record',selected_pct+'|'+selected_evt,0,600,380); $(this).blur(); return false;">{$kga.lang.add}</a>
    {/if}
    </div>
    <table>
        <colgroup>
          <col class="options" />
          <col class="date" />
          <col class="from" />
          <col class="to" />
          <col class="time" />
          <col class="wage" />
          <col class="knd" />
          <col class="pct" />
          <col class="evt" />
          <col class="trackingnumber" />
        </colgroup>
        <tbody>
            <tr>
                <td class="option">&nbsp;</td>
                <td class="date">{$kga.lang.datum}</td>
                <td class="from">{$kga.lang.in}</td>
                <td class="to">{$kga.lang.out}</td>
                <td class="time">{$kga.lang.time}</td>
                <td class="wage">{$kga.lang.wage}</td>
                <td class="knd">{$kga.lang.knd}</td>
                <td class="pct">{$kga.lang.pct}</td>
                <td class="evt">{$kga.lang.evt}</td>
                <td class="trackingnumber">{$kga.lang.trackingnr}</td>
            </tr>
        </tbody>
    </table>
</div>

<div id="zef">{$zef_display} </div>
