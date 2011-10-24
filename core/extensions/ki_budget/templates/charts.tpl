{literal}    
    <script type="text/javascript"> 
    $(document).ready(function() {
//        try {
	   		chartColors = {/literal}{$chartColors}{literal};
			bgt_ext_plot({/literal}{$javascript_arr_plotdata}{literal});
				recalculateWindow();
//		} catch(e) {
//			alert(e);
//		}
     });
    </script>
{/literal}

{section name=row loop=$arr_pct}
{if $arr_pct[row].pct_ID|in_array:$pct_selected}
<div class="bgt_project">
<div class="project_head project_overview">
{$arr_pct[row].pct_name|escape:'html'}
</div>
<div id="bgt_chartdiv_{$arr_pct[row].pct_ID}" class="bgt_plot_area" style="height:140px;width:200px;"></div> 
{assign var=temp value=$arr_pct[row].pct_ID} 
<span class="total">Total: {$arr_plotdata[$temp].total|string_format:"%.2f"}</span><br/>
<span class="budget">Budget: {$arr_plotdata[$temp].budget|string_format:"%.2f"}</span> <br/>
<span class="approved">Billable: {$arr_plotdata[$temp].billable_total|string_format:"%.2f"}</span><br/>
<span class="approved">Approved: {$arr_plotdata[$temp].approved|string_format:"%.2f"}</span>
{if $arr_plotdata[$temp].budget-$arr_plotdata[$temp].budget < 0}
<br><span style="text-color: red;" class="budgetminus">{$kga.lang.budget_minus}: {math equation="round((budget-total)*-makePlus,2)" budget=$arr_plotdata[$temp].budget total=$arr_plotdata[$temp].total) makePlus=1}</span> <br/>
{/if}
</div>
<!--{counter start=0 skip=1 assign="count"}-->
{foreach key=id item=evt from=$arr_plotdata[$temp]}
{if $id|in_array:$evt_selected}
<div class="bgt_project">
<div class="project_head">
{$arr_pct[row].pct_name|escape:'html'}&nbsp;
{$arr_pct[row].events[$id].evt_name|escape:'html'}
</div>
<div id="bgt_chartdiv_{$arr_pct[row].pct_ID}_evt_{$id}" class="bgt_plot_area" style="height:140px;width:200px; "></div>
<span class="total">Total: {$evt.total|string_format:"%.2f"}</span><br>
<span class="budget">Budget: {$evt.budget_total|string_format:"%.2f"}</span> <br>
<span class="approved">Approved: {$evt.approved_total|string_format:"%.2f"}</span>
{if $evt.budget <= 0}
<br><span style="text-color: red;" class="budgetminus">{$kga.lang.budget_minus}: {math equation="round((budget-total)*-makePlus,2)" budget=$evt.budget_total total=$evt.total makePlus=1}</span> <br/>
{/if}
</div>
{/if}
<!--{if $count is div by 7 && $count > 1}-->
<!--<br style="line-height: 250px;"/>-->
<!--{/if}-->
<!--{counter}-->
{/foreach}
<br>
<!--{assign var=numberOfEvents value=$arr_plotdata[$temp]|@count}-->
<!--<br style="line-height: {math equation="ceil(number / max) * height" number=$numberOfEvents max=7 height=270}px;"/>-->
<!--<br style="line-height: 250px;"/>-->

{/if}
{/section}
<div class="bgt_project_end"/>
</div>