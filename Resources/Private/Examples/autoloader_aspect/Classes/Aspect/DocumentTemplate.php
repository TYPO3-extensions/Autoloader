<?php
/**
 * This Example test the aspects before & after.
 *
 * @category   Extension
 * @package    AutoloaderAspect
 * @author     Carsten Biebricher <carsten.biebricher@hdnet.de>
 */

namespace HDNET\AutoloaderAspect\Aspect;

/**
 * This Example test the aspects before & after.
 *
 * @package    AutoloaderAspect
 * @subpackage Aspect
 * @author     Carsten Biebricher <carsten.biebricher@hdnet.de>
 */
class DocumentTemplate {

	/**
	 * Change the method-parameter BEFORE the joinpoint (original method) is called.
	 *
	 * @param object $object class of the joinpoint
	 * @param array  $params arguments of the joinpoint
	 *
	 * @return array
	 *
	 * @aspectClass \TYPO3\CMS\Backend\Template\DocumentTemplate
	 * @aspectJoinpoint header
	 * @aspectAdvice    before
	 */
	public function headerBefore($object, $params) {
		$params['args'][0] .= ' ASPECT (before)';
		return $params;
	}

	/**
	 * Change the method-parameter BEFORE the joinpoint (original method) is called.
	 *
	 * @param object $object class of the joinpoint
	 * @param array  $params arguments of the joinpoint
	 *
	 * @return array
	 *
	 * @aspectClass \TYPO3\CMS\Backend\Template\DocumentTemplate
	 * @aspectJoinpoint header
	 * @aspectAdvice    before
	 */
	public function headerBefore2($object, $params) {
		$params['args'][0] .= ' -2-';
		return $params;
	}

	/**
	 * Change the method-result After the joinpoint (original method) is called.
	 *
	 * @param object $object class of the joinpoint
	 * @param array  $params arguments of the joinpoint
	 *
	 * @return array
	 *
	 * @aspectClass \TYPO3\CMS\Backend\Template\DocumentTemplate
	 * @aspectJoinpoint header
	 * @aspectAdvice    after
	 */
	public function headerAfter($object, $params) {
		$params['result'] .= ' <h2>ASPECT (after)</h2>';
		return $params;
	}
}