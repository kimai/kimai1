<html>
  <head>
    <title></title>
    <meta content="">
    
{literal}
	<style type="text/css" media="all">
		body, div, dl, dt, dd, ul, ol, li, h1, h2, h3, h4, h5, h6, pre, form, fieldset, input, textarea, p, blockquote, th, td {
		  margin: 0;
		  padding: 0;
		}
		table {
		  border-collapse: collapse;
		  border-spacing: 0;
      margin-bottom:30px;
		}
		fieldset, img { border: 0; }
		address, caption, cite, code, dfn, em, strong, th, var {
		  font-style: normal;
		  font-weight: normal;
		}
		ol, ul { list-style: none; }
		caption, th { text-align: left; }
    h2 {
      margin-bottom:10px;
    }
		q:before { content: ''; }
		q:after { content: ''; }
		abbr, acronym { border: 0; }
		th {
		  text-align: left;
		  font-weight: bold;
		}
		th {
		  border-right: 1px solid #999999;
		  padding: 5px;
		}
		th:first-child { border-left: 1px solid #999999; }
		td {
		  border-right: 1px solid #999999;
		  padding: 5px;
		}
		td:first-child { border-left: 1px solid #999999; }
		h1 {
		  font-size: 150%;
		  font-weight: bold;
		  margin-bottom: 10px;
		}
		
	</style>
		
	<style type="text/css" media="print">
		
		body {
			color: black;
			font-family: Arial, Verdana, sans-serif;
			font-size: 11px;
		}

		th {
			background: #ccc;
			border-top: 1px solid #999;
			border-bottom: 1px solid #999;
			font-family: Arial, Verdana, sans-serif;
			font-size: 11px;
			font-weight: normal;
		}

		td {
			border-bottom: 1px solid #999;
			font-family: Arial, Verdana, sans-serif;
			font-size: 11px;
		}

		#div_selectform {
			display: none;
		}

		#div_liste {

		}

		#invertbtn, .invertclm {
			display: none;
		}
		
	</style>

	<style type="text/css" media="screen">
		body {
			color: black;
			font-family: Arial, Verdana, sans-serif;
			font-size: 11px;
      padding:10px;
		}

		th {
			background: #ccc;
			border-top: 1px solid #999;
			border-bottom: 1px solid #999;
			font-family: Arial, Verdana, sans-serif;
			font-size: 11px;
			font-weight: normal;
		}

		td {
			border-bottom: 1px solid #999;
			font-family: Arial, Verdana, sans-serif;
			font-size: 11px;
		}

		#div_liste {
			margin-top:10px;
			float: left;
			width: 600px;
		}
	</style>

{/literal}
  </head>
  <body>

<h2> {$kga.lang.export_extension.time_period}: {$timespan|escape:'html'} </h2>

{ if $customersFilter != "" }
<br/><b>{$kga.lang.customers}</b>: {$customersFilter|escape:'html'}
{/if}
{ if $projectsFilter != "" }
<br/><b>{$kga.lang.projects}</b>: {$projectsFilter|escape:'html'}
{/if}
<br/>

{if $summary != 0}
  <h2>{$kga.lang.export_extension.summary}</h2>


  <table border="1">
    <tbody>
      <tr>
        <th>{$kga.lang.activity}</th>
  { if $columns.dec_time }
        <th>{$kga.lang.export_extension.duration}</th>
  {/if}
  { if $columns.wage }
        <th>{$kga.lang.export_extension.costs}</th>
  {/if}
      </tr>

  {section name=row loop=$summary}
      <tr>
        <td>{$summary[row].name|escape:'html'}</td>
  { if $columns.dec_time }
        <td>{if $summary[row].time != -1}
          {$summary[row].time|escape:'html'}
        {/if}</td>
  {/if}
  { if $columns.wage }
        <td>{$summary[row].wage|escape:'html'}</td>
  {/if}
  { if $columns.budget }
        <td>{$summary[row].budget|escape:'html'}</td>
  {/if}
  { if $columns.approved }
        <td>{$summary[row].approved|escape:'html'}</td>
  {/if}
      </tr>
  {/section}

      <tr>
        <td>
          <i>{$kga.lang.export_extension.finalamount}</i>
        </td>
  { if $columns.dec_time }
        <td>{$timeSum|number_format:2:$kga.conf.decimalSeparator:""|escape:'html'}</td>
  {/if}
  { if $columns.wage }
        <td>{$wageSum|number_format:2:$kga.conf.decimalSeparator:""|escape:'html'}</td>
  {/if}
    { if $columns.wage }
        <td>{$budgetSum|number_format:2:$kga.conf.decimalSeparator:""|escape:'html'}</td>
  {/if}
    { if $columns.wage }
        <td>{$approvedSum|number_format:2:$kga.conf.decimalSeparator:""|escape:'html'}</td>
  {/if}
      </tr>

    </tbody>
  </table>
{/if}

<h2>{$kga.lang.export_extension.full_list}</h2>

          <table border="1">
            <tbody>

{*column headers------------------------------------------------*}
                <tr>
{ if $columns.date         } <th>{$kga.lang.datum}</th>       { /if }
{ if $columns.from         } <th>{$kga.lang.in}</th>          { /if }
{ if $columns.to           } <th>{$kga.lang.out}</th>         { /if }
{ if $columns.time         } <th>{$kga.lang.time}</th>        { /if }
{ if $columns.dec_time     } <th>{$kga.lang.timelabel}</th>   { /if }
{ if $columns.rate         } <th>{$kga.lang.rate}</th>        { /if }
{ if $columns.wage         } <th>{$kga.currency_name}</th>    { /if }
{ if $columns.budget       } <th>{$kga.lang.budget}</th>      { /if }
{ if $columns.approved     } <th>{$kga.lang.approved}</th>    { /if }
{ if $columns.status       } <th>{$kga.lang.status}</th>      { /if }
{ if $columns.billable     } <th>{$kga.lang.billable}</th>    { /if }
{ if $columns.customer     } <th>{$kga.lang.customer}</th>    { /if }
{ if $columns.project      } <th>{$kga.lang.project}</th>     { /if }
{ if $columns.activity     } <th>{$kga.lang.activity}</th>    { /if }
{ if $columns.decription   } <th>{$kga.lang.description}</th> { /if }
{ if $columns.comment      } <th>{$kga.lang.comment}</th>     { /if }
{ if $columns.location     } <th>{$kga.lang.location}</th>   { /if }
{ if $columns.trackingNumber   } <th>{$kga.lang.trackingNumber}</th>  { /if }
{ if $columns.user         } <th>{$kga.lang.username}</th>    { /if }
{ if $columns.cleared      } <th>{$kga.lang.cleared}</th>     { /if }

                </tr>
               
{section name=row loop=$exportData}

    
                <tr>
    
{*datum --------------------------------------------------------*}
{ if $columns.date }
                    <td>
                        { if $custom_dateformat }
                        {$exportData[row].time_in|date_format:$custom_dateformat|escape:'html'}
                        { else }
                        {$exportData[row].time_in|date_format:$kga.date_format.1|escape:'html'}
                        { /if }
                    </td>
{/if}

{*in -----------------------------------------------------------*}
{ if $columns.from }
                    <td>
                        { if $custom_timeformat }
                        {$exportData[row].time_in|date_format:$custom_timeformat|escape:'html'}
                        { else }
                        {$exportData[row].time_in|date_format:"%H:%M"|escape:'html'}
                        { /if }
                    </td>
{/if}

{*out ----------------------------------------------------------*}
{ if $columns.to }
                    <td>
                    
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
                    <td>
                    
{if $exportData[row].duration}

                            {$exportData[row].formattedDuration}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>
{/if}

{*decimal time --------------------------------------------------*}
{ if $columns.dec_time }
                    <td>
                    
{if $exportData[row].decimalDuration}
                            {$exportData[row].decimalDuration|replace:'.':$kga.conf.decimalSeparator|escape:'html'}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>
{/if}

{*rate ---------------------------------------------------------*}
{ if $columns.rate }
                    <td>
                    
                            {$exportData[row].rate|replace:'.':$kga.conf.decimalSeparator|escape:'html'}
                    </td>
{/if}

{*task wage ----------------------------------------------------*}
{ if $columns.wage }
                    <td>
                    
{if $exportData[row].wage}
                    
                        {$exportData[row].wage|replace:'.':$kga.conf.decimalSeparator|escape:'html'}
                      
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
                        {$exportData[row].billable|escape:'html'}
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
                        {$exportData[row].description|escape:'html'}
                    </td>
{/if}

{*comment -----------------------------------------------------*}
{ if $columns.comment }
                    <td>
                        {$exportData[row].comment|escape:'html'|nl2br}
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
                      {if $exportData[row].cleared}{$kga.lang.cleared}{else}{/if}
					</td>
{/if}
					

                </tr>
               
{/section}

{if $timeSum > 0 || $wageSum > 0}
<tr>
<td colspan="{$columns|@count}">
{$kga.lang.export_extension.finalamount}
</td>
</tr>
<tr>
  { if $columns.date } <td></td> {/if}
  { if $columns.from } <td></td> {/if}
  { if $columns.to    }<td></td> {/if}
  { if $columns.time } <td></td> {/if}
  { if $columns.dec_time } <td>
    {$timeSum|escape:'html'}
  </td> {/if}
  { if $columns.rate } <td></td> {/if}
  { if $columns.wage } <td>
    {$wageSum|escape:'html'}
  </td>{/if}
  { if $columns.budget } <td>
    {$budgetSum|escape:'html'}</td> {/if}
  { if $columns.approved } <td>
    {$approvedSum|escape:'html'}</td> {/if}
  { if $columns.status } <td></td> {/if}
  { if $columns.billable } <td></td> {/if}
  { if $columns.customer } <td></td> {/if}
  { if $columns.project } <td></td> {/if}
  { if $columns.activity } <td></td> {/if}
  { if $columns.description } <td></td> {/if}
  { if $columns.comment } <td></td> {/if}
  { if $columns.location } <td></td> {/if}
  { if $columns.trackingNumber } <td></td> {/if}
  { if $columns.user } <td></td> {/if}
  { if $columns.cleared } <td></td> {/if}
</tr>
{/if}
            </tbody>   
        </table>
</body>
</html>