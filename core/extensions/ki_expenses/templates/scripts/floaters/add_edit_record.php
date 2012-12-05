    <script type="text/javascript"> 
        
        $(document).ready(function() {
            $('#help').hide();

            $('#edit_day').datepicker();

            $('#expense_extension_form_add_edit_record').ajaxForm( {
              'success' : function(data) {
                var result = jQuery.parseJSON(data);
                if (result.result == "ok") {
                  floaterClose();
                  expense_extension_reload();
                }
                else {
                  alert(result.message);
                }
              }
            });

            <?php if (!isset($this->id)): ?>
            $("#add_edit_expense_project_ID").selectOptions(""+selected_project+"");
            <?php endif; ?>
            $('#floater_innerwrap').tabs({ selected: 0 });
        }); 
        
    </script>


<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title"><?php echo !isset($this->id) ? $this->kga['lang']['add'] : $this->kga['lang']['edit']?></span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();"><?php echo $this->kga['lang']['close']?></a>
            <a href="#" class="help" onClick="$(this).blur(); $('#help').slideToggle();"><?php echo $this->kga['lang']['help']?></a>
        </div>  
    </div>

    <div id="help">
        <div class="content">        
            <?php echo $this->kga['lang']['dateAndTimeHelp']?>
        </div>
    </div>
    
    <div class="menuBackground">

      <ul class="menu tabSelection">
          <li class="tab norm"><a href="#general">
                      <span class="aa">&nbsp;</span>
                      <span class="bb"><?php echo $this->kga['lang']['general']?></span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
          <li class="tab norm"><a href="#extended">
                      <span class="aa">&nbsp;</span>
                      <span class="bb"><?php echo $this->kga['lang']['advanced']?></span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
      </ul>
    </div>

    <form id="expense_extension_form_add_edit_record" action="../extensions/ki_expenses/processor.php" method="post"> 
                <input name="id" type="hidden" value="<?php echo $this->id?>" />
                <input name="axAction" type="hidden" value="add_edit_record" />


    <div id="floater_tabs" class="floater_content">

            <fieldset id="general">
                
                <ul>
                
                   <li>
                       <label for="projectID"><?php echo $this->kga['lang']['project']?>:</label>
                       <div class="multiFields">
                        <?php
                           echo $this->formSelect('projectID', $this->selected_project, array(
                              'id' => 'add_edit_expense_project_ID',
                              'class' => 'formfield',
                              'size' => '5',
                              'style' => 'width:400px',
                              'tabindex' => '1',
                              'onchange' => 'expense_add_edit_validate();'), $this->projects);
                        ?>
                        <br/>
                        <input type="input" style="width:395px;margin-top:3px" tabindex="2" size="10" name="filter" id="filter" onkeyup="filter_selects('add_edit_expense_project_ID', this.value); expense_add_edit_validate();"/>
                       </div>
                   </li>

                <li>
                     <label for="edit_day"><?php echo $this->kga['lang']['day']?>:</label>
                     <input id='edit_day' type='text' name='edit_day' value='<?php echo $this->escape($this->edit_day)?>' maxlength='10' size='10' tabindex='5' <?php if ($this->kga['conf']['autoselection']):?> onClick="this.select();" <?php endif; ?> />
                </li>


              
                   <li>
                       <label for="edit_time"><?php echo $this->kga['lang']['timelabel']?>:</label>
                        <input id='edit_time' type='text' name='edit_time' value='<?php echo $this->escape($this->edit_time)?>' maxlength='8'  size='8'  tabindex='7' <?php if ($this->kga['conf']['autoselection']):?> onClick="this.select();" <?php endif; ?> />
                        <a href="#" onClick="expense_pasteNow(); $(this).blur(); return false;"><?php echo $this->kga['lang']['now']?></a>
                   </li>


                   <li>
                       <label for="multiplier"><?php echo $this->kga['lang']['multiplier']?>:</label>
                        <input id='multiplier' type='text' name='multiplier' value='<?php echo $this->escape($this->multiplier)?>' maxlength='8'  size='8'  tabindex='9' <?php if ($this->kga['conf']['autoselection']):?> onClick="this.select();" <?php endif; ?> />
                   </li>


                   <li>
                       <label for="edit_value"><?php echo $this->kga['lang']['expense']?>:</label>
                        <input id='edit_value' type='text' name='edit_value' value='<?php echo $this->escape($this->edit_value)?>' maxlength='8'  size='8'  tabindex='10' <?php if ($this->kga['conf']['autoselection']):?> onClick="this.select();" <?php endif; ?> />
                   </li>


                   <li>
                       <label for="designation"><?php echo $this->kga['lang']['designation']?>:</label>
                        <input id='designation' type='text' name='designation' value='<?php echo $this->escape($this->designation)?>' maxlength='20'  size='20'  tabindex='11' <?php if ($this->kga['conf']['autoselection']):?> onClick="this.select();" <?php endif; ?> />
                   </li>

                   
          </ul>
          </fieldset>

          <fieldset id="extended">
            <ul>
          <li>
                        <label for="erase"><?php echo $this->kga['lang']['refundable_long']?>:</label>
                        <input type='checkbox' id='refundable' name='refundable' <?php if ($this->refundable):?> checked="checked" <?php endif; ?> tabindex='12'/>
                   </li>

                   <li>
                        <label for="comment"><?php echo $this->kga['lang']['comment']?>:</label>
                        <textarea id='comment' style="width:395px" class='comment' name='comment' cols='40' rows='5' tabindex='13'><?php echo $this->escape($this->comment)?></textarea>
                   </li>
                   
                   <li>
                       <label for="commentType"><?php echo $this->kga['lang']['commentType']?>:</label>
                       <?php echo $this->formSelect('commentType', array_search($this->commentType, $this->commentTypes), array(
                         'id' => 'commentType',
                         'class' => 'formfield',
                         'tabindex' => '14'), $this->commentTypes); ?>
                   </li>
                   
                    <li>
                        <label for="erase"><?php echo $this->kga['lang']['erase']?>:</label>
                        <input type='checkbox' id='erase' name='erase' tabindex='15'/>
                   </li>
        
                </ul>
            </fieldset>

    </div>


                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='<?php echo $this->kga['lang']['cancel']?>' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['submit']?>' />
                </div>

        </form>
</div>