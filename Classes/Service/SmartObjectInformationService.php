<?php
/**
 * SmartObjectInformationService.php
 *
 * @category   Extension
 * @package    Autoloader
 * @author     Tim Spiekerkoetter HDNET GmbH & Co. KG <tim.spiekerkoetter@hdnet.de>
 */

namespace HDNET\Autoloader\Service;

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
		if ($tableNameReflect = ModelUtility::getTableNameByModelReflectionAnnotation($modelClassName)) {
			return $this->generateSQLQuery($tableName, array($custom));
		}
		return $this->generateCompleteSQLQuery($tableName, $custom);
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

		$customFieldInformation = $this->getCustomModelFields($modelClassName);

		$searchFields = array();
		$customFields = array();
		foreach ($customFieldInformation as $info) {
			$key = $tableName . '.' . $info['name'];
			try {
				TranslateUtility::assureLabel($key, $extensionName, $info['name']);
				$label = TranslateUtility::getLllString($key, $extensionName);
			} catch (\Exception $ex) {
				$label = $info['name'];
			}


			/** @var Mapper $mapper */
			$mapper = ExtendedUtility::create('HDNET\\Autoloader\\Mapper');
			$field = $mapper->getTcaConfiguration(trim($info['var'], '\\'), $info['name']);

			$searchFields[] = $info['name'];

			$customFields[$info['name']] = $field;
		}

		if ($reflectionTableName !== FALSE) {
			$customConfiguration = array(
				'columns' => $customFields,
			);
			$base = is_array($GLOBALS['TCA'][$reflectionTableName]) ? $GLOBALS['TCA'][$reflectionTableName] : array();
			return ArrayUtility::mergeRecursiveDistinct($base, $customConfiguration);
		}

		$defaultColumns = array(
			'sys_language_uid' => array(
				'exclude' => 1,
				'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
				'config'  => array(
					'type'                => 'select',
					'foreign_table'       => 'sys_language',
					'foreign_table_where' => 'ORDER BY sys_language.title',
					'items'               => array(
						array(
							'LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',
							-1
						),
						array(
							'LLL:EXT:lang/locallang_general.xml:LGL.default_value',
							0
						)
					),
				),
			),
			'l10n_parent'      => array(
				'displayCond' => 'FIELD:sys_language_uid:>:0',
				'exclude'     => 1,
				'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
				'config'      => array(
					'type'                => 'select',
					'items'               => array(
						array(
							'',
							0
						),
					),
					'foreign_table'       => $tableName,
					'foreign_table_where' => 'AND ' . $tableName . '.pid=###CURRENT_PID### AND ' . $tableName . '.sys_language_uid IN (-1,0)',
				),
			),
			'l10n_diffsource'  => array(
				'config' => array(
					'type' => 'passthrough',
				),
			),
			't3ver_label'      => array(
				'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
				'config' => array(
					'type' => 'input',
					'size' => 30,
					'max'  => 255,
				)
			),
			'hidden'           => array(
				'exclude' => 1,
				'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
				'config'  => array(
					'type' => 'check',
				),
			),
			'starttime'        => array(
				'exclude'   => 1,
				'l10n_mode' => 'mergeIfNotBlank',
				'label'     => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
				'config'    => array(
					'type'     => 'input',
					'size'     => 13,
					'max'      => 20,
					'eval'     => 'datetime',
					'checkbox' => 0,
					'default'  => 0,
					'range'    => array(
						'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
					),
				),
			),
			'fe_group'         => $GLOBALS['TCA']['tt_content']['columns']['fe_group'],
			'editlock'         => Array(
				'exclude'   => 1,
				'l10n_mode' => 'mergeIfNotBlank',
				'label'     => 'LLL:EXT:lang/locallang_tca.xml:editlock',
				'config'    => Array(
					'type' => 'check'
				)
			),
			'endtime'          => array(
				'exclude'   => 1,
				'l10n_mode' => 'mergeIfNotBlank',
				'label'     => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
				'config'    => array(
					'type'     => 'input',
					'size'     => 13,
					'max'      => 20,
					'eval'     => 'datetime',
					'checkbox' => 0,
					'default'  => 0,
					'range'    => array(
						'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
					),
				),
			),
		);

		// extension icon
		$modelName = str_replace('\\', '_', $modelInformation['modelName']);
		$tableIconRelPath = ExtensionManagementUtility::extRelPath($extensionName) . 'Resources/Public/Icons/' . $modelName . '.png';
		$tableDefaultIconRelPath = ExtensionManagementUtility::extRelPath('hdnet') . 'ext_icon.gif';
		$tableIcon = is_file(ExtensionManagementUtility::extPath($extensionName) . 'Resources/Public/Icons/' . $modelName . '.png') ? $tableIconRelPath : $tableDefaultIconRelPath;

		// title
		$fields = array_keys($customFields);
		$labelField = 'title';
		if (!in_array($labelField, $fields)) {
			$labelField = $fields[0];
		}
		try {
			TranslateUtility::assureLabel($tableName, $extensionName);
			$title = TranslateUtility::getLllString($tableName, $extensionName);
		} catch (\Exception $ex) {
			$title = 'Table: ' . $tableName;
		}


		$columns = ArrayUtility::mergeRecursiveDistinct($defaultColumns, $customFields);
		return array(
			'ctrl'      => array(
				'title'                           => $title,
				'label'                           => $labelField,
				'tstamp'                          => 'tstamp',
				'crdate'                          => 'crdate',
				'cruser_id'                       => 'cruser_id',
				'dividers2tabs'                   => TRUE,
				'sortby'                          => 'sorting',
				'versioningWS'                    => 2,
				'versioning_followPages'          => TRUE,
				'origUid'                         => 't3_origuid',
				'languageField'                   => 'sys_language_uid',
				'transOrigPointerField'           => 'l10n_parent',
				'transOrigDiffSourceField'        => 'l10n_diffsource',
				'shadowColumnsForNewPlaceholders' => 'sys_language_uid,' . $labelField,
				'delete'                          => 'deleted',
				'enablecolumns'                   => array(
					'disabled'  => 'hidden',
					'starttime' => 'starttime',
					'endtime'   => 'endtime',
					'fe_group'  => 'fe_group',
				),
				'searchFields'                    => implode(',', $searchFields),
				'iconfile'                        => $tableIcon
			),
			'interface' => array(
				'showRecordFieldList' => implode(',', array_keys($columns)),
			),
			'types'     => array(
				'1' => array('showitem' => implode(',', array_keys($customFields)) . ',--palette--;LLL:EXT:hdnet/Resources/Private/Language/locallang.xml:language;language, --div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,--palette--;LLL:EXT:cms/locallang_tca.xlf:pages.palettes.access;access'),
			),
			'palettes'  => array(
				'access'   => array('showitem' => 'starttime, endtime, --linebreak--, hidden, editlock, --linebreak--, fe_group'),
				'language' => array('showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource'),
			),
			'columns'   => $columns,
		);
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
		return implode(',' . LF, $fields);
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
	 *
	 * @param string $tableName
	 * @param array  $fields
	 *
	 * @return string
	 */
	protected function generateSQLQuery($tableName, $fields) {
		return LF . 'CREATE TABLE ' . $tableName . ' (' . LF . implode(',' . LF, $fields) . LF . ');' . LF;
	}

	/**
	 * Generate complete SQL Query
	 *
	 * @param string $tableName
	 * @param string $custom
	 *
	 * @return string
	 */
	protected function generateCompleteSQLQuery($tableName, $custom) {
		$fields = array();
		$fields[] = 'uid int(11) NOT NULL auto_increment';
		$fields[] = 'pid int(11) DEFAULT \'0\' NOT NULL';

		if (strlen($custom)) {
			$fields[] = $custom;
		}

		$fields[] = 'tstamp int(11) unsigned DEFAULT \'0\' NOT NULL';
		$fields[] = 'crdate int(11) unsigned DEFAULT \'0\' NOT NULL';
		$fields[] = 'cruser_id int(11) unsigned DEFAULT \'0\' NOT NULL';
		$fields[] = 'deleted tinyint(4) unsigned DEFAULT \'0\' NOT NULL';
		$fields[] = 'hidden tinyint(4) unsigned DEFAULT \'0\' NOT NULL';
		$fields[] = 'starttime int(11) unsigned DEFAULT \'0\' NOT NULL';
		$fields[] = 'endtime int(11) unsigned DEFAULT \'0\' NOT NULL';
		$fields[] = 'fe_group varchar(100) DEFAULT \'0\' NOT NULL';
		$fields[] = 'editlock tinyint(4) unsigned DEFAULT \'0\' NOT NULL';

		/**
		 * @see http://docs.typo3.org/typo3cms/TCAReference/Reference/Ctrl/Index.html
		 * section: versioningWS
		 */
		$fields[] = 't3ver_oid int(11) DEFAULT \'0\' NOT NULL';
		$fields[] = 't3ver_id int(11) DEFAULT \'0\' NOT NULL';
		$fields[] = 't3ver_label varchar(255) DEFAULT \'\' NOT NULL';
		$fields[] = 't3ver_wsid int(11) DEFAULT \'0\' NOT NULL';
		$fields[] = 't3ver_state tinyint(4) DEFAULT \'0\' NOT NULL';
		$fields[] = 't3ver_stage int(11) DEFAULT \'0\' NOT NULL';
		$fields[] = 't3ver_count int(11) DEFAULT \'0\' NOT NULL';
		$fields[] = 't3ver_tstamp int(11) DEFAULT \'0\' NOT NULL';
		$fields[] = 't3ver_move_id int(11) DEFAULT \'0\' NOT NULL';

		$fields[] = 'sorting int(11) DEFAULT \'0\' NOT NULL';
		$fields[] = 't3_origuid int(11) DEFAULT \'0\' NOT NULL';
		$fields[] = 'sys_language_uid int(11) DEFAULT \'0\' NOT NULL';
		$fields[] = 'l10n_parent int(11) DEFAULT \'0\' NOT NULL';
		$fields[] = 'l10n_diffsource mediumblob';

		$fields[] = 'PRIMARY KEY (uid)';
		$fields[] = 'KEY parent (pid)';
		$fields[] = 'KEY t3ver_oid (t3ver_oid,t3ver_wsid)';
		$fields[] = 'KEY language (l10n_parent,sys_language_uid)';

		return $this->generateSQLQuery($tableName, $fields);
	}
}
