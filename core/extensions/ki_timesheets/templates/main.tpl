{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            ts_ext_onload();
        }); 
    </script>
{/literal}

<div id="timeSheet_head">
    <div class="left">
    {if $kga.user}
        <a href="#" onClick="floaterShow('../extensions/ki_timesheets/floaters.php','add_edit_timeSheetEntry',selected_project+'|'+selected_activity,0,650,580); $(this).blur(); return false;">{$kga.lang.add}</a>
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
          <col class="customer" />
          <col class="project" />
          <col class="activity" />
          <col class="trackingnumber" />
          <col class="username" />
        </colgroup>
        <tbody>
            <tr>
                <td class="option">&nbsp;</td>
                <td class="date">{$kga.lang.datum}</td>
                <td class="from">{$kga.lang.in}</td>
                <td class="to">{$kga.lang.out}</td>
                <td class="time">{$kga.lang.time}</td>
                <td class="wage">{$kga.lang.wage}</td>
                <td class="customer">{$kga.lang.customer}</td>
                <td class="project">{$kga.lang.project}</td>
                <td class="activity">{$kga.lang.activity}</td>
                <td class="trackingnumber">{$kga.lang.trackingNumber}</td>
                <td class="username">{$kga.lang.username}</td>
            </tr>
        </tbody>
    </table>
</div>

<div id="timeSheet">{$timeSheet_display} </div>
