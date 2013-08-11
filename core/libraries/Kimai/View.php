<?php
/**
 * View object for Kimai.
 *
 * @author Kevin Papst
 */
class Kimai_View extends Zend_View
{

    public function init()
    {
        $this->setBasePath(APPLICATION_PATH . '/templates');
        $this->addHelperPath(APPLICATION_PATH . '/templates/helpers', 'Zend_View_Helper');

        if (isset($GLOBALS['kga']['conf']['skin'])) {
            $this->enableSkinSupport($GLOBALS['kga']['conf']['skin']);
        }

        parent::init();
    }

    public function enableSkinSupport($skin)
    {
        // allow skin specific view helper
        $helperPath = APPLICATION_PATH . '/templates/helpers/' . ucfirst($skin) . '/';
        if(file_exists($helperPath) && is_dir($helperPath)) {
            $this->addHelperPath($helperPath, 'Zend_View_Helper_'.ucfirst($skin).'_');
        }
    }

}
