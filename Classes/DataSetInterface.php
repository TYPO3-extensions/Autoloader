<?php
/**
 * data set interface
 *
 * @category   Extension
 * @package    Autoloader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\Autoloader;

use HDNET\Autoloader\Loader;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * data set interface
 *
 * @package    Autoloader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
interface DataSetInterface extends SingletonInterface {

	/**
	 * Get TCA information
	 *
	 * @param string $tableName
	 *
	 * @return array
	 */
	public function getTca($tableName);

	/**
	 * Get database sql information
	 *
	 * @param string $tableName
	 *
	 * @return array
	 */
	public function getDatabaseSql($tableName);

	/**
	 * Get database sql key information
	 *
	 * @param string $tableName
	 *
	 * @return array
	 */
	public function getDatabaseSqlKey($tableName);
}