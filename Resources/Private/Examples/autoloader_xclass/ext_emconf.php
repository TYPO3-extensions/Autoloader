<?php
/**
 * $EM_CONF
 *
 * @category   Extension
 * @package    AutoloaderXclass
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = array(
	'title'       => 'Autoloader (Xclass - Check the default list view)',
	'description' => '',
	'constraints' => array(
		'depends' => array(
			'autoloader' => '1.4.0-9.9.9',
		),
	),
);