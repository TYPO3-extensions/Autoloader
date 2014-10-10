<?php
/**
 * General ext_localconf file and also an example for your own extension
 *
 * @category   Extension
 * @package    AutoloaderPlugin
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\HDNET\Autoloader\Loader::extLocalconf('HDNET', 'autoloader_plugin', array('Plugins'));