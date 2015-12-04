<?php
namespace Visol\EasyvoteEducation\Service;
use Visol\EasyvoteEducation\Domain\Model\Panel;

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

class PanelService implements \TYPO3\CMS\Core\SingletonInterface  {

	/**
	 * @var \Visol\Easyvote\Domain\Repository\KantonRepository
	 * @inject
	 */
	protected $kantonRepository = NULL;

	/**
	 * @var \Visol\Easyvote\Domain\Repository\CommunityUserRepository
	 * @inject
	 */
	protected $communityUserRepository = NULL;

	/**
	 * @var \Visol\EasyvoteEducation\Domain\Repository\PanelRepository
	 * @inject
	 */
	protected $panelRepository = NULL;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @param Panel $panel
	 * @return boolean
	 */
	public function isPanelInvitationAllowedForPanel(Panel $panel) {
		if (count($panel->getPanelInvitations()) > 0) {
			// If a panel already has invitations, it is allowed to have invitations (quite logical, isn't it :-)?)
			return TRUE;
		}

		$cityOfPanel = $panel->getCity();
		if ($cityOfPanel instanceof \Visol\Easyvote\Domain\Model\City) {
			// panelLimit is either NULL or an integer
			$panelLimitForKanton = $cityOfPanel->getKanton()->getPanelLimit();
			if (is_integer($panelLimitForKanton)) {
				$existingPanelsInKanton = $this->panelRepository->countPanelsWithInvitationsByKanton($panel->getCity()->getKanton());
				if ($existingPanelsInKanton >= $panelLimitForKanton) {
					return FALSE;
				}
			}

            if ($cityOfPanel->getKanton()->getPanelAllowedFrom() instanceof \DateTime) {
                // Panel has a date restriction
                if ($panel->getDate() instanceof \DateTime) {
                    $panelTimestamp = $panel->getDate()->getTimestamp();
                    $panelAllowedFromTimeStamp = $cityOfPanel->getKanton()->getPanelAllowedFrom()->getTimestamp();

                    if ($cityOfPanel->getKanton()->getPanelAllowedTo() instanceof \DateTime) {
                        // We also have an end date restriction
                        $panelAllowedToTimeStamp = $cityOfPanel->getKanton()->getPanelAllowedTo()->getTimestamp();
                        if ($panelTimestamp < $panelAllowedFromTimeStamp || $panelTimestamp > $panelAllowedToTimeStamp) {
                            // The panel doesn't take place in the given timeframe
                            return FALSE;
                        }
                    } else {
                        if ($panelTimestamp < $panelAllowedFromTimeStamp) {
                            // Panel is too early
                            return FALSE;
                        }
                    }
                } else {
                    // There is a date restriction, but panel has no date, so we can't know if invitations are allowed
                    return FALSE;
                }
            }

		}

		// return false if all panels are reached or existing panels don't have a city set
		return TRUE;
	}

	/**
	 * @param Panel $panel
	 * @return boolean
	 */
	public function areAllPanelInvitationsAccepted(Panel $panel) {
		foreach ($panel->getPanelInvitations() as $panelInvitation) {
			/** @var $panelInvitation \Visol\EasyvoteEducation\Domain\Model\PanelInvitation */
			if (is_null($panelInvitation->getAttendingCommunityUser())) {
				// The first PanelInvitation without an attending CommunityUser makes this false
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Send an invitiation to all politicians affected by a panel
	 * Affected by a panel means:
	 * a) Living in the Kanton a panel takes place
	 * b) Are politicians of a Party that is allowed for a Panel invitation
	 *
	 * @param Panel $panel
	 */
	public function sentMailAboutPanelToAffectedPoliticians(Panel $panel) {
		if (is_null($panel->getCity())) {
			return FALSE;
		}
		if (count($panel->getPanelInvitations())) {
			foreach ($panel->getPanelInvitations() as $panelInvitation) {
				/** @var $panelInvitation \Visol\EasyvoteEducation\Domain\Model\PanelInvitation */
				if (count($panelInvitation->getAllowedParties())) {

					foreach ($panelInvitation->getAllowedParties() as $party) {
						/** @var $party \Visol\Easyvote\Domain\Model\Party */
						$demand = [];
						$demand['kanton'] = $panel->getCity()->getKanton()->getUid();
						$partyMembers = $this->communityUserRepository->findPoliticiansByPartyAndDemand(
							$party, $demand
						);
						foreach ($partyMembers as $partyMember) {
							/** @var $partyMember \Visol\Easyvote\Domain\Model\CommunityUser */
							// Send information e-mail to politician
							/** @var \Visol\Easyvote\Service\TemplateEmailService $templateEmail */
							$templateEmail = $this->objectManager->get('Visol\Easyvote\Service\TemplateEmailService');
							$templateEmail->addRecipient($partyMember);
							$templateEmail->setTemplateName('panelCreatePolitician');
							$templateEmail->setExtensionName('easyvoteeducation');
							$templateEmail->assign('panel', $panel);
							$templateEmail->enqueue();
						}

					}
				}
			}
		}
	}
}