<?php
/**
 * $EM_CONF
 *
 * @category   Extension
 * @package    Autoloader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = array(
	'title'              => 'Autoloader',
	'description'        => 'Automatic components loading of ExtBase extensions to get more time for coffee in the company ;) This ext is not a PHP SPL autoloader or class loader - it is better! Loads CommandController, Xclass, Hooks, Aspects, FlexForms, Slots...',
	'version'            => '1.3.1',
	'state'              => 'beta',
	'clearcacheonload'   => 1,
	'author'             => 'Tim Lochmüller',
	'author_email'       => 'tl@hdnet.de',
	'author_company'     => 'hdnet.de',
	'constraints'        => array(
		'depends'   => array(
			'typo3' => '6.2.0-6.2.99',
		),
	),
);
