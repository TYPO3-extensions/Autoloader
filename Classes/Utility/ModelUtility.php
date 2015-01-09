<?php
/**
 * Utility to interact with the Model
 *
 * @category   Extension
 * @package    Autoloader\Utility
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\Autoloader\Utility;

use HDNET\Autoloader\Service\SmartObjectInformationService;
use HDNET\Autoloader\SmartObjectRegister;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
		return self::getTableNameByModelReflectionAnnotation($modelClassName) ?: self::getTableNameByModelName($modelClassName);
	}

	/**
	 * Get the table name by reflection
	 *
	 * @param string $modelClassName
	 *
	 * @return string
	 */
	static public function getTableNameByModelReflectionAnnotation($modelClassName) {
		return ReflectionUtility::getFirstTagValue($modelClassName, 'db');
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
		return GeneralUtility::trimExplode(',', (string)ReflectionUtility::getFirstTagValue($name, 'smartExclude'));
	}

	/**
	 * Get the record_type name by reflection
	 *
	 * @param string $modelClassName
	 *
	 * @return string
	 */
	static public function getRecordTypeFieldByModelReflection($modelClassName) {
		return ReflectionUtility::getFirstTagValue($modelClassName, 'recordType');
	}

	/**
	 * Get the parent class name by reflection
	 *
	 * @param string $modelClassName
	 *
	 * @return string
	 */
	static public function getParentClassByModelReflection($modelClassName) {
		return ReflectionUtility::getFirstTagValue($modelClassName, 'parentClass');
	}

	/**
	 * Get the base TCA for the given Model
	 *
	 * @param string $modelClassName
	 *
	 * @return array
	 */
	static public function getTcaInformation($modelClassName) {
		$informationService = SmartObjectInformationService::getInstance();
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
		$informationService = SmartObjectInformationService::getInstance();

		foreach ($classNames as $className) {
			if (ClassNamingUtility::getExtensionKeyByModel($className) !== $extensionKey) {
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

	/**
	 * Get the target model.
	 *
	 * @param string $modelName
	 * @param array  $data
	 *
	 * @return \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
	 */
	public static function getModel($modelName, $data) {
		$query = ExtendedUtility::getQuery($modelName);
		$query->getQuerySettings()
			->setRespectStoragePage(FALSE);
		$query->getQuerySettings()
			->setRespectSysLanguage(FALSE);
		return $query->matching($query->equals('uid', $data['uid']))
			->execute()
			->getFirst();
	}
}