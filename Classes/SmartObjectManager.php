<?php
/**
 * Management for Smart Objects
 *
 * @category   Extension
 * @package    Autoloader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\Autoloader;

use HDNET\Autoloader\Utility\ModelUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ClassReflection;

/**
 * Management for Smart Objects
 *
 * @package    Autoloader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class SmartObjectManager implements SingletonInterface {

	/**
	 * Register for smart object information
	 *
	 * @var array
	 */
	protected static $smartObjectRegistry = array();

	/**
	 * Add a model to the register
	 *
	 * @param $modelName
	 *
	 * @return null
	 */
	static public function registerSmartObject($modelName) {
		$extKey = self::getExtensionKeyByModel($modelName);
		if (!isset(self::$smartObjectRegistry[$extKey])) {
			self::$smartObjectRegistry[$extKey] = array();
		}
		if (!in_array($modelName, self::$smartObjectRegistry[$extKey])) {
			self::$smartObjectRegistry[$extKey][] = $modelName;
		}
	}

	/**
	 * Get the register content
	 *
	 * @return array
	 */
	static public function getSmartObjectRegister() {
		return self::$smartObjectRegistry;
	}

	/**
	 * Return the SQL String for all registered smart objects
	 *
	 * @return string
	 */
	static public function getSmartObjectRegisterSql() {
		$informationService = new \HDNET\Autoloader\Service\SmartObjectInformationService();
		$register = self::getSmartObjectRegister();

		$output = array();
		foreach ($register as $extensionConfiguration) {
			foreach ($extensionConfiguration as $modelName) {
				$output[] = $informationService->getDatabaseInformation($modelName);
			}
		}
		return implode(LF, $output);
	}

	/**
	 * Check if the given class is a smart object
	 *
	 * @param string $className
	 *
	 * @return bool
	 */
	static public function isSmartObjectClass($className) {
		if (!class_exists($className)) {
			return FALSE;
		}
		/** @var ClassReflection $classReflection */
		// do not object factory here, to take care of the loading in the ext_localconf
		$classReflection = new ClassReflection($className);
		if (!$classReflection->isInstantiable() || !$classReflection->isTaggedWith('db')) {
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Get the extension key by the given modelname
	 *
	 * @param string|object $modelClassName
	 *
	 * @return string
	 */
	static public function getExtensionKeyByModel($modelClassName) {
		if (is_object($modelClassName)) {
			$modelClassName = get_class($modelClassName);
		}
		$parts = GeneralUtility::trimExplode('\\', $modelClassName, TRUE);
		foreach ($parts as $part) {
			if (strtoupper($part) !== $part) {
				return GeneralUtility::camelCaseToLowerCaseUnderscored($part);
			}
		}

		// Fallback
		return GeneralUtility::camelCaseToLowerCaseUnderscored($parts[1]);
	}

	/**
	 * Check and create the TCA information
	 * disable this for better performance
	 */
	static public function checkAndCreateTcaInformation() {
		$register = self::getSmartObjectRegister();

		foreach ($register as $extensionKey => $models) {
			foreach ($models as $model) {
				if (strpos($model, '\\Content\\') !== FALSE) {
					continue;
				}
				$tableName = ModelUtility::getTableNameByModelReflectionAnnotation($model) ? : ModelUtility::getTableNameByModelName($model);
				$tcaFileName = ExtensionManagementUtility::extPath($extensionKey) . 'Configuration/TCA/' . $tableName . '.php';

				if (!is_file($tcaFileName)) {
					$dir = dirname($tcaFileName);
					if (!is_dir($dir)) {
						GeneralUtility::mkdir_deep($dir);
					}
					$content = '<?php

$base = \HDNET\Autoloader\Utility\ModelUtility::getTcaInformation(\'' . str_replace('\\', '\\\\', $model) . '\');

$custom = array();

return \HDNET\Autoloader\Utility\ArrayUtility::mergeRecursiveDistinct($base, $custom);';

					GeneralUtility::writeFile($tcaFileName, $content);
				}
			}
		}
	}

}
