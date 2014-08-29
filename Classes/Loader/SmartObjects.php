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
use HDNET\Autoloader\Utility\ModelUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ArrayUtility;

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
		$configuration = array();
		$modelPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Domain/Model/';
		if (!is_dir($modelPath)) {
			return $configuration;
		}
		/** @var \HDNET\Autoloader\Service\SmartObjectInformationService $informationService */
		$informationService = GeneralUtility::makeInstance('HDNET\\Autoloader\\Service\\SmartObjectInformationService');
		$models = FileUtility::getBaseFilesInDir($modelPath, 'php');

		foreach ($models as $model) {
			$className = $loader->getVendorName() . '\\' . ucfirst(GeneralUtility::underscoredToUpperCamelCase($loader->getExtensionKey())) . '\\Domain\\Model\\' . $model;
			if (SmartObjectManager::isSmartObjectClass($className)) {
				$entry = array(
					'className' => $className,
				);
				if ($type === LoaderInterface::EXT_TABLES) {
					if (ModelUtility::getTableNameByModelReflectionAnnotation($className)) {
						$entry['additionalTca'] = $informationService->getCustomModelFieldTca($className);
						$entry['tableName'] = ModelUtility::getTableName($className);
					}
				}
				$configuration[] = $entry;
			}
		}
		// already add for the following processes
		$this->addClassesToSmartRegister($configuration);

		return $configuration;
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

		foreach ($loaderInformation as $configuration) {
			if ($configuration['additionalTca']) {
				$tableName = $configuration['tableName'];
				$GLOBALS['TCA'][$tableName]['columns'] = ArrayUtility::arrayMergeRecursiveOverrule($GLOBALS['TCA'][$tableName]['columns'], $configuration['additionalTca']);
			}
		}

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
	 * @param array $loaderInformation
	 */
	protected function addClassesToSmartRegister($loaderInformation) {
		foreach ($loaderInformation as $configuration) {
			SmartObjectRegister::register($configuration['className']);
		}
	}
}