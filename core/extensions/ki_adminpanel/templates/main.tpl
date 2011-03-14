{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            ap_ext_onload();
        }); 
    </script>
{/literal}

<div id="ap_ext_panel">

{* edit customers *}

    <div id="ap_ext_sub5">
        <div class="ap_ext_panel_header">
            <a onClick="ap_ext_subtab_expand(5)">
                <span class="ap_ext_accordeon_triangle"></span>
                {$kga.lang.knds}
            </a>
        </div>
        <div id="ap_ext_s5" class="ap_ext_subtab ap_ext_subject">
            {$knd_display}
        </div>
    </div>

{* edit projects *}

    <div id="ap_ext_sub6">
        <div class="ap_ext_panel_header">
            <a onClick="ap_ext_subtab_expand(6)">
                <span class="ap_ext_accordeon_triangle"></span>
                {$kga.lang.pcts}
            </a>
        </div>
        <div id="ap_ext_s6" class="ap_ext_subtab ap_ext_subject">
            {$pct_display}
        </div>
    </div>

{* edit events *}

<div id="ap_ext_sub7">
    <div class="ap_ext_panel_header">
        <a onClick="ap_ext_subtab_expand(7)">
            <span class="ap_ext_accordeon_triangle"></span>
            {$kga.lang.evts}
        </a>
    </div>
    <div id="ap_ext_s7" class="ap_ext_subtab ap_ext_subject">
        {$evt_display}
    </div>
</div>

{* edit users *}
{ if $kga.usr.usr_sts == 0 }
	<div id="ap_ext_sub1">
		<div class="ap_ext_panel_header">
			<a onClick="ap_ext_subtab_expand(1)">
			    <span class="ap_ext_accordeon_triangle"></span>
			    {$kga.lang.users}
			</a>
		</div>
		<div id="ap_ext_s1" class="ap_ext_subtab ap_ext_4cols">
			{$admin.users}
		</div>
	</div>
{/if}
{* edit groups *}

	<div id="ap_ext_sub2">
		<div class="ap_ext_panel_header">
			<a onClick="ap_ext_subtab_expand(2)">
			    <span class="ap_ext_accordeon_triangle"></span>
			    {$kga.lang.groups}
			</a>
		</div>
		<div id="ap_ext_s2" class="ap_ext_subtab ap_ext_4cols">
			{$admin.groups}
		</div>
	</div>

{* advanced *}
{ if $kga.usr.usr_sts == 0 }
	<div id="ap_ext_sub3">
		<div class="ap_ext_panel_header">
			<a onClick="ap_ext_subtab_expand(3)">
			    <span class="ap_ext_accordeon_triangle"></span>
			    {$kga.lang.advanced}
			</a>
		</div>
		<div id="ap_ext_s3" class="ap_ext_subtab ap_ext_4cols">
			{$admin.advanced}
		</div>
	</div>
{ /if}

{* DB *}
{ if $kga.usr.usr_sts == 0 }
    <div id="ap_ext_sub4">
        <div class="ap_ext_panel_header">
            <a onClick="ap_ext_subtab_expand(4)">
                <span class="ap_ext_accordeon_triangle"></span>
                {$kga.lang.database}
            </a>
        </div>
        <div id="ap_ext_s4" class="ap_ext_subtab ap_ext_4cols">
            {$admin.database}
        </div>
    </div>

{ /if}

</div>
