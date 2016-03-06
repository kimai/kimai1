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
        global $kga;

        $this->setBasePath(APPLICATION_PATH . '/templates');
        $this->addHelperPath(APPLICATION_PATH . '/templates/helpers', 'Zend_View_Helper');

        parent::init();
        $this->kga = $kga;
    }
}
