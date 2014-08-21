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

use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
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
	 * Dummy source
	 *
	 * @var array<string>
	 */
	protected $sourceTypes = array(
		'dummy',
	);

	/**
	 * Dummy target
	 *
	 * @var string
	 */
	protected $targetType = 'NULL';

	/**
	 * Dummy function that do nothing
	 *
	 * @param mixed                                 $source
	 * @param string                                $targetType
	 * @param array                                 $childProperties
	 * @param PropertyMappingConfigurationInterface $configuration
	 *
	 * @return mixed|null|\TYPO3\CMS\Extbase\Error\Error
	 */
	public function convertFrom($source, $targetType, array $childProperties = array(), PropertyMappingConfigurationInterface $configuration = NULL) {
		return NULL;
	}
}
 