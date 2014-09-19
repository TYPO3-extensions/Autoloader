<?php
/**
 * Loading eID
 *
 * @category   Extension
 * @package    Autoloader
 * @subpackage Loader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */


namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Loading eID
 *
 * @package    Autoloader
 * @subpackage Loader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class ExtensionId implements LoaderInterface {

	/**
	 * Get all the complex data for the loader.
	 * This return value will be cached and stored in the database
	 * There is no file monitoring for this cache
	 *
	 * @param Loader  $loader
	 * @param integer $type
	 *
	 * @return array
	 */
	public function prepareLoader(Loader $loader, $type) {
		$scripts = array();
		$folder = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Resources/Private/Php/eID/';
		$files = FileUtility::getBaseFilesInDir($folder, 'php');

		foreach ($files as $eIdFile) {
			$scripts[] = array(
				'name' => $eIdFile,
				'path' => 'EXT:' . $loader->getExtensionKey() . '/Resources/Private/Php/eID/' . $eIdFile . '.php',
			);
		}
		return $scripts;
	}

	/**
	 * Run the loading process for the ext_tables.php file
	 *
	 * @param Loader $loader
	 * @param array  $loaderInformation
	 *
	 * @return NULL
	 */
	public function loadExtensionTables(Loader $loader, array $loaderInformation) {
		return NULL;
	}

	/**
	 * Run the loading process for the ext_localconf.php file
	 *
	 * @param \HDNET\Autoloader\Loader $loader
	 * @param array                    $loaderInformation
	 *
	 * @internal param \HDNET\Autoloader\Loader $autoLoader
	 * @return NULL
	 */
	public function loadExtensionConfiguration(Loader $loader, array $loaderInformation) {
		foreach ($loaderInformation as $elements) {
			$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include'][$elements['name']] = $elements['path'];
		}
		return NULL;
	}
}