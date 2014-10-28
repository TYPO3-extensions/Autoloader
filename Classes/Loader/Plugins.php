<?php
/**
 * Loading Plugins
 *
 * @category   Extension
 * @package    Autoloader\Loader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\TranslateUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Loading Plugins
 *
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class Plugins implements LoaderInterface {

	/**
	 * Get all the complex data for the loader.
	 * This return value will be cached and stored in the database
	 * There is no file monitoring for this cache
	 *
	 * @param Loader $loader
	 * @param int    $type
	 *
	 * @return array
	 */
	public function prepareLoader(Loader $loader, $type) {
		$pluginInformation = array();

		$controllerPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Controller/';
		$controllers = FileUtility::getBaseFilesRecursivelyInDir($controllerPath, 'php');

		$extKey = GeneralUtility::underscoredToUpperCamelCase($loader->getExtensionKey());
		foreach ($controllers as $controller) {
			$controllerName = $loader->getVendorName() . '\\' . $extKey . '\\Controller\\' . str_replace('/', '\\', $controller);

			if (!$loader->isInstantiableClass($controllerName)) {
				continue;
			}

			$controllerKey = str_replace('/', '\\', $controller);
			$controllerKey = str_replace('Controller', '', $controllerKey);

			/** @var $controllerReflection \TYPO3\CMS\Extbase\Reflection\ClassReflection */
			$controllerReflection = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Reflection\\ClassReflection', $controllerName);
			$methods = $controllerReflection->getMethods();

			foreach ($methods as $method) {
				/** @var $method \TYPO3\CMS\Extbase\Reflection\MethodReflection */
				if ($method->isTaggedWith('plugin')) {
					$pluginKeys = GeneralUtility::trimExplode(' ', implode(' ', $method->getTagValues('plugin')), TRUE);
					$actionName = str_replace('Action', '', $method->getName());

					foreach ($pluginKeys as $pluginKey) {
						$this->addPluginInformation($pluginInformation, $pluginKey, $controllerKey, $actionName, $method->isTaggedWith('noCache'));

					}
				}
			}
		}

		return $pluginInformation;
	}

	/**
	 * Add the given plugin information to the plugin information array
	 *
	 * @param $pluginInformation
	 * @param $pluginKey
	 * @param $controllerKey
	 * @param $actionName
	 * @param $noCache
	 */
	protected function addPluginInformation(&$pluginInformation, $pluginKey, $controllerKey, $actionName, $noCache) {
		if (!isset($pluginInformation[$pluginKey])) {
			$pluginInformation[$pluginKey] = array(
				'cache'   => array(),
				'noCache' => array(),
			);
		}

		// cache
		if (!isset($pluginInformation[$pluginKey]['cache'][$controllerKey])) {
			$pluginInformation[$pluginKey]['cache'][$controllerKey] = $actionName;
		} else {
			$pluginInformation[$pluginKey]['cache'][$controllerKey] .= ',' . $actionName;
		}

		// no Cache
		if ($noCache) {
			if (!isset($pluginInformation[$pluginKey]['noCache'][$controllerKey])) {
				$pluginInformation[$pluginKey]['noCache'][$controllerKey] = $actionName;
			} else {
				$pluginInformation[$pluginKey]['noCache'][$controllerKey] .= ',' . $actionName;
			}
		}
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
		foreach (array_keys($loaderInformation) as $key) {
			$label = TranslateUtility::getLllOrHelpMessage('plugin.' . $key, $loader->getExtensionKey());
			ExtensionUtility::registerPlugin($loader->getExtensionKey(), $key, $label);
		}
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
		$prefix = $loader->getVendorName() . '.' . $loader->getExtensionKey();
		foreach ($loaderInformation as $key => $information) {
			ExtensionUtility::configurePlugin($prefix, $key, $information['cache'], $information['noCache']);
		}
	}
}