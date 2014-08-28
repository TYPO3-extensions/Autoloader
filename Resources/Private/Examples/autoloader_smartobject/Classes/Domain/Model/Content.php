<?php
/**
 * Content Model
 *
 * Example for an existing object
 *
 * @category   Extension
 * @package    AutoloaderSmartobject
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\AutoloaderSmartobject\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Content Model
 *
 * Example for an existing object
 *
 * @package    AutoloaderSmartobject
 * @subpackage Domain\Model
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 * @db         tt_content
 */
class Content extends AbstractEntity {

	/**
	 * Bodytext
	 *
	 * @var string
	 */
	protected $bodytext;

	/**
	 * Other field
	 *
	 * @var string
	 * @db
	 */
	protected $otherField;

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

	/**
	 * Set other field
	 *
	 * @param string $otherField
	 */
	public function setOtherField($otherField) {
		$this->otherField = $otherField;
	}

	/**
	 * Get other field
	 *
	 * @return string
	 */
	public function getOtherField() {
		return $this->otherField;
	}

} 