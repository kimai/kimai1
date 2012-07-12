{cycle values="odd,even" reset=true print=false}
{if $exportData}
        <div id="xptable">
        
          <table>
              
            <colgroup>
              <col class="date" />
              <col class="from" />
              <col class="to" />
              <col class="time" />
              <col class="dec_time" />
              <col class="rate" />
              <col class="wage" />
              <col class="budget" />
              <col class="approved" />
              <col class="status" />
              <col class="billable" />
              <col class="client" />
              <col class="project" />
              <col class="activity" />
              <col class="description" />
              <col class="comment" />
              <col class="location" />
              <col class="trackingNumber" />
              <col class="user" />
              <col class="cleared" />
            </colgroup>

            <tbody>

{assign var="day_buffer" value="0"}
{assign var="time_in_buffer" value=0}
                
{section name=row loop=$exportData}

<tr id="xp{$exportData[row].type}{$exportData[row].id}" class="{cycle values="odd,even"} {if !$exportData[row].time_out}active{/if}
 {if $exportData[row].type=="exp"}expense{/if}">
               

{*datum --------------------------------------------------------*}

                    <td class="date
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.date}disabled{/if}
                    ">
                        {$exportData[row].time_in|date_format:$dateformat|escape:'html'}
                    </td>

{*in -----------------------------------------------------------*}

                    <td class="from
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.from}disabled{/if}
                    ">
                        {$exportData[row].time_in|date_format:$timeformat|escape:'html'}
                    </td>

{*out ----------------------------------------------------------*}

                    <td class="to
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.to}disabled{/if}
                    ">
                    
{if $exportData[row].time_out}
                        {$exportData[row].time_out|date_format:$timeformat|escape:'html'}
{else}                     
                        &ndash;&ndash;:&ndash;&ndash;
{/if}
                    </td>

{*task time ----------------------------------------------------*}

                    <td class="time
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.time}disabled{/if}
                    ">
                    
{if $exportData[row].duration}
                    
                        {$exportData[row].formattedDuration}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>

{*decimal time --------------------------------------------------*}

                    <td class="dec_time
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.dec_time}disabled{/if}
                    ">
                    
{if $exportData[row].decimalDuration}
                    
                        {$exportData[row].decimalDuration|replace:'.':$kga.conf.decimalSeparator|escape:'html'}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>

{*rate ---------------------------------------------------------*}

                    <td class="rate
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.rate}disabled{/if}
                    ">
                    
                            {$exportData[row].rate|replace:'.':$kga.conf.decimalSeparator|escape:'html'}
                    </td>

{*task wage ----------------------------------------------------*}

                    <td class="wage
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.wage}disabled{/if}
                    ">
                    
{if $exportData[row].wage}
                    
                        {$exportData[row].wage|replace:'.':$kga.conf.decimalSeparator|escape:'html'}
                      
{else}  
                        &ndash;
{/if}
                    </td>
                    
{*budget --------------------------------------------------*}
                    <td class="budget
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.budget}disabled{/if}
                    ">
                        {$exportData[row].budget|escape:'html'}
                    </td>
                    
                    
{*approved --------------------------------------------------*}
                    <td class="approved
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.approved}disabled{/if}
                    ">
                        {$exportData[row].approved|escape:'html'}
                    </td>
                    
                    
{*status --------------------------------------------------*}
                    <td class="status
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.status}disabled{/if}
                    ">
                        {$exportData[row].status|escape:'html'}
                    </td>
                    
                    
{*billable --------------------------------------------------*}
                    <td class="billable
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.billable}disabled{/if}
                    ">
                        {$exportData[row].billable|escape:'html'}%
                    </td>
                    

{*client name --------------------------------------------------*}

                    <td class="customer
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.customer}disabled{/if}
                    ">
                        {$exportData[row].customerName|escape:'html'}
                    </td>

{*project name -------------------------------------------------*}

                    <td class="project
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.project}disabled{/if}
                    ">
                        
                        <a href ="#" class="preselect_lnk" 
                            onClick="buzzer_preselect('project',{$exportData[row].projectID},'{$exportData[row].projectName|replace:"'":"\\'"|escape:'html'}',{$exportData[row].customerID},'{$exportData[row].customerName|replace:"'":"\\'"|escape:'html'}'); 
                            return false;">
                            {$exportData[row].projectName|escape:'html'}
                            {if $kga.conf.project_comment_flag == 1}
                                {if $exportData[row].projectComment}
                                    <span class="lighter">({$exportData[row].projectComment|escape:'html'})</span>
                                {/if}
                            {/if}
                        </a>
                    </td>


{*activity name and comment bubble --------------------------------*}

                    <td class="activity
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.activity}disabled{/if}
                    ">
                        
                        <a href ="#" class="preselect_lnk" 
                            onClick="buzzer_preselect('activity',{$exportData[row].activityID},'{$exportData[row].activityName|replace:"'":"\\'"|escape:'html'}',0,0); 
                            return false;">
                            {$exportData[row].activityName|escape:'html'} 
                        </a>
                    </td>
                    
{*description -----------------------------------------------------*}

                    <td class="description
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.comment}disabled{/if}
                    ">
                        {$exportData[row].description|escape:'html'|nl2br}
                        
                    </td>

{*comment -----------------------------------------------------*}

                    <td class="comment
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.comment}disabled{/if}
                    ">
                        {$exportData[row].comment|escape:'html'|nl2br}
                        
                    </td>

{*location ----------------------------------------------------*}

                    <td class="location
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.location}disabled{/if}
                    ">
                        {$exportData[row].location|escape:'html'}
                        
                    </td>

{*tracking number ---------------------------------------------*}

                    <td class="trackingNumber
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.trackingNumber}disabled{/if}
                    ">
                        {$exportData[row].trackingNumber|escape:'html'}
                        
                    </td>

{*user --------------------------------------------------------*}

                    <td class="user
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.user}disabled{/if}
                    ">
                        {$exportData[row].username|escape:'html'}
                        
                    </td>


          <td class="cleared
                        {if $exportData[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $exportData[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.cleared}disabled{/if}
                    ">
                      <a class ="{if $exportData[row].cleared}is_cleared{else}isnt_cleared{/if}" href ="#" onClick="export_toggle_cleared('{$exportData[row].type}{$exportData[row].id}'); return false;"></a>
          </td>
          

                </tr>

{assign var="day_buffer" value=$exportData[row].time_in|date_format:"%d"}
{assign var="time_in_buffer" value=$exportData[row].time_in}
               
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
    lists_update_annotations(parseInt($('#gui div.ki_export').attr('id').substring(7)),ts_user_annotations,ts_customer_annotations,ts_project_annotations,ts_activity_annotations);
    $('#display_total').html(ts_total);
    {/literal}
    
</script>