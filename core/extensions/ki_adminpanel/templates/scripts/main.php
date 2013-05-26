    <script type="text/javascript"> 
        $(document).ready(function() {
            adminPanel_extension_onload();
        }); 
    </script>

<div id="adminPanel_extension_panel">

<!-- edit customers -->

    <div id="adminPanel_extension_sub6">
        <div class="adminPanel_extension_panel_header">
            <a onClick="adminPanel_extension_subtab_expand(6)">
                <span class="adminPanel_extension_accordeon_triangle"></span>
                <?php echo $this->kga['lang']['customers']?>
            </a>
        </div>
        <div id="adminPanel_extension_s6" class="adminPanel_extension_subtab adminPanel_extension_subject">
            <?php echo $this->customer_display?>
        </div>
    </div>

<!-- edit projects -->

    <div id="adminPanel_extension_sub7">
        <div class="adminPanel_extension_panel_header">
            <a onClick="adminPanel_extension_subtab_expand(7)">
                <span class="adminPanel_extension_accordeon_triangle"></span>
                <?php echo $this->kga['lang']['projects']?>
            </a>
        </div>
        <div id="adminPanel_extension_s7" class="adminPanel_extension_subtab adminPanel_extension_subject">
            <?php echo $this->project_display?>
        </div>
    </div>

<!-- edit activities -->

<div id="adminPanel_extension_sub8">
    <div class="adminPanel_extension_panel_header">
        <a onClick="adminPanel_extension_subtab_expand(8)">
            <span class="adminPanel_extension_accordeon_triangle"></span>
            <?php echo $this->kga['lang']['activities']?>
        </a>
    </div>
    <div id="adminPanel_extension_s8" class="adminPanel_extension_subtab adminPanel_extension_subject">
        <?php echo $this->activity_display ?>
    </div>
</div>

<!-- edit users -->
	<div id="adminPanel_extension_sub1">
		<div class="adminPanel_extension_panel_header">
			<a onClick="adminPanel_extension_subtab_expand(1)">
			    <span class="adminPanel_extension_accordeon_triangle"></span>
			    <?php echo $this->kga['lang']['users']?>
			</a>
		</div>
		<div id="adminPanel_extension_s1" class="adminPanel_extension_subtab adminPanel_extension_4cols">
			<?php echo $this->admin['users']; ?>
		</div>
	</div>

<!-- edit groups -->

	<div id="adminPanel_extension_sub2">
		<div class="adminPanel_extension_panel_header">
			<a onClick="adminPanel_extension_subtab_expand(2)">
			    <span class="adminPanel_extension_accordeon_triangle"></span>
			    <?php echo $this->kga['lang']['groups']?>
			</a>
		</div>
		<div id="adminPanel_extension_s2" class="adminPanel_extension_subtab adminPanel_extension_4cols">
			<?php echo $this->admin['groups']?>
		</div>
	</div>

<!-- edit global roles -->

        <div id="adminPanel_extension_sub9">
                <div class="adminPanel_extension_panel_header">
                        <a onClick="adminPanel_extension_subtab_expand(9)">
                            <span class="adminPanel_extension_accordeon_triangle"></span>
                            <?php echo $this->kga['lang']['globalRoles']?>
                        </a>
                </div>
                <div id="adminPanel_extension_s9" class="adminPanel_extension_subtab">
                        <?php echo $this->globalRoles_display?>
                </div>
        </div>

<!-- edit membership roles -->

        <div id="adminPanel_extension_sub10">
                <div class="adminPanel_extension_panel_header">
                        <a onClick="adminPanel_extension_subtab_expand(10)">
                            <span class="adminPanel_extension_accordeon_triangle"></span>
                            <?php echo $this->kga['lang']['membershipRoles']?>
                        </a>
                </div>
                <div id="adminPanel_extension_s10" class="adminPanel_extension_subtab">
                        <?php echo $this->membershipRoles_display?>
                </div>
        </div>
	
<!-- edit status -->

	<div id="adminPanel_extension_sub3">
		<div class="adminPanel_extension_panel_header">
			<a onClick="adminPanel_extension_subtab_expand(3)">
			    <span class="adminPanel_extension_accordeon_triangle"></span>
			    <?php echo $this->kga['lang']['status']?>
			</a>
		</div>
		<div id="adminPanel_extension_s3" class="adminPanel_extension_subtab adminPanel_extension_4cols">
			<?php echo $this->admin['status'] ?>
		</div>
	</div>
	
<?php if ($this->showAdvancedTab): ?>
    <!-- advanced -->
	<div id="adminPanel_extension_sub4">
		<div class="adminPanel_extension_panel_header">
			<a onClick="adminPanel_extension_subtab_expand(4)">
			    <span class="adminPanel_extension_accordeon_triangle"></span>
			    <?php echo $this->kga['lang']['advanced']?>
			</a>
		</div>
		<div id="adminPanel_extension_s4" class="adminPanel_extension_subtab adminPanel_extension_4cols">
			<?php echo $this->admin['advanced']?>
		</div>
	</div>
<?php endif; ?>

<?php if (isset($this->admin['database'])): ?>
    <!-- DB -->
    <div id="adminPanel_extension_sub5">
        <div class="adminPanel_extension_panel_header">
            <a onClick="adminPanel_extension_subtab_expand(5)">
                <span class="adminPanel_extension_accordeon_triangle"></span>
                <?php echo $this->kga['lang']['database']?>
            </a>
        </div>
        <div id="adminPanel_extension_s5" class="adminPanel_extension_subtab adminPanel_extension_4cols">
            <?php echo $this->admin['database']?>
        </div>
    </div>
<?php endif; ?>

</div>
