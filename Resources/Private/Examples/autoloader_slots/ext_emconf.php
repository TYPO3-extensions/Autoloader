<?php
/**
 * $EM_CONF
 *
 * @category   Extension
 * @package    AutoloaderSlots
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */


$EM_CONF[$_EXTKEY] = array(
	'title'              => 'Autoloader (Slots - Check the TYPO3 Login screen)',
	'description'        => '',
	'category'           => 'misc',
	'shy'                => 0,
	'version'            => '0.0.0',
	'dependencies'       => '',
	'conflicts'          => '',
	'loadOrder'          => '',
	'module'             => '',
	'priority'           => '',
	'state'              => 'stable',
	'uploadfolder'       => 0,
	'createDirs'         => '',
	'modify_tables'      => '',
	'clearcacheonload'   => 0,
	'lockType'           => '',
	'author'             => 'Tim Lochmüller',
	'author_email'       => 'tl@hdnet.de',
	'author_company'     => 'hdnet.de',
	'CGLcompliance'      => '',
	'CGLcompliance_note' => '',
	'constraints'        => array(
		'depends'   => array(
			'typo3'      => '6.2.0-0.0.0',
			'autoloader' => '1.0.0-0.0.0',
		),
		'conflicts' => array(),
		'suggests'  => array(),
	),
	'suggests'           => array(),
);

?>