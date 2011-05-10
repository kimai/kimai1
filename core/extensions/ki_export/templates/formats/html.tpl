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

<h2> {$kga.lang.xp_ext.time_period}: {$timespan|escape:'html'} </h2>

{ if $customersFilter != "" }
<br/><b>{$kga.lang.knds}</b>: {$customersFilter|escape:'html'}
{/if}
{ if $projectsFilter != "" }
<br/><b>{$kga.lang.pcts}</b>: {$projectsFilter|escape:'html'}
{/if}
<br/>

{if $summary != 0}
  <h2>{$kga.lang.xp_ext.summary}</h2>


  <table border="1">
    <tbody>
      <tr>
        <th>{$kga.lang.evt}</th>
  { if $columns.dec_time }
        <th>{$kga.lang.xp_ext.duration}</th>
  {/if}
  { if $columns.wage }
        <th>{$kga.lang.xp_ext.costs}</th>
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
      </tr>
  {/section}

      <tr>
        <td>
          <i>{$kga.lang.xp_ext.finalamount}</i>
        </td>
  { if $columns.dec_time }
        <td>{$timeSum|number_format:2:$kga.conf.decimalSeparator:""|escape:'html'}</td>
  {/if}
  { if $columns.wage }
        <td>{$wageSum|number_format:2:$kga.conf.decimalSeparator:""|escape:'html'}</td>
  {/if}
      </tr>

    </tbody>
  </table>
{/if}

<h2>{$kga.lang.xp_ext.full_list}</h2>

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
{ if $columns.knd          } <th>{$kga.lang.knd}</th>         { /if }
{ if $columns.pct          } <th>{$kga.lang.pct}</th>         { /if }
{ if $columns.action       } <th>{$kga.lang.evt}</th>         { /if }
{ if $columns.comment      } <th>{$kga.lang.comment}</th>     { /if }
{ if $columns.location     } <th>{$kga.lang.zlocation}</th>   { /if }
{ if $columns.trackingnr   } <th>{$kga.lang.trackingnr}</th>  { /if }
{ if $columns.user         } <th>{$kga.lang.username}</th>    { /if }
{ if $columns.cleared      } <th>{$kga.lang.cleared}</th>     { /if }

                </tr>
               
{section name=row loop=$arr_data}

    
                <tr>
    
{*datum --------------------------------------------------------*}
{ if $columns.date }
                    <td>
                        { if $custom_dateformat }
                        {$arr_data[row].time_in|date_format:$custom_dateformat|escape:'html'}
                        { else }
                        {$arr_data[row].time_in|date_format:$kga.date_format.1|escape:'html'}
                        { /if }
                    </td>
{/if}

{*in -----------------------------------------------------------*}
{ if $columns.from }
                    <td>
                        { if $custom_timeformat }
                        {$arr_data[row].time_in|date_format:$custom_timeformat|escape:'html'}
                        { else }
                        {$arr_data[row].time_in|date_format:"%H:%M"|escape:'html'}
                        { /if }
                    </td>
{/if}

{*out ----------------------------------------------------------*}
{ if $columns.to }
                    <td>
                    
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
                    <td>
                    
{if $arr_data[row].zef_time}

                            {$arr_data[row].zef_duration}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>
{/if}

{*decimal time --------------------------------------------------*}
{ if $columns.dec_time }
                    <td>
                    
{if $arr_data[row].dec_zef_time}
                            {$arr_data[row].dec_zef_time|replace:'.':$kga.conf.decimalSeparator|escape:'html'}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>
{/if}

{*rate ---------------------------------------------------------*}
{ if $columns.rate }
                    <td>
                    
                            {$arr_data[row].zef_rate|replace:'.':$kga.conf.decimalSeparator|escape:'html'}
                    </td>
{/if}

{*task wage ----------------------------------------------------*}
{ if $columns.wage }
                    <td>
                    
{if $arr_data[row].wage}
                    
                        {$arr_data[row].wage|replace:'.':$kga.conf.decimalSeparator|escape:'html'}
                      
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
                        {$arr_data[row].comment|escape:'html'|nl2br}
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
                      {if $arr_data[row].cleared}{$kga.lang.cleared}{else}{/if}
					</td>
{/if}
					

                </tr>
               
{/section}

{if $timeSum > 0 || $wageSum > 0}
<tr>
<td colspan="{$columns|@count}">
{$kga.lang.xp_ext.finalamount}
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
  { if $columns.knd } <td></td> {/if}
  { if $columns.pct } <td></td> {/if}
  { if $columns.action } <td></td> {/if}
  { if $columns.comment } <td></td> {/if}
  { if $columns.location } <td></td> {/if}
  { if $columns.trackingnr } <td></td> {/if}
  { if $columns.user } <td></td> {/if}
  { if $columns.cleared } <td></td> {/if}
</tr>
{/if}
            </tbody>   
        </table>
</body>
</html>