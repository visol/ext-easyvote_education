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
use Visol\EasyvoteEducation\Domain\Model\Panel;

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
			$partyUids = GeneralUtility::trimExplode(',', $selection);
			if (count($partyUids) > 0 && count($partyUids) < 3) {
				// parties must be added
				/** @var \Visol\EasyvoteEducation\Domain\Model\PanelInvitation $panelInvitation */
				$panelInvitation = $this->objectManager->get('Visol\EasyvoteEducation\Domain\Model\PanelInvitation');
				$panelInvitation->setPanel($panel);
				foreach ($partyUids as $partyUid) {
					$party = $this->partyRepository->findByUid($partyUid);
					if ($party instanceof \Visol\Easyvote\Domain\Model\Party && $party->isIsYoungParty()) {
						$panelInvitation->addAllowedParty($party);
					}
				}
				if (count($panelInvitation->getAllowedParties())) {
					$this->panelInvitationRepository->add($panelInvitation);
					$panel->addPanelInvitation($panelInvitation);
					$this->panelRepository->update($panel);
					$this->persistenceManager->persistAll();
				}
			}
			return json_encode(array('reloadPanelInvitations' => $panel->getUid()));
		}
	}

	/**
	 * Action delete
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\PanelInvitation $panelInvitation
	 * @ignorevalidation $panelInvitation
	 * @return string
	 */
	public function deleteAction(\Visol\EasyvoteEducation\Domain\Model\PanelInvitation $panelInvitation) {
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
			/** @var \Visol\EasyvoteEducation\Domain\Model\PanelInvitation $panelInvitation */
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
	 * @param \Visol\EasyvoteEducation\Domain\Model\PanelInvitation $panelInvitation
	 */
	public function attendAction(\Visol\EasyvoteEducation\Domain\Model\PanelInvitation $panelInvitation) {
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
	 * @param \Visol\EasyvoteEducation\Domain\Model\PanelInvitation $panelInvitation
	 */
	public function ignoreAction(\Visol\EasyvoteEducation\Domain\Model\PanelInvitation $panelInvitation) {
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

}