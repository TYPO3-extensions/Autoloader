<?php
/**
 * Loading Hooks
 *
 * @category   Extension
 * @package    Autoloader
 * @subpackage Loader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */


namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ExtendedUtility;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Loading Hooks
 *
 * @package    Autoloader
 * @subpackage Loader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class Hooks implements LoaderInterface {

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
		$hooks = array();
		$files = FileUtility::getBaseFilesInDir(ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Hooks/', 'php');

		$extKey = GeneralUtility::underscoredToUpperCamelCase($loader->getExtensionKey());
		foreach ($files as $hookFile) {
			$hookClass = $loader->getVendorName() . '\\' . $extKey . '\\Hooks\\' . $hookFile;
			$hookBase = 'EXT:' . $loader->getExtensionKey() . '/Classes/Hooks/' . $hookFile . '.php:' . $hookClass;

			if (!$loader->isInstantiableClass($hookClass)) {
				continue;
			}

			/** @var $classReflection \TYPO3\CMS\Extbase\Reflection\ClassReflection */
			$classReflection = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Reflection\\ClassReflection', $hookClass);

			// add class hook
			$classTags = $classReflection->getTagsValues();
			if (isset($classTags['hook'])) {
				if (is_array($classTags['hook'])) {
					$classTags['hook'] = implode(' ', $classTags['hook']);
				}
				$classTags['hook'] = GeneralUtility::trimExplode(' ', $classTags['hook'], TRUE);
				$hooks[] = array(
					'locations'     => $classTags['hook'],
					'configuration' => $hookBase,
				);
			}

			// add method hooks
			foreach ($classReflection->getMethods() as $methodReflection) {
				/** @var $methodReflection \TYPO3\CMS\Extbase\Reflection\MethodReflection */
				$methodTags = $methodReflection->getTagsValues();
				if (isset($methodTags['hook'])) {
					if (is_array($methodTags['hook'])) {
						$methodTags['hook'] = implode(' ', $methodTags['hook']);
					}
					$methodTags['hook'] = GeneralUtility::trimExplode(' ', $methodTags['hook'], TRUE);
					$hooks[] = array(
						'locations'     => $methodTags['hook'],
						'configuration' => $hookBase . '->' . $methodReflection->getName(),
					);
				}
			}
		}
		return $hooks;
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
		return NULL;
	}

	/**
	 * Run the loading process for the ext_localconf.php file
	 *
	 * @param \HDNET\Autoloader\Loader $loader
	 * @param array                    $loaderInformation
	 *
	 * @internal param \HDNET\Autoloader\Loader $autoLoader
	 * @return NULL
	 */
	public function loadExtensionConfiguration(Loader $loader, array $loaderInformation) {
		foreach ($loaderInformation as $hook) {
			ExtendedUtility::addHooks($hook['locations'], $hook['configuration']);
		}
		return NULL;
	}
}