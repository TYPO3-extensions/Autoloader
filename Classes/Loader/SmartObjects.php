<?php
/**
 * Loading SmartObjects
 *
 * @category   Extension
 * @package    Autoloader
 * @subpackage Loader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */


namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\SmartObjectManager;
use HDNET\Autoloader\SmartObjectRegister;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Loading SmartObjects
 *
 * @package    Autoloader
 * @subpackage Loader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class SmartObjects implements LoaderInterface {

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
		$classNames = array();
		$modelPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Domain/Model/';
		if (!is_dir($modelPath)) {
			return $classNames;
		}

		$models = FileUtility::getBaseFilesInDir($modelPath, 'php');

		foreach ($models as $model) {
			$className = $loader->getVendorName() . '\\' . ucfirst(GeneralUtility::underscoredToUpperCamelCase($loader->getExtensionKey())) . '\\Domain\\Model\\' . $model;
			if (SmartObjectManager::isSmartObjectClass($className)) {
				$classNames[] = $className;
			}
		}
		// already add for the following processes
		$this->addClassesToSmartRegister($classNames);

		return $classNames;

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
		$this->addClassesToSmartRegister($loaderInformation);
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
		$this->addClassesToSmartRegister($loaderInformation);
		return NULL;
	}

	/**
	 * Add the given classes to the SmartObject Register
	 *
	 * @param array $classNames
	 */
	protected function addClassesToSmartRegister($classNames) {
		foreach ($classNames as $className) {
			SmartObjectRegister::register($className);
		}
	}
}