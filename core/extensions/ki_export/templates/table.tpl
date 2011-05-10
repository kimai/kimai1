{cycle values="odd,even" reset=true print=false}
{if $arr_data}
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
              <col class="client" />
              <col class="project" />
              <col class="action" />
              <col class="comment" />
              <col class="location" />
              <col class="trackingnr" />
              <col class="user" />
              <col class="cleared" />
            </colgroup>

            <tbody>

{assign var="day_buffer" value="0"}
{assign var="time_in_buffer" value=0}
                
{section name=row loop=$arr_data}

<tr id="xp{$arr_data[row].type}{$arr_data[row].id}" class="{cycle values="odd,even"} {if !$arr_data[row].time_out}active{/if}
 {if $arr_data[row].type=="exp"}expense{/if}">
               












                   

{*datum --------------------------------------------------------*}

                    <td class="date
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.date}disabled{/if}
                    ">
                        {$arr_data[row].time_in|date_format:$dateformat|escape:'html'}
                    </td>

{*in -----------------------------------------------------------*}

                    <td class="from
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.from}disabled{/if}
                    ">
                        {$arr_data[row].time_in|date_format:$timeformat|escape:'html'}
                    </td>

{*out ----------------------------------------------------------*}

                    <td class="to
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.to}disabled{/if}
                    ">
                    
{if $arr_data[row].time_out}
                        {$arr_data[row].time_out|date_format:$timeformat|escape:'html'}
{else}                     
                        &ndash;&ndash;:&ndash;&ndash;
{/if}
                    </td>

{*task time ----------------------------------------------------*}

                    <td class="time
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.time}disabled{/if}
                    ">
                    
{if $arr_data[row].zef_time}
                    
                        {$arr_data[row].zef_duration}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>

{*decimal time --------------------------------------------------*}

                    <td class="dec_time
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.dec_time}disabled{/if}
                    ">
                    
{if $arr_data[row].dec_zef_time}
                    
                        {$arr_data[row].dec_zef_time|replace:'.':$kga.conf.decimalSeparator|escape:'html'}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>

{*rate ---------------------------------------------------------*}

                    <td class="rate
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.rate}disabled{/if}
                    ">
                    
                            {$arr_data[row].zef_rate|replace:'.':$kga.conf.decimalSeparator|escape:'html'}
                    </td>

{*task wage ----------------------------------------------------*}

                    <td class="wage
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.wage}disabled{/if}
                    ">
                    
{if $arr_data[row].wage}
                    
                        {$arr_data[row].wage|replace:'.':$kga.conf.decimalSeparator|escape:'html'}
                      
{else}  
                        &ndash;
{/if}
                    </td>

{*client name --------------------------------------------------*}

                    <td class="knd
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.knd}disabled{/if}
                    ">
                        {$arr_data[row].knd_name|escape:'html'}
                    </td>

{*project name -------------------------------------------------*}

                    <td class="pct
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.pct}disabled{/if}
                    ">
                        
                        <a href ="#" class="preselect_lnk" 
                            onClick="buzzer_preselect('pct',{$arr_data[row].pct_ID},'{$arr_data[row].pct_name|replace:"'":"\\'"|escape:'html'}',{$arr_data[row].pct_kndID},'{$arr_data[row].knd_name|replace:"'":"\\'"|escape:'html'}'); 
                            return false;">
                            {$arr_data[row].pct_name|escape:'html'}
                            {if $kga.conf.pct_comment_flag == 1}
                                {if $arr_data[row].pct_comment}
                                    <span class="lighter">({$arr_data[row].pct_comment|escape:'html'})</span>
                                {/if}
                            {/if}
                        </a>
                    </td>


{*event name and comment bubble --------------------------------*}

                    <td class="evt
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.evt}disabled{/if}
                    ">
                        
                        <a href ="#" class="preselect_lnk" 
                            onClick="buzzer_preselect('evt',{$arr_data[row].zef_evtID},'{$arr_data[row].evt_name|replace:"'":"\\'"|escape:'html'}',0,0); 
                            return false;">
                            {$arr_data[row].evt_name|escape:'html'} 
                        </a>
                    </td>

{*comment -----------------------------------------------------*}

                    <td class="comment
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.comment}disabled{/if}
                    ">
                        {$arr_data[row].comment|escape:'html'|nl2br}
                        
                    </td>

{*location ----------------------------------------------------*}

                    <td class="location
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.location}disabled{/if}
                    ">
                        {$arr_data[row].location|escape:'html'}
                        
                    </td>

{*tracking number ---------------------------------------------*}

                    <td class="trackingnr
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.trackingnr}disabled{/if}
                    ">
                        {$arr_data[row].trackingnr|escape:'html'}
                        
                    </td>

{*user --------------------------------------------------------*}

                    <td class="user
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.user}disabled{/if}
                    ">
                        {$arr_data[row].username|escape:'html'}
                        
                    </td>


          <td class="cleared
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                        {if $disabled_columns.cleared}disabled{/if}
                    ">
                      <a class ="{if $arr_data[row].cleared}is_cleared{else}isnt_cleared{/if}" href ="#" onClick="xp_toggle_cleared('{$arr_data[row].type}{$arr_data[row].id}'); return false;"></a>
          </td>
          

                </tr>

{assign var="day_buffer" value=$arr_data[row].time_in|date_format:"%d"}
{assign var="time_in_buffer" value=$arr_data[row].time_in}
               
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
    lists_update_annotations(parseInt($('#gui div.ki_export').attr('id').substring(7)),ts_usr_ann,ts_knd_ann,ts_pct_ann,ts_evt_ann);
    $('#display_total').html(ts_total);
    {/literal}
    
</script>