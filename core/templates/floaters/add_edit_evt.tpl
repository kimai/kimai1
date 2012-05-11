{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {

            $('.disableInput').click(function(){
              var input = $(this);
              if (input.is (':checked'))
                $('#activityProjects').attr("disabled","");
              else
                $('#activityProjects').attr("disabled","disabled");
            });

             $('#add_edit_evt').ajaxForm(function() {

                if ($('#activityGroups').val() == null) {
                  alert("{/literal}{$kga.lang.atLeastOneGroup}{literal}");
                  return;
                }

                 floaterClose();
                 hook_chgEvt();
             });
             $('#floater_innerwrap').tabs({ selected: 0 });
         }); 
    </script>
{/literal}

<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title">{if $id}{$kga.lang.edit}: {$kga.lang.evt}{else}{$kga.lang.new_evt}{/if}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
        </div>       
    </div>
    
    <div class="menuBackground">

      <ul class="menu tabSelection">
          <li class="tab norm"><a href="#general">
                      <span class="aa">&nbsp;</span>
                      <span class="bb">{$kga.lang.general}</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
          <li class="tab norm"><a href="#projects">
                      <span class="aa">&nbsp;</span>
                      <span class="bb">{$kga.lang.pcts}</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
{if $groupIDs|@count gt 1}
          <li class="tab norm"><a href="#groups">
                      <span class="aa">&nbsp;</span>
                      <span class="bb">{$kga.lang.groups}</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
{/if}
          <li class="tab norm"><a href="#comment">
                      <span class="aa">&nbsp;</span>
                      <span class="bb">{$kga.lang.comment}</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
      </ul>
    </div>
    
        <form id="add_edit_evt" action="processor.php" method="post"> 
                
                <input name="evt_filter"   type="hidden" value="0" />
 
                <input name="axAction" type="hidden" value="add_edit_KndPctEvt" />   
                <input name="axValue" type="hidden" value="evt" />   
                <input name="id" type="hidden" value="{$id}" />   


    <div id="floater_tabs" class="floater_content">


            <fieldset id="general">

                <ul>
                
                    <li>
                        <label for="name" >{$kga.lang.evt}:</label>
                        <input type="text" name="name" id="focus" value="{$name|escape:'html'}" />
                    </li>
                
                    <li>
                        <label for="defaultRate" >{$kga.lang.default_rate}:</label>
                        <input type="text" name="defaultRate" value="{$defaultRate|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
                    </li>
                
                    <li>
                        <label for="myRate" >{$kga.lang.my_rate}:</label>
                        <input type="text" name="myRate" id="focus" value="{$myRate|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
                    </li>
                
                    <li>
                        <label for="myRate" >{$kga.lang.fixed_rate}:</label>
                        <input type="text" name="fixedRate" id="focus" value="{$fixedRate|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
                    </li>

                    <li>
                         <label for="visible">{$kga.lang.visibility}:</label>
                         <input name="visible" type="checkbox" value='1' {if $visible || !$id }checked="checked"{/if} />
                    </li>
                 </ul>

            </fieldset>
            
            <fieldset id="comment">
                <ul>
                    <li>
                         <label for="comment">{$kga.lang.comment}:</label>
                         <textarea class='comment' name='comment' cols='30' rows='5' >{$comment|escape:'html'}</textarea>
                    </li>
                </ul>
            </fieldset>
          

{if $groupIDs|@count gt 1}   
    <fieldset id="groups">
                <ul>
                 
                    <li>
                        <label for="activityGroups" >{$kga.lang.groups}:</label>
                        <select class="formfield" id="activityGroups" name="activityGroups[]" multiple size='5' style="width:255px">
                            {html_options values=$groupIDs output=$groupNames selected=$selectedGroups}
                        </select>
                    </li>      
                </ul>
            </fieldset>
{else}
                    <input id="groups" id="activityGroups" name="groups[]" type="hidden" value="{$selectedGroups.0|escape:'html'}" />
{/if}        

    <fieldset id="projects">
                <ul>

                    <li>
                        <label for="projects">{$kga.lang.pcts}:</label>
                        <select class="formfield" id="activityProjects" name="projects[]" multiple size='5' style="width:255px" {if !$evt_assignable}disabled="disabled"{/if}>
                            {html_options values=$projectIDs output=$projectNames selected=$selectedProjects}
                        </select>
                    </li>

                    <li>
                        <label for="assignable" >{$kga.lang.taskAssignable}:</label>
                        <input type="checkbox" class="disableInput formfield" value="1" name="assignable" {if $assignable}checked="checked"{/if}/> {$kga.lang.taskAssignableDescription}
                    </li>


                    
                </ul>
      </fieldset>
        
    </div>
                                             
                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}'/>
                </div>
        </form>
</div>
