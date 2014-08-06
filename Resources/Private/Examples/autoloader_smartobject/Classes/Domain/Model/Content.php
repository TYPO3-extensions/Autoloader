<?php
/**
 * Content Model
 *
 * Example for an existing object
 *
 * @category   Extension
 * @package    AutoloaderSmart
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\AutoloaderSmartobject\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Content Model
 *
 * Example for an existing object
 *
 * @package    AutoloaderSmart
 * @subpackage Domain\Model
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 * @db         tt_content
 */
class Test extends AbstractEntity {

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
	 * @param string $bodytext
	 */
	public function setBodytext($bodytext) {
		$this->bodytext = $bodytext;
	}

	/**
	 * @return string
	 */
	public function getBodytext() {
		return $this->bodytext;
	}

	/**
	 * @param string $otherField
	 */
	public function setOtherField($otherField) {
		$this->otherField = $otherField;
	}

	/**
	 * @return string
	 */
	public function getOtherField() {
		return $this->otherField;
	}

} 