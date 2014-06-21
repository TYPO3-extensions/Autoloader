<?php
/**
 * Example Teaser Model
 *
 * @category   Extension
 * @package    AutoloaderContentobject
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\AutoloaderContentobject\Domain\Model\Content;

/**
 * Example Teaser Model
 *
 * @package    AutoloaderContentobject
 * @subpackage Domain\Model\Content
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 * @db         tt_content
 */
class Teaser extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * Bodytext (already exists, so no db annotation)
	 *
	 * @var string
	 */
	protected $bodytext;

	/**
	 * Set bodytext
	 *
	 * @param string $bodytext
	 */
	public function setBodytext($bodytext) {
		$this->bodytext = $bodytext;
	}

	/**
	 * Get bodytext
	 *
	 * @return string
	 */
	public function getBodytext() {
		return $this->bodytext;
	}

} 