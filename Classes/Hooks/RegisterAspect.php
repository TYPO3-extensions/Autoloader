<?php
/**
 * Register the aspect files and create the Xclass.
 *
 * @category   Extension
 * @package    Autoloader
 * @author     Carsten Biebricher <carsten.biebricher@hdnet.de>
 */

namespace HDNET\Autoloader\Hooks;

use HDNET\Autoloader\Utility\ExtendedUtility;
use TYPO3\CMS\Core\Database\TableConfigurationPostProcessingHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Register the aspect files and create the needed Xclasses.
 *
 * @package    Autoloader
 * @subpackage Hooks
 * @author     Carsten Biebricher <carsten.biebricher@hdnet.de>
 * @hook       TYPO3_CONF_VARS|SC_OPTIONS|GLOBAL|extTablesInclusion-PostProcessing
 */
class RegisterAspect implements TableConfigurationPostProcessingHookInterface {

	/**
	 * The xclass template content
	 *
	 * @var string
	 */
	static protected $xclassTemplate = FALSE;

	/**
	 * Function which may process data created / registered by extTables
	 * scripts (f.e. modifying TCA data of all extensions)
	 *
	 * @return void
	 */
	public function processData() {
		$aspectCollection = $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Aspect'];

		$xClasses = $this->prepareConfiguration($aspectCollection);

		// Cache the template file
		if (!self::$xclassTemplate) {
			$xclassTemplatePath = GeneralUtility::getFileAbsFileName(
				ExtensionManagementUtility::extPath('autoloader') . 'Resources/Private/Php/Templates/Xclass/Aspect.tmpl'
			);
			self::$xclassTemplate = GeneralUtility::getUrl($xclassTemplatePath);
		}

		foreach ($xClasses as $xClassName => $xClass) {
			// Register the Xclass in TYPO3
			$this->registerXclass($xClassName);

			// get the Cache Identifier
			$cacheIdentifier = $this->getCacheIdentifier($xClassName);

			/** @var $cache \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend */
			$cache = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->getCache('autoloader');
			if (!$cache->has($cacheIdentifier)) {
				$code = $this->generateXclassCode($xClassName, $xClass, self::$xclassTemplate);
				// !! ;) !! in the xclass-template ist a <?php string for better development
				$code = str_replace('<?php', '', $code);
				$cache->set($cacheIdentifier, $code);
			}
		}
	}

	/**
	 * Generate the Xclass code on base of the xclass-template.
	 *
	 * @param string $xClassName
	 * @param array $xClass
	 * @param string $xclassTemplate
	 *
	 * @return string full xclass-code
	 */
	protected function generateXclassCode($xClassName, $xClass, $xclassTemplate) {
		$shortName = $this->getXclassShortname($xClassName);

		$xclassTemplate = str_replace('__classname__', $shortName, $xclassTemplate);
		$xclassTemplate = str_replace('__extendedClass__', '\\' . $xClassName, $xclassTemplate);

		$beforeConfiguration = array();
		$replaceConfiguration = array();
		$afterConfiguration = array();
		$throwConfiguration = array();
		$joinpointMethods = array();

		foreach ($xClass as $joinpoint => $advices) {
			$beforeConfiguration[$joinpoint] = $this->getConfigurationArray('before', $joinpoint, $advices);
			$replaceConfiguration[$joinpoint] = $this->getConfigurationArray('replace', $joinpoint, $advices);
			$afterConfiguration[$joinpoint] = $this->getConfigurationArray('after', $joinpoint, $advices);
			$throwConfiguration[$joinpoint] = $this->getConfigurationArray('throw', $joinpoint, $advices);

			$joinpointMethods[$joinpoint] = $this->getJoinPointMethod($joinpoint, $xClass);
		}

		$xclassTemplate = str_replace('__beforeAspectsConfiguration__', $this->mergeConfigurationArrayForCode($beforeConfiguration), $xclassTemplate);
		$xclassTemplate = str_replace('__replaceAspectsConfiguration__', $this->mergeConfigurationArrayForCode($replaceConfiguration), $xclassTemplate);
		$xclassTemplate = str_replace('__afterAspectsConfiguration__', $this->mergeConfigurationArrayForCode($afterConfiguration), $xclassTemplate);
		$xclassTemplate = str_replace('__throwAspectsConfiguration__', $this->mergeConfigurationArrayForCode($throwConfiguration), $xclassTemplate);

		$xclassTemplate = str_replace('__joinpointMethods__', implode("\n", $joinpointMethods), $xclassTemplate);

		return $xclassTemplate;
	}

	/**
	 * Return the Joinpoint method.
	 *
	 * @param string $joinpoint
	 * @param array $xClass
	 *
	 * @return string
	 */
	protected function getJoinPointMethod($joinpoint, $xClass) {
		$config = $xClass[$joinpoint];
		$code = array();

		$code[] = 'public function ' . $joinpoint . '(';

		// arguments
		if (is_array($config['arguments']) && sizeof($config['arguments']) > 0) {
			$args = array();
			foreach ($config['arguments'] as $arguments) {
				$type = '';
				if ($arguments['typeHint'] !== NULL || $arguments['typeHint'] !== '') {
					$type = $arguments['typeHint'] . ' ';
				}
				$args[] = $type . '$' . $arguments['name'];
			}

			$code[] = implode(',', $args);
		}

		$code[] = ') {';

		$code[] = '$args = func_get_args();';
		$code[] = 'return $this->aspectLogic(\'' . $joinpoint . '\', $args);';

		$code[] = '}';

		return implode("\n", $code);
	}

	/**
	 * Creates code for a configuration array like:
	 * array(
	 *  {joinpoint} => array(
	 *          {method1}, {method2}, {method3}
	 *      )
	 * )
	 * @param string $type before, throw, after, replace
	 * @param string $joinpoint
	 * @param array $advices
	 *
	 * @return string
	 */
	protected function getConfigurationArray($type, $joinpoint, $advices) {
		$code = array();

		$code[] = '\'' . $joinpoint . '\'' . ' => array(';
		foreach ($advices[$type] as $method) {
			$code[] = 'array(';
			$code[] = '\'id\' => \'' . GeneralUtility::shortMD5($method['originClassName'] . $method['originMethodName'], 13) . '\',';
			$code[] = '\'class\' => \'\\' . $method['originClassName'] . '\',';
			$code[] = '\'function\' => \'' . $method['originMethodName'] . '\',';
			$code[] = '),';
		}
		$code[] = '),';

		return implode(LF, $code);
	}

	/**
	 * Merge the configuration array for code
	 *
	 * @param array $configuration
	 *
	 * @return string
	 */
	protected function mergeConfigurationArrayForCode($configuration) {
		$code[] = 'array(';
		$code[] = implode("\n", $configuration);
		$code[] = ')';

		return implode(LF, $code);
	}

	/**
	 * Register the Xclass in the TYPO3_CONF_VARS.
	 *
	 * @param string $xClassName
	 */
	protected function registerXclass($xClassName) {
		// Register the Xclass in TYPO3
		$shortName = $this->getXclassShortname($xClassName);
		$loaderClassName = 'HDNET\\Autoloader\\Xclass\\' . $shortName;
		ExtendedUtility::addXclass($xClassName, $loaderClassName);
	}

	/**
	 * Return from the full namespace the classname.
	 * @param string $xClassName
	 *
	 * @return string
	 */
	protected function getXclassShortname($xClassName) {
		$classNameArray = explode('\\', $xClassName);
		$shortName = array_pop($classNameArray);

		return $shortName;
	}

	/**
	 * Return the Cache identifier.
	 *
	 * @param string $xClassName
	 *
	 * @return string
	 */
	protected function getCacheIdentifier($xClassName) {
		$shortName = $this->getXclassShortname($xClassName);
		$cacheIdentifier = 'XCLASS' . '_' . str_replace('\\', '', 'HDNET\\Autoloader\\Xclass\\' . $shortName);

		return $cacheIdentifier;
	}

	/**
	 * Prepare the Configuration.
	 *
	 * @param array $aspectCollection
	 *
	 * @return array
	 */
	protected function prepareConfiguration($aspectCollection) {
		$xClasses = array();
		foreach ($aspectCollection as $aspects) {
			foreach ($aspects as $aspect) {
				if (!array_key_exists($aspect['aspectClassName'], $xClasses)) {
					$xClasses[$aspect['aspectClassName']] = array();
				}

				if (!array_key_exists($aspect['aspectJoinpoint'], $xClasses[$aspect['aspectClassName']])) {
					$xClasses[$aspect['aspectClassName']][$aspect['aspectJoinpoint']] = array();
					$xClasses[$aspect['aspectClassName']][$aspect['aspectJoinpoint']]['arguments'] = $aspect['aspectJoinpointArguments'];
				}

				if (!array_key_exists($aspect['aspectAdvice'], $xClasses[$aspect['aspectClassName']][$aspect['aspectJoinpoint']])) {
					$xClasses[$aspect['aspectClassName']][$aspect['aspectJoinpoint']][$aspect['aspectAdvice']] = array();
				}

				$xClasses[$aspect['aspectClassName']][$aspect['aspectJoinpoint']][$aspect['aspectAdvice']][] = array(
					'originClassName' => $aspect['originClassName'],
					'originMethodName' => $aspect['originMethodName']
				);
			}
		}

		return $xClasses;
	}
}