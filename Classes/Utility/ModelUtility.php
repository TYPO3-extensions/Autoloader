<?php
/**
 * Utility to interact with the Model
 *
 * @category   Extension
 * @package    Autoloader\Utility
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\Autoloader\Utility;

use HDNET\Autoloader\SmartObjectManager;
use HDNET\Autoloader\SmartObjectRegister;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ClassReflection;

/**
 * Utility to interact with the Model
 *
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */
class ModelUtility {

	/**
	 * Get the table name by either reflection or model name
	 *
	 * @param $modelClassName
	 *
	 * @return string
	 */
	static public function getTableName($modelClassName) {
		return self::getTableNameByModelReflectionAnnotation($modelClassName) ? : self::getTableNameByModelName($modelClassName);
	}

	/**
	 * Get the table name by reflection
	 *
	 * @param string $modelClassName
	 *
	 * @return string
	 */
	static public function getTableNameByModelReflectionAnnotation($modelClassName) {
		$classReflection = new ClassReflection($modelClassName);
		if ($classReflection->isTaggedWith('db')) {
			$databaseAnnotation = $classReflection->getTagValues('db');
			$value = trim($databaseAnnotation[0]);
			return $value === '' ? FALSE : $value;
		}
		return FALSE;
	}

	/**
	 * Resolve the table name for the given class name
	 * Original method from extbase core to create the table name
	 *
	 * @param string $className
	 *
	 * @return string The table name
	 * @see DataMapFactory->resolveTableName
	 */
	static public function getTableNameByModelName($className) {
		$className = ltrim($className, '\\');
		if (strpos($className, '\\') !== FALSE) {
			$classNameParts = explode('\\', $className, 6);
			// Skip vendor and product name for core classes
			if (strpos($className, 'TYPO3\\CMS\\') === 0) {
				$classPartsToSkip = 2;
			} else {
				$classPartsToSkip = 1;
			}
			$tableName = 'tx_' . strtolower(implode('_', array_slice($classNameParts, $classPartsToSkip)));
		} else {
			$tableName = strtolower($className);
		}
		return $tableName;
	}

	/**
	 * get the smart exclude values e.g. language, workspace,
	 * enableFields from the given model
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	static public function getSmartExcludesByModelName($name) {
		$classReflection = new ClassReflection($name);
		if ($classReflection->isTaggedWith('smartExclude')) {
			$smartExclude = $classReflection->getTagValues('smartExclude');
			return GeneralUtility::trimExplode(',', $smartExclude[0]);
		}
		return array();

	}

	/**
	 * Get the record_type name by reflection
	 *
	 * @param string $modelClassName
	 *
	 * @return string
	 */
	static public function getRecordTypeFieldByModelReflection($modelClassName) {
		$classReflection = new ClassReflection($modelClassName);
		if ($classReflection->isTaggedWith('recordType')) {
			$recordType = $classReflection->getTagValues('recordType');
			$value = trim($recordType[0]);
			return $value === '' ? FALSE : $value;
		}
		return FALSE;
	}

	/**
	 * Get the parent class name by reflection
	 *
	 * @param string $modelClassName
	 *
	 * @return string
	 */
	static public function getParentClassByModelReflection($modelClassName) {
		$classReflection = new ClassReflection($modelClassName);
		if ($classReflection->isTaggedWith('parentClass')) {
			$parentClass = $classReflection->getTagValues('parentClass');
			$value = trim($parentClass[0]);
			return $value === '' ? FALSE : $value;
		}
		return FALSE;
	}

	/**
	 * Get the base TCA for the given Model
	 *
	 * @param string $modelClassName
	 *
	 * @return array
	 */
	static public function getTcaInformation($modelClassName) {
		/** @var \HDNET\Autoloader\Service\SmartObjectInformationService $informationService */
		$informationService = GeneralUtility::makeInstance('HDNET\\Autoloader\\Service\\SmartObjectInformationService');
		return $informationService->getTcaInformation($modelClassName);
	}

	/**
	 * Get the default TCA incl. smart object fields.
	 * Add missing fields to the existing TCA structure.
	 *
	 * @param string $extensionKey
	 * @param string $tableName
	 *
	 * @return array
	 */
	static public function getTcaOverrideInformation($extensionKey, $tableName) {
		$return = isset($GLOBALS['TCA'][$tableName]) ? $GLOBALS['TCA'][$tableName] : array();
		$classNames = SmartObjectRegister::getRegister();

		/** @var \HDNET\Autoloader\Service\SmartObjectInformationService $informationService */
		$informationService = GeneralUtility::makeInstance('HDNET\\Autoloader\\Service\\SmartObjectInformationService');

		foreach ($classNames as $className) {
			if (SmartObjectManager::getExtensionKeyByModel($className) !== $extensionKey) {
				continue;
			}
			if (self::getTableNameByModelReflectionAnnotation($className) === $tableName) {
				$additionalTca = $informationService->getCustomModelFieldTca($className);
				foreach ($additionalTca as $fieldName => $configuration) {
					if (!isset($return['columns'][$fieldName])) {
						$return['columns'][$fieldName] = $configuration;
					}
				}
			}
		}

		return $return;
	}

}