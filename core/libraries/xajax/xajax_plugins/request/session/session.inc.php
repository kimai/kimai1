<?


if (false == class_exists('xajaxPlugin') || false == class_exists('xajaxPluginManager'))
{
	$sBaseFolder = dirname(dirname(dirname(__FILE__)));
	$sXajaxCore = $sBaseFolder . '/xajax_core';

	if (false == class_exists('xajaxPlugin'))
		require $sXajaxCore . '/xajaxPlugin.inc.php';
	if (false == class_exists('xajaxPluginManager'))
		require $sXajaxCore . '/xajaxPluginManager.inc.php';
}


//if (!defined ('XAJAX_UPLOAD_FUNCTION')) define ('XAJAX_UPLOAD_FUNCTION', 'upfunction');

//require_once dirname(__FILE__) . '/xajaxUploadFunction.inc.php';

class clsSession extends xajaxRequestPlugin
{


//--------------------------------------------------------------------------------------------------------------------------------
	
	private $sCallName = "Session";
	private $sXajaxPrefix = "xajax_";	


	private $sSessionCheck = "";
	private $sSessionExpired = "";

	public function clsSession() 
	{
		

	}

	public function configure($sName, $mValue)
	{
		switch ($sName) 
		{
			case 'sessionCheck':
							if (is_string($mValue) || is_array($mValue)) 
							{
								$this->sSessionCheck = $mValue;
							}
							break;
			
			case 'sessionExpired':
							if (is_string($mValue) || is_array($mValue)) 
							{
								$this->sSessionExpired = $mValue;
							}
							break;
		}
	}


//--------------------------------------------------------------------------------------------------------------------------------
	
} 


$objPluginManager =& xajaxPluginManager::getInstance();
$objPluginManager->registerPlugin(new clsSession(), 10);