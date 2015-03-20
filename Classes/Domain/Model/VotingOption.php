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
 * Voting option
 */
class VotingOption extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * Title
	 *
	 * @var string
	 * @validate NotEmpty
	 * @copy clone
	 */
	protected $title = '';

	/**
	 * Style
	 *
	 * @var integer
	 * @copy clone
	 */
	protected $style = 0;

	/**
	 * Cached votes
	 *
	 * @var integer
	 * @copy ignore
	 */
	protected $cachedVotes = 0;

	/**
	 * Cached rank
	 *
	 * @var integer
	 * @copy ignore
	 */
	protected $cachedRank = 0;

	/**
	 * Image
	 *
	 * @var \Visol\Easyvote\Domain\Model\FileReference
	 * @copy reference
	 */
	protected $image = NULL;

	/**
	 * Votes
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Visol\EasyvoteEducation\Domain\Model\Vote>
	 * @cascade remove
	 * @lazy
	 * @copy ignore
	 */
	protected $votes = NULL;

	/**
	 * Voting (parent)
	 *
	 * @var \Visol\EasyvoteEducation\Domain\Model\Voting
	 */
	protected $voting = NULL;

	/**
	 * @var integer
	 */
	protected $sorting = 9999;

	/**
	 * @var integer
	 */
	protected $cachedVotingResult = 0;

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
		$this->votes = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
	 * Returns the style
	 *
	 * @return integer $style
	 */
	public function getStyle() {
		return $this->style;
	}

	/**
	 * Sets the style
	 *
	 * @param integer $style
	 * @return void
	 */
	public function setStyle($style) {
		$this->style = $style;
	}

	/**
	 * Returns the cachedVotes
	 *
	 * @return integer $cachedVotes
	 */
	public function getCachedVotes() {
		return $this->cachedVotes;
	}

	/**
	 * Sets the cachedVotes
	 *
	 * @param integer $cachedVotes
	 * @return void
	 */
	public function setCachedVotes($cachedVotes) {
		$this->cachedVotes = $cachedVotes;
	}

	/**
	 * Returns the cachedRank
	 *
	 * @return integer $cachedRank
	 */
	public function getCachedRank() {
		return $this->cachedRank;
	}

	/**
	 * Sets the cachedRank
	 *
	 * @param integer $cachedRank
	 * @return void
	 */
	public function setCachedRank($cachedRank) {
		$this->cachedRank = $cachedRank;
	}

	/**
	 * Returns the image
	 *
	 * @return \Visol\Easyvote\Domain\Model\FileReference $image
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * Sets the image
	 *
	 * @param \Visol\Easyvote\Domain\Model\FileReference $image
	 * @return void
	 */
	public function setImage(\Visol\Easyvote\Domain\Model\FileReference $image) {
		$this->image = $image;
	}

	/**
	 * Adds a Vote
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Vote $vote
	 * @return void
	 */
	public function addVote(\Visol\EasyvoteEducation\Domain\Model\Vote $vote) {
		$this->votes->attach($vote);
	}

	/**
	 * Removes a Vote
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Vote $voteToRemove The Vote to be removed
	 * @return void
	 */
	public function removeVote(\Visol\EasyvoteEducation\Domain\Model\Vote $voteToRemove) {
		$this->votes->detach($voteToRemove);
	}

	/**
	 * Returns the votes
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Visol\EasyvoteEducation\Domain\Model\Vote> $votes
	 */
	public function getVotes() {
		return $this->votes;
	}

	/**
	 * Sets the votes
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Visol\EasyvoteEducation\Domain\Model\Vote> $votes
	 * @return void
	 */
	public function setVotes(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $votes) {
		$this->votes = $votes;
	}

	/**
	 * @return Voting
	 */
	public function getVoting() {
		return $this->voting;
	}

	/**
	 * @param Voting $voting
	 */
	public function setVoting($voting) {
		$this->voting = $voting;
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

	/**
	 * @return int
	 */
	public function getCachedVotingResult() {
		return $this->cachedVotingResult;
	}

	/**
	 * @param int $cachedVotingResult
	 */
	public function setCachedVotingResult($cachedVotingResult) {
		$this->cachedVotingResult = $cachedVotingResult;
	}

}