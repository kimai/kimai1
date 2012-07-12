{cycle values="odd,even" reset=true print=false}
{if $expenses}
        <div id="exptable">
        
          <table>
              
            <colgroup>
              <col class="option" />
              <col class="date" />
              <col class="time" />
              <col class="value" />
              <col class="refundable" />
              <col class="client" />
              <col class="project" />
              <col class="designation" />
              <col class="username" />
            </colgroup>

            <tbody>
                
{assign var="day_buffer" value="0"}

{section name=row loop=$expenses}

                <tr id="expEntry{$expenses[row].expenseID}" class="{cycle values="odd,even"}">
               
{*--OPTIONS-----------------------------------------------------*}
                    <td nowrap class="option
                                            {if $expenses[row].timestamp|date_format:"%d" != $day_buffer}
                                                {if $kga.show_daySeperatorLines}break_day{/if}
                                            {else}
                                                {if $expenses[row].end != $timestamp_buffer}
                                                    {if $kga.show_gabBreaks}break_gap{/if}
                                                {/if}
                                            {/if}
                    ">

{* only users can see options *}
{if $kga.user &&  ($kga.conf.editLimit == "-" || time()-$expenses[row].timestamp <= $kga.conf.editLimit)}

                        
{*Edit Record Button *}
                        {strip}<a href ='#' onClick="expense_editRecord({$expenses[row].expenseID}); $(this).blur(); return false;" title='{$kga.lang.edit}'>
                            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit}' border='0' />
                        </a>{/strip}
                        

    {* quick erase trashcan *}
    {if $kga.conf.quickdelete > 0}
                        {strip}<a href ='#' class='quickdelete' onClick="expense_quickdelete({$expenses[row].expenseID}); return false;">
                            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan.png' width='13' height='13' alt='{$kga.lang.quickdelete}' title='{$kga.lang.quickdelete}' border=0 />
                        </a>{/strip}
    {/if}


{/if}

{*--/OPTIONS----------------------------------------------------*}
                    
                    </td>                   

{*datum --------------------------------------------------------*}

                    <td class="date
                                            {if $expenses[row].timestamp|date_format:"%d" != $day_buffer}
                                                {if $kga.show_daySeperatorLines}break_day{/if}
                                            {else}
                                                {if $expenses[row].end != $timestamp_buffer}
                                                    {if $kga.show_gabBreaks}break_gap{/if}
                                                {/if}
                                            {/if}
                    ">
                        {$expenses[row].timestamp|date_format:$kga.date_format.1|escape:'html'}
                    </td>

{*time ---------------------------------------------------------*}

                    <td class="time
                                            {if $expenses[row].timestamp|date_format:"%d" != $day_buffer}
                                                {if $kga.show_daySeperatorLines}break_day{/if}
                                            {else}
                                                {if $expenses[row].end != $timestamp_buffer}
                                                    {if $kga.show_gabBreaks}break_gap{/if}
                                                {/if}
                                            {/if}
                    ">
                        {$expenses[row].timestamp|date_format:"%H:%M"|escape:'html'}
                    </td>

{*value --------------------------------------------------------*}

                    <td class="value
                                            {if $expenses[row].timestamp|date_format:"%d" != $day_buffer}
                                                {if $kga.show_daySeperatorLines}break_day{/if}
                                            {else}
                                                {if $expenses[row].end != $timestamp_buffer}
                                                    {if $kga.show_gabBreaks}break_gap{/if}
                                                {/if}
                                            {/if}
                    ">
                        {$expenses[row].value*$expenses[row].multiplier|number_format:2:$kga.conf.decimalSeparator:""|escape:'html'}
                    </td>
                    

{*refundable -------------------------------------------------*}

                    <td class="refundable
                                            {if $expenses[row].timestamp|date_format:"%d" != $day_buffer}
                                                {if $kga.show_daySeperatorLines}break_day{/if}
                                            {else}
                                                {if $expenses[row].end != $timestamp_buffer}
                                                    {if $kga.show_gabBreaks}break_gap{/if}
                                                {/if}
                                            {/if}
                    ">
                            {if $expenses[row].refundable} {$kga.lang.yes} {else} {$kga.lang.no} {/if}
                    </td>


{*client name --------------------------------------------------*}

                    <td class="customer
                                            {if $expenses[row].timestamp|date_format:"%d" != $day_buffer}
                                                {if $kga.show_daySeperatorLines}break_day{/if}
                                            {else}
                                                {if $expenses[row].end != $timestamp_buffer}
                                                    {if $kga.show_gabBreaks}break_gap{/if}
                                                {/if}
                                            {/if}
                    ">
                        {$expenses[row].customerName|escape:'html'}
                    </td>

{*project name -------------------------------------------------*}

                    <td class="project
                                            {if $expenses[row].timestamp|date_format:"%d" != $day_buffer}
                                                {if $kga.show_daySeperatorLines}break_day{/if}
                                            {else}
                                                {if $expenses[row].end != $timestamp_buffer}
                                                    {if $kga.show_gabBreaks}break_gap{/if}
                                                {/if}
                                            {/if}
                    ">
                            {$expenses[row].projectName|escape:'html'}
                    </td>


{*designation and comment bubble -------------------------------*}

                    <td class="designation
                                            {if $expenses[row].timestamp|date_format:"%d" != $day_buffer}
                                                {if $kga.show_daySeperatorLines}break_day{/if}
                                            {else}
                                                {if $expenses[row].end != $timestamp_buffer}
                                                    {if $kga.show_gabBreaks}break_gap{/if}
                                                {/if}
                                            {/if}
                    ">
                            {$expenses[row].designation|escape:'html'}
                        
{if $expenses[row].comment}
    {if $expenses[row].commentType == '0'}
                        <a href="#" onClick="comment({$expenses[row].expenseID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/blase.gif' width="12" height="13" title='{$expenses[row].comment|escape:'html'}' border="0" /></a>
    {/if}
    {if $expenses[row].commentType == '1'}
                        <a href="#" onClick="comment({$expenses[row].expenseID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/blase_sys.gif' width="12" height="13" title='{$expenses[row].comment|escape:'html'}' border="0" /></a>
    {/if}
    {if $expenses[row].commentType == '2'}
                        <a href="#" onClick="comment({$expenses[row].expenseID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/blase_caution.gif' width="12" height="13" title='{$expenses[row].comment|escape:'html'}' border="0" /></a>
    {/if}
{/if}
                    </td>

                    <td class="username
                        {if $expenses[row].timestamp|date_format:"%d" != $day_buffer}
                            {if $kga.show_daySeperatorLines}break_day{/if}
                        {else}
                            {if $expenses[row].end != $start_buffer}
                                {if $kga.show_gabBreaks}break_gap{/if}
                            {/if}
                        {/if}
                    ">
                    {$expenses[row].name|escape:'html'}
                    </td>
                    
                </tr>

{if $expenses[row].comment}   
                <tr id="expense_c{$expenses[row].expenseID}" class="comm{$expenses[row].commentType}" {if $hideComments}style="display:none;"{/if}>
                    <td colspan="8">{$expenses[row].comment|escape:'html'|nl2br}</td>
                </tr>
{/if}

{assign var="day_buffer" value=$expenses[row].timestamp|date_format:"%d"}
               
{/section}
                
            </tbody>   
        </table>
    </div>  
{else}
<div style='padding:5px;color:#f00'>
    <strong>{$kga.lang.noEntries}</strong>
</div>
{/if}

{*Annotations for sublists to set-------------------------------*}
<script type="text/javascript"> 
    expense_user_annotations = null;
    expense_customer_annotations = null;
    expense_project_annotations = null;
    expense_activity_annotations = null;
    expenses_total = '{$total}';

    { if $user_annotations }
    expense_user_annotations = new Array();
    {foreach key=id item=value from=$user_annotations}
      expense_user_annotations[{$id}] = '{$value}';
    {/foreach}
    {/if}

    { if $customer_annotations }
    expense_customer_annotations = new Array();
    {foreach key=id item=value from=$customer_annotations}
      expense_customer_annotations[{$id}] = '{$value}';
    {/foreach}
    {/if}

    { if $project_annotations }
    expense_project_annotations = new Array();
    {foreach key=id item=value from=$project_annotations}
      expense_project_annotations[{$id}] = '{$value}';
    {/foreach}
    {/if}

    { if $activity_annotations }
    expense_activity_annotations = new Array();
    {foreach key=id item=value from=$activity_annotations}
      expense_activity_annotations[{$id}] = '{$value}';
    {/foreach}
    {/if}
    
    {literal}
    lists_update_annotations(parseInt($('#gui div.ki_expenses').attr('id').substring(7)),expense_user_annotations,expense_customer_annotations,expense_project_annotations,expense_activity_annotations);
    $('#display_total').html(expenses_total);
    {/literal}
    
</script>