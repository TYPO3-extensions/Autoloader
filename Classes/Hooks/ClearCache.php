<?php
/**
 * Clear Cache hook for the Backend
 *
 * @category   Extension
 * @package    Autoloader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\Autoloader\Hooks;

use TYPO3\CMS\Backend\Toolbar\ClearCacheActionsHookInterface;
use TYPO3\CMS\Core\Http\AjaxRequestHandler;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Clear Cache hook for the Backend
 *
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 * @hook       TYPO3_CONF_VARS|SC_OPTIONS|additionalBackendItems|cacheActions
 */
class ClearCache implements ClearCacheActionsHookInterface {

	/**
	 * Modifies CacheMenuItems array
	 *
	 * @param array $cacheActions
	 * @param array $optionValues
	 *
	 * @return void
	 */
	public function manipulateCacheActions(&$cacheActions, &$optionValues) {
		if ($this->isProduction() || !$this->isAdmin()) {
			return;
		}

		$cacheActions[] = array(
			'id'    => 'autoloader',
			'title' => 'EXT:autoloader caches',
			'href'  => 'ajax.php?ajaxID=autoloader::clearCache',
			'icon'  => '<img src="' . ExtensionManagementUtility::extRelPath('autoloader') . '/ext_icon.png">',
		);
	}

	/**
	 * clear Cache ajax handler
	 *
	 * @param array              $ajaxParams
	 * @param AjaxRequestHandler $ajaxObj
	 */
	public function clear($ajaxParams, AjaxRequestHandler $ajaxObj) {
		if ($this->isProduction() || !$this->isAdmin()) {
			return;
		}

		/** @var \TYPO3\CMS\Core\Cache\CacheManager $cacheManager */
		$cacheManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager');
		$cacheManager->getCache('autoloader')
			->flush();
	}

	/**
	 * Return TRUE if the current instance is in production mode
	 *
	 * @return bool
	 */
	protected function isProduction() {
		return GeneralUtility::getApplicationContext()
			->isProduction();
	}

	/**
	 * Check if the user is a admin
	 *
	 * @return bool
	 */
	protected function isAdmin() {
		return is_object($GLOBALS['BE_USER']) && $GLOBALS['BE_USER']->isAdmin();
	}
}