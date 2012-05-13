{literal}    
    <script type="text/javascript"> 
    $(document).ready(function() {
//        try {
	   		chartColors = {/literal}{$chartColors}{literal};
			budget_extension_plot({/literal}{$javascript_arr_plotdata}{literal});
				recalculateWindow();
//		} catch(e) {
//			alert(e);
//		}
     });
    </script>
{/literal}

{section name=row loop=$projects}
{if $projects[row].projectID|in_array:$projects_selected}
<div class="budget_project">
<div class="project_head project_overview">
{$projects[row].name|escape:'html'}
</div>
<div id="budget_chartdiv_{$projects[row].projectID}" class="budget_plot_area" style="height:140px;width:200px;"></div> 
{assign var=temp value=$projects[row].projectID} 
<span class="total">Total: {$arr_plotdata[$temp].total|string_format:"%.2f"}</span><br/>
<span class="budget">Budget: {$arr_plotdata[$temp].budget|string_format:"%.2f"}</span> <br/>
<span class="approved">Billable: {$arr_plotdata[$temp].billable_total|string_format:"%.2f"}</span><br/>
<span class="approved">Approved: {$arr_plotdata[$temp].approved|string_format:"%.2f"}</span>
{if $arr_plotdata[$temp].budget-$arr_plotdata[$temp].budget < 0}
<br><span style="text-color: red;" class="budgetminus">{$kga.lang.budget_minus}: {math equation="round((budget-total)*-makePlus,2)" budget=$arr_plotdata[$temp].budget total=$arr_plotdata[$temp].total) makePlus=1}</span> <br/>
{/if}
</div>
<!--{counter start=0 skip=1 assign="count"}-->
{foreach key=id item=activity from=$arr_plotdata[$temp]}
{if $id|in_array:$activities_selected}
<div class="budget_project">
<div class="project_head">
{$projects[row].name|escape:'html'}&nbsp;
{$projects[row].activities[$id].activityName|escape:'html'}
</div>
<div id="budget_chartdiv_{$projects[row].projectID}_activity_{$id}" class="budget_plot_area" style="height:140px;width:200px; "></div>
<span class="total">Total: {$activity.total|string_format:"%.2f"}</span><br>
<span class="budget">Budget: {$activity.budget_total|string_format:"%.2f"}</span> <br>
<span class="approved">Approved: {$activity.approved_total|string_format:"%.2f"}</span>
{if $activity.budget <= 0}
<br><span style="text-color: red;" class="budgetminus">{$kga.lang.budget_minus}: {math equation="round((budget-total)*-makePlus,2)" budget=$activity.budget_total total=$activity.total makePlus=1}</span> <br/>
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
<div class="budget_project_end"/>
</div>