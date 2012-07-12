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
{ if $columns.budget       } <td>{$kga.lang.budget}</td>      { /if }
{ if $columns.approved     } <td>{$kga.lang.approved}</td>    { /if }
{ if $columns.status       } <td>{$kga.lang.status}</td>      { /if }
{ if $columns.billable     } <td>{$kga.lang.billable}</td>    { /if }
{ if $columns.customer     } <td>{$kga.lang.customer}</td>    { /if }
{ if $columns.project      } <td>{$kga.lang.project}</td>     { /if }
{ if $columns.activity     } <td>{$kga.lang.activity}</td>    { /if }
{ if $columns.description  } <td>{$kga.lang.description}</td> { /if }
{ if $columns.comment      } <td>{$kga.lang.comment}</td>     { /if }
{ if $columns.location     } <td>{$kga.lang.location}</td>   { /if }
{ if $columns.trackingNumber   } <td>{$kga.lang.trackingNumber}</td>  { /if }
{ if $columns.user         } <td>{$kga.lang.username}</td>    { /if }
{ if $columns.cleared      } <td>{$kga.lang.cleared}</td>     { /if }
</tr> 
</thead> 
{section name=row loop=$exportData}
<tr> 
{*datum --------------------------------------------------------*}
{ if $columns.date }
                    <td class=date>
                        { if $custom_dateformat }
                        {$exportData[row].time_in|date_format:$custom_dateformat|escape:'html'}
                        { else }
                        {$exportData[row].time_in|date_format:$kga.date_format.1|escape:'html'}
                        { /if }
                    </td>
{/if}

{*in -----------------------------------------------------------*}
{ if $columns.from }
                    <td align=right class=time>
                        { if $custom_timeformat }
                        {$exportData[row].time_in|date_format:$custom_timeformat|escape:'html'}
                        { else }
                        {$exportData[row].time_in|date_format:"%H:%M"|escape:'html'}
                        { /if }
                    </td>
{/if}

{*out ----------------------------------------------------------*}
{ if $columns.to }
                    <td align=right class=time>
                    
{if $exportData[row].time_out}
                        { if $custom_timeformat }
                        {$exportData[row].time_out|date_format:$custom_timeformat|escape:'html'}
                        { else }
                        {$exportData[row].time_out|date_format:"%H:%M"|escape:'html'}
                        { /if }
{else}                     
                        &ndash;&ndash;:&ndash;&ndash;
{/if}
                    </td>
{/if}

{*task time ----------------------------------------------------*}
{ if $columns.time }
                    <td align=right class=duration>
                    
{if $exportData[row].duration}

                            {$exportData[row].formattedDuration}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>
{/if}

{*decimal time --------------------------------------------------*}
{ if $columns.dec_time }
                    <td align=right class=decimal>
                    
{if $exportData[row].decimalDuration}
                            {$exportData[row].decimalDuration}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>
{/if}

{*rate ---------------------------------------------------------*}
{ if $columns.rate }
                    <td align=right class=decimal>
                    
                            {$exportData[row].rate}
                    </td>
{/if}

{*task wage ----------------------------------------------------*}
{ if $columns.wage }
                    <td align=right class=decimal>
                    
{if $exportData[row].wage}
                    
                        {$exportData[row].wage}
                      
{else}  
                        &ndash;
{/if}
                    </td>
{/if}

{*budget --------------------------------------------------*}
{ if $columns.budget }
                    <td>
                        {$exportData[row].budget|escape:'html'}
                    </td>
{/if}


{*approved --------------------------------------------------*}
{ if $columns.approved }
                    <td>
                        {$exportData[row].approved|escape:'html'}
                    </td>
{/if}

{*status --------------------------------------------------*}
{ if $columns.status }
                    <td>
                        {$exportData[row].status|escape:'html'}
                    </td>
{/if}

{*billable --------------------------------------------------*}
{ if $columns.billable }
                    <td>
                        {$exportData[row].billable|escape:'html'}%
                    </td>
{/if}

{*client name --------------------------------------------------*}
{ if $columns.customer }
                    <td>
                        {$exportData[row].customerName|escape:'html'}
                    </td>
{/if}

{*project name -------------------------------------------------*}
{ if $columns.project }
                    <td>
                            {$exportData[row].projectName|escape:'html'}
                    </td>
{/if}


{*activity name and comment bubble --------------------------------*}
{ if $columns.activity }
                    <td>
                            {$exportData[row].activityName|escape:'html'} 
                    </td>
{/if}

{*description --------------------------------------------------*}
{ if $columns.description }
                    <td>
                        {$exportData[row].description|escape:'html'}%
                    </td>
{/if}

{*comment -----------------------------------------------------*}
{ if $columns.comment }
                    <td>
                        {$exportData[row].comment|escape:'html'|replace:"\n":"&#10;"}
                    </td>
{/if}

{*location ----------------------------------------------------*}
{ if $columns.location }
                    <td>
                        {$exportData[row].location|escape:'html'}
                        
                    </td>
{/if}

{*tracking number ---------------------------------------------*}
{ if $columns.trackingNumber }
                    <td>
                        {$exportData[row].trackingNumber|escape:'html'}
                        
                    </td>
{/if}

{*user --------------------------------------------------------*}
{ if $columns.user }
                    <td>
                        {$exportData[row].username|escape:'html'}
                        
                    </td>
{/if}

{*cleared -----------------------------------------------------*}
{ if $columns.cleared }
          <td>
                      {if $exportData[row].cleared}cleared{else}{/if}
          </td>
{/if}
          

                </tr>
               
{/section}

</table> 

</body> 
</html>  
 
