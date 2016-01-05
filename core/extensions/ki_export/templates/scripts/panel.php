<div id="export_panel">
    <div class="w">
        <div class="c">
            <div class="w">
                <div class="c">
                    <div id="export_extension_tab_filter">
                        <select id="export_extension_tab_filter_cleared" name="cleared" onchange="export_extension_reload()">
                            <option value="-1" <?php if (!$this->kga['conf']['hideClearedEntries']): ?> selected="selected"<?php endif; ?>> <?php echo $this->kga['lang']['export_extension']['cleared_all'] ?></option>
                            <option value="1"><?php echo $this->kga['lang']['export_extension']['cleared_cleared'] ?></option>
                            <option value="0" <?php if ($this->kga['conf']['hideClearedEntries']): ?> selected="selected" <?php endif; ?>> <?php echo $this->kga['lang']['export_extension']['cleared_open'] ?></option>
                        </select>
                        <select id="export_extension_tab_filter_refundable" name="refundable" onchange="export_extension_reload()">
                            <option value="-1" selected="selected"><?php echo $this->kga['lang']['export_extension']['refundable_all'] ?></option>
                            <option value="0"><?php echo $this->kga['lang']['export_extension']['refundable_refundable'] ?></option>
                            <option value="1"><?php echo $this->kga['lang']['export_extension']['refundable_not_refundable'] ?></option>
                        </select>
                        <select id="export_extension_tab_filter_type" name="type" onchange="export_extension_reload()">
                            <option value="-1" selected="selected"><?php echo $this->kga['lang']['export_extension']['times_and_expenses'] ?></option>
                            <option value="0"><?php echo $this->kga['lang']['export_extension']['times'] ?></option>
                            <option value="1"><?php echo $this->kga['lang']['export_extension']['expenses'] ?></option>
                        </select>
                    </div>
                    <div id="export_extension_tab_timeformat">
                        <span><?php echo $this->kga['lang']['export_extension']['timeformat'] ?>
                            :<a href="#" class="helpfloater"><?php echo $this->kga['lang']['export_extension']['export_timeformat_help'] ?></a></span>
                        <input type="text" name="time_format" value="<?php echo $this->escape($this->timeformat) ?>" id="export_extension_timeformat" onchange="export_extension_reload()">
                        <span><?php echo $this->kga['lang']['export_extension']['dateformat'] ?>
                            :<a href="#" class="helpfloater"><?php echo $this->kga['lang']['export_extension']['export_timeformat_help'] ?></a></span>
                        <input type="text" name="date_format" value="<?php echo $this->escape($this->dateformat) ?>" id="export_extension_dateformat" onchange="export_extension_reload()">
                    </div>
                    <div id="export_extension_tab_location">
                        <span><?php echo $this->kga['lang']['export_extension']['stdrd_location'] ?></span>
                        <input type="text" name="std_loc" value="" id="export_extension_default_location" onchange="export_extension_reload()">
                    </div>
                </div>
            </div>
            <div class="l">&nbsp;</div>
            <div class="r">&nbsp;</div>
        </div>
    </div>
    <div class="l">
        <div class="w">
            <div class="c">
                <a id="export_extension_select_filter" href="#" class="select_btn"><?php echo $this->kga['lang']['filter'] ?></a>
                <a id="export_extension_select_location" href="#" class="select_btn"><?php echo $this->kga['lang']['export_extension']['stdrd_location'] ?></a>
                <a id="export_extension_select_timeformat" href="#" class="select_btn"><?php echo $this->kga['lang']['export_extension']['timeformat'] ?></a>
            </div>
        </div>
        <div class="l">&nbsp;</div>
    </div>
    <div class="r">
        <div class="w">
            <div class="c">
                <a id="export_extension_export_pdf" href="#" class="output_btn"><?php echo $this->kga['lang']['export_extension']['exportPDF'] ?></a>
                <a id="export_extension_export_xls" href="#" class="output_btn"><?php echo $this->kga['lang']['export_extension']['exportXLS'] ?></a>
                <a id="export_extension_export_csv" href="#" class="output_btn"><?php echo $this->kga['lang']['export_extension']['exportCSV'] ?></a>
                <a id="export_extension_print" href="#" class="output_btn"><?php echo $this->kga['lang']['export_extension']['print'] ?></a>
            </div>
        </div>
        <div class="l">&nbsp;</div>
        <div class="r">&nbsp;</div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        export_extension_onload();
    });
</script>