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
					
					
					
					<div id="xp_ext_tab_timeformat">
						<span>Zeitformat:</span>
						<input type="text" name="time_format" value="H:M" id="xp_ext_timeformat" onChange="xp_ext_reload()">
						<span>Datumsformat:</span>
						<input type="text" name="date_format" value="d.m." id="xp_ext_dateformat" onChange="xp_ext_reload()">
					</div>
					
					<div id="xp_ext_tab_location">
						<span>Standard-Ort:</span>
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
				<a id="xp_ext_select_filter"     href="#" class="select_btn">Filter</a>
				<a id="xp_ext_select_location"   href="#" class="select_btn">Location</a>
				<a id="xp_ext_select_timeformat" href="#" class="select_btn">Timeformat</a>
			</div>
		</div>
		<div class="l">&nbsp;</div>
	</div>
	<div class="r">
		<div class="w">
			<div class="c">
				
				<a id="xp_ext_export_pdf" href="#" class="output_btn">PDF</a>
				<a id="xp_ext_export_xls" href="#" class="output_btn">XLS</a>
				<a id="xp_ext_print"      href="#" class="output_btn">Print</a>
				
			</div>
		</div>
		<div class="l">&nbsp;</div><div class="r">&nbsp;</div>
	</div>
</div>