{include file="_header.tpl"}
	<div id="div_selectform" style="float: left;width: 230px;background: #efefef; padding: 10px;margin-right: 20px;border: 1px solid #999;">
		<form id="selectform" action="" method="post">
			
			<input type="submit" name="submit" value="Excell-Export" id="Excell-Export">
			
		<!-- <form id="selectform" action="" method="post" onsubmit="return false;"> -->
			
			<br />
			<br />
			
			Filter:
			<div id="div_filter">
				<select name="select_filter" onchange="form_showJobs();return false;">
					<option value = "-1">alle</option>
					<option value = "1">abgerechnet</option>
					<option value = "0">offen</option>
				</select>
			</div>
			
			<br />
			
			von:<br />
			{html_select_date field_array="startdatum" field_order="DMY" start_year="-20" end_year="+1" onchange="form_showJobs();return false;"}<br />
			bis:<br />
			{html_select_date field_array="enddatum" field_order="DMY" start_year="-20" end_year="+1" onchange="form_showJobs();return false;"}<br />
			<br />
			Kunde:<br />
			<select name="select_kunde" onchange="form_changeProject();return false;" style="width: 220px;">
				<option value="0">alle Kunden</option>
			{foreach from=$kunden item="kunde"}
				<option value="{$kunde.knd_ID}">{$kunde.knd_name}</option>
			{/foreach}
			</select><br /><br />
			Projekt:
			<div id="div_selectProjekte">
				<select name="select_projekt" style="width: 220px;" onchange="form_showJobs();return false;">
					<option value="0">alle Projekte</option>
				</select>
			</div>
			<br />
			Tätigkeiten:
			<div id="div_selectEvents">
				<select name="select_events" size="20" multiple="multiple" style="width: 220px;" onchange="form_showJobs();return false;">
					<option>Events</option>
				</select>
			</div>
			
			<br />
			
			Mitarbeiter:
			<div id="div_selectUser">
				<select name="select_user" size="6" multiple="multiple" style="width: 220px;" onchange="form_showJobs();return false;">
					<option>Mitarbeiter</option>
				</select>
			</div> Wenn keine Tätigkeit ausgewählt wurde, werden automatisch alle angezeigt!
			
			<br />
			<br />
			
			Zeitformat:
			<input type="text" name="options_timeformat" value="{$options_timeformat|default:'H:i'}" /><br/>
			H:i:s => Stunde:Minute:Sekunde<br>
			d.m.Y => Tag.Monat.Jahr
			
			<script type="text/javascript">
				form_showEvents();
				form_showUser();
			</script>
			
		</form>

	</div>
	<div id="div_liste">
	</div>
	<br style="clear: both;" />
{include file="_footer.tpl"}