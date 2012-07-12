{literal}    
    <script type="text/javascript"> 
        
        $(document).ready(function() {
            $('#help').hide();
            $('#floater input#timeformat').attr('value',$('#export_extension_timeformat').attr('value'));
            $('#floater input#dateformat').attr('value',$('#export_extension_dateformat').attr('value'));
            $('#floater input#default_location').attr('value',$('#export_extension_default_location').attr('value'));
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
        <span id="floater_title">{$kga.lang.export_extension.exportPDF}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
        </div>  
    </div>

    <div id="help">
        <div class="content">
        </div>
    </div>


    <div class="floater_content">
        
        <form id="export_extension_form_export_PDF" action="../extensions/ki_export/processor.php" method="post" target="_blank"> 
            <fieldset>
                
                <ul>
                
                   <li>
                       <label for="print_comments">{$kga.lang.export_extension.print_comment}:</label>
                       <input type="checkbox" value="true" name="print_comments" id="print_comments" {if $prefs.print_comments}checked="checked"{/if}/>
                   </li>
                
                   <li>
                       <label for="print_summary">{$kga.lang.export_extension.print_summary}:</label>
                       <input type="checkbox" value="true" name="print_summary" id="print_summary" {if $prefs.print_summary}checked="checked"{/if}>
                   </li>
                
                   <li>
                       <label for="create_bookmarks">{$kga.lang.export_extension.create_bookmarks}:</label>
                       <input type="checkbox" value="true" name="create_bookmarks" id="create_bookmarks" {if $prefs.create_bookmarks}checked="checked"{/if}/>
                   </li>
                
                   <li>
                       <label for="download_pdf">{$kga.lang.export_extension.download_pdf}:</label>
                       <input type="checkbox" value="true" name="download_pdf" id="download_pdf" {if $prefs.download_pdf}checked="checked"{/if}/>
                   </li>
                
                   <li>
                       <label for="customer_new_page">{$kga.lang.export_extension.customer_new_page}:</label>
                       <input type="checkbox" value="true" name="customer_new_page" id="customer_new_page" {if $prefs.customer_new_page}checked="checked"{/if}/>
                   </li>
                
                   <li>
                       <label for="reverse_order">{$kga.lang.export_extension.reverse_order}:</label>
                       <input type="checkbox" value="true" name="reverse_order" id="reverse_order" {if $prefs.reverse_order}checked="checked"{/if}/>
                   </li>
                
                   <li>
                       <label for="reverse_order">{$kga.lang.comment}:</label>
                       <textarea name="document_comment" id="document_comment"></textarea>
                   </li>
                
                   <li>
                       <label for="axAction">{$kga.lang.export_extension.pdf_format}:</label>
                       <select name="axAction" id="axAction">
                         <option value="export_pdf" {if $prefs.pdf_format=='export_pdf'}selected="selected"{/if}>{$kga.lang.export_extension.export_pdf}</option>
                         <option value="export_pdf2" {if $prefs.pdf_format=='export_pdf2'}selected="selected"{/if}>{$kga.lang.export_extension.export_pdf2}</option>
                       </select>
                   </li>
                   <li>
	 					{$kga.lang.export_extension.dl_hint}
					</li>
                 </ul>
                   



{* -------------------------------------------------------------------- *} 

                <!-- <input name="id" type="hidden" value="" /> -->
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