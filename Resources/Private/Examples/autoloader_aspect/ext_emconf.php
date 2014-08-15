<?php
/**
 * $EM_CONF
 *
 * @category   Extension
 * @package    AutoloaderAspect
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */


$EM_CONF[$_EXTKEY] = array(
	'title'              => 'Autoloader (Aspect - Check the default list view)',
	'description'        => '',
	'category'           => 'misc',
	'shy'                => 0,
	'version'            => '0.0.0',
	'dependencies'       => '',
	'conflicts'          => '',
	'loadOrder'          => '',
	'module'             => '',
	'priority'           => '',
	'state'              => 'alpha',
	'uploadfolder'       => 0,
	'createDirs'         => '',
	'modify_tables'      => '',
	'clearcacheonload'   => 0,
	'lockType'           => '',
	'author'             => 'Carsten Biebricher',
	'author_email'       => 'carsten.biebricher@hdnet.de',
	'author_company'     => 'hdnet.de',
	'CGLcompliance'      => '',
	'CGLcompliance_note' => '',
	'constraints'        => array(
		'depends'   => array(
			'typo3'      => '6.2.0-0.0.0',
			'autoloader' => '1.1.0-0.0.0',
		),
		'conflicts' => array(),
		'suggests'  => array(),
	),
	'suggests'           => array(),
);