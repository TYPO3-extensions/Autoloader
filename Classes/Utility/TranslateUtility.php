<?php
/**
 * TranslateUtility.php
 *
 * @category   Extension
 * @package    Autoloader
 * @subpackage Utility
 * @author     Carsten Biebricher <carsten.biebricher@hdnet.de>
 */

namespace HDNET\Autoloader\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TranslateUtility
 *
 * @package    Autoloader
 * @subpackage Utility
 * @author     Carsten Biebricher <carsten.biebricher@hdnet.de>
 */
class TranslateUtility {

	/**
	 * Assure the translation for the given key.
	 * If not exists create the label in the xml/xlf file.
	 * Returns the localization.
	 *
	 * Use the Slot to handle the label
	 *
	 * @see LocalizationUtility::translate
	 *
	 * @param string $key       key in the localization file
	 * @param string $extensionName
	 * @param string $default   default value of the label
	 * @param array  $arguments the arguments of the extension, being passed over to vsprintf
	 *
	 * @return string
	 */
	public static function assureLabel($key, $extensionName, $default = NULL, $arguments = NULL) {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['autoloader']['assureLabel'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['autoloader']['assureLabel'] as $classConfig) {
				$className = GeneralUtility::getUserObj($classConfig);
				if (is_object($className) && method_exists($className, 'assureLabel')) {
					$className->assureLabel($key, $extensionName, $default, $arguments);
				}
			}
		}

		return (string)$default;
	}

	/**
	 * Get the correct LLL string for the given key and extension
	 *
	 * @param        $key
	 * @param        $extensionKey
	 * @param string $file
	 *
	 * @return string
	 */
	static public function getLllString($key, $extensionKey, $file = 'locallang.xml') {
		return 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/' . $file . ':' . $key;
	}

}