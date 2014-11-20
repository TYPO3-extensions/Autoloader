<?php
/**
 * Custom Backend Preview for Elements like Content Objects.
 *
 * @category   Extension
 * @package    Autoloader
 * @author     Carsten Biebricher <carsten.biebricher@hdnet.de>
 */
namespace HDNET\Autoloader\Hooks;

use HDNET\Autoloader\Utility\ExtendedUtility;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use TYPO3\CMS\Backend\View\PageLayoutView;

/**
 * Class ElementBackendPreview
 *
 * @author Carsten Biebricher <carsten.biebricher@hdnet.de>
 * @see    \TYPO3\CMS\Backend\View\PageLayoutView::tt_content_drawItem
 * @hook   TYPO3_CONF_VARS|SC_OPTIONS|cms/layout/class.tx_cms_layout.php|tt_content_drawItem
 */
class ElementBackendPreview implements PageLayoutViewDrawItemHookInterface {

	/**
	 * Preprocesses the preview rendering of a content element.
	 *
	 * @param PageLayoutView $parentObject  Calling parent object
	 * @param bool           $drawItem      Whether to draw the item using the default functionalities
	 * @param string         $headerContent Header content
	 * @param string         $itemContent   Item content
	 * @param array          $row           Record row of tt_content
	 *
	 * @return void
	 */
	public function preProcess(PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row) {
		if (!$this->isAutoloaderContenobject($row)) {
			return;
		}

		if (!$this->hasBackendPreview($row)) {
			return;
		}

		$itemContent = $this->getBackendPreview($row);
		$drawItem = FALSE;
	}

	/**
	 * Render the Backend Preview Template and return the HTML.
	 *
	 * @param array $row
	 *
	 * @return string
	 */
	protected function getBackendPreview($row) {
		$ctype = $row['CType'];
		/** @var array $config */
		$config = $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['ContentObject'][$ctype];

		$model = ModelUtility::getModel($config['modelClass'], $row);

		$view = $this->createStandaloneView($config['extensionKey'], $config['backendTemplatePath']);
		$view->assignMultiple(array(
				'data'   => $row,
				'object' => $model
			));
		return $view->render();
	}

	/**
	 * Check if the ContentObject has a Backend Preview Template.
	 *
	 * @param array $row
	 *
	 * @return bool
	 */
	protected function hasBackendPreview($row) {
		$ctype = $row['CType'];
		/** @var array $config */
		$config = $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['ContentObject'][$ctype];

		$beTemplatePath = GeneralUtility::getFileAbsFileName($config['backendTemplatePath']);

		if (file_exists($beTemplatePath)) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Check if the the Element is registered by the ContenObject-Autoloader.
	 *
	 * @param array $row
	 *
	 * @return bool
	 */
	protected function isAutoloaderContenobject(array $row) {
		$ctype = $row['CType'];
		return (bool) $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['ContentObject'][$ctype];
	}

	/**
	 * Create a StandaloneView for the ContentObject.
	 *
	 * @param string $extensionKey
	 * @param string $backendTemplatePath
	 *
	 * @return \TYPO3\CMS\Fluid\View\StandaloneView
	 */
	protected function createStandaloneView($extensionKey, $backendTemplatePath) {
		$siteRelPath = ExtensionManagementUtility::siteRelPath($extensionKey);
		$absTemplatePathAndFilename = GeneralUtility::getFileAbsFileName($backendTemplatePath);

		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
		$view = ExtendedUtility::create('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$view->setTemplatePathAndFilename($absTemplatePathAndFilename);
		$partialPath = $siteRelPath . 'Resources/Private/Partials';
		// @todo add setPartialRootPaths check for TYPO3 CMS 7.0 / move to central function see ContentController
		$view->setPartialRootPath($partialPath);

		return $view;
	}

}