{cycle values="odd,even" reset=true print=false}
{if $arr_data}
        <div id="xptable">
        
          <table>
              
            <colgroup>
              {if $kga.global}<col class="alias" />{/if}
              <col class="date" />
              <col class="from" />
              <col class="to" />
              <col class="time" />
              <col class="dec_time" />
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

{if $arr_data[row].time_out}                
                <tr id="xp{$arr_data[row].type}{$arr_data[row].id}" class="{cycle values="odd,even"}">
{else}                    
                <tr id="xp{$arr_data[row].type}{$arr_data[row].id}" class="{cycle values="odd,even"} active">
{/if}
               






{if $kga.global}
                    <td class="alias
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                    ">
                        {$arr_data[row].usr_alias}
                    </td>
{/if} 






                   

{*datum --------------------------------------------------------*}

                    <td class="date
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                    ">
                        { if $custom_dateformat }
                        {$arr_data[row].time_in|date_format:$custom_dateformat}
                        { else }
                        {$arr_data[row].time_in|date_format:$kga.date_format.1}
                        { /if }
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
                    ">
                        { if $custom_timeformat }
                        {$arr_data[row].time_in|date_format:$custom_timeformat}
                        { else }
                        {$arr_data[row].time_in|date_format:"%H:%M"}
                        { /if }
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
                    ">
                    
{if $arr_data[row].time_out}
                        { if $custom_timeformat }
                        {$arr_data[row].time_out|date_format:$custom_timeformat}
                        { else }
                        {$arr_data[row].time_out|date_format:"%H:%M"}
                        { /if }
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
                    ">
                    
{if $arr_data[row].zef_time}
                    
                        <a title='{$arr_data[row].zef_coln}'>
                            {$arr_data[row].zef_apos}
                        </a>
                      
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
                    ">
                    
{if $arr_data[row].dec_zef_time}
                    
                        <a title='{$arr_data[row].zef_coln}'>
                            {$arr_data[row].dec_zef_time}
                        </a>
                      
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
                    ">
                    
                            {$arr_data[row].zef_rate}
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
                    ">
                    
{if $arr_data[row].wage}
                    
                        {$arr_data[row].wage}
                      
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
                    ">
                        {$arr_data[row].knd_name}
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
                    ">
                        
                        <a href ="#" class="preselect_lnk" 
                            onClick="ts_ext_preselect('pct',{$arr_data[row].pct_ID},'{$arr_data[row].pct_name|replace:"'":"\\'"}',{$arr_data[row].pct_kndID},'{$arr_data[row].knd_name}'); 
                            return false;">
                            {$arr_data[row].pct_name}
                            {if $kga.conf.pct_comment_flag == 1}
                                {if $arr_data[row].pct_comment}
                                    <span class="lighter">({$arr_data[row].pct_comment})</span>
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
                    ">
                        
                        <a href ="#" class="preselect_lnk" 
                            onClick="ts_ext_preselect('evt',{$arr_data[row].zef_evtID},'{$arr_data[row].evt_name|replace:"'":"\\'"}',0,0); 
                            return false;">
                            {$arr_data[row].evt_name} 
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
                    ">
                        {$arr_data[row].comment|nl2br}
                        
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
                    ">
                        {$arr_data[row].location}
                        
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
                    ">
                        {$arr_data[row].trackingnr}
                        
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
                    ">
                        {$arr_data[row].username}
                        
                    </td>


					<td class="cleared
                        {if $arr_data[row].time_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_data[row].time_out != $time_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
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
      ts_usr_ann[{$id}] = "{$value}";
    {/foreach}
    {/if}

    { if $knd_ann }
    ts_knd_ann = new Array();
    {foreach key=id item=value from=$knd_ann}
      ts_knd_ann[{$id}] = "{$value}";
    {/foreach}
    {/if}

    { if $pct_ann }
    ts_pct_ann = new Array();
    {foreach key=id item=value from=$pct_ann}
      ts_pct_ann[{$id}] = "{$value}";
    {/foreach}
    {/if}

    { if $evt_ann }
    ts_evt_ann = new Array();
    {foreach key=id item=value from=$evt_ann}
      ts_evt_ann[{$id}] = "{$value}";
    {/foreach}
    {/if}
    
    {literal}
    lists_update_annotations(parseInt($('#gui div.ki_export').attr('id').substring(7)),ts_usr_ann,ts_knd_ann,ts_pct_ann,ts_evt_ann);
    $('#display_total').html(ts_total);
    {/literal}
    
</script>