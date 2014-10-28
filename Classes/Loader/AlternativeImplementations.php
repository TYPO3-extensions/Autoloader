<?php
/**
 * Loading AlternativeImplementations
 *
 * @category   Extension
 * @package    Autoloader\Loader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Loading AlternativeImplementations
 *
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class AlternativeImplementations implements LoaderInterface {

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
		$classNames = array();
		$alternativeImpPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/AlternativeImplementations/';
		$alternativeClasses = FileUtility::getBaseFilesInDir($alternativeImpPath, 'php');

		$extKey = GeneralUtility::underscoredToUpperCamelCase($loader->getExtensionKey());

		foreach ($alternativeClasses as $aic) {
			$aicClass = $loader->getVendorName() . '\\' . $extKey . '\\AlternativeImplementations\\' . $aic;

			if (!$loader->isInstantiableClass($aicClass)) {
				continue;
			}

			/** @var $classReflection \TYPO3\CMS\Extbase\Reflection\ClassReflection */
			$classReflection = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Reflection\\ClassReflection', $aicClass);
			$originalName = $classReflection->getParentClass()
				->getName();
			$classNames[] = array(
				'originalName'    => $originalName,
				'classReflection' => $classReflection,
			);
		}
		return $classNames;

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
	 * @param Loader $loader
	 * @param array  $loaderInformation
	 *
	 * @return NULL
	 */
	public function loadExtensionConfiguration(Loader $loader, array $loaderInformation) {
		/** @var \TYPO3\CMS\Extbase\Object\Container\Container $objectManager */
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\Container\\Container');
		foreach ($loaderInformation as $classInformation) {
			$objectManager->registerImplementation($classInformation['originalName'], $classInformation['classReflection']);
		}
		return NULL;
	}
}