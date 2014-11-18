<?php
/**
 * $EM_CONF
 *
 * @category   Extension
 * @package    AutoloaderPlugin
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = array(
	'title'       => 'Autoloader (Plugin - There are two plugins in the plugin selection - no output)',
	'description' => '',
	'constraints' => array(
		'depends' => array(
			'autoloader' => '1.4.2-9.9.9',
		),
	),
);