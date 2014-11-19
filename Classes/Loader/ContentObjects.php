<?php
/**
 * Loading Slots
 *
 * @category   Extension
 * @package    Autoloader\Loader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\SmartObjectRegister;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\TranslateUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ClassReflection;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Loading Slots
 *
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
				$defaultFields = $this->getDefaultTcaFields();
				$fieldConfiguration = array_diff($fieldConfiguration, $defaultFields);
			}

			$icon = ExtensionManagementUtility::extRelPath($loader->getExtensionKey());
			$extPath = ExtensionManagementUtility::extPath($loader->getExtensionKey());
			if (is_file($extPath . 'ext_icon.png')) {
				$icon .= 'ext_icon.png';
			} elseif (is_file($extPath . 'ext_icon.gif')) {
				$icon .= 'ext_icon.gif';
			} else {
				$icon = ExtensionManagementUtility::extRelPath('autoloader') . 'ext_icon.png';
			}

			$entry = array(
				'fieldConfiguration' => implode(',', $fieldConfiguration),
				'modelClass'         => $className,
				'model'              => $model,
				'icon'               => $icon,
			);

			SmartObjectRegister::register($entry['modelClass']);
			$loaderInformation[$key] = $entry;
		}

		$this->checkAndCreateDummyTemplates($loaderInformation, $loader);

		return $loaderInformation;
	}

	/**
	 * Check if the templates are exist and create a dummy, if there
	 * is no valid template
	 *
	 * @param array  $loaderInformation
	 * @param Loader $loader
	 */
	protected function checkAndCreateDummyTemplates(array $loaderInformation, Loader $loader) {
		foreach ($loaderInformation as $configuration) {
			$templatePath = ExtensionManagementUtility::siteRelPath($loader->getExtensionKey()) . 'Resources/Private/Templates/Content/' . $configuration['model'] . '.html';
			$beTemplatePath = ExtensionManagementUtility::siteRelPath($loader->getExtensionKey()) . 'Resources/Private/Templates/Content/' . $configuration['model'] . 'Backend.html';
			$absoluteTemplatePath = GeneralUtility::getFileAbsFileName($templatePath);
			$absoluteBeTemplatePath = GeneralUtility::getFileAbsFileName($beTemplatePath);
			if (!file_exists($absoluteTemplatePath)) {
				$templateContent = 'Use object to get access to your domain model: <f:debug>{object}</f:debug>';
				FileUtility::writeFileAndCreateFolder($absoluteTemplatePath, $templateContent);

				$beTemplateContent = 'The ContentObject Preview is configurable in the ContentObject Backend Template.<br />
<code>File: ' . $beTemplatePath . '</code><br /><strong>Alternative you can delete this file to go back to the old behavior.</strong><br />';
				FileUtility::writeFileAndCreateFolder($absoluteBeTemplatePath, $beTemplateContent);
			}
		}
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
	 * You have to add e.g. a RTE yourself (please check the TCA documentation)
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
	 * Wrap the given field configuration in the CE default TCA fields
	 *
	 * @param string $configuration
	 *
	 * @return string
	 */
	protected function wrapDefaultTcaConfiguration($configuration) {
		$configuration = trim($configuration) ? trim($configuration) . ',' : '';
		return '--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.general;general,
    --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.header;header,
    --div--;LLL:EXT:autoloader/Resources/Private/Language/locallang.xml:contentData,
    ' . $configuration . '
    --div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
    --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.visibility;visibility,
    --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;access,
    --div--;LLL:EXT:cms/locallang_ttc.xml:tabs.extended';
	}

	/**
	 * Get the fields that are in the default configuration
	 *
	 * @param null|string $configuration
	 *
	 * @return array
	 */
	protected function getDefaultTcaFields($configuration = NULL) {
		if ($configuration === NULL) {
			$configuration = $this->wrapDefaultTcaConfiguration('');
		}
		$defaultFields = array();
		$existingFields = array_keys($GLOBALS['TCA']['tt_content']['columns']);
		$parts = GeneralUtility::trimExplode(',', $configuration, TRUE);
		foreach ($parts as $fieldConfiguration) {
			$fieldConfiguration = GeneralUtility::trimExplode(';', $fieldConfiguration, TRUE);
			if (in_array($fieldConfiguration[0], $existingFields)) {
				$defaultFields[] = $fieldConfiguration[0];
			} elseif ($fieldConfiguration[0] == '--palette--') {
				$paletteConfiguration = $GLOBALS['TCA']['tt_content']['palettes'][$fieldConfiguration[2]]['showitem'];
				if (is_string($paletteConfiguration)) {
					$defaultFields = array_merge($defaultFields, $this->getDefaultTcaFields($paletteConfiguration));
				}
			}
		}
		return $defaultFields;
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

			ExtensionManagementUtility::addPlugin(array(
				TranslateUtility::getLllOrHelpMessage('tt_content.' . $e, $loader->getExtensionKey()),
				$loader->getExtensionKey() . '_' . $e
			), 'CType');


			$typeKey = $loader->getExtensionKey() . '_' . $e;
			if (!isset($GLOBALS['TCA']['tt_content']['types'][$typeKey]['showitem'])) {
				$baseTcaConfiguration = $this->wrapDefaultTcaConfiguration($config['fieldConfiguration']);

				if (ExtensionManagementUtility::isLoaded('gridelements')) {
					$baseTcaConfiguration .= ',tx_gridelements_container,tx_gridelements_columns';
				}

				$GLOBALS['TCA']['tt_content']['types'][$typeKey]['showitem'] = $baseTcaConfiguration;
			}

			$icon = ExtensionManagementUtility::extRelPath($loader->getExtensionKey());
			if (is_file(ExtensionManagementUtility::extPath($loader->getExtensionKey(), 'ext_icon.gif'))) {
				$icon .= 'ext_icon.gif';
			} else {
				$icon .= 'ext_icon.png';
			}

			ExtensionManagementUtility::addPageTSConfig('
mod.wizards.newContentElement.wizardItems.' . $loader->getExtensionKey() . '.elements.' . $loader->getExtensionKey() . '_' . $e . ' {
    icon = ' . $icon . '
    title = ' . TranslateUtility::getLllOrHelpMessage('tt_content.' . $e, $loader->getExtensionKey()) . '
    description = ' . TranslateUtility::getLllOrHelpMessage('tt_content.' . $e . '.description', $loader->getExtensionKey()) . '
    tt_content_defValues {
        CType = ' . $loader->getExtensionKey() . '_' . $e . '
    }
}');
			$cObjectConfiguration = array(
				'extensionKey'        => $loader->getExtensionKey(),
				'backendTemplatePath' => 'EXT:' . $loader->getExtensionKey() . '/Resources/Private/Templates/Content/' . $config['model'] . 'Backend.html',
				'modelClass'          => $config['modelClass']
			);

			$GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['ContentObject'][$loader->getExtensionKey() . '_' . GeneralUtility::camelCaseToLowerCaseUnderscored($config['model'])] = $cObjectConfiguration;
		}

		if ($loaderInformation) {
			ExtensionManagementUtility::addPageTSConfig('
mod.wizards.newContentElement.wizardItems.' . $loader->getExtensionKey() . ' {
	show = *
	header = ' . TranslateUtility::getLllOrHelpMessage('tt_content.' . $loader->getExtensionKey() . '.header', $loader->getExtensionKey()) . '
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
                config.tx_extbase.persistence.classes.' . $config['modelClass'] . '.mapping.tableName = tt_content'), 43);
		}

		return NULL;
	}
}