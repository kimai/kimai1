<?php
class Config {
	private static $instance;
	private static $config = array();

	/**
	 * Constructor (singleton!)
	 *
	 * @return void
	 */
	private function __construct() {
		// current dir might be in an extension or in core..
		self::$config = parse_ini_file("../../config/config.ini");
		if (! self::$config) {
			self::$config = parse_ini_file("../config/config.ini");
		}
	}

	public static function getConfig($type) {
		if (! isset(self::$instance)) {
			self::$instance = new Config();
		}
		if (isset(self::$config[$type])) {
			return self::$config[$type];
		}
		return false;
	}
}