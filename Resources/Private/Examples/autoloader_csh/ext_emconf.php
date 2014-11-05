<?php
/**
 * $EM_CONF
 *
 * @category   Extension
 * @package    AutoloaderCsh
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = array(
	'title'       => 'Autoloader (CSH - create context sensitive help files for the smart objects)',
	'description' => '',
	'constraints' => array(
		'depends' => array(
			'autoloader' => '1.4.0-9.9.9',
		),
	),
);