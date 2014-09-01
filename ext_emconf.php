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
	'description'        => 'Automatic components loading of ExtBase extensions to get more time for coffee in the company ;) Loads CommandController, Xclass, Hooks, Aspects, FlexForms, Slots...',
	'version'            => '1.2.4',
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
