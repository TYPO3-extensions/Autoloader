<?php
/**
 * ClassNamingUtility.php
 *
 * @category   Extension
 * @package    Autoloader
 * @author     Tim Spiekerkoetter HDNET GmbH & Co. KG <tim.spiekerkoetter@hdnet.de>
 */

namespace HDNET\Autoloader\Utility;

/**
 * ClassNamingUtility
 *
 * @package    Autoloader
 * @subpackage Utility
 * @author     Tim Spiekerkoetter HDNET GmbH & Co. KG <tim.spiekerkoetter@hdnet.de>
 */
class ClassNamingUtility {

	/**
	 * Explodes a modelName like \Vendor\Ext\Domain\Model\Foo
	 * into several pieces like vendorName, extensionName, subpackageKey and controllerName
	 *
	 * @param string $modelName The model name to be exploded
	 *
	 * @return string Extension name
	 * @see \TYPO3\CMS\Core\Utility\ClassNamingUtility::explodeObjectControllerName
	 */
	static public function explodeObjectModelName($modelName) {
		if (strpos($modelName, '\\') !== FALSE) {
			if (substr($modelName, 0, 9) === 'TYPO3\\CMS') {
				$extensionName = '^(?P<vendorName>[^\\\\]+\\\[^\\\\]+)\\\(?P<extensionName>[^\\\\]+)';
			} else {
				$extensionName = '^(?P<vendorName>[^\\\\]+)\\\\(?P<extensionName>[^\\\\]+)';
			}

			preg_match('/' . $extensionName . '\\\\Domain\\\\Model\\\\(?P<modelName>[a-z\\\\]+)$/ix', $modelName, $matches);
		} else {
			preg_match('/^Tx_(?P<extensionName>[^_]+)_Domain_Model_(?P<modelName>[a-z_]+)/ix', $modelName, $matches);
		}

		return $matches;
	}
}
