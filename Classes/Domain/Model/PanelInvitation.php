<?php
namespace Visol\EasyvoteEducation\Domain\Model;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015
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
 * Panel Invitation
 */
class PanelInvitation extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * Allowed Parties
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Visol\Easyvote\Domain\Model\Party>
	 * @lazy
	 */
	protected $allowedParties = NULL;

	/**
	 * Attending Community User
	 *
	 * @var \Visol\Easyvote\Domain\Model\CommunityUser
	 * @lazy
	 */
	protected $attendingCommunityUser = NULL;

	/**
	 * Ignoring Community Users
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Visol\Easyvote\Domain\Model\CommunityUser>
	 * @lazy
	 */
	protected $ignoringCommunityUsers = NULL;

	/**
	 * Panel
	 *
	 * @var \Visol\EasyvoteEducation\Domain\Model\Panel;
	 * @lazy
	 */
	protected $panel = NULL;

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
		$this->allowedParties = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->ignoringCommunityUsers = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Adds a Party
	 *
	 * @param \Visol\Easyvote\Domain\Model\Party $allowedParty
	 * @return void
	 */
	public function addAllowedParty(\Visol\Easyvote\Domain\Model\Party $allowedParty) {
		$this->allowedParties->attach($allowedParty);
	}

	/**
	 * Removes a Party
	 *
	 * @param \Visol\Easyvote\Domain\Model\Party $allowedPartyToRemove The Party to be removed
	 * @return void
	 */
	public function removeAllowedParty(\Visol\Easyvote\Domain\Model\Party $allowedPartyToRemove) {
		$this->allowedParties->detach($allowedPartyToRemove);
	}

	/**
	 * Returns the allowedParties
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Visol\Easyvote\Domain\Model\Party> $allowedParties
	 */
	public function getAllowedParties() {
		return $this->allowedParties;
	}

	/**
	 * Sets the allowedParties
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Visol\Easyvote\Domain\Model\Party> $allowedParties
	 * @return void
	 */
	public function setAllowedParties(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $allowedParties) {
		$this->allowedParties = $allowedParties;
	}

	/**
	 * Returns the attendingCommunityUser
	 *
	 * @return \Visol\Easyvote\Domain\Model\CommunityUser $attendingCommunityUser
	 */
	public function getAttendingCommunityUser() {
		return $this->attendingCommunityUser;
	}

	/**
	 * Sets the attendingCommunityUser
	 *
	 * @param \Visol\Easyvote\Domain\Model\CommunityUser $attendingCommunityUser
	 * @return void
	 */
	public function setAttendingCommunityUser($attendingCommunityUser) {
		$this->attendingCommunityUser = $attendingCommunityUser;
	}

	/**
	 * Adds a CommunityUser
	 *
	 * @param \Visol\Easyvote\Domain\Model\CommunityUser $ignoringCommunityUser
	 * @return void
	 */
	public function addIgnoringCommunityUser(\Visol\Easyvote\Domain\Model\CommunityUser $ignoringCommunityUser) {
		$this->ignoringCommunityUsers->attach($ignoringCommunityUser);
	}

	/**
	 * Removes a CommunityUser
	 *
	 * @param \Visol\Easyvote\Domain\Model\CommunityUser $ignoringCommunityUserToRemove The CommunityUser to be removed
	 * @return void
	 */
	public function removeIgnoringCommunityUser(\Visol\Easyvote\Domain\Model\CommunityUser $ignoringCommunityUserToRemove) {
		$this->ignoringCommunityUsers->detach($ignoringCommunityUserToRemove);
	}

	/**
	 * Returns the ignoringCommunityUsers
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Visol\Easyvote\Domain\Model\CommunityUser> $ignoringCommunityUsers
	 */
	public function getIgnoringCommunityUsers() {
		return $this->ignoringCommunityUsers;
	}

	/**
	 * Sets the ignoringCommunityUsers
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Visol\Easyvote\Domain\Model\CommunityUser> $ignoringCommunityUsers
	 * @return void
	 */
	public function setIgnoringCommunityUsers(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $ignoringCommunityUsers) {
		$this->ignoringCommunityUsers = $ignoringCommunityUsers;
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

}