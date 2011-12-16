<?php /* Smarty version 2.6.20, created on 2011-12-08 15:19:00
         compiled from main.tpl */ ?>
<?php echo '    
    <script type="text/javascript"> 
        $(document).ready(function() {
            ap_ext_onload();
        }); 
    </script>
'; ?>


<div id="ap_ext_panel">


    <div id="ap_ext_sub5">
        <div class="ap_ext_panel_header">
            <a onClick="ap_ext_subtab_expand(5)">
                <span class="ap_ext_accordeon_triangle"></span>
                <?php echo $this->_tpl_vars['kga']['lang']['knds']; ?>

            </a>
        </div>
        <div id="ap_ext_s5" class="ap_ext_subtab ap_ext_subject">
            <?php echo $this->_tpl_vars['knd_display']; ?>

        </div>
    </div>


    <div id="ap_ext_sub6">
        <div class="ap_ext_panel_header">
            <a onClick="ap_ext_subtab_expand(6)">
                <span class="ap_ext_accordeon_triangle"></span>
                <?php echo $this->_tpl_vars['kga']['lang']['pcts']; ?>

            </a>
        </div>
        <div id="ap_ext_s6" class="ap_ext_subtab ap_ext_subject">
            <?php echo $this->_tpl_vars['pct_display']; ?>

        </div>
    </div>


<div id="ap_ext_sub7">
    <div class="ap_ext_panel_header">
        <a onClick="ap_ext_subtab_expand(7)">
            <span class="ap_ext_accordeon_triangle"></span>
            <?php echo $this->_tpl_vars['kga']['lang']['evts']; ?>

        </a>
    </div>
    <div id="ap_ext_s7" class="ap_ext_subtab ap_ext_subject">
        <?php echo $this->_tpl_vars['evt_display']; ?>

    </div>
</div>

<?php if ($this->_tpl_vars['kga']['usr']['usr_sts'] == 0): ?>
	<div id="ap_ext_sub1">
		<div class="ap_ext_panel_header">
			<a onClick="ap_ext_subtab_expand(1)">
			    <span class="ap_ext_accordeon_triangle"></span>
			    <?php echo $this->_tpl_vars['kga']['lang']['users']; ?>

			</a>
		</div>
		<div id="ap_ext_s1" class="ap_ext_subtab ap_ext_4cols">
			<?php echo $this->_tpl_vars['admin']['users']; ?>

		</div>
	</div>
<?php endif; ?>

	<div id="ap_ext_sub2">
		<div class="ap_ext_panel_header">
			<a onClick="ap_ext_subtab_expand(2)">
			    <span class="ap_ext_accordeon_triangle"></span>
			    <?php echo $this->_tpl_vars['kga']['lang']['groups']; ?>

			</a>
		</div>
		<div id="ap_ext_s2" class="ap_ext_subtab ap_ext_4cols">
			<?php echo $this->_tpl_vars['admin']['groups']; ?>

		</div>
	</div>
	

	<div id="ap_ext_sub3">
		<div class="ap_ext_panel_header">
			<a onClick="ap_ext_subtab_expand(3)">
			    <span class="ap_ext_accordeon_triangle"></span>
			    <?php echo $this->_tpl_vars['kga']['lang']['status']; ?>

			</a>
		</div>
		<div id="ap_ext_s3" class="ap_ext_subtab ap_ext_4cols">
			<?php echo $this->_tpl_vars['admin']['status']; ?>

		</div>
	</div>
	

<?php if ($this->_tpl_vars['kga']['usr']['usr_sts'] == 0): ?>
	<div id="ap_ext_sub4">
		<div class="ap_ext_panel_header">
			<a onClick="ap_ext_subtab_expand(4)">
			    <span class="ap_ext_accordeon_triangle"></span>
			    <?php echo $this->_tpl_vars['kga']['lang']['advanced']; ?>

			</a>
		</div>
		<div id="ap_ext_s4" class="ap_ext_subtab ap_ext_4cols">
			<?php echo $this->_tpl_vars['admin']['advanced']; ?>

		</div>
	</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['kga']['usr']['usr_sts'] == 0): ?>
    <div id="ap_ext_sub5">
        <div class="ap_ext_panel_header">
            <a onClick="ap_ext_subtab_expand(5)">
                <span class="ap_ext_accordeon_triangle"></span>
                <?php echo $this->_tpl_vars['kga']['lang']['database']; ?>

            </a>
        </div>
        <div id="ap_ext_s5" class="ap_ext_subtab ap_ext_4cols">
            <?php echo $this->_tpl_vars['admin']['database']; ?>

        </div>
    </div>

<?php endif; ?>

</div>