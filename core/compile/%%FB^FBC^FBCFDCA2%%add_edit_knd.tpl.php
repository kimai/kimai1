<?php /* Smarty version 2.6.20, created on 2011-12-11 16:08:44
         compiled from add_edit_knd.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'add_edit_knd.tpl', 55, false),array('modifier', 'escape', 'add_edit_knd.tpl', 86, false),array('function', 'html_options', 'add_edit_knd.tpl', 110, false),)), $this); ?>
<?php echo '    
    <script type="text/javascript"> 
        $(document).ready(function() {
    
            $(\'.disableInput\').click(function(){
              var input = $(this);
              if (input.is (\':checked\'))
                input.siblings().attr("disabled","disabled");
              else
                input.siblings().attr("disabled","");
            });

            $(\'#add_edit_knd\').ajaxForm(function() { 

                if ($(\'#knd_grps\').val() == null) {
                  alert("'; ?>
<?php echo $this->_tpl_vars['kga']['lang']['atLeastOneGroup']; ?>
<?php echo '");
                  return;
                }

                floaterClose();
                hook_chgKnd();
            });
             $(\'#floater_innerwrap\').tabs({ selected: 0 });
        }); 
    </script>
'; ?>


<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title"><?php if ($this->_tpl_vars['id']): ?><?php echo $this->_tpl_vars['kga']['lang']['edit']; ?>
: <?php echo $this->_tpl_vars['kga']['lang']['knd']; ?>
<?php else: ?><?php echo $this->_tpl_vars['kga']['lang']['new_knd']; ?>
<?php endif; ?></span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();"><?php echo $this->_tpl_vars['kga']['lang']['close']; ?>
</a>
        </div>       
    </div>
    
    <div class="menuBackground">

      <ul class="menu tabSelection">
          <li class="tab norm"><a href="#general">
                      <span class="aa">&nbsp;</span>
                      <span class="bb"><?php echo $this->_tpl_vars['kga']['lang']['general']; ?>
</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
          <li class="tab norm"><a href="#address">
                      <span class="aa">&nbsp;</span>
                      <span class="bb"><?php echo $this->_tpl_vars['kga']['lang']['address']; ?>
</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
          <li class="tab norm"><a href="#contact">
                      <span class="aa">&nbsp;</span>
                      <span class="bb"><?php echo $this->_tpl_vars['kga']['lang']['contact']; ?>
</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
<?php if (count($this->_tpl_vars['sel_grp_IDs']) > 1): ?>
          <li class="tab norm"><a href="#groups">
                      <span class="aa">&nbsp;</span>
                      <span class="bb"><?php echo $this->_tpl_vars['kga']['lang']['groups']; ?>
</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
<?php endif; ?>
          <li class="tab norm"><a href="#comment">
                      <span class="aa">&nbsp;</span>
                      <span class="bb"><?php echo $this->_tpl_vars['kga']['lang']['comment']; ?>
</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
      </ul>
    </div>
    
    <form id="add_edit_knd" action="processor.php" method="post"> 
                
    <input name="knd_filter"   type="hidden" value="0" />

    <input name="axAction"     type="hidden" value="add_edit_KndPctEvt" />   
    <input name="axValue"      type="hidden" value="knd" />   
    <input name="id"           type="hidden" value="<?php echo $this->_tpl_vars['id']; ?>
" />   

    <div id="floater_tabs" class="floater_content">

            <fieldset id="general">

                <ul>
                
                    <li>
                        <label for="knd_name" ><?php echo $this->_tpl_vars['kga']['lang']['knd']; ?>
:</label>
                        <input type="text" name="knd_name" id="focus" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['knd_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
                    </li>

                    <li>
                        <label for="knd_vat" ><?php echo $this->_tpl_vars['kga']['lang']['vat']; ?>
:</label>
                        <input type="text" name="knd_vat"  value="<?php echo ((is_array($_tmp=$this->_tpl_vars['knd_vat'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
                    </li> 

                    <li>
                         <label for="knd_visible"><?php echo $this->_tpl_vars['kga']['lang']['visibility']; ?>
:</label>
                         <input name="knd_visible" type="checkbox" value='1' <?php if ($this->_tpl_vars['knd_visible'] || ! $this->_tpl_vars['id']): ?>checked="checked"<?php endif; ?> />
                    </li>   

                    <li>
                      <label for="knd_password"><?php echo $this->_tpl_vars['kga']['lang']['password']; ?>
:</label>
                      <div class="multiFields">
                        <input type="password" name='knd_password' cols='30' rows='3' value="" <?php if (! $this->_tpl_vars['knd_password']): ?>disabled="disabled"<?php endif; ?>/><br/>
                        <input type="checkbox" name="knd_no_password" value="1" class="disableInput" <?php if (! $this->_tpl_vars['knd_password']): ?>checked="checked"<?php endif; ?>><?php echo $this->_tpl_vars['kga']['lang']['nopassword']; ?>

                      </div>
                    </li> 

                    <li>
                      <label for="knd_timezone"><?php echo $this->_tpl_vars['kga']['lang']['timezone']; ?>
:</label>
                      <select name="knd_timezone">
                        <?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['timezones'],'output' => $this->_tpl_vars['timezones'],'selected' => $this->_tpl_vars['knd_timezone']), $this);?>

                      </select>
                    </li>

                </ul>
                
            </fieldset>

            <fieldset id="comment">

                <ul>

                    <li>
                         <label for="knd_comment"><?php echo $this->_tpl_vars['kga']['lang']['comment']; ?>
:</label>
                         <textarea class='comment' name='knd_comment' cols='30' rows='3' ><?php echo ((is_array($_tmp=$this->_tpl_vars['knd_comment'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</textarea>
                    </li>   

                </ul>
                
            </fieldset>

<?php if (count($this->_tpl_vars['sel_grp_IDs']) > 1): ?>
            <fieldset id="groups">

                <ul>
                    
                    <li>
                        <label for="knd_grp" ><?php echo $this->_tpl_vars['kga']['lang']['groups']; ?>
:</label>
                        <select class="formfield" id ="knd_grps" name="knd_grp[]" multiple size='3' style="width:255px">
                            <?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['sel_grp_IDs'],'output' => $this->_tpl_vars['sel_grp_names'],'selected' => $this->_tpl_vars['grp_selection']), $this);?>

                        </select>
                    </li>

                </ul>
                
            </fieldset>
<?php else: ?>
                    <input id="knd_grps" name="knd_grp[]" type="hidden" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['grp_selection']['0'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
<?php endif; ?>  

            <fieldset id="address">

                <ul>

                    <li>
                        <label for="knd_company" ><?php echo $this->_tpl_vars['kga']['lang']['company']; ?>
:</label>
                        <input type="text" name="knd_company"  value="<?php echo ((is_array($_tmp=$this->_tpl_vars['knd_company'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
                    </li>

                    <li>
                        <label for="knd_contact" ><?php echo $this->_tpl_vars['kga']['lang']['contactPerson']; ?>
:</label>
                        <input type="text" name="knd_contact"  value="<?php echo ((is_array($_tmp=$this->_tpl_vars['knd_contact'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
                    </li>
                                      
                    <li>
                        <label for="knd_street" ><?php echo $this->_tpl_vars['kga']['lang']['street']; ?>
:</label>
                        <input type="text" name="knd_street"  value="<?php echo ((is_array($_tmp=$this->_tpl_vars['knd_street'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
                    </li>
                          
                    <li>
                        <label for="knd_zipcode" ><?php echo $this->_tpl_vars['kga']['lang']['zipcode']; ?>
:</label>
                        <input type="text" name="knd_zipcode"  value="<?php echo ((is_array($_tmp=$this->_tpl_vars['knd_zipcode'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
                    </li>
                          
                    <li>
                        <label for="knd_city" ><?php echo $this->_tpl_vars['kga']['lang']['city']; ?>
:</label>
                        <input type="text" name="knd_city"  value="<?php echo ((is_array($_tmp=$this->_tpl_vars['knd_city'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
                    </li>     

                </ul>
                
            </fieldset>

            <fieldset id="contact">

                <ul>   
                          
                    <li>
                        <label for="knd_tel" ><?php echo $this->_tpl_vars['kga']['lang']['telephon']; ?>
:</label>
                        <input type="text" name="knd_tel"  value="<?php echo ((is_array($_tmp=$this->_tpl_vars['knd_tel'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
                    </li>        
                          
                    <li>
                        <label for="knd_fax" ><?php echo $this->_tpl_vars['kga']['lang']['fax']; ?>
:</label>
                        <input type="text" name="knd_fax"  value="<?php echo ((is_array($_tmp=$this->_tpl_vars['knd_fax'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
                    </li>        
                          
                    <li>
                        <label for="knd_mobile" ><?php echo $this->_tpl_vars['kga']['lang']['mobilephone']; ?>
:</label>
                        <input type="text" name="knd_mobile"  value="<?php echo ((is_array($_tmp=$this->_tpl_vars['knd_mobile'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
                    </li>        
                          
                    <li>
                        <label for="knd_mail" ><?php echo $this->_tpl_vars['kga']['lang']['mail']; ?>
:</label>
                        <input type="text" name="knd_mail"  value="<?php echo ((is_array($_tmp=$this->_tpl_vars['knd_mail'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
                    </li>        
                          
                    <li>
                        <label for="knd_homepage" ><?php echo $this->_tpl_vars['kga']['lang']['homepage']; ?>
:</label>
                        <input type="text" name="knd_homepage"  value="<?php echo ((is_array($_tmp=$this->_tpl_vars['knd_homepage'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" />
                    </li>        

                </ul>
                
            </fieldset>
        
    </div>
                                             
                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='<?php echo $this->_tpl_vars['kga']['lang']['cancel']; ?>
' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='<?php echo $this->_tpl_vars['kga']['lang']['submit']; ?>
' />
                </div>
        </form>
        
</div>