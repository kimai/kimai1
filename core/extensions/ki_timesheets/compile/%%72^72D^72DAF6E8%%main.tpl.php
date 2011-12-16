<?php /* Smarty version 2.6.20, created on 2011-12-07 17:39:08
         compiled from main.tpl */ ?>
<?php echo '    
    <script type="text/javascript"> 
        $(document).ready(function() {
            ts_ext_onload();
        }); 
    </script>
'; ?>


<div id="zef_head">
    <div class="left">
    <?php if ($this->_tpl_vars['kga']['usr']): ?>
        <a href="#" onClick="floaterShow('../extensions/ki_timesheets/floaters.php','add_edit_record',selected_pct+'|'+selected_evt,0,650,580); $(this).blur(); return false;"><?php echo $this->_tpl_vars['kga']['lang']['add']; ?>
</a>
    <?php endif; ?>
    </div>
    <table>
        <colgroup>
          <col class="options" />
          <col class="date" />
          <col class="from" />
          <col class="to" />
          <col class="time" />
          <col class="wage" />
          <col class="knd" />
          <col class="pct" />
          <col class="evt" />
          <col class="trackingnumber" />
          <col class="username" />
        </colgroup>
        <tbody>
            <tr>
                <td class="option">&nbsp;</td>
                <td class="date"><?php echo $this->_tpl_vars['kga']['lang']['datum']; ?>
</td>
                <td class="from"><?php echo $this->_tpl_vars['kga']['lang']['in']; ?>
</td>
                <td class="to"><?php echo $this->_tpl_vars['kga']['lang']['out']; ?>
</td>
                <td class="time"><?php echo $this->_tpl_vars['kga']['lang']['time']; ?>
</td>
                <td class="wage"><?php echo $this->_tpl_vars['kga']['lang']['wage']; ?>
</td>
                <td class="knd"><?php echo $this->_tpl_vars['kga']['lang']['knd']; ?>
</td>
                <td class="pct"><?php echo $this->_tpl_vars['kga']['lang']['pct']; ?>
</td>
                <td class="evt"><?php echo $this->_tpl_vars['kga']['lang']['evt']; ?>
</td>
                <td class="trackingnumber"><?php echo $this->_tpl_vars['kga']['lang']['trackingnr']; ?>
</td>
                <td class="username"><?php echo $this->_tpl_vars['kga']['lang']['username']; ?>
</td>
            </tr>
        </tbody>
    </table>
</div>

<div id="zef"><?php echo $this->_tpl_vars['zef_display']; ?>
 </div>