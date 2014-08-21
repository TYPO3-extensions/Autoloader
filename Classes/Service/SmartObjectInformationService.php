<?php
/**
 * SmartObjectInformationService.php
 *
 * @category   Extension
 * @package    Autoloader
 * @author     Tim Spiekerkoetter HDNET GmbH & Co. KG <tim.spiekerkoetter@hdnet.de>
 */

namespace HDNET\Autoloader\Service;

use HDNET\Autoloader\DataSet;
use HDNET\Autoloader\Mapper;
use HDNET\Autoloader\Utility\ArrayUtility;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\ExtendedUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use HDNET\Autoloader\Utility\TranslateUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SmartObjectInformationService
 *
 * @package    Autoloader
 * @subpackage Service
 * @author     Tim Spiekerkoetter HDNET GmbH & Co. KG <tim.spiekerkoetter@hdnet.de>
 */
class SmartObjectInformationService {

	/**
	 * Get database information
	 *
	 * @param $modelClassName
	 *
	 * @return string
	 */
	public function getDatabaseInformation($modelClassName) {
		$tableName = ModelUtility::getTableName($modelClassName);
		$custom = $this->getCustomDatabaseInformation($modelClassName);

		// disable complete table generation
		// for extending existing tables
		if (ModelUtility::getTableNameByModelReflectionAnnotation($modelClassName)) {
			return $this->generateSQLQuery($tableName, $custom);
		}
		return $this->generateCompleteSQLQuery($modelClassName, $tableName, $custom);
	}

	/**
	 * Get the custom Model field TCA structure
	 *
	 * @param       $modelClassName
	 * @param array $searchFields
	 *
	 * @return array
	 */
	public function getCustomModelFieldTca($modelClassName, &$searchFields = array()) {
		$modelInformation = ClassNamingUtility::explodeObjectModelName($modelClassName);
		$extensionName = GeneralUtility::camelCaseToLowerCaseUnderscored($modelInformation['extensionName']);
		$tableName = ModelUtility::getTableNameByModelName($modelClassName);
		$customFieldInfo = $this->getCustomModelFields($modelClassName);
		$searchFields = array();
		$customFields = array();
		foreach ($customFieldInfo as $info) {
			$key = $tableName . '.' . $info['name'];
			try {
				TranslateUtility::assureLabel($key, $extensionName, $info['name']);
				$label = TranslateUtility::getLllString($key, $extensionName);
			} catch (\Exception $ex) {
				$label = $info['name'];
			}


			/** @var Mapper $mapper */
			$mapper = ExtendedUtility::create('HDNET\\Autoloader\\Mapper');
			$field = $mapper->getTcaConfiguration(trim($info['var'], '\\'), $info['name'], $label);

			$searchFields[] = $info['name'];

			$customFields[$info['name']] = $field;
		}

		return $customFields;
	}

	/**
	 * Pre build TCA information for the given model
	 *
	 * @param string $modelClassName
	 *
	 * @return array
	 */
	public function getTcaInformation($modelClassName) {
		$modelInformation = ClassNamingUtility::explodeObjectModelName($modelClassName);
		$extensionName = GeneralUtility::camelCaseToLowerCaseUnderscored($modelInformation['extensionName']);
		$reflectionTableName = ModelUtility::getTableNameByModelReflectionAnnotation($modelClassName);
		$tableName = ModelUtility::getTableNameByModelName($modelClassName);

		$searchFields = array();
		$customFields = $this->getCustomModelFieldTca($modelClassName, $searchFields);

		if ($reflectionTableName !== FALSE) {
			$customConfiguration = array(
				'columns' => $customFields,
			);
			$base = is_array($GLOBALS['TCA'][$reflectionTableName]) ? $GLOBALS['TCA'][$reflectionTableName] : array();
			return ArrayUtility::mergeRecursiveDistinct($base, $customConfiguration);
		}

		$excludes = ModelUtility::getSmartExcludesByModelName($modelClassName);

		$dataSet = new DataSet();
		$dataImplementations = $dataSet->getAllAndExcludeList($excludes);
		$baseTca = $dataSet->getTcaInformation($dataImplementations, $tableName);

		// extension icon

		// title
		$fields = array_keys($customFields);
		$labelField = 'title';
		if (!in_array($labelField, $fields)) {
			$labelField = $fields[0];
		}
		try {
			TranslateUtility::assureLabel($tableName, $extensionName);
		} catch (\Exception $ex) {
		}

		$baseTca['columns'] = ArrayUtility::mergeRecursiveDistinct($baseTca['columns'], $customFields);

		// items
		$showitem = array_keys($customFields);
		if (!in_array('language', $excludes)) {
			$showitem[] = '--palette--;LLL:EXT:lang/locallang_general.xlf:LGL.language;language';
		}

		if (!in_array('workspaces', $excludes)) {
			$baseTca['ctrl']['shadowColumnsForNewPlaceholders'] .= ',' . $labelField;
		}
		if (!in_array('enableFields', $excludes)) {
			$showitem[] = '--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access';
			$showitem[] = '--palette--;LLL:EXT:cms/locallang_tca.xlf:pages.palettes.access;access';
		}
		$showitem[] = '--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.extended';


		$overrideTca = array(
			'ctrl'      => array(
				'title'         => TranslateUtility::getLllOrHelpMessage($tableName, $extensionName),
				'label'         => $labelField,
				'tstamp'        => 'tstamp',
				'crdate'        => 'crdate',
				'cruser_id'     => 'cruser_id',
				'dividers2tabs' => TRUE,
				'sortby'        => 'sorting',
				'delete'        => 'deleted',
				'searchFields'  => implode(',', $searchFields),
				'iconfile'      => $this->getTableIcon($modelClassName)
			),
			'interface' => array(
				'showRecordFieldList' => implode(',', array_keys($baseTca['columns'])),
			),
			'types'     => array(
				'1' => array('showitem' => implode(',', $showitem)),
			),
			'palettes'  => array(
				'access' => array('showitem' => 'starttime, endtime, --linebreak--, hidden, editlock, --linebreak--, fe_group'),
			),
		);
		return ArrayUtility::mergeRecursiveDistinct($baseTca, $overrideTca);
	}

	/**
	 * Get the table icon for the given model name
	 *
	 * @param string $modelClassName
	 *
	 * @return string
	 */
	protected function getTableIcon($modelClassName) {
		$modelInformation = ClassNamingUtility::explodeObjectModelName($modelClassName);
		$extensionName = GeneralUtility::camelCaseToLowerCaseUnderscored($modelInformation['extensionName']);
		$modelName = str_replace('\\', '_', $modelInformation['modelName']);
		$tableIconRelPath = ExtensionManagementUtility::extRelPath($extensionName) . 'Resources/Public/Icons/' . $modelName . '.png';
		$tableDefaultIcon = ExtensionManagementUtility::extRelPath('autoloader') . 'ext_icon.png';
		return is_file(ExtensionManagementUtility::extPath($extensionName) . 'Resources/Public/Icons/' . $modelName . '.png') ? $tableIconRelPath : $tableDefaultIcon;
	}

	/**
	 * Get custom database information for the given model
	 *
	 * @param string $modelClassName
	 *
	 * @return string
	 */
	protected function getCustomDatabaseInformation($modelClassName) {
		$fieldInformation = $this->getCustomModelFields($modelClassName);
		$fields = array();
		foreach ($fieldInformation as $info) {
			if ($info['db'] === '') {
				$info['db'] = $this->getDatabaseMappingByVarType($info['var']);
			} else {
				try {
					$info['db'] = $this->getDatabaseMappingByVarType($info['db']);
				} catch (\Exception $ex) {

				}
			}
			$fields[] = $info['name'] . ' ' . $info['db'];
		}
		return $fields;
	}

	/**
	 * Get the right mapping
	 *
	 * @param $var
	 *
	 * @throws \HDNET\Autoloader\Exception
	 * @return string
	 */
	protected function getDatabaseMappingByVarType($var) {
		/** @var Mapper $mapper */
		$mapper = ExtendedUtility::create('HDNET\\Autoloader\\Mapper');
		return $mapper->getDatabaseDefinition($var);
	}

	/**
	 * Get custom database information for the given model
	 *
	 * @param string $modelClassName
	 *
	 * @return array
	 */
	protected function getCustomModelFields($modelClassName) {
		/** @var $classReflection \TYPO3\CMS\Extbase\Reflection\ClassReflection */
		$classReflection = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Reflection\\ClassReflection', $modelClassName);
		$fields = array();
		foreach ($classReflection->getProperties() as $property) {
			/** @var \TYPO3\CMS\Extbase\Reflection\PropertyReflection $property */
			if ($property->isTaggedWith('db')) {
				$var = '';
				if ($property->isTaggedWith('var')) {
					$var = $property->getTagValues('var');
					$var = $var[0];
				}

				$dbInformation = $property->getTagValues('db');
				$fields[] = array(
					'name' => GeneralUtility::camelCaseToLowerCaseUnderscored($property->getName()),
					'db'   => trim($dbInformation[0]),
					'var'  => trim($var),
				);
			}
		}
		return $fields;
	}

	/**
	 * Generae SQL Query
	 *custom
	 *
	 * @param string $tableName
	 * @param array  $fields
	 *
	 * @return string
	 */
	protected function generateSQLQuery($tableName, $fields) {
		if (!$fields) {
			return '';
		}
		return LF . 'CREATE TABLE ' . $tableName . ' (' . LF . implode(',' . LF, $fields) . LF . ');' . LF;
	}

	/**
	 * Generate complete SQL Query
	 *
	 * @param string $modelClassName
	 * @param string $tableName
	 * @param array  $custom
	 *
	 * @return string
	 */
	protected function generateCompleteSQLQuery($modelClassName, $tableName, $custom) {
		$fields = array();
		$fields[] = 'uid int(11) NOT NULL auto_increment';
		$fields[] = 'pid int(11) DEFAULT \'0\' NOT NULL';
		$fields[] = 'tstamp int(11) unsigned DEFAULT \'0\' NOT NULL';
		$fields[] = 'crdate int(11) unsigned DEFAULT \'0\' NOT NULL';
		$fields[] = 'cruser_id int(11) unsigned DEFAULT \'0\' NOT NULL';
		$fields[] = 'deleted tinyint(4) unsigned DEFAULT \'0\' NOT NULL';
		$fields[] = 'sorting int(11) DEFAULT \'0\' NOT NULL';

		if ($custom) {
			foreach ($custom as $field) {
				$fields[] = $field;
			}
		}

		$excludes = ModelUtility::getSmartExcludesByModelName($modelClassName);
		$dataSet = new DataSet();
		$dataImplementations = $dataSet->getAllAndExcludeList($excludes);

		// add data set fields
		$fields = array_merge($fields, $dataSet->getDatabaseSqlInformation($dataImplementations, $tableName));

		// default keys
		$fields[] = 'PRIMARY KEY (uid)';
		$fields[] = 'KEY parent (pid)';

		// add data set keys
		$fields = array_merge($fields, $dataSet->getDatabaseSqlKeyInformation($dataImplementations, $tableName));

		return $this->generateSQLQuery($tableName, $fields);
	}
}
