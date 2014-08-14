<?php
/**
 * Loading Slots
 *
 * @category   Extension
 * @package    Autoloader
 * @subpackage Loader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Service\SmartObjectInformationService;
use HDNET\Autoloader\SmartObjectRegister;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use HDNET\Autoloader\Utility\TranslateUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ClassReflection;
use TYPO3\CMS\Extbase\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Loading Slots
 *
 * @package    Autoloader
 * @subpackage Loader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class ContentObjects implements LoaderInterface {

	/**
	 * Prepare the content object loader
	 *
	 * @param Loader $loader
	 * @param int    $type
	 *
	 * @return array
	 */
	public function prepareLoader(Loader $loader, $type) {
		$loaderInformation = array();
		$smartObjectInformationService = new SmartObjectInformationService();

		$modelPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Domain/Model/Content/';
		$models = FileUtility::getBaseFilesInDir($modelPath, 'php');
		if ($models) {
			TranslateUtility::assureLabel('tt_content.' . $loader->getExtensionKey() . '.header', $loader->getExtensionKey(), $loader->getExtensionKey() . ' (Header)', NULL, 'xml');
		}
		foreach ($models as $model) {
			$key = GeneralUtility::camelCaseToLowerCaseUnderscored($model);
			$className = $loader->getVendorName() . '\\' . ucfirst(GeneralUtility::underscoredToUpperCamelCase($loader->getExtensionKey())) . '\\Domain\\Model\\Content\\' . $model;
			if (!$loader->isInstantiableClass($className)) {
				continue;
			}
			$fieldConfiguration = array();

			// create labels in the ext_tables run, to have a valid DatabaseConnection
			if ($type === LoaderInterface::EXT_TABLES) {
				TranslateUtility::assureLabel('tt_content.' . $key, $loader->getExtensionKey(), $key . ' (Title)', NULL, 'xml');
				TranslateUtility::assureLabel('tt_content.' . $key . '.description', $loader->getExtensionKey(), $key . ' (Description)', NULL, 'xml');
				$fieldConfiguration = $this->getClassPropertiesInLowerCaseUnderscored($className);
			}

			$icon = ExtensionManagementUtility::extRelPath($loader->getExtensionKey());
			$extPath = ExtensionManagementUtility::extPath($loader->getExtensionKey());
			if (is_file($extPath . 'ext_icon.png')) {
				$icon .= 'ext_icon.png';
			} else if (is_file($extPath . 'ext_icon.gif')) {
				$icon .= 'ext_icon.gif';
			} else {
				$icon = ExtensionManagementUtility::extRelPath('autoloader') . 'ext_icon.png';
			}

			$entry =  array(
				'fieldConfiguration' => implode(',', $fieldConfiguration),
				'modelClass'         => $className,
				'model'              => $model,
				'icon'               => $icon,
			);

			if ($type === LoaderInterface::EXT_TABLES) {
				if (ModelUtility::getTableNameByModelReflectionAnnotation($className)) {
					$entry['additionalTca'] = $smartObjectInformationService->getCustomModelFieldTca($className);
					$entry['tableName'] = ModelUtility::getTableName($className);
				}
			}

			$loaderInformation[$key] = $entry;
		}

		return $loaderInformation;
	}

	/**
	 * Same as getClassProperties, but the fields are in LowerCaseUnderscored
	 *
	 * @param $className
	 *
	 * @return array
	 * @see getClassProperties
	 */
	protected function getClassPropertiesInLowerCaseUnderscored($className) {
		$properties = $this->getClassProperties($className);
		foreach ($properties as $key => $value) {
			$properties[$key] = GeneralUtility::camelCaseToLowerCaseUnderscored($value);
		}
		return $properties;
	}

	/**
	 * Basic configuration for the properties.
	 * You have to add e.g. a RTE yourself: bodytext;LLL:EXT:cms/locallang_ttc.xml:bodytext_formlabel;;richtext:rte_transform[flag=rte_enabled|mode=ts_css]
	 *
	 * @param string $className
	 *
	 * @return array
	 */
	protected function getClassProperties($className) {
		$properties = array();
		/** @var ClassReflection $classReflection */
		$classReflection = new ClassReflection($className);
		foreach ($classReflection->getProperties() as $property) {
			/** @var \TYPO3\CMS\Extbase\Reflection\PropertyReflection $property */
			if ($property->getDeclaringClass()
			             ->getName() === $classReflection->getName()
			) {
				$properties[] = $property->getName();
			}
		}
		return $properties;
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
		// content register
		foreach ($loaderInformation as $e => $config) {
			SmartObjectRegister::register($config['modelClass']);

			if ($config['additionalTca']) {
				$tableName = $config['tableName'];
				$GLOBALS['TCA'][$tableName]['columns'] = ArrayUtility::arrayMergeRecursiveOverrule($GLOBALS['TCA'][$tableName]['columns'], $config['additionalTca']);
			}

			ExtensionManagementUtility::addPlugin(array(
				TranslateUtility::getLllString('tt_content.' . $e, $loader->getExtensionKey()),
				$loader->getExtensionKey() . '_' . $e
			), 'CType');

			$baseTcaConfiguration = '
    --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.general;general,
    --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.header;header,
    --div--;Inhaltsdaten,
    ' . $config['fieldConfiguration'] . ',
    --div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
    --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.visibility;visibility,
    --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;access,
    --div--;LLL:EXT:cms/locallang_ttc.xml:tabs.extended';

			if (ExtensionManagementUtility::isLoaded('gridelements')) {
				$baseTcaConfiguration .= ',tx_gridelements_container,tx_gridelements_columns';
			}

			$GLOBALS['TCA']['tt_content']['types'][$loader->getExtensionKey() . '_' . $e]['showitem'] = $baseTcaConfiguration;

			$icon = ExtensionManagementUtility::extRelPath($loader->getExtensionKey());
			if (is_file(ExtensionManagementUtility::extPath($loader->getExtensionKey(), 'ext_icon.gif'))) {
				$icon .= 'ext_icon.gif';
			} else {
				$icon .= 'ext_icon.png';
			}

			ExtensionManagementUtility::addPageTSConfig('
mod.wizards.newContentElement.wizardItems.' . $loader->getExtensionKey() . '.elements.' . $loader->getExtensionKey() . '_' . $e . ' {
    icon = ' . $icon . '
    title = ' . TranslateUtility::getLllString('tt_content.' . $e, $loader->getExtensionKey()) . '
    description = ' . TranslateUtility::getLllString('tt_content.' . $e . '.description', $loader->getExtensionKey()) . '
    tt_content_defValues {
        CType = ' . $loader->getExtensionKey() . '_' . $e . '
    }
}');

		}

		if ($loaderInformation) {
			ExtensionManagementUtility::addPageTSConfig('
mod.wizards.newContentElement.wizardItems.' . $loader->getExtensionKey() . ' {
	show = *
	header = ' . TranslateUtility::getLllString('tt_content.' . $loader->getExtensionKey() . '.header', $loader->getExtensionKey()) . '
}');
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
		static $loadPlugin = TRUE;
		if ($loadPlugin) {
			$loadPlugin = FALSE;
			ExtensionUtility::configurePlugin('HDNET.autoloader', 'Content', array('Content' => 'index'), array('Content' => ''));
		}
		foreach ($loaderInformation as $e => $config) {
			ExtensionManagementUtility::addTypoScript($loader->getExtensionKey(), 'setup', trim('
        tt_content.' . $loader->getExtensionKey() . '_' . $e . ' = COA
        tt_content.' . $loader->getExtensionKey() . '_' . $e . ' {
            10 =< lib.stdheader
            20 =< tt_content.list.20.autoloader_content
            20.settings {
                contentElement = ' . $config['model'] . '
                extensionKey = ' . $loader->getExtensionKey() . '
                vendorName = ' . $loader->getVendorName() . '
            }
        }
                config.tx_extbase.persistence.classes.' . $config['modelClass'] . '.mapping.tableName = tt_content')
			, 43);
		}

		return NULL;
	}

}