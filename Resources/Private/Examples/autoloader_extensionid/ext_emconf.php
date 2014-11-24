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
	'title'       => 'Autoloader (eID - run http://your-domain.de/?eID=Test to get a Hello World)',
	'description' => '',
	'constraints' => array(
		'depends' => array(
			'autoloader' => '1.4.3-9.9.9',
		),
	),
);