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

<div class="keys">
{section name=row loop=$arr_keys}
<div class="key"><div class="key_color" style="background-color:{$arr_keys[row].color}"></div>{$arr_keys[row].name|escape:'html'}</div>
{/section}
<div class="key_end"/>
</div>

{section name=row loop=$arr_pct}
<div class="bgt_project">
<div class="project_head">
{$arr_pct[row].pct_name|escape:'html'}
</div>
<div id="bgt_chartdiv_{$arr_pct[row].pct_ID}" style="height:140px;width:150px; "></div> 
</div>
{/section}
<div class="bgt_project_end"/>
</div>