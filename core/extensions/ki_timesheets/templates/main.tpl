{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            ts_ext_onload();
        }); 
    </script>
{/literal}

<div id="zef_head">
    <div class="left">
        <a href="#" onClick="floaterShow('../extensions/ki_timesheets/floaters.php','add_edit_record',0,0,600,570); return false;">{$kga.lang.add}</a>
    </div>
    <table>
        <colgroup>
          <col class="options" />
          <col class="date" />
          <col class="from" />
          <col class="to" />
          <col class="time" />
          <col class="knd" />
          <col class="pct" />
          <col class="evt" />
        </colgroup>
        <tbody>
            <tr>
                <td class="option">&nbsp;</td>
                <td class="date">{$kga.lang.datum}</td>
                <td class="from">{$kga.lang.in}</td>
                <td class="to">{$kga.lang.out}</td>
                <td class="time">{$kga.lang.time}</td>
                <td class="knd">{$kga.lang.knd}</td>
                <td class="pct">{$kga.lang.pct}</td>
                <td class="evt">{$kga.lang.evt}</td>
            </tr>
        </tbody>
    </table>
</div>

<div id="zef">{$zef_display} </div>

<div id="knd_head">
        <input class="livefilterfield" onkeyup="filter_lists('knd', this.value);" type="text" id="filt_knd" name="filt_knd"/>
    {$kga.lang.knds} 
    
        
{if $kga.usr.usr_sts != 2 }    
    <div class="right">
        <a href="#" onClick="floaterShow('floaters.php','add_edit_knd',0,0,450,200); return false;">{$kga.lang.add}</a>
    </div>
{/if}
</div>

<div id="pct_head">
        <input class="livefilterfield" onkeyup="filter_lists('pct', this.value);" type="text" id="filt_pct" name="filt_pct"/>
    {$kga.lang.pcts}
    
    
{if $kga.usr.usr_sts != 2 }  
    <div class="right">
        <a href="#" onClick="floaterShow('floaters.php','add_edit_pct',0,0,450,200); return false;">{$kga.lang.add}</a>
    </div>
{/if}
</div>

<div id="evt_head">
        <input class="livefilterfield" onkeyup="filter_lists('evt', this.value);" type="text" id="filt_evt" name="filt_evt"/>
    {$kga.lang.evts}
    
    
{if $kga.usr.usr_sts != 2 } 
    <div class="right">
        <a href="#" onClick="floaterShow('floaters.php','add_edit_evt',0,0,450,200); return false;">{$kga.lang.add}</a>
    </div>
{/if}
</div>

<div id="knd">{$knd_display}</div>
<div id="pct">{$pct_display}</div>
<div id="evt">{$evt_display}</div>

<div id="zefShrink">&nbsp;</div>
<div id="kndShrink">&nbsp;</div>

