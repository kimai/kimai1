<?php

// TODO: check include path
ini_set ( 'date.timezone', 'Europe/Berlin' );

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

ini_set('include_path', ini_get('include_path')
    . PATH_SEPARATOR . __DIR__ . '/library'
    . PATH_SEPARATOR . __DIR__ . '/../core/libraries'
);

class UnitTestHelper
{
    /**
     * Access protected or private methods
     *
     * use the following code to access any protected or private class method
     * $obj = new MyClass();
     * $method = UnitTestHelper::getMethod($obj, 'nameOfMethod');
     * $result = $method->invoke($obj,'param1','param2');
     *
     * @param Object|string $obj
     * @param string $name
     *
     * @return method
     */
    public static function getMethod($obj, $name) {
        $class = new ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
