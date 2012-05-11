{cycle values="odd,even" reset=true print=false}
{if $arr_zef}
        <div id="zeftable">
        
          <table>
              
            <colgroup>
              <col class="option" />
              <col class="date" />
              <col class="from" />
              <col class="to" />
              <col class="time" />
              <col class="wage" />
              <col class="client" />
              <col class="project" />
              <col class="action" />
              <col class="trackingnumber" />
              <col class="username" />
            </colgroup>

            <tbody>

{assign var="latest_running_task" value="-1"}
{assign var="time_buffer" value="0"}
{assign var="day_buffer" value="0"}
{assign var="start_buffer" value=0}
                
{section name=row loop=$arr_zef}

{*Assign initial value to time buffer which must be larger than or equal to "end"*}
{if $time_buffer==0}
{assign var="time_buffer" value=$arr_zef[row].end}
{/if}

{if $arr_zef[row].end}                
                <tr id="zefEntry{$arr_zef[row].timeEntryID}" class="{cycle values="odd,even"}">
{else}

{if $latest_running_task == -1}
  {assign var="latest_running_task" value=$arr_zef[row].timeEntryID}
{/if}
                <tr id="zefEntry{$arr_zef[row].timeEntryID}" class="{cycle values="odd,even"} active">
{/if}
               
                    <td nowrap class="option 
                                            {if $arr_zef[row].end > $time_buffer}
                                                {if $showOverlapLines}time_overlap{/if}
                                            {elseif $arr_zef[row].start|date_format:"%d" != $day_buffer}
                                                {if $kga.show_daySeperatorLines}break_day{/if}
                                            {elseif $arr_zef[row].end != $start_buffer}
                                                {if $kga.show_gabBreaks}break_gap{/if}
                                            {/if}
                    ">

{* only users can see options *}
{if $kga.usr}

                        
  {*Stop oder Record Button?*}
  {if $arr_zef[row].end}


  {*--OPTIONS----------------------------------------------------*}

                        {if $kga.show_RecordAgain}{strip}<a href ='#' class='recordAgain' onClick="ts_ext_recordAgain({$arr_zef[row].projectID},{$arr_zef[row].activityID},{$arr_zef[row].timeEntryID}); return false;">
                            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/button_recordthis.gif' width='13' height='13' alt='{$kga.lang.recordAgain}' title='{$kga.lang.recordAgain} (ID:{$arr_zef[row].timeEntryID})' border='0' />
                        </a>{/strip}{/if}


  {else}


                        {strip}<a href ='#' class='stop' onClick="ts_ext_stopRecord({$arr_zef[row].timeEntryID}); return false;">
                            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/button_stopthis.gif' width='13' height='13' alt='{$kga.lang.stop}' title='{$kga.lang.stop} (ID:{$arr_zef[row].timeEntryID})' border='0' />
                        </a>{/strip}

  {/if}

                        
  {*Edit Record Button*}
  {if $kga.conf.editLimit == "-" || time()-$arr_zef[row].end <= $kga.conf.editLimit}
                        {strip}<a href ='#' onClick="editRecord({$arr_zef[row].timeEntryID}); $(this).blur(); return false;" title='{$kga.lang.edit}'>
                            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit}' border='0' />
                        </a>{/strip}
  {/if} 

  {* quick erase trashcan *}
  {if $kga.conf.quickdelete > 0}
                      {strip}<a href ='#' class='quickdelete' onClick="quickdelete({$arr_zef[row].timeEntryID}); return false;">
                          <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan.png' width='13' height='13' alt='{$kga.lang.quickdelete}' title='{$kga.lang.quickdelete}' border=0 />
                      </a>{/strip}
  {/if}

{/if}

{*--/OPTIONS----------------------------------------------------*}
                    
                    </td>












                   

{*datum --------------------------------------------------------*}

                    <td class="date
                      {if $arr_zef[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $arr_zef[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $arr_zef[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                        {$arr_zef[row].start|date_format:$kga.date_format.1|escape:'html'}
                    </td>

{*in -----------------------------------------------------------*}

                    <td class="from
                      {if $arr_zef[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $arr_zef[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $arr_zef[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                        {$arr_zef[row].start|date_format:"%H:%M"|escape:'html'}
                    </td>

{*out ----------------------------------------------------------*}

                    <td class="to
                      {if $arr_zef[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $arr_zef[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $arr_zef[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                    
{if $arr_zef[row].end}
                        {$arr_zef[row].end|date_format:"%H:%M"|escape:'html'}
{else}                     
                        &ndash;&ndash;:&ndash;&ndash;
{/if}
                    </td>

{*task time ----------------------------------------------------*}

                    <td class="time
                      {if $arr_zef[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $arr_zef[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $arr_zef[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                    
{if $arr_zef[row].duration}
                    
                        {$arr_zef[row].formattedDuration}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>

{*task wage ----------------------------------------------------*}

                    <td class="wage
                      {if $arr_zef[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $arr_zef[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $arr_zef[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                    
{if $arr_zef[row].wage}
                    
                        {$arr_zef[row].wage|replace:'.':$kga.conf.decimalSeparator|escape:'html'}
                      
{else}  
                        &ndash;
{/if}
                    </td>

{*client name --------------------------------------------------*}

                    <td class="knd
                      {if $arr_zef[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $arr_zef[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $arr_zef[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                        {$arr_zef[row].customerName|escape:'html'}
                    </td>

{*project name -------------------------------------------------*}

                    <td class="pct
                      {if $arr_zef[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $arr_zef[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $arr_zef[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                        
                        <a href ="#" class="preselect_lnk" 
                            onClick="buzzer_preselect('pct',{$arr_zef[row].projectID},'{$arr_zef[row].projectName|replace:"'":"\\'"|escape:'html'}',{$arr_zef[row].customerID},'{$arr_zef[row].customerName|replace:"'":"\\'"|escape:'html'}'); 
                            return false;">
                            {$arr_zef[row].projectName|escape:'html'}
                            {if $kga.conf.pct_comment_flag == 1}
                                {if $arr_zef[row].projectComment}
                                    <span class="lighter">({$arr_zef[row].projectComment|escape:'html'})</span>
                                {/if}
                            {/if}
                        </a>
                    </td>


{*event name and comment bubble --------------------------------*}

                    <td class="evt
                      {if $arr_zef[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $arr_zef[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $arr_zef[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                        
                        <a href ="#" class="preselect_lnk" 
                            onClick="buzzer_preselect('evt',{$arr_zef[row].activityID},'{$arr_zef[row].activityName|replace:"'":"\\'"|escape:'html'}',0,0); 
                            return false;">
                            {$arr_zef[row].activityName|escape:'html'} 
                        </a>
                        
{if $arr_zef[row].comment}
    {if $arr_zef[row].commentType == '0'}
                        <a href="#" onClick="ts_comment({$arr_zef[row].timeEntryID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/blase.gif' width="12" height="13" title='{$arr_zef[row].comment|escape:'html'}' border="0" /></a>
    {/if}
    {if $arr_zef[row].commentType == '1'}
                        <a href="#" onClick="ts_comment({$arr_zef[row].timeEntryID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/blase_sys.gif' width="12" height="13" title='{$arr_zef[row].comment|escape:'html'}' border="0" /></a>
    {/if}
    {if $arr_zef[row].commentType == '2'}
                        <a href="#" onClick="ts_comment({$arr_zef[row].timeEntryID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/blase_caution.gif' width="12" height="13" title='{$arr_zef[row].comment|escape:'html'}' border="0" /></a>
    {/if}
{/if}
                    </td>

                    <td class="trackingnumber
                      {if $arr_zef[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $arr_zef[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $arr_zef[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                    {$arr_zef[row].trackingNumber|escape:'html'}
                    </td>

                    <td class="username
                      {if $arr_zef[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $arr_zef[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $arr_zef[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                    {$arr_zef[row].userName|escape:'html'}
                    </td>

                </tr>

{if $arr_zef[row].comment}                
                <tr id="c{$arr_zef[row].timeEntryID}" class="comm{$arr_zef[row].commentType|escape:'html'}" {if $hideComments}style="display:none;"{/if}>
                    <td colspan="11">{$arr_zef[row].comment|escape:'html'|nl2br}</td>
                </tr>
{/if}

{assign var="day_buffer" value=$arr_zef[row].start|date_format:"%d"}
{assign var="start_buffer" value=$arr_zef[row].start}
{assign var="time_buffer" value=$arr_zef[row].start}
               
{/section}
                
            </tbody>   
        </table>
    </div>  
{else}
<div style='padding:5px;color:#f00'>
    <strong>{$kga.lang.noEntries}</strong>
</div>
{/if}

<script type="text/javascript"> 
    ts_usr_ann = null;
    ts_knd_ann = null;
    ts_pct_ann = null;
    ts_evt_ann = null;
    ts_total = '{$total}';

    { if $usr_ann }
    ts_usr_ann = new Array();
    {foreach key=id item=value from=$usr_ann}
      ts_usr_ann[{$id}] = '{$value}';
    {/foreach}
    {/if}

    { if $knd_ann }
    ts_knd_ann = new Array();
    {foreach key=id item=value from=$knd_ann}
      ts_knd_ann[{$id}] = '{$value}';
    {/foreach}
    {/if}

    { if $pct_ann }
    ts_pct_ann = new Array();
    {foreach key=id item=value from=$pct_ann}
      ts_pct_ann[{$id}] = '{$value}';
    {/foreach}
    {/if}

    { if $evt_ann }
    ts_evt_ann = new Array();
    {foreach key=id item=value from=$evt_ann}
      ts_evt_ann[{$id}] = '{$value}';
    {/foreach}
    {/if}
    
    {literal}
    lists_update_annotations(parseInt($('#gui div.ki_timesheet').attr('id').substring(7)),ts_usr_ann,ts_knd_ann,ts_pct_ann,ts_evt_ann);
    $('#display_total').html(ts_total);
    {/literal}

  {if $latest_running_task == -1}
    updateRecordStatus(false);
  {else}
    updateRecordStatus({$latest_running_task},{$arr_zef[0].start},
                             {$arr_zef[0].customerID},'{$arr_zef[0].customerName|replace:"'":"\\'"|escape:'html'}',
                             {$arr_zef[0].projectID},'{$arr_zef[0].projectName|replace:"'":"\\'"|escape:'html'}',
                             {$arr_zef[0].activityID},'{$arr_zef[0].activityName|replace:"'":"\\'"|escape:'html'}');
  {/if}
    
</script>