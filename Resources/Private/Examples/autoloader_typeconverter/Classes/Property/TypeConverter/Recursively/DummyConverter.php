<?php
/**
 * Dummy Type Converter (second level)
 *
 * @category   Extension
 * @package    AutoloaderTypeconverter
 * @subpackage Property\TypeConverter
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\AutoloaderTypeconverter\Property\TypeConverter\Recursively;

use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;

/**
 * Dummy Type Converter (second level)
 *
 * @package    AutoloaderTypeconverter
 * @subpackage Property\TypeConverter
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class DummyConverter extends AbstractTypeConverter {

	/**
	 * @var array<string>
	 */
	protected $sourceTypes = array(
		'dummySecond',
	);

	/**
	 * @var string
	 */
	protected $targetType = 'NULL';

	/**
	 * @param mixed                                                             $source
	 * @param string                                                            $targetType
	 * @param array                                                             $convertedChildProperties
	 * @param \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface $configuration
	 *
	 * @return mixed|null|\TYPO3\CMS\Extbase\Error\Error
	 */
	public function convertFrom($source, $targetType, array $convertedChildProperties = array(), \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface $configuration = NULL) {
		return NULL;
	}
}
 