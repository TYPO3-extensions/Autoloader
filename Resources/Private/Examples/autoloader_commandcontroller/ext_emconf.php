<?php
/**
 * $EM_CONF
 *
 * @category   Extension
 * @package    AutoloaderCommandcontroller
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = array(
	'title'       => 'Autoloader (CommandController - Check Scheduler Command Controller List)',
	'description' => '',
	'constraints' => array(
		'depends' => array(
			'autoloader' => '1.5.1-9.9.9',
		),
	),
);