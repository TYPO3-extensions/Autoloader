<?php
/**
 * General ext_localconf file and also an example for your own extension
 *
 * @category   Extension
 * @package    AutoloaderHooks
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\HDNET\Autoloader\Loader::extTables('HDNET', 'autoloader_csh', array(
	'SmartObjects',
	'TcaFiles',
	'ContextSensitiveHelps'
));