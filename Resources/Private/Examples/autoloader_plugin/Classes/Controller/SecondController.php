<?php
/**
 * Second controller
 *
 * @package    AutoloaderPlugin
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

namespace HDNET\AutoloaderPlugin\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Second controller
 *
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class SecondController  extends ActionController{

	/**
	 * @plugin Second
	 */
	public function secondAction(){

	}

	/**
	 * @plugin Second
	 * @noCache
	 */
	public function aNoCacheAction(){

	}
}