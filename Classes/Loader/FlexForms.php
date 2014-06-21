<?php
/**
 * Loading FlexForms
 *
 * @category   Extension
 * @package    Autoloader
 * @subpackage Loader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Loading FlexForms
 *
 * @package    Autoloader
 * @subpackage Loader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class FlexForms implements LoaderInterface {

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
		$flexForms = array();
		$FlexFormPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Configuration/FlexForms/';
		$extensionName = GeneralUtility::underscoredToUpperCamelCase($loader->getExtensionKey());
		$FlexForms = FileUtility::getBaseFilesInDir($FlexFormPath, 'xml');
		foreach ($FlexForms as $fileKey) {
			$pluginSignature = strtolower($extensionName . '_' . $fileKey);
			$flexForms[] = array(
				'pluginSignature' => $pluginSignature,
				'path'            => 'FILE:EXT:' . $loader->getExtensionKey() . '/Configuration/FlexForms/' . $fileKey . '.xml',
			);
		}
		return $flexForms;
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
		foreach ($loaderInformation as $info) {
			$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$info['pluginSignature']] = 'layout,select_key,recursive';
			$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$info['pluginSignature']] = 'pi_flexform';
			ExtensionManagementUtility::addPiFlexFormValue($info['pluginSignature'], $info['path']);
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

	}
}