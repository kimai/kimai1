{literal}
    <script type="text/javascript"> 
        $(document).ready(function() {
            export_extension_onload();
        }); 
    </script>
{/literal}
<div id="export_panel">
	<div class="w">
		<div class="c">
			<div class="w">
				<div class="c">
					
					<div id="export_extension_tab_filter">
						<select id="export_extension_tab_filter_cleared" name="cleared" onChange="export_extension_reload()">
						  <option value="-1" {if !$kga.conf.hideClearedEntries}selected="selected"{/if}>{$kga.lang.export_extension.cleared_all}</option>
						  <option value="1">{$kga.lang.export_extension.cleared_cleared}</option>
						  <option value="0" {if $kga.conf.hideClearedEntries}selected="selected"{/if}>{$kga.lang.export_extension.cleared_open}</option>
						</select>
						<select id="export_extension_tab_filter_refundable" name="refundable" onChange="export_extension_reload()">
						  <option value="-1" selected="selected">{$kga.lang.export_extension.refundable_all}</option>
						  <option value="0">{$kga.lang.export_extension.refundable_refundable}</option>
						  <option value="1">{$kga.lang.export_extension.refundable_not_refundable}</option>
						</select>
                        <select id="export_extension_tab_filter_type" name="type" onChange="export_extension_reload()">
                         <option value="-1" selected="selected">{$kga.lang.export_extension.times_and_expenses}</option>
                         <option value="0">{$kga.lang.export_extension.times}</option>
                         <option value="1">{$kga.lang.export_extension.expenses}</option>
                       </select>
					</div>
					
					<div id="export_extension_tab_timeformat">
						<span>{$kga.lang.export_extension.timeformat}:<a href="#" class="helpfloater">{$kga.lang.export_extension.export_timeformat_help}</a></span>
						<input type="text" name="time_format" value="{$timeformat|escape:'html'}" id="export_extension_timeformat" onChange="export_extension_reload()">
						<span>{$kga.lang.export_extension.dateformat}:<a href="#" class="helpfloater">{$kga.lang.export_extension.export_timeformat_help}</a></span>
						<input type="text" name="date_format" value="{$dateformat|escape:'html'}" id="export_extension_dateformat" onChange="export_extension_reload()">
					</div>
					
					<div id="export_extension_tab_location">
						<span>{$kga.lang.export_extension.stdrd_location}</span>
						<input type="text" name="std_loc" value="" id="export_extension_default_location" onChange="export_extension_reload()">
					</div>
					
				</div>
			</div>
			<div class="l">&nbsp;</div><div class="r">&nbsp;</div>
		</div>
	</div>
	<div class="l">
		<div class="w">
			<div class="c">
				<a id="export_extension_select_filter"     href="#" class="select_btn">{$kga.lang.export_extension.filter}</a>
				<a id="export_extension_select_location"   href="#" class="select_btn">{$kga.lang.export_extension.location}</a>
				<a id="export_extension_select_timeformat" href="#" class="select_btn">{$kga.lang.export_extension.timeformat}</a>
			</div>
		</div>
		<div class="l">&nbsp;</div>
	</div>
	<div class="r">
		<div class="w">
			<div class="c">
				<a id="export_extension_export_pdf" href="#" class="output_btn">{$kga.lang.export_extension.exportPDF}</a>
				<a id="export_extension_export_xls" href="#" class="output_btn">{$kga.lang.export_extension.exportXLS}</a>
				<a id="export_extension_export_csv" href="#" class="output_btn">{$kga.lang.export_extension.exportCSV}</a>
				<a id="export_extension_print"      href="#" class="output_btn">{$kga.lang.export_extension.print}</a>
			</div>
		</div>
		<div class="l">&nbsp;</div><div class="r">&nbsp;</div>
	</div>
</div>






