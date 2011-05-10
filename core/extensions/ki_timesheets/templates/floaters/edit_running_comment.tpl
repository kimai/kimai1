{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            $('#edit_running_comment').ajaxForm(function() { 
                floaterClose();
                ts_ext_reload();
            });
        }); 
    </script>
{/literal}

<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title">{$kga.lang.comment}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
        </div>       
    </div>

    <div class="floater_content">

    {* send to CORE (!!!) processor *}
    
        <form id="edit_running_comment" action="../extensions/ki_timesheets/processor.php" method="post"> 
            <fieldset>

                <ul>
                
                    <li>
                        <label for="comment">{$kga.lang.comment}:</label>
                        <textarea id='comment' style="width:395px" class='comment' name='comment' cols='40' rows='5' tabindex='13'>{$comment|escape:'html'}</textarea>
                   </li>
                   
                   <li>
                       <label for="comment_type">{$kga.lang.comment_type}:</label>
                       <select id="comment_type" class="formfield" name="comment_type" tabindex="14" >
                           {html_options values=$comment_values output=$comment_types selected=$comment_active}
                       </select>
                   </li>    

                </ul>
 
                <input name="axAction"     type="hidden" value="edit_running_comment" />   
                <input name="axValue"      type="hidden" value="{$id}" />     
                                             
                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}' />
                </div>
                
            </fieldset>
        </form>
        
    </div>
</div>
