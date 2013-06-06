    <script type="text/javascript"> 
        
        $(document).ready(function() {
            $('#help').hide();
            $('#floater input#timeformat').prop('value',$('#export_extension_timeformat').prop('value'));
            $('#floater input#dateformat').prop('value',$('#export_extension_dateformat').prop('value'));
            $('#floater input#default_location').prop('value',$('#default_location').prop('value'));
            $('#floater input#axValue').prop('value',filterUsers.join(":")+'|'+filterCustomers.join(":")+'|'+filterProjects.join(":")+'|'+filterActivities.join(":"));
            $('#floater input#filter_cleared').prop('value',$('#export_extension_tab_filter_cleared').prop('value'));
            $('#floater input#filter_refundable').prop('value',$('#export_extension_tab_filter_refundable').prop('value'));
            $('#floater input#filter_type').prop('value',$('#export_extension_tab_filter_type').prop('value'));
            $('#floater input#axColumns').prop('value',export_enabled_columns());
            $('.floater_content fieldset label').css('width','200px');
            
            $('#floater input#first_day').prop('value',new Date($('#pick_in').val()).getTime()/1000);
            $('#floater input#last_day').prop('value',new Date($('#pick_out').val()).getTime()/1000);

        }); 
        
    </script>


<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->kga['lang']['export_extension']['exportCSV']?></span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();"><?php echo $this->kga['lang']['close']?></a>
        </div>  
    </div>

    <div id="help">
        <div class="content">   
        </div>
    </div>


    <div class="floater_content">
        
        <form id="export_extension_form_export_CSV" action="../extensions/ki_export/processor.php" method="post">
	
            <fieldset>
                
                <ul>
                
                   <li>
                       <label for="column_delimiter"><?php echo $this->kga['lang']['export_extension']['column_delimiter']?>:</label>
                       <input type="text" value="<?php echo $this->escape($this->prefs['column_delimiter'])?>" name="column_delimiter" id="column_delimiter" size="1"/>
                   </li>
                
                   <li>
                       <label for="quote_char"><?php echo $this->kga['lang']['export_extension']['quote_char']?>:</label>
                       <input type="text" value="<?php echo $this->escape($this->prefs['quote_char'])?>" name="quote_char" id="quote_char" size="1">
                   </li>
                
                   <li>
                       <label for="reverse_order"><?php echo $this->kga['lang']['export_extension']['reverse_order']?>:</label>
                       <input type="checkbox" value="true" name="reverse_order" id="reverse_order"/>
                   </li>

                   <li>
	      				<?php echo $this->kga['lang']['export_extension']['dl_hint']?>
					</li>
                 </ul>
                   


                <!-- <input name="id" type="hidden" value="" /> -->
                <input name="axAction" type="hidden" value="export_csv" />
                <input name="axValue" id="axValue" type="hidden" value="" />
                <input name="first_day" id="first_day" type="hidden" value="" />
                <input name="last_day" id="last_day" type="hidden" value="" />
                <input name="axColumns"  id="axColumns" type="hidden" value=""/>
                <input name="timeformat" id="timeformat" type="hidden" value=""/>
                <input name="dateformat" id="dateformat" type="hidden" value=""/>
                <input name="default_location" id="default_location" type="hidden" value=""/>
                <input name="filter_cleared" id="filter_cleared" type="hidden" value=""/>
                <input name="filter_refundable" id="filter_refundable" type="hidden" value=""/>
                <input name="filter_type" id="filter_type" type="hidden" value=""/>

                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='<?php echo $this->kga['lang']['cancel']?>' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['submit']?>' onClick="floaterClose();"/>
                </div>


            </fieldset>
        </form>

    </div>
</div>