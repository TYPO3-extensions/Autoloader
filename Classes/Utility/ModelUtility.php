<?php
/**
 * Utility to interact with the Model
 *
 * @category   Extension
 * @package    Autoloader
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\Autoloader\Utility;

use HDNET\Autoloader\Service\SmartObjectInformationService;
use TYPO3\CMS\Extbase\Reflection\ClassReflection;

/**
 * Utility to interact with the Model
 *
 * @package    Autoloader
 * @subpackage Utility
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
		$tableName = self::getTableNameByModelReflectionAnnotation($modelClassName) ? : self::getTableNameByModelName($modelClassName);
		return $tableName;
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
			$db = $classReflection->getTagValues('db');
			$value = trim($db[0]);
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
	 * Get the record_type name by reflection
	 *
	 * @param string $modelClassName
	 *
	 * @return string
	 */
	static public function getRecordTypeFieldByModelReflection($modelClassName) {
		$classReflection = new ClassReflection($modelClassName);
		if ($classReflection->isTaggedWith('record_type')) {
			$record_type = $classReflection->getTagValues('record_type');
			$value = trim($record_type[0]);
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
		if ($classReflection->isTaggedWith('parentclass')) {
			$parentclass = $classReflection->getTagValues('parentclass');
			$value = trim($parentclass[0]);
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
		$informationService = new SmartObjectInformationService();
		return $informationService->getTcaInformation($modelClassName);
	}

}