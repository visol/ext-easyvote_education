<?php
namespace Visol\EasyvoteEducation\Domain\Model;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * While this class is called "Voting", it is a generic class for every step inside a Panel presentation.
 * This is because the requirements changed during the project and we didn't want to refactor anything.
 */
class Voting extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	const TYPE_VOTING_YESNOABSTENTION = 1;
	const TYPE_VOTING_TEXT = 2; // unused
	const TYPE_VOTING_TEXTANDIMAGES = 3;
	const TYPE_VOTING_EMPTY = 4; // unused

	const TYPE_VIDEO = 10;
	const TYPE_TEXT = 11;

	/**
	 * @var int
	 */
	protected $type = 0;

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
	 * Video URL
	 *
	 * @var string
	 */
	protected $video = '';

	/**
	 * @var string
	 * @transient
	 */
	protected $videoUrl;

	/**
	 * Text content
	 *
	 * @var string
	 */
	protected $content = '';

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
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param int $type
	 */
	public function setType($type) {
		$this->type = $type;
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
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param string $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function getVideo() {
		return $this->video;
	}

	/**
	 * @param string $video
	 */
	public function setVideo($video) {
		$this->video = $video;
	}

	public function getVideoUrl() {
		/** @var \TYPO3\CMS\Frontend\MediaWizard\MediaWizardProvider $mediaWizardProvider */
		$mediaWizardProvider = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\MediaWizard\MediaWizardProvider');
		if ($mediaWizardProvider->canHandle($this->video)) {
			return $mediaWizardProvider->rewriteUrl($this->video);
		}
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