{cycle values="odd,even" reset=true print=false}
{if $arr_zef}
        <div id="zeftable">
        
          <table>
              
            <colgroup>
              <col class="option" />
              {if $kga.global}<col class="alias" />{/if}
              <col class="date" />
              <col class="from" />
              <col class="to" />
              <col class="time" />
              <col class="client" />
              <col class="project" />
              <col class="action" />
            </colgroup>

            <tbody>

{assign var="day_buffer" value="0"}
{assign var="zef_in_buffer" value=0}
                
{section name=row loop=$arr_zef}

{if $arr_zef[row].zef_out}                
                <tr id="zefEntry{$arr_zef[row].zef_ID}" class="{cycle values="odd,even"}">
{else}                    
                <tr id="zefEntry{$arr_zef[row].zef_ID}" class="{cycle values="odd,even"} active">
{/if}
               
                    <td nowrap class="option 
                                            {if $arr_zef[row].zef_in|date_format:"%d" != $day_buffer}
                                                {if $kga.show_daySeperatorLines}break_day{/if}
                                            {else}
                                                {if $arr_zef[row].zef_out != $zef_in_buffer}
                                                    {if $kga.show_gabBreaks}break_gap{/if}
                                                {/if}
                                            {/if}
                    ">

                        
{*Stop oder Record Button?*}
{if $arr_zef[row].zef_out}


{*--OPTIONS----------------------------------------------------*}

                        {if $kga.show_RecordAgain}{strip}<a href ='#' class='recordAgain' onClick="ts_ext_recordAgain({$arr_zef[row].zef_pctID},{$arr_zef[row].zef_evtID},{$arr_zef[row].zef_ID}); return false;">
                            <img src='../skins/{$kga.conf.skin}/grfx/button_recordthis.gif' width='13' height='13' alt='{$kga.lang.recordAgain}' title='{$kga.lang.recordAgain} (ID:{$arr_zef[row].zef_ID})' border='0' />
                        </a>{/strip}{/if}


{else}


                        {strip}<a href ='#' class='stop' onClick="ts_ext_stopRecord({$arr_zef[row].zef_ID}); return false;">
                            <img src='../skins/{$kga.conf.skin}/grfx/button_stopthis.gif' width='13' height='13' alt='{$kga.lang.stop}' title='{$kga.lang.stop} (ID:{$arr_zef[row].zef_ID})' border='0' />
                        </a>{/strip}

{/if}

                        
{*Edit Record Button - nur einblenden wenn fertig recorded*}
{if $arr_zef[row].zef_out}
                        {strip}<a href ='#' onClick="editRecord({$arr_zef[row].zef_ID}); return false;" title='{$kga.lang.edit}'>
                            <img src='../skins/{$kga.conf.skin}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit}' border='0' />
                        </a>{/strip}
                        

    {* quick erase trashcan *}
    {if $kga.conf.quickdelete == 1}
                        {strip}<a href ='#' class='quickdelete' onClick="quickdelete({$arr_zef[row].zef_ID}); return false;">
                            <img src='../skins/{$kga.conf.skin}/grfx/button_trashcan.png' width='13' height='13' alt='{$kga.lang.quickdelete}' title='{$kga.lang.quickdelete}' border=0 />
                        </a>{/strip}
    {/if}

{/if} 

{*--/OPTIONS----------------------------------------------------*}
                    
                    </td>





{if $kga.global}
                    <td class="alias
                        {if $arr_zef[row].zef_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_zef[row].zef_out != $zef_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                    ">
                        {$arr_zef[row].usr_alias}
                    </td>
{/if} 






                   

{*datum --------------------------------------------------------*}

                    <td class="date
                        {if $arr_zef[row].zef_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_zef[row].zef_out != $zef_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                    ">
                        {$arr_zef[row].zef_in|date_format:$kga.date_format.1}
                    </td>

{*in -----------------------------------------------------------*}

                    <td class="from
                        {if $arr_zef[row].zef_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_zef[row].zef_out != $zef_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                    ">
                        {$arr_zef[row].zef_in|date_format:"%H:%M"}
                    </td>

{*out ----------------------------------------------------------*}

                    <td class="to
                        {if $arr_zef[row].zef_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_zef[row].zef_out != $zef_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                    ">
                    
{if $arr_zef[row].zef_out}
                        {$arr_zef[row].zef_out|date_format:"%H:%M"}
{else}                     
                        &ndash;&ndash;:&ndash;&ndash;
{/if}
                    </td>

{*task time ----------------------------------------------------*}

                    <td class="time
                        {if $arr_zef[row].zef_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_zef[row].zef_out != $zef_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                    ">
                    
{if $arr_zef[row].zef_time}
                    
                        <a title='{$arr_zef[row].zef_coln}'>
                            {$arr_zef[row].zef_apos}
                        </a>
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>

{*client name --------------------------------------------------*}

                    <td class="knd
                        {if $arr_zef[row].zef_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_zef[row].zef_out != $zef_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                    ">
                        {$arr_zef[row].knd_name}
                    </td>

{*project name -------------------------------------------------*}

                    <td class="pct
                        {if $arr_zef[row].zef_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_zef[row].zef_out != $zef_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                    ">
                        
                        <a href ="#" class="preselect_lnk" 
                            onClick="ts_ext_preselect('pct',{$arr_zef[row].pct_ID},'{$arr_zef[row].pct_name}',{$arr_zef[row].pct_kndID},'{$arr_zef[row].knd_name}'); 
                            return false;">
                            {$arr_zef[row].pct_name}
                            {if $kga.conf.pct_comment_flag == 1}
                                {if $arr_zef[row].pct_comment}
                                    <span class="lighter">({$arr_zef[row].pct_comment})</span>
                                {/if}
                            {/if}
                        </a>
                    </td>


{*event name and comment bubble --------------------------------*}

                    <td class="evt
                        {if $arr_zef[row].zef_in|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $arr_zef[row].zef_out != $zef_in_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                    ">
                        
                        <a href ="#" class="preselect_lnk" 
                            onClick="ts_ext_preselect('evt',{$arr_zef[row].zef_evtID},'{$arr_zef[row].evt_name}',0,0); 
                            return false;">
                            {$arr_zef[row].evt_name} 
                        </a>
                        
{if $arr_zef[row].zef_comment}
    {if $arr_zef[row].zef_comment_type == '0'}
                        <a href="#" onClick="comment({$arr_zef[row].zef_ID}); return false;"><img src='../skins/{$kga.conf.skin}/grfx/blase.gif' width="12" height="13" title='{$arr_zef[row].zef_comment}' border="0" /></a>
    {/if}
    {if $arr_zef[row].zef_comment_type == '1'}
                        <a href="#" onClick="comment({$arr_zef[row].zef_ID}); return false;"><img src='../skins/{$kga.conf.skin}/grfx/blase_sys.gif' width="12" height="13" title='{$arr_zef[row].zef_comment}' border="0" /></a>
    {/if}
    {if $arr_zef[row].zef_comment_type == '2'}
                        <a href="#" onClick="comment({$arr_zef[row].zef_ID}); return false;"><img src='../skins/{$kga.conf.skin}/grfx/blase_caution.gif' width="12" height="13" title='{$arr_zef[row].zef_comment}' border="0" /></a>
    {/if}
{/if}
                    </td>

                </tr>
                
                <tr id="c{$arr_zef[row].zef_ID}" class="comm{$arr_zef[row].zef_comment_type}" style="display:none;">
                    <td colspan=8>{$arr_zef[row].zef_comment|nl2br}</td>
                </tr>

{assign var="day_buffer" value=$arr_zef[row].zef_in|date_format:"%d"}
{assign var="zef_in_buffer" value=$arr_zef[row].zef_in}
               
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
    $('#display_total').html('{$total}');
</script>
