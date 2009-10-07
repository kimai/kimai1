{literal}    
    <script type="text/javascript"> 
        
        $(document).ready(function() {
            $('#help').hide();

            $('#xp_ext_form_export_XLS').ajaxForm(function() { 
                
                // $edit_in_time = $('#edit_in_day').val()+$('#edit_in_time').val();
                // $edit_out_time = $('#edit_out_day').val()+$('#edit_out_time').val();
                
				// floaterClose();
				// xp_ext_reload();
                
            });

        }); 
        
    </script>
{/literal}


<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title">{$kga.lang.exportXLS}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
            <a href="#" class="help" onClick="$(this).blur(); $('#help').slideToggle();">{$kga.lang.help}</a>
			<a href="#" class="options down" onClick="floaterOptions();">{$kga.lang.options}</a>
        </div>  
    </div>

    <div id="help">
        <div class="content">        
        </div>
    </div>


    <div id="floater_content"><div id="floater_dimensions">
        
        <form id="xp_ext_form_export_XLS" action="../extensions/ki_timesheets/processor.php" method="post"> 
            <fieldset>
                
                <ul>
                
                   <li>
                       <label for="pct_ID">{$kga.lang.pct}:</label>
                       <input type="input" tabindex="2" size="20" maxlength="20" name="filter" id="filter" />
                   </li>
                   


{* -------------------------------------------------------------------- *} 

                <!-- <input name="id" type="hidden" value="" /> -->
                <input name="axAction" type="hidden" value="export_XLS" />

                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}' />
                </div>

{* -------------------------------------------------------------------- *} 

            </fieldset>
        </form>

    </div></div>
</div>