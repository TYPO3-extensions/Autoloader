<?php
/**
 * $EM_CONF
 *
 * @category   Extension
 * @package    AutoloaderHooks
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = array(
	'title'       => 'Autoloader (Hooks - You see a additional message in the recordList view in the footer)',
	'description' => '',
	'constraints' => array(
		'depends' => array(
			'autoloader' => '1.1.0-9.9.9',
		),
	),
);