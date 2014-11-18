<?php
/**
 * $EM_CONF
 *
 * @category   Extension
 * @package    AutoloaderSlots
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = array(
	'title'       => 'Autoloader (Slots - Check the TYPO3 Login screen)',
	'description' => '',
	'constraints' => array(
		'depends' => array(
			'autoloader' => '1.4.2-9.9.9',
		),
	),
);