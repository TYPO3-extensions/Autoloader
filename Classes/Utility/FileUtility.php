<?php
/**
 * FileUtility
 *
 * @category   Extension
 * @package    Autoloader
 * @subpackage Utility
 * @author     Tim Lochmüller <tim.locahmueller@hdnet.de>
 */

namespace HDNET\Autoloader\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * FileUtility
 *
 * @package    Autoloader
 * @subpackage Utility
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class FileUtility {

	/**
	 * Get all base file names in the given directory with the given file extension
	 * Check also if the directory exists
	 *
	 * @param $dirPath
	 * @param $fileExtension
	 *
	 * @return array
	 */
	static function getBaseFilesInDir($dirPath, $fileExtension) {
		if (!is_dir($dirPath)) {
			return array();
		}
		$files = GeneralUtility::getFilesInDir($dirPath, $fileExtension);
		foreach ($files as $key => $file) {
			$files[$key] = pathinfo($file, PATHINFO_FILENAME);
		}
		return array_values($files);
	}
}