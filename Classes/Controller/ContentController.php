<?php
/**
 * Content Controller
 *
 * @category   Extension
 * @package    Autoloader\Controller
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\Autoloader\Controller;

use HDNET\Autoloader\Utility\ExtendedUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Content Controller
 *
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class ContentController extends ActionController {

	/**
	 * Render the content Element via ExtBase
	 */
	public function indexAction() {
		$extensionKey = $this->settings['extensionKey'];
		$vendorName = $this->settings['vendorName'];
		$name = $this->settings['contentElement'];
		$data = $this->configurationManager->getContentObject()->data;

		$camelCaseExtKey = GeneralUtility::underscoredToUpperCamelCase($extensionKey);
		$targetObject = $vendorName . '\\' . $camelCaseExtKey . '\\Domain\\Model\\Content\\' . $name;
		$model = ModelUtility::getModel($targetObject, $data);

		$view = $this->createStandaloneView();
		$view->assignMultiple(array(
			'data'     => $data,
			'object'   => $model,
			'settings' => $this->settings
		));
		return $view->render();
	}

	/**
	 * Create a StandaloneView for the ContentObject.
	 *
	 * @return \TYPO3\CMS\Fluid\View\StandaloneView
	 */
	protected function createStandaloneView() {
		$extensionKey = $this->settings['extensionKey'];
		$name = $this->settings['contentElement'];
		$siteRelPath = ExtensionManagementUtility::siteRelPath($extensionKey);

		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
		$view = ExtendedUtility::create('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$view->setTemplatePathAndFilename($siteRelPath . 'Resources/Private/Templates/Content/' . $name . '.html');
		$partialPath = $siteRelPath . 'Resources/Private/Partials';
		// @todo add setPartialRootPaths check for TYPO3 CMS 7.0 / move to central function see ElementBackendPreview
		$view->setPartialRootPath($partialPath);

		return $view;
	}

	/**
	 * Get the target object
	 *
	 * @param string $objectName
	 * @param array  $data
	 *
	 * @return object
	 * @deprecated moved to ModelUtility
	 * @see        HDNET\Autoloader\Utility\ModelUtility::getModel
	 */
	protected function getObject($objectName, $data) {
		$query = ExtendedUtility::getQuery($objectName);
		$query->getQuerySettings()
			->setRespectStoragePage(FALSE);
		$query->getQuerySettings()
			->setRespectSysLanguage(FALSE);
		return $query->matching($query->equals('uid', $data['uid']))
			->execute()
			->getFirst();
	}
}
