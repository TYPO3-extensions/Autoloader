<?php
/**
 * $EM_CONF
 *
 * @category   Extension
 * @package    AutoloaderContentobject
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = array(
	'title'       => 'Autoloader (Contentobject - You should create a Teaser Content Element)',
	'description' => '',
	'constraints' => array(
		'depends' => array(
			'autoloader' => '1.5.1-9.9.9',
		),
	),
);