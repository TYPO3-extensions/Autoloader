<?php
/**
 * $EM_CONF
 *
 * @category   Extension
 * @package    AutoloaderSmart
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = array(
	'title'       => 'Autoloader (Smart object - You should create test records of an smart object)',
	'description' => '',
	'constraints' => array(
		'depends' => array(
			'autoloader' => '1.4.2-9.9.9',
		),
	),
);