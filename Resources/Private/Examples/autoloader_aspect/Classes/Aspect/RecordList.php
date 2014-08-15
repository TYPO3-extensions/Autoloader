<?php
/**
 * This Example test the aspects before & after.
 * This example show, that not all 'AFTER' aspects works as expected.
 *
 * @category   Extension
 * @package    AutoloaderAspect
 * @author     Carsten Biebricher <carsten.biebricher@hdnet.de>
 */

namespace HDNET\AutoloaderAspect\Aspect;

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This Example test the aspects before & after.
 *
 * @package    AutoloaderAspect
 * @subpackage Aspect
 * @author     Carsten Biebricher <carsten.biebricher@hdnet.de>
 */
class RecordList {

	/**
	 * Called BEFORE the main-method and add a FlashMessage to the page.
	 *
	 * @param object $object class of the joinpoint
	 * @param array  $params arguments of the joinpoint
	 *
	 * @aspectClass \TYPO3\CMS\Recordlist\RecordList
	 * @aspectJoinpoint main
	 * @aspectAdvice    before
	 */
	public function mainBefore($object, $params) {
		$flashMessage = new FlashMessage(
			'If you see this message, the list view is successfully extended with aspectAdvice::before',
			'AutoloaderAspect',
			FlashMessage::OK
		);

		// Add FlashMessage
		$flashMessageService = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessageService');
		/** @var $defaultFlashMessageQueue \TYPO3\CMS\Core\Messaging\FlashMessageQueue */
		$defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
		$defaultFlashMessageQueue->enqueue($flashMessage);
	}

	/**
	 * Called BEFORE the main-method and add a FlashMessage to the page.
	 *
	 * @param object $object class of the joinpoint
	 * @param array  $params arguments of the joinpoint
	 *
	 * @aspectClass \TYPO3\CMS\Recordlist\RecordList
	 * @aspectJoinpoint main
	 * @aspectAdvice    after
	 */
	public function mainAfter($object, $params) {
		$flashMessage = new FlashMessage(
			'If you see this message, the list view is successfully extended with aspectAdvice::after',
			'Aspect',
			FlashMessage::OK
		);

		// Add FlashMessage
		$flashMessageService = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessageService');
		/** @var $defaultFlashMessageQueue \TYPO3\CMS\Core\Messaging\FlashMessageQueue */
		$defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
		$defaultFlashMessageQueue->enqueue($flashMessage);
	}

}