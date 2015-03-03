<?php
namespace Visol\EasyvoteEducation\Domain\Model;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Lorenz Ulrich <lorenz.ulrich@visol.ch>, visol digitale Dienstleistungen GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Voting
 */
class Voting extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * Title
	 *
	 * @var string
	 * @validate NotEmpty
	 * @copy clone
	 */
	protected $title = '';

	/**
	 * Short title
	 *
	 * @var string
	 * @copy clone
	 */
	protected $short = '';

	/**
	 * Is visible
	 *
	 * @var boolean
	 * @copy clone
	 */
	protected $isVisible = FALSE;

	/**
	 * Is voting enabled?
	 *
	 * @var boolean
	 * @copy clone
	 */
	protected $isVotingEnabled = FALSE;

	/**
	 * Voting duration
	 *
	 * @var integer
	 * @copy clone
	 */
	protected $votingDuration = 0;

	/**
	 * Voting Options
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Visol\EasyvoteEducation\Domain\Model\VotingOption>
	 * @cascade remove
	 * @lazy
	 * @copy clone
	 */
	protected $votingOptions = NULL;

	/**
	 * Panel (parent)
	 *
	 * @var \Visol\EasyvoteEducation\Domain\Model\Panel
	 * @copy reference
	 */
	protected $panel = NULL;

	/**
	 * @var integer
	 * @copy clone
	 */
	protected $sorting = 9999;

	/**
	 * __construct
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all ObjectStorage properties
	 * Do not modify this method!
	 * It will be rewritten on each save in the extension builder
	 * You may modify the constructor of this class instead
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		$this->votingOptions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the short
	 *
	 * @return string $short
	 */
	public function getShort() {
		return $this->short;
	}

	/**
	 * Sets the short
	 *
	 * @param string $short
	 * @return void
	 */
	public function setShort($short) {
		$this->short = $short;
	}

	/**
	 * Returns the isVisible
	 *
	 * @return boolean $isVisible
	 */
	public function getIsVisible() {
		return $this->isVisible;
	}

	/**
	 * Sets the isVisible
	 *
	 * @param boolean $isVisible
	 * @return void
	 */
	public function setIsVisible($isVisible) {
		$this->isVisible = $isVisible;
	}

	/**
	 * Returns the boolean state of isVisible
	 *
	 * @return boolean
	 */
	public function isIsVisible() {
		return $this->isVisible;
	}

	/**
	 * Returns the isVotingEnabled
	 *
	 * @return boolean $isVotingEnabled
	 */
	public function getIsVotingEnabled() {
		return $this->isVotingEnabled;
	}

	/**
	 * Sets the isVotingEnabled
	 *
	 * @param boolean $isVotingEnabled
	 * @return void
	 */
	public function setIsVotingEnabled($isVotingEnabled) {
		$this->isVotingEnabled = $isVotingEnabled;
	}

	/**
	 * Returns the boolean state of isVotingEnabled
	 *
	 * @return boolean
	 */
	public function isIsVotingEnabled() {
		return $this->isVotingEnabled;
	}

	/**
	 * Returns the votingDuration
	 *
	 * @return integer $votingDuration
	 */
	public function getVotingDuration() {
		return $this->votingDuration;
	}

	/**
	 * Sets the votingDuration
	 *
	 * @param integer $votingDuration
	 * @return void
	 */
	public function setVotingDuration($votingDuration) {
		$this->votingDuration = $votingDuration;
	}

	/**
	 * Adds a VotingOption
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption
	 * @return void
	 */
	public function addVotingOption(\Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption) {
		$this->votingOptions->attach($votingOption);
	}

	/**
	 * Removes a VotingOption
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOptionToRemove The VotingOption to be removed
	 * @return void
	 */
	public function removeVotingOption(\Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOptionToRemove) {
		$this->votingOptions->detach($votingOptionToRemove);
	}

	/**
	 * Returns the votingOptions
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Visol\EasyvoteEducation\Domain\Model\VotingOption> $votingOptions
	 */
	public function getVotingOptions() {
		return $this->votingOptions;
	}

	/**
	 * Sets the votingOptions
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Visol\EasyvoteEducation\Domain\Model\VotingOption> $votingOptions
	 * @return void
	 */
	public function setVotingOptions(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $votingOptions) {
		$this->votingOptions = $votingOptions;
	}

	/**
	 * @return Panel
	 */
	public function getPanel() {
		return $this->panel;
	}

	/**
	 * @param Panel $panel
	 */
	public function setPanel($panel) {
		$this->panel = $panel;
	}

	/**
	 * @return int
	 */
	public function getSorting() {
		return $this->sorting;
	}

	/**
	 * @param int $sorting
	 */
	public function setSorting($sorting) {
		$this->sorting = $sorting;
	}

}