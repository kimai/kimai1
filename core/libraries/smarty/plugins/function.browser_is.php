<?php
/*
 * Smarty {browser_is} function plugin
 *
 * Type:     function
 * Name:     browser_is
 * Purpose:  return true if the client browser matches supplied description.
 */


/* Usage:
 *
 *     1.   HI! the browser that you are using is <i>{browser_is}</i> !
 *
 *     2.   The browser you are using is {browser_is show="version"}
 *
 *     3.   The browser you are using is {browser_is show="all"}
 *
 *     4.   {browser_is vendor="ns"} or
 *          {browser_is vendor="ie" version=4.5 assign=result} or
 *          {browser_is vendor="ns" minversion=4.5 assign=result} or
 *          {browser_is vendor="ie" majorversion=4 assign=result} or
 *          {browser_is vendor="ie" majorversion=5 plattform=windows assign=result} or
 *          {browser_is vendor="ns" majorversion=4 minorversion=5 assign=result}
 *
 *          {if $result} .... {/if}
 */

function smarty_function_browser_is($params, &$smarty)
{

  global $_SERVER;
  $agent = $_SERVER['HTTP_USER_AGENT'];
  $result = $params['assign']; 

/* A good list of agent strings can be found at 
 * http://www.pgts.com.au/pgtsj/pgtsj0208c.html */

  $vendors = array('opera' => 'opera',
                   'webtv' => 'webtv',
                   'mspie' => 'mspie',
                   'konqueror' => 'konqueror',
                   'icab' => 'icab',
                   'omniweb' => 'omniweb',
                   'phoenix' => 'phoenix',
                   'libwww' => 'lynx/amaya',
                   'safari' => 'safari',
                   'galeon' => 'galeon',
                   'compatible. ie' => 'ie',
                   'microsoft internet explorer' => 'ie',
                   'msie' => 'ie',
                   'firebird' => 'ns',
                   'mozilla' => 'ns',
                   'netscape' => 'ns');

$plattforms = array( 
                   'windows' => 'windows',
                   'mac'          => 'mac',
                   'linux'        => 'linux'
  );  
                 
  while (list($match,$vendor)=each($vendors)) {
    if (preg_match('@'.$match.'[ /(v]{0,2}([0-9].[0-9a-zA-Z]{1,6})@i',$agent,$info)) {
      $version=$info[1];
      $pos=strpos($version,".");
      if ($pos>0) {
        $major_version=substr($version,0,$pos);
        $minor_version=substr($version,$pos+1,strlen($version));
      } else {
        $major_version=$version;
        $minor_version=0;
      }
      break;
    }
  }

while (list($match,$plattform) = each($plattforms)) {
                if (preg_match('@'.$match.'@i',$agent)) {
                        break;
                }
  }

  if (isset($params['vendor'])) {
     if (strcmp($vendor,$params['vendor'])) {
       if (isset($result)) $smarty->assign($result,false);
       return "";
    }
  }

  if (isset($params['plattform'])) {
     if (strcmp($plattform,$params['plattform'])) {
       if (isset($result)) $smarty->assign($result,false);
       return "";
    }
  }

  if (isset($params['version'])) {
     if ($version != $params['version']) {
       if (isset($result)) $smarty->assign($result,false);
       return "";
    }
  }

  if (isset($params['minversion'])) {
     if ((float)$version < (float)$params['minversion']) {
       if (isset($result)) $smarty->assign($result,false);
       return "";
    }
  }

  if (isset($params['majorversion'])) {
     if ($major_version != (int)$params['majorversion']) {
       if (isset($result)) $smarty->assign($result,false);
       return "";
    }
  }

  if (isset($params['minorversion'])) {
     if ($minor_version != (int)$params['minorversion']) {
       if (isset($result)) $smarty->assign($result,false);
       return "";
    }
  }

  if (isset($result)) {
    $smarty->assign($result,true);
    return "";
  }

  if (isset($params['show'])) {
    if (!strcmp($params['show'],"version")) {
      return "$plattform $vendor $version";
    }
  }
  return $vendor;
}
?>