{literal}    
    <script type="text/javascript"> 
        
        $(document).ready(function() {
            $('#help').hide();
            $('#floater input#timeformat').attr('value',$('#export_extension_timeformat').attr('value'));
            $('#floater input#dateformat').attr('value',$('#export_extension_dateformat').attr('value'));
            $('#floater input#default_location').attr('value',$('#default_location').attr('value'));
            $('#floater input#axValue').attr('value',filterUsers.join(":")+'|'+filterCustomers.join(":")+'|'+filterProjects.join(":")+'|'+filterActivities.join(":"));
            $('#floater input#filter_cleared').attr('value',$('#export_extension_tab_filter_cleared').attr('value'));
            $('#floater input#filter_refundable').attr('value',$('#export_extension_tab_filter_refundable').attr('value'));
            $('#floater input#filter_type').attr('value',$('#export_extension_tab_filter_type').attr('value'));
            $('#floater input#axColumns').attr('value',export_enabled_columns());
            $('.floater_content fieldset label').css('width','200px');
            
            $('#floater input#first_day').attr('value',new Date($('#pick_in').val()).getTime()/1000);
            $('#floater input#last_day').attr('value',new Date($('#pick_out').val()).getTime()/1000);
        }); 
        
    </script>
{/literal}

<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title">{$kga.lang.export_extension.print}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
        </div>  
    </div>

    <div id="help">
        <div class="content">
        </div>
    </div>


    <div class="floater_content">

        
        <form id="export_extension_form_print" action="../extensions/ki_export/processor.php" method="post" target="_blank"> 
            <fieldset>                  

		        <ul>
			        <li>
				      {$kga.lang.export_extension.print_hint}
              </li>
                
                   <li>
                       <label for="print_summary">{$kga.lang.export_extension.print_summary}:</label>
                       <input type="checkbox" value="true" name="print_summary" id="print_summary" {if $prefs.print_summary}checked="checked"{/if}>
                   </li>
                
                   <li>
                       <label for="reverse_order">{$kga.lang.export_extension.reverse_order}:</label>
                       <input type="checkbox" value="true" name="reverse_order" id="reverse_order" {if $prefs.reverse_order}checked="checked"{/if}/>
                   </li>
				</ul>
{* -------------------------------------------------------------------- *} 

                <input name="axAction" type="hidden" value="export_html" />
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
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}' onClick="floaterClose();"/>
                </div>

{* -------------------------------------------------------------------- *} 

            </fieldset>
	</form>
    </div>
</div>