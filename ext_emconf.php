<?php
/**
 * $EM_CONF
 *
 * @category   Extension
 * @package    Autoloader
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */


$EM_CONF[$_EXTKEY] = array(
	'title'              => 'Autoloader',
	'description'        => 'Automatic components loading of ExtBase extensions to get more time for coffee in the company ;)',
	'category'           => 'misc',
	'shy'                => 0,
	'version'            => '1.1.0',
	'dependencies'       => '',
	'conflicts'          => '',
	'loadOrder'          => '',
	'module'             => '',
	'priority'           => '',
	'state'              => 'beta',
	'uploadfolder'       => 0,
	'createDirs'         => '',
	'modify_tables'      => '',
	'clearcacheonload'   => 1,
	'lockType'           => '',
	'author'             => 'Tim Lochmüller',
	'author_email'       => 'tl@hdnet.de',
	'author_company'     => 'hdnet.de',
	'CGLcompliance'      => '',
	'CGLcompliance_note' => '',
	'constraints'        => array(
		'depends'   => array(
			'typo3' => '6.2.0-6.2.99',
		),
		'conflicts' => array(),
		'suggests'  => array(),
	),
	'suggests'           => array(),
);