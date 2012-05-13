<?php

class Extensions {

  private $ext_configs = array();

  private $php_include_files   = array();
  private $css_extension_files = array();
  private $js_extension_files  = array();
  private $extensions          = array();
  private $tab_change_trigger  = array();
  private $tss_hooks           = array();
  private $rec_hooks           = array();
  private $stp_hooks           = array();
  private $chu_hooks           = array();
  private $chk_hooks           = array();
  private $chp_hooks           = array();
  private $che_hooks           = array();
  private $lft_hooks           = array(); // list filter hooks
  private $rsz_hooks           = array(); // resize hooks
  private $timeouts            = array();

  private $extensionsDir;
  private $kga;

  public function __construct(&$kga,$dir) {
    $this->kga = &$kga;
    $this->extensionsDir = $dir;
  }

  /**
   * PARSE EXTENSION CONFIGS (ext_configs)
   */
  public function loadConfigurations() {
    $handle = opendir($this->extensionsDir);

    if (!$handle)
      return;
    
    while (false !== ($dir = readdir($handle))) {

      if (is_file($dir) OR ($dir == ".") OR ($dir == "..") OR (substr($dir,0) == ".") OR (substr($dir,0,1) == "#"))
        continue;

      // make path absolute
      $dir = $this->extensionsDir.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR;

      if (file_exists($dir.'kimai_include.php'))
        $this->addValue($this->extensionsDir.$dir.'kimai_include.php',$php_include_files);

      if (!file_exists($dir.'config.ini'))
        continue;

      $settings = parse_ini_file($dir.'config.ini');
                       	
     	// Check if user has the correct rank to use this extension      
     	if (isset($this->kga['user']))
        switch ($this->kga['user']['status']) {
          case 0:
          if ($settings['ADMIN_ALLOWED'] != "1")
            continue 2;
          break;

          case 1:
            if ($settings['GROUP_LEADER_ALLOWED'] != "1")
              continue 2;
          break;
        
          case 2:
            if ($settings['USER_ALLOWED'] != "1")
              continue 2;
          break;
        }
     	else if ($settings['CUSTOMER_ALLOWED'] != "1")
     	  continue;

      $this->extensions[] = array('name' => $settings['EXTENSION_NAME'],
        'key' => $settings['EXTENSION_KEY'],
        'initFile' => $settings['EXTENSION_INIT_FILE'],
        'tabChangeTrigger' => isset($settings['TAB_CHANGE_TRIGGER'])?$settings['TAB_CHANGE_TRIGGER']:"");
                       	
      $this->addOptionalValue($settings,'CSS_INCLUDE_FILES',$this->css_extension_files);
		
		  // add JavaScript files
		  $this->addOptionalValue($settings,'JS_INCLUDE_FILES',$this->js_extension_files);
		
      // read trigger function for tab change
      $this->addOptionalValue($settings,'TAB_CHANGE_TRIGGER',$this->tab_change_trigger);
                                  
      // read hook triggers
      $this->addOptionalValue($settings,'TIMESPACE_CHANGE_TRIGGER', $this->tss_hooks);
      $this->addOptionalValue($settings,'BUZZER_RECORD_TRIGGER', $this->rec_hooks);
      $this->addOptionalValue($settings,'BUZZER_STOP_TRIGGER', $this->stp_hooks);
      $this->addOptionalValue($settings,'CHANGE_USER_TRIGGER', $this->chu_hooks);
      $this->addOptionalValue($settings,'CHANGE_CUSTOMER_TRIGGER', $this->chk_hooks);
      $this->addOptionalValue($settings,'CHANGE_PROJECT_TRIGGER', $this->chp_hooks);
      $this->addOptionalValue($settings,'CHANGE_ACTIVITY_TRIGGER', $this->che_hooks);
      $this->addOptionalValue($settings,'LIST_FILTER_TRIGGER', $this->lft_hooks);
      $this->addOptionalValue($settings,'RESIZE_TRIGGER', $this->rsz_hooks);
                    
      // add Timeout clearing
      $this->addOptionalValue($settings,'REG_TIMEOUTS', $this->timeouts);
    }

    closedir($handle);
  }

  /**
   * Add a settings value to the list. Duplicate entries will be prevented.
   * If the settings value is an array each item in the entry will be added.
   */
  private function addValue($value,&$list) {
    if(is_array($value)) {
			foreach($value as $subvalue) {
				if(!in_array($subvalue, $list))
					$list[] = $subvalue;
			}
		} else {
			if(!in_array($value, $list))
				$list[] = $value;
		}
  }

  private function addOptionalValue(&$settings,$key,&$list) {
    if (isset($settings[$key]))
      $this->addValue($settings[$key],$list);
  }

  public function extensionsTabData() {
    return $this->extensions;
  }

  public function phpIncludeFiles() {
    return $this->php_include_files;
  }

  public function cssExtensionFiles() {
    return $this->css_extension_files;
  }

  public function jsExtensionFiles() {
    return $this->js_extension_files;
  }

  public function timeframeChangedHooks() {
    return implode($this->tss_hooks);
  }

  public function buzzerRecordHooks() {
    return implode($this->rec_hooks);
  }

  public function buzzerStopHooks() {
    return implode($this->stp_hooks);
  }

  public function usersChangedHooks() {
    return implode($this->chu_hooks);
  }

  public function customersChangedHooks() {
    return implode($this->chk_hooks);
  }

  public function projectsChangedHooks() {
    return implode($this->chp_hooks);
  }

  public function activitiesChangedHooks() {
    return implode($this->che_hooks);
  }

  public function filterHooks() {
    return implode($this->lft_hooks);
  }

  public function resizeHooks() {
    return implode($this->rsz_hooks);
  }

  public function timeoutList() {
    $timeoutlist = "";
    foreach ($this->timeouts as $timeout) {
        $timeoutlist .=  "kill_timeout('" . $timeout . "');" ;
    }
    return $timeoutlist;
  }

}

?>