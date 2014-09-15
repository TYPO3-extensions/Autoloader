<?php
/**
 * TempClassAutoloader.php
 *
 * @category   Extension
 * @package    Autoloader
 * @subpackage Utility
 * @author     Carsten Biebricher <carsten.biebricher@hdnet.de>
 */
namespace HDNET\Autoloader\Autoload;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TempClassLoader
 * Thx to SJBR
 *
 * @package    Autoloader
 * @subpackage Utility
 * @author     Carsten Biebricher <carsten.biebricher@hdnet.de>
 */
class TempClassLoader {

	/**
	 * Cached class loader class name.
	 *
	 * @var string
	 */
	static protected $className = __CLASS__;

	/**
	 * Name space of the Domain Model of StaticInfoTables
	 *
	 * @var string
	 */
	static protected $namespace = 'HDNET\\Autoloader\\Xclass\\';

	/**
	 * Is TRUE, if the autoloader is registered
	 *
	 * @var bool
	 */
	static protected $isRegistered = FALSE;

	/**
	 * Registers the cached class loader.
	 *
	 * @return boolean TRUE in case of success
	 */
	static public function registerAutoloader() {
		if (self::$isRegistered) {
			return FALSE;
		}

		self::$isRegistered = TRUE;
		return spl_autoload_register(static::$className . '::autoload', TRUE, TRUE);
	}

	/**
	 * Autoload function for cached classes.
	 *
	 * @param string $className Class name
	 *
	 * @return void
	 */
	static public function autoload($className) {
		$className = ltrim($className, '\\');

		if (strpos($className, static::$namespace) !== FALSE) {
			$optimizedClassName = str_replace('\\', '', $className);
			$cacheIdentifier = 'XCLASS' . '_' . $optimizedClassName;

			/** @var $cache \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend */
			$cache = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->getCache('autoloader');
			if ($cache->has($cacheIdentifier)) {
				$cache->requireOnce($cacheIdentifier);
			}
		}
	}
}

?>