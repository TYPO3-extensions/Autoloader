<?php
/**
 * Dummy Type Converter
 *
 * @category   Extension
 * @package    AutoloaderTypeconverter
 * @subpackage Property\TypeConverter
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\AutoloaderTypeconverter\Property\TypeConverter;

use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;

/**
 * Dummy Type Converter
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
		'dummy',
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
 