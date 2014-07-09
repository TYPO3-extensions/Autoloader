<?php
/**
 * Register for Smart Objects
 *
 * @category   Extension
 * @package    Autoloader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\Autoloader;

/**
 * Register for Smart Objects
 *
 * @package    Autoloader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class SmartObjectRegister {

	/**
	 * Register for smart object information
	 *
	 * @var array
	 */
	static protected $smartObjectRegistry = array();

	/**
	 * Add a model to the register
	 *
	 * @param $modelName
	 *
	 * @return null
	 */
	static public function register($modelName) {
		if (!in_array($modelName, self::$smartObjectRegistry)) {
			self::$smartObjectRegistry[] = $modelName;
		}
	}

	/**
	 * Get the register content
	 *
	 * @return array
	 */
	static public function getRegister() {
		return self::$smartObjectRegistry;
	}

}