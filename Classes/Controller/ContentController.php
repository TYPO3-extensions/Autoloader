<?php
/**
 * Content Controller
 *
 * @category   Extension
 * @package    Autoloader
 * @subpackage Controller
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\Autoloader\Controller;

use HDNET\Autoloader\Utility\ExtendedUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Content Controller
 *
 * @package    Autoloader
 * @subpackage Controller
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 * @deprecated
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

		$targetObject = $vendorName . '\\' . GeneralUtility::underscoredToUpperCamelCase($extensionKey) . '\\Domain\\Model\\Content\\' . $name;
		$object = $this->getObject($targetObject, $data);

		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
		$view = ExtendedUtility::create('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$view->assignMultiple(array(
			'data'   => $data,
			'object' => $object,
		));
		$view->setTemplatePathAndFilename(ExtensionManagementUtility::siteRelPath($extensionKey) . 'Resources/Private/Templates/Content/' . $name . '.html');
		$view->setPartialRootPath(ExtensionManagementUtility::siteRelPath($extensionKey) . 'Resources/Private/Partials');

		return $view->render();
	}

	/**
	 * Get the target object
	 *
	 * @param string $objectName
	 * @param array  $data
	 *
	 * @return object
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
