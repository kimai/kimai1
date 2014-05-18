<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 02.05.14
 * Time: 14:00
 */

class Kimai_Extension_Extension
{

    private $config = array();

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getId() {
        return $this->getDefault('id', $this->config['basename']);
    }

    /**
     * Returns the (translated) display name for this extension.
     * @return mixed
     */
    public function getName() {
        $id = $this->getId();
        $name = $this->getDefault('name', $id);
        return Kimai_Registry::getTranslations()->translate($id, 'extensions', $name);
    }

    /**
     * Returns the position within the menu.
     * @return mixed
     */
    public function getPosition() {
        return $this->getDefault('position', 50);
    }

    /**
     * Helper function to return the config or a default value.
     * @param $key
     * @param $default
     * @return mixed
     */
    private function getDefault($key, $default) {
        return isset($this->config[$key]) ? $this->config[$key] : $default;
    }

    /**
     * Setup the plugin.
     * This function will load a the file setup.php if it is existing.
     */
    public function setup() {
        $baseDir = $this->getBaseDir();
        if (file_exists($baseDir . 'setup.php')) {
            include $baseDir . 'setup.php';
        }
    }

    protected function getBaseDir() {
        return $this->config['basepath'] . '/' . $this->config['basename'] . '/';
    }

    public function getInitFile() {
        return $this->config['basename'] . '/' . $this->getDefault('init_file', 'init.php');
    }

    public function getStylesheets() {
        return $this->getDefault('stylesheets', array());
    }

    public function getJavascripts() {
        return $this->getDefault('javascripts', array());
    }

    public function getDirectory() {
        return $this->config['basename'];
    }

    public function isCustomerAllowed() {
        return $this->getDefault('customer', false);
    }

    public function getPermission() {
        return $this->getDefault('permission', null);
    }
}