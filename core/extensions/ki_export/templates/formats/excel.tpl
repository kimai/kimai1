<html xmlns:o="urn:schemas-microsoft-com:office:office" 
xmlns:x="urn:schemas-microsoft-com:office:excel" 
xmlns="http://www.w3.org/TR/REC-html40"> 

<head> 
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<style> 
{literal}
.date { 
mso-number-format:"Short Date"; 
} 
.time { 
mso-number-format:"h\:mm\:ss\;\@"; 
} 
.duration {
mso-number-format:"h\:mm\;\@";
}
.decimal {
mso-number-format:Fixed;
}
{/literal}
</style> 
<!--[if gte mso 9]><xml>
 <x:ExcelWorkbook>
  <x:ExcelWorksheets>
   <x:ExcelWorksheet>
    <x:Name>Tabelle1</x:Name>
    <x:WorksheetOptions>
     <x:DefaultColWidth>10</x:DefaultColWidth>
     <x:Selected/>
     <x:Panes>
      <x:Pane>
       <x:Number>3</x:Number>
       <x:ActiveRow>4</x:ActiveRow>
       <x:ActiveCol>3</x:ActiveCol>
      </x:Pane>
     </x:Panes>
     <x:ProtectContents>False</x:ProtectContents>
     <x:ProtectObjects>False</x:ProtectObjects>
     <x:ProtectScenarios>False</x:ProtectScenarios>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
 </x:ExcelWorkbook>
</xml><![endif]-->
</head> 

<body> 
<table> 
<thead><tr> 
{*column headers------------------------------------------------*}
{ if $columns.date         } <td>{$kga.lang.datum}</td>       { /if }
{ if $columns.from         } <td>{$kga.lang.in}</td>          { /if }
{ if $columns.to           } <td>{$kga.lang.out}</td>         { /if }
{ if $columns.time         } <td>{$kga.lang.time}</td>        { /if }
{ if $columns.dec_time     } <td>{$kga.lang.timelabel}</td>   { /if }
{ if $columns.rate         } <td>{$kga.lang.rate}</td>        { /if }
{ if $columns.wage         } <td>{$kga.currency_name}</td>    { /if }
{ if $columns.knd          } <td>{$kga.lang.knd}</td>         { /if }
{ if $columns.pct          } <td>{$kga.lang.pct}</td>         { /if }
{ if $columns.action       } <td>{$kga.lang.evt}</td>         { /if }
{ if $columns.comment      } <td>{$kga.lang.comment}</td>     { /if }
{ if $columns.location     } <td>{$kga.lang.zlocation}</td>   { /if }
{ if $columns.trackingnr   } <td>{$kga.lang.trackingnr}</td>  { /if }
{ if $columns.user         } <td>{$kga.lang.username}</td>    { /if }
{ if $columns.cleared      } <td>{$kga.lang.cleared}</td>     { /if }
</tr> 
</thead> 
{section name=row loop=$arr_data}
<tr> 
{*datum --------------------------------------------------------*}
{ if $columns.date }
                    <td class=date>
                        { if $custom_dateformat }
                        {$arr_data[row].time_in|date_format:$custom_dateformat|escape:'html'}
                        { else }
                        {$arr_data[row].time_in|date_format:$kga.date_format.1|escape:'html'}
                        { /if }
                    </td>
{/if}

{*in -----------------------------------------------------------*}
{ if $columns.from }
                    <td align=right class=time>
                        { if $custom_timeformat }
                        {$arr_data[row].time_in|date_format:$custom_timeformat|escape:'html'}
                        { else }
                        {$arr_data[row].time_in|date_format:"%H:%M"|escape:'html'}
                        { /if }
                    </td>
{/if}

{*out ----------------------------------------------------------*}
{ if $columns.to }
                    <td align=right class=time>
                    
{if $arr_data[row].time_out}
                        { if $custom_timeformat }
                        {$arr_data[row].time_out|date_format:$custom_timeformat|escape:'html'}
                        { else }
                        {$arr_data[row].time_out|date_format:"%H:%M"|escape:'html'}
                        { /if }
{else}                     
                        &ndash;&ndash;:&ndash;&ndash;
{/if}
                    </td>
{/if}

{*task time ----------------------------------------------------*}
{ if $columns.time }
                    <td align=right class=duration>
                    
{if $arr_data[row].zef_time}

                            {$arr_data[row].zef_duration}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>
{/if}

{*decimal time --------------------------------------------------*}
{ if $columns.dec_time }
                    <td align=right class=decimal>
                    
{if $arr_data[row].dec_zef_time}
                            {$arr_data[row].dec_zef_time}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>
{/if}

{*rate ---------------------------------------------------------*}
{ if $columns.rate }
                    <td align=right class=decimal>
                    
                            {$arr_data[row].zef_rate}
                    </td>
{/if}

{*task wage ----------------------------------------------------*}
{ if $columns.wage }
                    <td align=right class=decimal>
                    
{if $arr_data[row].wage}
                    
                        {$arr_data[row].wage}
                      
{else}  
                        &ndash;
{/if}
                    </td>
{/if}

{*client name --------------------------------------------------*}
{ if $columns.knd }
                    <td>
                        {$arr_data[row].knd_name|escape:'html'}
                    </td>
{/if}

{*project name -------------------------------------------------*}
{ if $columns.pct }
                    <td>
                            {$arr_data[row].pct_name|escape:'html'}
                    </td>
{/if}


{*event name and comment bubble --------------------------------*}
{ if $columns.action }
                    <td>
                            {$arr_data[row].evt_name|escape:'html'} 
                    </td>
{/if}

{*comment -----------------------------------------------------*}
{ if $columns.comment }
                    <td>
                        {$arr_data[row].comment|escape:'html'|replace:"\n":"&#10;"}
                    </td>
{/if}

{*location ----------------------------------------------------*}
{ if $columns.location }
                    <td>
                        {$arr_data[row].location|escape:'html'}
                        
                    </td>
{/if}

{*tracking number ---------------------------------------------*}
{ if $columns.trackingnr }
                    <td>
                        {$arr_data[row].trackingnr|escape:'html'}
                        
                    </td>
{/if}

{*user --------------------------------------------------------*}
{ if $columns.user }
                    <td>
                        {$arr_data[row].username|escape:'html'}
                        
                    </td>
{/if}

{*cleared -----------------------------------------------------*}
{ if $columns.cleared }
          <td>
                      {if $arr_data[row].cleared}cleared{else}{/if}
          </td>
{/if}
          

                </tr>
               
{/section}

</table> 

</body> 
</html>  
 
