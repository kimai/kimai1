{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            expense_extension_onload();
        }); 
    </script>
{/literal}

<div id="expenses_head">
    <div class="left">
    {if $kga.user}
        <a href="#" onClick="floaterShow('../extensions/ki_expenses/floaters.php','add_edit_record',0,0,600,300); $(this).blur(); return false;">{$kga.lang.add}</a>
    {/if}
    </div>
    <table>
        <colgroup>
          <col class="options" />
          <col class="date" />
          <col class="time" />
          <col class="value" />
          <col class="refundable" />
          <col class="customer" />
          <col class="project" />
          <col class="designation" />
              <col class="username" />
        </colgroup>
        <tbody>
            <tr>
                <td class="option">&nbsp;</td>
                <td class="date">{$kga.lang.datum}</td>
                <td class="time">{$kga.lang.timelabel}</td>
                <td class="value">{$kga.lang.expense}</td>
                <td class="refundable">{$kga.lang.refundable}</td>
                <td class="customer">{$kga.lang.customer}</td>
                <td class="project">{$kga.lang.project}</td>
                <td class="designation">{$kga.lang.designation}</td>
                <td class="username">{$kga.lang.username}</td>
            </tr>
        </tbody>
    </table>
</div>

<div id="expenses">{$expenses_display} </div>