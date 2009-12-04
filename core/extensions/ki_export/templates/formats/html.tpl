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
		}
		fieldset, img { border: 0; }
		address, caption, cite, code, dfn, em, strong, th, var {
		  font-style: normal;
		  font-weight: normal;
		}
		ol, ul { list-style: none; }
		caption, th { text-align: left; }
		h1, h2, h3, h4, h5, h6 {
		  font-size: 100%;
		  font-weight: normal;
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
                        {$arr_data[row].time_in|date_format:$custom_dateformat}
                        { else }
                        {$arr_data[row].time_in|date_format:$kga.date_format.1}
                        { /if }
                    </td>
{/if}

{*in -----------------------------------------------------------*}
{ if $columns.from }
                    <td>
                        { if $custom_timeformat }
                        {$arr_data[row].time_in|date_format:$custom_timeformat}
                        { else }
                        {$arr_data[row].time_in|date_format:"%H:%M"}
                        { /if }
                    </td>
{/if}

{*out ----------------------------------------------------------*}
{ if $columns.to }
                    <td>
                    
{if $arr_data[row].time_out}
                        { if $custom_timeformat }
                        {$arr_data[row].time_out|date_format:$custom_timeformat}
                        { else }
                        {$arr_data[row].time_out|date_format:"%H:%M"}
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

                            {$arr_data[row].zef_apos}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>
{/if}

{*decimal time --------------------------------------------------*}
{ if $columns.dec_time }
                    <td>
                    
{if $arr_data[row].dec_zef_time}
                            {$arr_data[row].dec_zef_time}
                      
{else}  
                        &ndash;:&ndash;&ndash;
{/if}
                    </td>
{/if}

{*rate ---------------------------------------------------------*}
{ if $columns.rate }
                    <td>
                    
                            {$arr_data[row].zef_rate}
                    </td>
{/if}

{*task wage ----------------------------------------------------*}
{ if $columns.wage }
                    <td>
                    
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
                        {$arr_data[row].knd_name}
                    </td>
{/if}

{*project name -------------------------------------------------*}
{ if $columns.pct }
                    <td>
                            {$arr_data[row].pct_name}
                    </td>
{/if}


{*event name and comment bubble --------------------------------*}
{ if $columns.action }
                    <td>
                            {$arr_data[row].evt_name} 
                    </td>
{/if}

{*comment -----------------------------------------------------*}
{ if $columns.comment }
                    <td>
                        {$arr_data[row].comment|nl2br}
                    </td>
{/if}

{*location ----------------------------------------------------*}
{ if $columns.location }
                    <td>
                        {$arr_data[row].location}
                        
                    </td>
{/if}

{*tracking number ---------------------------------------------*}
{ if $columns.trackingnr }
                    <td>
                        {$arr_data[row].trackingnr}
                        
                    </td>
{/if}

{*user --------------------------------------------------------*}
{ if $columns.user }
                    <td>
                        {$arr_data[row].username}
                        
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
                
            </tbody>   
        </table>

</body>
</html>