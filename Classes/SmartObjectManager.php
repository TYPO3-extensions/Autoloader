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
	 * Return the SQL String for all registered smart objects
	 *
	 * @return string
	 */
	static public function getSmartObjectRegisterSql() {
		$informationService = new \HDNET\Autoloader\Service\SmartObjectInformationService();
		$register = SmartObjectRegister::getRegister();

		$output = array();
		foreach ($register as $modelName) {
			$output[] = $informationService->getDatabaseInformation($modelName);
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
	 * @throws \HDNET\Autoloader\Exception
	 */
	static public function getExtensionKeyByModel($modelClassName) {
		$matches = array();

		if (is_object($modelClassName)) {
			$modelClassName = get_class($modelClassName);
		}

		if (strpos($modelClassName, '\\') !== FALSE) {
			if (substr($modelClassName, 0, 9) === 'TYPO3\\CMS') {
				$extensionName = '^(?P<vendorName>[^\\\\]+\\\[^\\\\]+)\\\(?P<extensionName>[^\\\\]+)';
			} else {
				$extensionName = '^(?P<vendorName>[^\\\\]+)\\\\(?P<extensionName>[^\\\\]+)';
			}
			preg_match(
				'/' . $extensionName . '\\\\Domain\\\\Model\\\\(?P<modelName>[a-z\\\\]+)$/ix',
				$modelClassName,
				$matches
			);
		} else {
			preg_match(
				'/^Tx_(?P<extensionName>[^_]+)_Domain_Model_(?P<modelName>[a-z_]+)$/ix',
				$modelClassName,
				$matches
			);
		}

		if(empty($matches)) {
			throw new Exception('Could not determine extension key for: ' . $modelClassName, 1406577758);
		}

		return GeneralUtility::camelCaseToLowerCaseUnderscored($matches['extensionName']);
	}

	/**
	 * Check and create the TCA information
	 * disable this for better performance
	 */
	static public function checkAndCreateTcaInformation() {
		$register = SmartObjectRegister::getRegister();

		foreach ($register as $model) {
			if (strpos($model, '\\Content\\') !== FALSE) {
				continue;
			}
			$extensionKey = self::getExtensionKeyByModel($model);
			$tableName = ModelUtility::getTableNameByModelReflectionAnnotation($model) ? : ModelUtility::getTableNameByModelName($model);
			$tcaFileName = ExtensionManagementUtility::extPath($extensionKey) . 'Configuration/TCA/' . $tableName . '.php';

			if (!is_file($tcaFileName)) {
				$dir = dirname($tcaFileName);
				if (!is_dir($dir)) {
					GeneralUtility::mkdir_deep($dir);
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
