<script type="text/javascript"> 
  $(document).ready(function() {

    var options = { 
      beforeSubmit:  function() { 

        if ( ($('#password').val() != "" || $('#retypePassword').val() != "")
              && !validatePassword($('#password').val(),$('#retypePassword').val()))
          return false;

        if ($('#core_prefs').attr('submitting')) {
          return false;
        }
        else {
          $('#core_prefs').attr('submitting', true);
          return true;
        }
      },
      success: function() {
        $('#core_prefs').removeAttr('submitting');

        window.location.reload();
      },
      'error' : function() {
          $('#core_prefs').removeAttr('submitting');
      }
    }; 
    
    $('#core_prefs').ajaxForm(options); 
    $('#floater_innerwrap').tabs({ selected: 0 });

  }); 
</script>

<?php
    $this->floater()
        ->setTitle($this->translate('preferences'))
        ->setFormAction('processor.php')
        ->setFormId('core_prefs')
        ->addTab('prefGeneral', $this->translate('general'))
        ->addTab('prefSublists', $this->translate('sublists'))
        ->addTab('prefList', $this->translate('list'));

    echo $this->floater()->floaterBegin();
?>

    <input name="axAction" type="hidden" value="editPrefs" />
    <input name="id" type="hidden" value="0" />

    <?php echo $this->floater()->tabContentBegin('prefGeneral'); ?>
        <ul>
          <li>
            <label for="skin"><?php echo $this->kga['lang']['skin']?>:</label>
              <?php echo $this->formSelect('skin', $this->kga['conf']['skin'], null, $this->skins); ?>
          </li>

          <li>
            <label for="pw"><?php echo $this->kga['lang']['newPassword']?>:</label>
            <input type="password" name="password" size="15" id="password" /> <?php echo $this->kga['lang']['minLength']?>
          </li>

          <li>
            <label for="pw"><?php echo $this->kga['lang']['retypePassword']?>:</label>
            <input type="password" name="retypePassword" size="15" id="retypePassword" />
          </li>

          <li>
            <label for="rate"><?php echo $this->kga['lang']['my_rate']?>:</label>
            <?php echo $this->formText('rate', str_replace('.',$this->kga['conf']['decimalSeparator'],$this->rate), array(
              'size' => 9)); ?>
          </li>

          <li>
            <label for="lang"><?php echo $this->kga['lang']['lang']?>:</label>
              <?php echo $this->formSelect('lang', $this->kga['conf']['lang'], null, $this->langs); ?>
          </li>

          <li>
            <label for="timezone"><?php echo $this->kga['lang']['timezone']?>:</label>
              <?php echo $this->timeZoneSelect('timezone', $this->kga['timezone']); ?>
          </li>

          <li>
            <label for="autoselection"></label>
            <?php echo $this->formCheckbox('autoselection', '1',array('checked' => $this->kga['conf']['autoselection']));
                echo $this->kga['lang']['autoselection']?>
          </li>

          <li>
            <label for="openAfterRecorded"></label>
            <?php echo $this->formCheckbox('openAfterRecorded', '1',array('checked' => isset($this->kga['conf']['openAfterRecorded']) && $this->kga['conf']['openAfterRecorded']));
                echo $this->kga['lang']['openAfterRecorded']?>
          </li>
        </ul>
    <?php echo $this->floater()->tabContentEnd(); ?>

    <?php echo $this->floater()->tabContentBegin('prefSublists'); ?>
        <ul>
          <li>
            <?php echo $this->kga['lang']['sublistAnnotations']?>:
            <?php echo $this->formSelect('sublistAnnotations', isset($this->kga['conf']['sublistAnnotations']) && $this->kga['conf']['sublistAnnotations'], null, array(
$this->kga['lang']['timelabel'], $this->kga['lang']['export_extension']['costs'], $this->kga['lang']['timelabel'].' & '. $this->kga['lang']['export_extension']['costs']
)); ?>
          </li>
          <li>
            <label for="flip_project_display"></label>
            <?php echo $this->formCheckbox('flip_project_display', '1',array('checked' => $this->kga['conf']['flip_project_display'])),
                $this->kga['lang']['flip_project_display']?>
          </li>
          <li>
            <label for="project_comment_flag"></label>
            <?php echo $this->formCheckbox('project_comment_flag', '1',array('checked' => $this->kga['conf']['project_comment_flag'])),
                $this->kga['lang']['project_comment_flag']?>
          </li>
          <li>
            <label for="showIDs"></label>
            <?php echo $this->formCheckbox('showIDs', '1',array('checked' => $this->kga['conf']['showIDs'])),
                $this->kga['lang']['showIDs']?>
          </li>
          <li>
            <label for="noFading"></label>
            <?php echo $this->formCheckbox('noFading', '1',array('checked' => $this->kga['conf']['noFading'])),
                $this->kga['lang']['noFading']?>
          </li>
          <li>
            <label for="user_list_hidden"></label>
            <?php echo $this->formCheckbox('user_list_hidden', '1',array('checked' => $this->kga['conf']['user_list_hidden'])),
                $this->kga['lang']['user_list_hidden']?>
          </li>
        </ul>
    <?php echo $this->floater()->tabContentEnd(); ?>

    <?php echo $this->floater()->tabContentBegin('prefList'); ?>
        <ul>
          <li>
            <label for="rowlimit"><?php echo $this->kga['lang']['rowlimit']?>:</label>
            <?php echo $this->formText('rowlimit', $this->kga['conf']['rowlimit'], array('size' => 9)); ?>
          </li>
          <li>
            <label for="hideClearedEntries"></label>
            <?php echo $this->formCheckbox('hideClearedEntries', '1',array('checked' => $this->kga['conf']['hideClearedEntries'])), $this->kga['lang']['hideClearedEntries']?>
          </li>
          <li>
            <?php echo $this->kga['lang']['quickdelete']?>:
            <?php echo $this->formSelect('quickdelete', $this->kga['conf']['quickdelete'], null, array($this->kga['lang']['quickdeleteHide'], $this->kga['lang']['quickdeleteShow'], $this->kga['lang']['quickdeleteShowConfirm'])); ?>
          </li>
          <li>
            <label for="showCommentsByDefault"></label>
            <?php echo $this->formCheckbox('showCommentsByDefault', '1',array('checked' => isset($this->kga['conf']['showCommentsByDefault']) && $this->kga['conf']['showCommentsByDefault'])), $this->kga['lang']['showCommentsByDefault']?>
          </li>
          <li>
            <label for="showTrackingNumber"></label>
            <?php echo $this->formCheckbox('showTrackingNumber', '1', array('checked' => isset($this->kga['conf']['showTrackingNumber']) && $this->kga['conf']['showTrackingNumber'])), $this->kga['lang']['showTrackingNumber']?>
          </li>     
          <li>
            <label for="hideOverlapLines"></label>
            <?php echo $this->formCheckbox('hideOverlapLines', '1',array('checked' => isset($this->kga['conf']['hideOverlapLines']) && $this->kga['conf']['hideOverlapLines'])), $this->kga['lang']['hideOverlapLines']?>
          </li>     
        </ul>
    <?php echo $this->floater()->tabContentEnd(); ?>

<?php echo $this->floater()->floaterEnd(); ?>
