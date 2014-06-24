<?php
/**
 * Loading Xclass
 *
 * @category   Extension
 * @package    Autoloader
 * @subpackage Loader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ExtendedUtility;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Loading Xclass
 *
 * @package    Autoloader
 * @subpackage Loader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class Xclass implements LoaderInterface {

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
		$return = array();
		if ($type === LoaderInterface::EXT_TABLES) {
			retrun $return;
		}
		$xClassesPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Xclass/';
		$xClasses = FileUtility::getBaseFilesInDir($xClassesPath, 'php');

		$extKey = GeneralUtility::underscoredToUpperCamelCase($loader->getExtensionKey());
		foreach ($xClasses as $xClass) {
			$xclassName = $loader->getVendorName() . '\\' . $extKey . '\\Xclass\\' . $xClass;
			if (!$loader->isInstantiableClass($xclassName)) {
				continue;
			}
			/** @var $xclassReflection \TYPO3\CMS\Extbase\Reflection\ClassReflection */
			$xclassReflection = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Reflection\\ClassReflection', $xclassName);
			$originalName = $xclassReflection->getParentClass()
			                                 ->getName();
			$return[] = array(
				'source' => $originalName,
				'target' => $xclassName,
			);
		}

		return $return;
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
	 * @param Loader $loader
	 * @param array  $loaderInformation
	 *
	 * @return NULL
	 */
	public function loadExtensionConfiguration(Loader $loader, array $loaderInformation) {
		foreach ($loaderInformation as $xclass) {
			ExtendedUtility::addXclass($xclass['source'], $xclass['target']);
		}
		return NULL;
	}
}