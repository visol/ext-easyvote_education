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
 * Panel
 */
class Panel extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * Title
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $title = '';

	/**
	 * Description
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Date/Time
	 *
	 * @var \DateTime
	 */
	protected $date = NULL;

	/**
	 * Room
	 *
	 * @var string
	 */
	protected $room = '';

	/**
	 * Address
	 *
	 * @var string
	 */
	protected $address = '';

	/**
	 * Organization
	 *
	 * @var string
	 */
	protected $organization = '';

	/**
	 * Class
	 *
	 * @var string
	 */
	protected $class = '';

	/**
	 * Number of participants (approx.)
	 *
	 * @var string
	 */
	protected $numberOfParticipants = '';

	/**
	 * Terms accepted
	 *
	 * @var boolean
	 * @validate NotEmpty
	 */
	protected $termsAccepted = FALSE;

	/**
	 * City
	 *
	 * @var \Visol\Easyvote\Domain\Model\City
	 */
	protected $city = NULL;

	/**
	 * Image
	 *
	 * @var \Visol\Easyvote\Domain\Model\FileReference
	 */
	protected $image = NULL;

	/**
	 * Creator
	 *
	 * @var \Visol\Easyvote\Domain\Model\CommunityUser
	 */
	protected $creator = NULL;

	/**
	 * Votings
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Visol\EasyvoteEducation\Domain\Model\Voting>
	 * @cascade remove
	 */
	protected $votings = NULL;

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
		$this->votings = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
	 * Returns the description
	 *
	 * @return string $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Returns the date
	 *
	 * @return \DateTime $date
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * Sets the date
	 *
	 * @param \DateTime $date
	 * @return void
	 */
	public function setDate(\DateTime $date) {
		$this->date = $date;
	}

	/**
	 * Returns the room
	 *
	 * @return string $room
	 */
	public function getRoom() {
		return $this->room;
	}

	/**
	 * Sets the room
	 *
	 * @param string $room
	 * @return void
	 */
	public function setRoom($room) {
		$this->room = $room;
	}

	/**
	 * Returns the address
	 *
	 * @return string $address
	 */
	public function getAddress() {
		return $this->address;
	}

	/**
	 * Sets the address
	 *
	 * @param string $address
	 * @return void
	 */
	public function setAddress($address) {
		$this->address = $address;
	}

	/**
	 * Returns the organization
	 *
	 * @return string $organization
	 */
	public function getOrganization() {
		return $this->organization;
	}

	/**
	 * Sets the organization
	 *
	 * @param string $organization
	 * @return void
	 */
	public function setOrganization($organization) {
		$this->organization = $organization;
	}

	/**
	 * Returns the class
	 *
	 * @return string $class
	 */
	public function getClass() {
		return $this->class;
	}

	/**
	 * Sets the class
	 *
	 * @param string $class
	 * @return void
	 */
	public function setClass($class) {
		$this->class = $class;
	}

	/**
	 * Returns the numberOfParticipants
	 *
	 * @return string $numberOfParticipants
	 */
	public function getNumberOfParticipants() {
		return $this->numberOfParticipants;
	}

	/**
	 * Sets the numberOfParticipants
	 *
	 * @param string $numberOfParticipants
	 * @return void
	 */
	public function setNumberOfParticipants($numberOfParticipants) {
		$this->numberOfParticipants = $numberOfParticipants;
	}

	/**
	 * Returns the termsAccepted
	 *
	 * @return boolean $termsAccepted
	 */
	public function getTermsAccepted() {
		return $this->termsAccepted;
	}

	/**
	 * Sets the termsAccepted
	 *
	 * @param boolean $termsAccepted
	 * @return void
	 */
	public function setTermsAccepted($termsAccepted) {
		$this->termsAccepted = $termsAccepted;
	}

	/**
	 * Returns the boolean state of termsAccepted
	 *
	 * @return boolean
	 */
	public function isTermsAccepted() {
		return $this->termsAccepted;
	}

	/**
	 * Returns the city
	 *
	 * @return \Visol\Easyvote\Domain\Model\City $city
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * Sets the city
	 *
	 * @param \Visol\Easyvote\Domain\Model\City $city
	 * @return void
	 */
	public function setCity(\Visol\Easyvote\Domain\Model\City $city) {
		$this->city = $city;
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
	 * Returns the creator
	 *
	 * @return \Visol\Easyvote\Domain\Model\CommunityUser $creator
	 */
	public function getCreator() {
		return $this->creator;
	}

	/**
	 * Sets the creator
	 *
	 * @param \Visol\Easyvote\Domain\Model\CommunityUser $creator
	 * @return void
	 */
	public function setCreator(\Visol\Easyvote\Domain\Model\CommunityUser $creator) {
		$this->creator = $creator;
	}

	/**
	 * Adds a Voting
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Voting $voting
	 * @return void
	 */
	public function addVoting(\Visol\EasyvoteEducation\Domain\Model\Voting $voting) {
		$this->votings->attach($voting);
	}

	/**
	 * Removes a Voting
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Voting $votingToRemove The Voting to be removed
	 * @return void
	 */
	public function removeVoting(\Visol\EasyvoteEducation\Domain\Model\Voting $votingToRemove) {
		$this->votings->detach($votingToRemove);
	}

	/**
	 * Returns the votings
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Visol\EasyvoteEducation\Domain\Model\Voting> $votings
	 */
	public function getVotings() {
		return $this->votings;
	}

	/**
	 * Sets the votings
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Visol\EasyvoteEducation\Domain\Model\Voting> $votings
	 * @return void
	 */
	public function setVotings(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $votings) {
		$this->votings = $votings;
	}

}