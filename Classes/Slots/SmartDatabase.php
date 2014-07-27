<?php
/**
 * Add the smart object SQL string
 *
 * @category   Extension
 * @package    Autoloader
 * @subpackage Slots
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\Autoloader\Slots;

use HDNET\Autoloader\SmartObjectManager;

/**
 * Add the smart object SQL string
 *
 * @package    Autoloader
 * @subpackage Slots
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class SmartDatabase {

	/**
	 * Add the smart object SQL string the the signal below
	 *
	 * @signalClass \TYPO3\CMS\Install\Service\SqlExpectedSchemaService
	 * @signalName tablesDefinitionIsBeingBuilt
	 */
	public function loadSmartObjectTables(array $sqlString) {
		$sqlString[] = SmartObjectManager::getSmartObjectRegisterSql();
		return array('sqlString' => $sqlString);
	}

	/**
	 * Add the smart object SQL string the the signal below
	 *
	 * @signalClass \TYPO3\CMS\Extensionmanager\Utility\InstallUtility
	 * @signalName tablesDefinitionIsBeingBuilt
	 */
	public function updateSmartObjectTables(array $sqlString, $extensionKey) {
		$sqlString[] = SmartObjectManager::getSmartObjectRegisterSql();
		return array('sqlString' => $sqlString, 'extensionKey' => $extensionKey);
	}
}
