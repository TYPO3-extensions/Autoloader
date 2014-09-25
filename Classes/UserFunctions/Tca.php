<?php
/**
 * Tca UserFunctions
 *
 * @category   Extension
 * @package    Autoloader\UserFunctions
 * @author     Carsten Biebricher <carsten.biebricher@hdnet.de>
 */

namespace HDNET\Autoloader\UserFunctions;

/**
 * Tca UserFunctions
 *
 * @author     Carsten Biebricher <carsten.biebricher@hdnet.de>
 */
class Tca {

	/**
	 * Generate the help message for object storage fields
	 *
	 * @param array $configuration
	 * @param \TYPO3\CMS\Backend\Form\FormEngine $formEngine
	 *
	 * @return string
	 */
	public function objectStorageInfoField($configuration, $formEngine) {
		$infoField = '<div style="padding: 5px; border: 2px solid red;">';
		$infoField .= '<strong>Please configure your TCA for this field.</strong><br/>';
		$infoField .= 'You see this message because you have NOT configured the TCA.';
		$infoField .= '<ul><li>table: <em>' . $configuration['table'] . '</em></li>';
		$infoField .= '<li>field: <em>' . $configuration['field'] . '</em></li>';
		$infoField .= '<li>config-file';
		$infoField .= '<ul><li>own table: <em>Configuration/TCA/' . $configuration['table'] . '.php</em></li>';
		$infoField .= '<li>foreign table: <em>Configuration/TCA/Overrides/' . $configuration['table'] . '.php</em></li></ul>';
		$infoField .= '</li></ul>';
		$infoField .= 'Common foreign tables are <em>tt_content</em>, <em>tt_address</em>, &hellip;.<br/><br/>';
		$infoField .= 'Information about proper TCA configuration as ';
		$infoField .= '<a href="http://docs.typo3.org/typo3cms/TCAReference/Reference/Columns/Group/Index.html" target="_blank">group</a>, ';
		$infoField .= '<a href="http://docs.typo3.org/typo3cms/TCAReference/Reference/Columns/Inline/Index.html" target="_blank">inline</a> or ';
		$infoField .= '<a href="http://docs.typo3.org/typo3cms/TCAReference/Reference/Columns/Select/Index.html" target="_blank">select</a>';
		$infoField .= '-type can be found in the TCA-documentation.<br/>';
		$infoField .= '</div>';

		return $infoField;
	}
}
