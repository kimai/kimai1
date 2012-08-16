{cycle values="odd,even" reset=true print=false}
{if $timeSheetEntries}
        <div id="timeSheetTable">
        
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
              <col class="activity" />
              <col class="trackingnumber" />
              <col class="username" />
            </colgroup>

            <tbody>

{assign var="latest_running_task" value="-1"}
{assign var="time_buffer" value="0"}
{assign var="day_buffer" value="0"}
{assign var="start_buffer" value=0}
                
{section name=row loop=$timeSheetEntries}

{*Assign initial value to time buffer which must be larger than or equal to "end"*}
{if $time_buffer==0}
{assign var="time_buffer" value=$timeSheetEntries[row].end}
{/if}

{if $timeSheetEntries[row].end}                
                <tr id="timeSheetEntry{$timeSheetEntries[row].timeEntryID}" class="{cycle values="odd,even"}">
{else}

{if $latest_running_task == -1}
  {assign var="latest_running_task" value=$timeSheetEntries[row].timeEntryID}
{/if}
                <tr id="timeSheetEntry{$timeSheetEntries[row].timeEntryID}" class="{cycle values="odd,even"} active">
{/if}
               
                    <td nowrap class="option 
                                            {if $timeSheetEntries[row].end > $time_buffer}
                                                {if $showOverlapLines}time_overlap{/if}
                                            {elseif $timeSheetEntries[row].start|date_format:"%d" != $day_buffer}
                                                {if $kga.show_daySeperatorLines}break_day{/if}
                                            {elseif $timeSheetEntries[row].end != $start_buffer}
                                                {if $kga.show_gabBreaks}break_gap{/if}
                                            {/if}
                    ">

{* only users can see options *}
{if $kga.user}

                        
  {*Stop oder Record Button?*}
  {if $timeSheetEntries[row].end}


  {*--OPTIONS----------------------------------------------------*}

                        {if $kga.show_RecordAgain}{strip}<a href ='#' class='recordAgain' onClick="ts_ext_recordAgain({$timeSheetEntries[row].projectID},{$timeSheetEntries[row].activityID},{$timeSheetEntries[row].timeEntryID}); return false;">
                            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/button_recordthis.gif' width='13' height='13' alt='{$kga.lang.recordAgain}' title='{$kga.lang.recordAgain} (ID:{$timeSheetEntries[row].timeEntryID})' border='0' />
                        </a>{/strip}{/if}


  {else}


                        {strip}<a href ='#' class='stop' onClick="ts_ext_stopRecord({$timeSheetEntries[row].timeEntryID}); return false;">
                            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/button_stopthis.gif' width='13' height='13' alt='{$kga.lang.stop}' title='{$kga.lang.stop} (ID:{$timeSheetEntries[row].timeEntryID})' border='0' />
                        </a>{/strip}

  {/if}

                        
  {*Edit Record Button*}
  {if $kga.conf.editLimit == "-" || time()-$timeSheetEntries[row].end <= $kga.conf.editLimit}
                        {strip}<a href ='#' onClick="editRecord({$timeSheetEntries[row].timeEntryID}); $(this).blur(); return false;" title='{$kga.lang.edit}'>
                            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit}' border='0' />
                        </a>{/strip}
  {/if} 

  {* quick erase trashcan *}
  {if $kga.conf.quickdelete > 0}
                      {strip}<a href ='#' class='quickdelete' onClick="quickdelete({$timeSheetEntries[row].timeEntryID}); return false;">
                          <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan.png' width='13' height='13' alt='{$kga.lang.quickdelete}' title='{$kga.lang.quickdelete}' border=0 />
                      </a>{/strip}
  {/if}

{/if}

{*--/OPTIONS----------------------------------------------------*}
                    
                    </td>












                   

{*datum --------------------------------------------------------*}

                    <td class="date
                      {if $timeSheetEntries[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $timeSheetEntries[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $timeSheetEntries[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                        {$timeSheetEntries[row].start|date_format:$kga.date_format.1|escape:'html'}
                    </td>

{*in -----------------------------------------------------------*}

                    <td class="from
                      {if $timeSheetEntries[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $timeSheetEntries[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $timeSheetEntries[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                        {$timeSheetEntries[row].start|date_format:"%H:%M"|escape:'html'}
                    </td>

{*out ----------------------------------------------------------*}

                    <td class="to
                      {if $timeSheetEntries[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $timeSheetEntries[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $timeSheetEntries[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                    
{if $timeSheetEntries[row].end}
                        {$timeSheetEntries[row].end|date_format:"%H:%M"|escape:'html'}
{else}                     
                        &ndash;&ndash;:&ndash;&ndash;
{/if}
                    </td>

{*task time ----------------------------------------------------*}

                    <td class="time
                      {if $timeSheetEntries[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $timeSheetEntries[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $timeSheetEntries[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                    
{if $timeSheetEntries[row].duration}
                    
                        {$timeSheetEntries[row].formattedDuration}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>

{*task wage ----------------------------------------------------*}

                    <td class="wage
                      {if $timeSheetEntries[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $timeSheetEntries[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $timeSheetEntries[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                    
{if $timeSheetEntries[row].wage}
                    
                        {$timeSheetEntries[row].wage|replace:'.':$kga.conf.decimalSeparator|escape:'html'}
                      
{else}  
                        &ndash;
{/if}
                    </td>

{*client name --------------------------------------------------*}

                    <td class="customer
                      {if $timeSheetEntries[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $timeSheetEntries[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $timeSheetEntries[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                        {$timeSheetEntries[row].customerName|escape:'html'}
                    </td>

{*project name -------------------------------------------------*}

                    <td class="project
                      {if $timeSheetEntries[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $timeSheetEntries[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $timeSheetEntries[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                        
                        <a href ="#" class="preselect_lnk" 
                            onClick="buzzer_preselect('project',{$timeSheetEntries[row].projectID},'{$timeSheetEntries[row].projectName|replace:"'":"\\'"|escape:'html'}',{$timeSheetEntries[row].customerID},'{$timeSheetEntries[row].customerName|replace:"'":"\\'"|escape:'html'}'); 
                            return false;">
                            {$timeSheetEntries[row].projectName|escape:'html'}
                            {if $kga.conf.project_comment_flag == 1}
                                {if $timeSheetEntries[row].projectComment}
                                    <span class="lighter">({$timeSheetEntries[row].projectComment|escape:'html'})</span>
                                {/if}
                            {/if}
                        </a>
                    </td>


{*activity name and comment bubble --------------------------------*}

                    <td class="activity
                      {if $timeSheetEntries[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $timeSheetEntries[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $timeSheetEntries[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                        
                        <a href ="#" class="preselect_lnk" 
                            onClick="buzzer_preselect('activity',{$timeSheetEntries[row].activityID},'{$timeSheetEntries[row].activityName|replace:"'":"\\'"|escape:'html'}',0,0); 
                            return false;">
                            {$timeSheetEntries[row].activityName|escape:'html'} 
                        </a>
                        
{if $timeSheetEntries[row].comment}
    {if $timeSheetEntries[row].commentType == '0'}
                        <a href="#" onClick="ts_comment({$timeSheetEntries[row].timeEntryID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/blase.gif' width="12" height="13" title='{$timeSheetEntries[row].comment|escape:'html'}' border="0" /></a>
    {/if}
    {if $timeSheetEntries[row].commentType == '1'}
                        <a href="#" onClick="ts_comment({$timeSheetEntries[row].timeEntryID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/blase_sys.gif' width="12" height="13" title='{$timeSheetEntries[row].comment|escape:'html'}' border="0" /></a>
    {/if}
    {if $timeSheetEntries[row].commentType == '2'}
                        <a href="#" onClick="ts_comment({$timeSheetEntries[row].timeEntryID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/blase_caution.gif' width="12" height="13" title='{$timeSheetEntries[row].comment|escape:'html'}' border="0" /></a>
    {/if}
{/if}
                    </td>

                    <td class="trackingnumber
                      {if $timeSheetEntries[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $timeSheetEntries[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $timeSheetEntries[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                    {$timeSheetEntries[row].trackingNumber|escape:'html'}
                    </td>

                    <td class="username
                      {if $timeSheetEntries[row].end > $time_buffer}
                          {if $showOverlapLines}time_overlap{/if}
                      {elseif $timeSheetEntries[row].start|date_format:"%d" != $day_buffer}
                          {if $kga.show_daySeperatorLines}break_day{/if}
                      {elseif $timeSheetEntries[row].end != $start_buffer}
                          {if $kga.show_gabBreaks}break_gap{/if}
                      {/if}
                    ">
                    {$timeSheetEntries[row].userName|escape:'html'}
                    </td>

                </tr>

{if $timeSheetEntries[row].comment}                
                <tr id="c{$timeSheetEntries[row].timeEntryID}" class="comm{$timeSheetEntries[row].commentType|escape:'html'}" {if $hideComments}style="display:none;"{/if}>
                    <td colspan="11">{$timeSheetEntries[row].comment|escape:'html'|nl2br}</td>
                </tr>
{/if}

{assign var="day_buffer" value=$timeSheetEntries[row].start|date_format:"%d"}
{assign var="start_buffer" value=$timeSheetEntries[row].start}
{assign var="time_buffer" value=$timeSheetEntries[row].start}
               
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
    ts_user_annotations = null;
    ts_customer_annotations = null;
    ts_project_annotations = null;
    ts_activity_annotations = null;
    ts_total = '{$total}';

    { if $user_annotations }
    ts_user_annotations = new Array();
    {foreach key=id item=value from=$user_annotations}
      ts_user_annotations[{$id}] = '{$value}';
    {/foreach}
    {/if}

    { if $customer_annotations }
    ts_customer_annotations = new Array();
    {foreach key=id item=value from=$customer_annotations}
      ts_customer_annotations[{$id}] = '{$value}';
    {/foreach}
    {/if}

    { if $project_annotations }
    ts_project_annotations = new Array();
    {foreach key=id item=value from=$project_annotations}
      ts_project_annotations[{$id}] = '{$value}';
    {/foreach}
    {/if}

    { if $activity_annotations }
    ts_activity_annotations = new Array();
    {foreach key=id item=value from=$activity_annotations}
      ts_activity_annotations[{$id}] = '{$value}';
    {/foreach}
    {/if}
    
    {literal}
    lists_update_annotations(parseInt($('#gui div.ki_timesheet').attr('id').substring(7)),ts_user_annotations,ts_customer_annotations,ts_project_annotations,ts_activity_annotations);
    $('#display_total').html(ts_total);
    {/literal}
    
  {if $latest_running_task == -1 || $latest_running_task == ''}
    updateRecordStatus(false);
  {else}

    updateRecordStatus({$latest_running_task},{$timeSheetEntries[0].start},
                             {$timeSheetEntries[0].customerID},'{$timeSheetEntries[0].customerName|replace:"'":"\\'"|escape:'html'}',
                             {$timeSheetEntries[0].projectID},'{$timeSheetEntries[0].projectName|replace:"'":"\\'"|escape:'html'}',
                             {$timeSheetEntries[0].activityID},'{$timeSheetEntries[0].activityName|replace:"'":"\\'"|escape:'html'}');
  {/if}
    
</script>