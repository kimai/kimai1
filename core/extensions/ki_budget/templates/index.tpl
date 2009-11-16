{*     IMPORTANT NOTE:
       Javascript or jQuery stuff that should run when your extension *has finished loading*  
       should sit in an special onload function like this:
*}
{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            bgt_ext_onload();
            
            plotdata = new Array();
{/literal}
            {foreach key=id item=value from=$arr_plotdata}
              plotdata[{$id}] = {$value};
            {/foreach}{literal}

            chartColors = {/literal}{$chartColors}{literal};

            bgt_ext_plot(plotdata);
        });
    </script>
{/literal}


<div id="bgt">

<div class="legend">
{section name=row loop=$arr_legend}
<div class="legend_entry"><div class="legend_color" style="background-color:{$arr_legend[row].color}"></div>{$arr_legend[row].name}</div>
{/section}
<div class="legend_entry_end"/>
</div>

{section name=row loop=$arr_pct}
<div class="bgt_project">
<div class="project_head">
{$arr_pct[row].pct_name}
</div>
<div id="bgt_chartdiv_{$arr_pct[row].pct_ID}" style="height:140px;width:150px; "></div> 
</div>
{/section}
<div class="bgt_project_end"/>
</div>