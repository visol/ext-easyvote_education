<?php
namespace Visol\EasyvoteEducation\Controller;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Visol\EasyvoteEducation\Domain\Model\Panel;
use Visol\EasyvoteEducation\Domain\Model\PanelInvitation;

/**
 * VotingController
 */
class PanelInvitationController extends \Visol\EasyvoteEducation\Controller\AbstractController {

	/**
	 * @param Panel $panel
	 * @return string
	 */
	public function listForCurrentUserAction(Panel $panel) {
		if ($this->isCurrentUserOwnerOfPanel($panel)) {
			$this->view->assign('panel', $panel);
			return json_encode(array('content' => $this->view->render()));
		}
	}

	/**
	 * Action create
	 *
	 * @param Panel $panel
	 * @param string $selection
	 * @return string
	 */
	public function createAction(Panel $panel, $selection = '') {
		if ($this->isCurrentUserOwnerOfPanel($panel)) {
			if (count($panel->getPanelInvitations()) < $panel->getNumberOfAllowedPanelInvitations()) {
				$partyUids = GeneralUtility::trimExplode(',', $selection);
				// at least one party and not more than two parties must be selected
				if (count($partyUids) > 0 && count($partyUids) < 3) {
					// parties must be added
					/** @var PanelInvitation $panelInvitation */
					$panelInvitation = $this->objectManager->get('Visol\EasyvoteEducation\Domain\Model\PanelInvitation');
					$panelInvitation->setPanel($panel);
					foreach ($partyUids as $partyUid) {
						$party = $this->partyRepository->findByUid($partyUid);
						if ($party instanceof \Visol\Easyvote\Domain\Model\Party && $party->isIsYoungParty()) {
							$panelInvitation->addAllowedParty($party);
						}
					}
					// Add panel invitation only if at least one party could be added
					if (count($panelInvitation->getAllowedParties())) {
						$this->panelInvitationRepository->add($panelInvitation);
						$panel->addPanelInvitation($panelInvitation);
						$this->panelRepository->update($panel);
						$this->persistenceManager->persistAll();
					} else {
						// TODO handle error
					}
				}
			} else {
				// TODO tried to add more invitations than allowed
			}
			return json_encode(array('reloadPanelInvitations' => $panel->getUid()));
		}
	}

	/**
	 * Action delete
	 *
	 * @param PanelInvitation $panelInvitation
	 * @ignorevalidation $panelInvitation
	 * @return string
	 * @unused currently not used and maintained
	 */
	public function deleteAction(PanelInvitation $panelInvitation) {
		// currently disabled
		return json_encode(array('reloadPanelInvitations' => $panelInvitation->getPanel()->getUid()));

		if ($this->isCurrentUserOwnerOfPanel($panelInvitation->getPanel()) && !is_object($panelInvitation->getAttendingCommunityUser())) {
			$this->panelInvitationRepository->remove($panelInvitation);
			$this->persistenceManager->persistAll();
			return json_encode(array('removeElement' => TRUE));
		}
	}

	/**
	 * Get all parties that are (still) available for the current panel
	 * For each panel, a party can only be added in one panelInvitation
	 * This method is called by Select2 AJAX to ensure only available parties can be added
	 *
	 * @param Panel $panel
	 * @return string
	 */
	public function getAvailablePartiesForPanelAction(Panel $panel) {
		$parties = $this->partyRepository->findYoungParties();

		// Build an array containing all parties that are already part of existing panelInvitations
		$unavailableParties = array();
		foreach ($panel->getPanelInvitations() as $panelInvitation) {
			/** @var PanelInvitation $panelInvitation */
			foreach ($panelInvitation->getAllowedParties() as $unavailableParty) {
				/** @var \Visol\Easyvote\Domain\Model\Party $unavailableParty */
				$unavailableParties[] = $unavailableParty->getUid();
			}
		}

		$returnArray['results'] = array();
		foreach ($parties as $party) {
			/** @var $party \Visol\Easyvote\Domain\Model\Party */
			// If current party was used before, it may not be used again
			if (in_array($party->getUid(), $unavailableParties)) {
				continue;
			}
			$returnArray['results'][] = array(
				'id' => $party->getUid(),
				'text' => $party->getTitle(),
				'shortTitle' => $party->getShortTitle(),
			);
		}
		$returnArray['more'] = FALSE;
		return json_encode($returnArray);
	}

	/**
	 * Attend a panel
	 *
	 * @param PanelInvitation $panelInvitation
	 */
	public function attendAction(PanelInvitation $panelInvitation) {
		if ($communityUser = $this->getLoggedInUser()) {
			if ($communityUser->isPolitician()) {
				// TODO check if parties in panelInvitation match party of user
				if (is_null($panelInvitation->getAttendingCommunityUser())) {
					$panelInvitation->setAttendingCommunityUser($communityUser);
					$this->panelInvitationRepository->update($panelInvitation);
					$this->persistenceManager->persistAll();
					return json_encode(array('reloadPanelParticipations' => TRUE));
				} else {
					// TODO error: Another user attends in the meantime
				}
			} else {
				// TODO not a politician
			}
		} else {
			// TODO access denied
		}
	}

	/**
	 * Ignore a panelInvitation
	 *
	 * @param PanelInvitation $panelInvitation
	 */
	public function ignoreAction(PanelInvitation $panelInvitation) {
		if ($communityUser = $this->getLoggedInUser()) {
			if ($communityUser->isPolitician()) {
				$panelInvitation->addIgnoringCommunityUser($communityUser);
				$this->panelInvitationRepository->update($panelInvitation);
				$this->persistenceManager->persistAll();
				return json_encode(array('reloadPanelParticipations' => TRUE));
			} else {
				// TODO not a politician
			}
		} else {
			// TODO access denied
		}
	}

	/**
	 * This action initializes the function and calls listForPartyByDemandAction through AJAX
	 */
	public function manageInvitationsAction() {
	}

	/**
	 * Return all panel invitations for the party that the currently authentication party administrator is a member of
	 * If an empty demand is passed, only active panels are returned
	 *
	 * @param array $demand
	 */
	public function listForPartyByDemandAction($demand = NULL) {
		if ($party = $this->getPartyIfCurrentUserIsAdministrator()) {
			$this->view->assign('demand', $demand);

			$this->view->assign('party', $party);

			$allInvitations = $this->panelInvitationRepository->findByAllowedPartyAndDemand($party, NULL);
			$this->view->assign('allInvitations', $allInvitations);

			$filteredInvitations = $this->panelInvitationRepository->findByAllowedPartyAndDemand($party, $demand);
			$this->view->assign('filteredInvitations', $filteredInvitations);
		} else {
			// TODO not logged in or not a party administrator
		}
	}

	/**
	 * Removes a user passed by POST from a PanelInvitation
	 *
	 * @param PanelInvitation $object
	 */
	public function removeUserAction(PanelInvitation $object) {
		if ($party = $this->getPartyIfCurrentUserIsAdministrator()) {
			$postData = $this->getPostData();
			if (is_array($postData) && array_key_exists('communityUser', $postData)) {
				/** @var \Visol\Easyvote\Domain\Model\CommunityUser $communityUser */
				$communityUser = $this->communityUserRepository->findByUid((int)$postData['communityUser']);
				// Party is a lazy property of CommunityUser
				if ($communityUser->getParty() instanceof \TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy) {
					$communityUser->getParty()->_loadRealInstance();
				}
				if ($communityUser->getParty() === $party) {
					$object->setAttendingCommunityUser(0);
					$this->panelInvitationRepository->update($object);
					$this->persistenceManager->persistAll();
					return json_encode(array('namespace' => 'EasyvoteEducation', 'function' => 'getPanelInvitations', 'arguments' => $object->getUid()));
				} else {
					// TODO denied trying removing a user of another party
				}
			} else {
				// TODO no communityUser submitted
			}
		} else {
			// TODO not logged in or not a party administrator
		}
	}

	/**
	 * Assigns a user passed by POST to a PanelInvitation
	 *
	 * @param PanelInvitation $object
	 */
	public function assignUserAction(PanelInvitation $object) {
		if ($party = $this->getPartyIfCurrentUserIsAdministrator()) {
			$postData = $this->getPostData();
			if (is_array($postData) && array_key_exists('communityUser', $postData)) {
				/** @var \Visol\Easyvote\Domain\Model\CommunityUser $communityUser */
				$communityUser = $this->communityUserRepository->findByUid((int)$postData['communityUser']);
				// Party is a lazy property of CommunityUser
				if ($communityUser->getParty() instanceof \TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy) {
					$communityUser->getParty()->_loadRealInstance();
				}
				if ($communityUser->getParty() === $party) {
					if (is_null($object->getAttendingCommunityUser())) {
						$object->setAttendingCommunityUser($communityUser);
						$this->panelInvitationRepository->update($object);
						$this->persistenceManager->persistAll();
						return json_encode(array('namespace' => 'EasyvoteEducation', 'function' => 'getPanelInvitations', 'arguments' => $object->getUid()));
					} else {
						// TODO there is an attending community user in the meantime
						return json_encode(array('namespace' => 'EasyvoteEducation', 'function' => 'getPanelInvitations', 'arguments' => $object->getUid()));
					}
				} else {
					// TODO denied trying removing a user of another party
				}
			} else {
				// TODO no communityUser submitted
			}
		} else {
			// TODO not logged in or not a party administrator
		}
	}

	/**
	 * Filter box for panel invitations
	 */
	public function filterAction() {
		$kantons = $this->kantonRepository->findAll();
		$this->view->assign('kantons', $kantons);
		$statusFilters = array(
			'active' => LocalizationUtility::translate('panelInvitations.filter.status.active', 'easyvote_education'),
			'pending' => LocalizationUtility::translate('panelInvitations.filter.status.pending', 'easyvote_education'),
			'archived' => LocalizationUtility::translate('panelInvitations.filter.status.archived', 'easyvote_education'),
		);
		$this->view->assign('statusFilters', $statusFilters);
	}



}