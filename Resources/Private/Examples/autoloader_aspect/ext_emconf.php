<?php
/**
 * $EM_CONF
 *
 * @category   Extension
 * @package    AutoloaderAspect
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = array(
	'title'       => 'Autoloader (Aspect - Check the default list view)',
	'description' => '',
	'constraints' => array(
		'depends' => array(
			'autoloader' => '1.3.0-9.9.9',
		),
	),
);