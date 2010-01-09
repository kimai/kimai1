{literal}
    <script type="text/javascript"> 
        $(document).ready(function() {
            xp_ext_onload();
        }); 
    </script>
{/literal}
<div id="xp_panel">
	<div class="w">
		<div class="c">
			<div class="w">
				<div class="c">
					
					<div id="xp_ext_tab_filter">
						<input type="radio" name="cleared" value="-1" id="xp_ext_cleared_0" onChange="xp_ext_reload()" checked="checked"/> {$kga.lang.xp_ext.cleared_all}
						<input type="radio" name="cleared" value="0" id="xp_ext_cleared_1" onChange="xp_ext_reload()"/> {$kga.lang.xp_ext.cleared_cleared}
						<input type="radio" name="cleared" value="1" id="xp_ext_cleared_2" onChange="xp_ext_reload()"/> {$kga.lang.xp_ext.cleared_open}
					</div>
					
					<div id="xp_ext_tab_timeformat">
						<span>{$kga.lang.xp_ext.timeformat}:<a href="#" class="helpfloater">{$kga.lang.xp_ext.export_timeformat_help}</a></span>
						<input type="text" name="time_format" value="{$timeformat}" id="xp_ext_timeformat" onChange="xp_ext_reload()">
						<span>{$kga.lang.xp_ext.dateformat}:<a href="#" class="helpfloater">{$kga.lang.xp_ext.export_timeformat_help}</a></span>
						<input type="text" name="date_format" value="{$dateformat}" id="xp_ext_dateformat" onChange="xp_ext_reload()">
					</div>
					
					<div id="xp_ext_tab_location">
						<span>{$kga.lang.xp_ext.stdrd_location}</span>
						<input type="text" name="std_loc" value="" id="xp_ext_default_location" onChange="xp_ext_reload()">
					</div>
					
				</div>
			</div>
			<div class="l">&nbsp;</div><div class="r">&nbsp;</div>
		</div>
	</div>
	<div class="l">
		<div class="w">
			<div class="c">
				<a id="xp_ext_select_filter"     href="#" class="select_btn">{$kga.lang.xp_ext.filter}</a>
				<a id="xp_ext_select_location"   href="#" class="select_btn">{$kga.lang.xp_ext.location}</a>
				<a id="xp_ext_select_timeformat" href="#" class="select_btn">{$kga.lang.xp_ext.timeformat}</a>
			</div>
		</div>
		<div class="l">&nbsp;</div>
	</div>
	<div class="r">
		<div class="w">
			<div class="c">
				<a id="xp_ext_export_pdf" href="#" class="output_btn">{$kga.lang.xp_ext.exportPDF}</a>
				<a id="xp_ext_export_xls" href="#" class="output_btn">{$kga.lang.xp_ext.exportXLS}</a>
				<a id="xp_ext_export_csv" href="#" class="output_btn">{$kga.lang.xp_ext.exportCSV}</a>
				<a id="xp_ext_print"      href="#" class="output_btn">{$kga.lang.xp_ext.print}</a>
			</div>
		</div>
		<div class="l">&nbsp;</div><div class="r">&nbsp;</div>
	</div>
</div>






