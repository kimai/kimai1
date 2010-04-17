{literal}    
    <script type="text/javascript"> 
        
        $(document).ready(function() {
            $('#help').hide();
            $('#floater input#timeformat').attr('value',$('#xp_ext_timeformat').attr('value'));
            $('#floater input#dateformat').attr('value',$('#xp_ext_dateformat').attr('value'));
            $('#floater input#default_location').attr('value',$('#default_location').attr('value'));
            $('#floater input#axValue').attr('value',filterUsr.join(":")+'|'+filterKnd.join(":")+'|'+filterPct.join(":"));
            $('#floater input#filter_cleared').attr('value',$('#xp_ext_tab_filter_cleared').attr('value'));
            $('#floater input#filter_type').attr('value',$('#xp_ext_tab_filter_type').attr('value'));
            $('#floater input#axColumns').attr('value',xp_enabled_columns());
            $('#floater_content fieldset label').css('width','200px');
        }); 
        
    </script>
{/literal}


<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title">{$kga.lang.xp_ext.exportPDF}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
        </div>  
    </div>

    <div id="help">
        <div class="content">
        </div>
    </div>


    <div id="floater_content"><div id="floater_dimensions">
        
        <form id="xp_ext_form_export_PDF" action="../extensions/ki_export/processor.php" method="post" target="_blank"> 
            <fieldset>
                
                <ul>
                
                   <li>
                       <label for="print_comments">{$kga.lang.xp_ext.print_comment}:</label>
                       <input type="checkbox" value="true" name="print_comments" id="print_comments" checked="checked"/>
                   </li>
                
                   <li>
                       <label for="print_summary">{$kga.lang.xp_ext.print_summary}:</label>
                       <input type="checkbox" value="true" name="print_summary" id="print_summary" checked="checked">
                   </li>
                
                   <li>
                       <label for="create_bookmarks">{$kga.lang.xp_ext.create_bookmarks}:</label>
                       <input type="checkbox" value="true" name="create_bookmarks" id="create_bookmarks" checked="checked"/>
                   </li>
                
                   <li>
                       <label for="download_pdf">{$kga.lang.xp_ext.download_pdf}:</label>
                       <input type="checkbox" value="true" name="download_pdf" id="download_pdf" checked="checked"/>
                   </li>
                
                   <li>
                       <label for="hide_cleared_entries">{$kga.lang.xp_ext.hide_cleared_entries}:</label>
                       <input type="checkbox" value="true" name="hide_cleared_entries" id="hide_cleared_entries" checked="checked"/>
                   </li>
                
                   <li>
                       <label for="customer_new_page">{$kga.lang.xp_ext.customer_new_page}:</label>
                       <input type="checkbox" value="true" name="customer_new_page" id="customer_new_page"/>
                   </li>
                
                   <li>
                       <label for="axAction">{$kga.lang.xp_ext.pdf_format}:</label>
                       <select name="axAction" id="axAction">
                         <option value="export_pdf">{$kga.lang.xp_ext.export_pdf}</option>
                         <option value="export_pdf2">{$kga.lang.xp_ext.export_pdf2}</option>
                       </select>
                   </li>
                   <li>
	 					{$kga.lang.xp_ext.dl_hint}
					</li>
                 </ul>
                   



{* -------------------------------------------------------------------- *} 

                <!-- <input name="id" type="hidden" value="" /> -->
                <input name="axValue" id="axValue" type="hidden" value="" />
                <input name="axColumns"  id="axColumns" type="hidden" value=""/>
                <input name="timeformat" id="timeformat" type="hidden" value=""/>
                <input name="dateformat" id="dateformat" type="hidden" value=""/>
                <input name="default_location" id="default_location" type="hidden" value=""/>
                <input name="filter_cleared" id="filter_cleared" type="hidden" value=""/>
                <input name="filter_type" id="filter_type" type="hidden" value=""/>

                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}' onClick="floaterClose();"/>
                </div>

{* -------------------------------------------------------------------- *} 

            </fieldset>
        </form>

    </div></div>
</div>