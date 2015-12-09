<?php
namespace Visol\EasyvoteEducation\Controller;

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
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Visol\EasyvoteEducation\Domain\Model\Panel;
use Visol\EasyvoteEducation\Domain\Model\PanelInvitation;

class PanelInvitationController extends \Visol\EasyvoteEducation\Controller\AbstractController
{

    /**
     * List all panel invitations for a given panel
     * Called by AJAX in editPanelInvitations
     *
     * @param Panel $panel
     * @return string
     */
    public function listForCurrentUserAction(Panel $panel)
    {
        if ($this->isCurrentUserOwnerOfPanel($panel)) {
            $this->view->assign('panel', $panel);
            return json_encode(array('content' => $this->view->render()));
        } else {
            // Error: Non-owner trying to list panel invitations
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelInvitationController/listForCurrentUserAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Action create
     *
     * @param Panel $panel
     * @param string $selection
     * @return string
     */
    public function createAction(Panel $panel, $selection = '')
    {
        if ($this->isCurrentUserOwnerOfPanel($panel)) {
            if (count($panel->getPanelInvitations()) < $panel->getNumberOfAllowedPanelInvitations()) {
                $partyUids = GeneralUtility::trimExplode(',', $selection);
                // at least one party and not more than two parties must be selected
                if (count($partyUids) > 0/* && count($partyUids) < 3*/) {
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
                        return json_encode(array('reloadPanelInvitations' => $panel->getUid()));
                    } else {
                        // Error: Trying to add a panel invitation for a not allowed party
                        $reason = LocalizationUtility::translate('ajax.status.403.panelInvitationController.createAction.notAllowedParty',
                            'easyvote_education');
                        $reason .= '<br />PanelInvitationController/createAction';
                        return json_encode(array('status' => 403, 'reason' => $reason));
                    }
                }
            } else {
                // Error: Limit of panel invitations for panel reached
                $reason = LocalizationUtility::translate('ajax.status.403.panelInvitationController.createAction.noMoreInvitations',
                    'easyvote_education');
                $reason .= '<br />PanelInvitationController/createAction';
                return json_encode(array('status' => 403, 'reason' => $reason));
            }
        } else {
            // Error: Non-owner trying to add panel invitation
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelInvitationController/createAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Delete a panel invitation
     *
     * @param PanelInvitation $panelInvitation
     * @ignorevalidation $panelInvitation
     * @return string
     */
    public function deleteAction(PanelInvitation $panelInvitation)
    {
        if ($this->isCurrentUserOwnerOfPanel($panelInvitation->getPanel()) && !is_object($panelInvitation->getAttendingCommunityUser())) {
            $this->panelInvitationRepository->remove($panelInvitation);
            $this->persistenceManager->persistAll();
            return json_encode(array('reloadPanelInvitations' => $panelInvitation->getPanel()->getUid()));
        } else {
            // Error: Non-owner trying to delete panel invitation
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelInvitationController/listForCurrentUserAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
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
    public function getAvailablePartiesForPanelAction(Panel $panel)
    {
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
        $returnArray['more'] = false;
        return json_encode($returnArray);
    }

    /**
     * Attend a panel
     *
     * @param PanelInvitation $panelInvitation
     * @return string
     */
    public function attendAction(PanelInvitation $panelInvitation)
    {
        if ($communityUser = $this->getLoggedInUser()) {
            if ($communityUser->isPolitician()) {
                if (is_null($panelInvitation->getAttendingCommunityUser())) {
                    // Party is a lazy property of CommunityUser
                    if ($communityUser->getParty() instanceof \TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy) {
                        $communityUser->getParty()->_loadRealInstance();
                    }
                    $isPanelInvitationForPartyOfCommunityUser = false;
                    foreach ($panelInvitation->getAllowedParties() as $party) {
                        /** @var $party \Visol\Easyvote\Domain\Model\Party */
                        if ($party === $communityUser->getParty()) {
                            $isPanelInvitationForPartyOfCommunityUser = true;
                        }
                    }
                    if ($isPanelInvitationForPartyOfCommunityUser) {
                        $panelInvitation->setAttendingCommunityUser($communityUser);

                        // Send confirmation e-mail to politician
                        /** @var \Visol\Easyvote\Service\TemplateEmailService $templateEmail */
                        $templateEmail = $this->objectManager->get('Visol\Easyvote\Service\TemplateEmailService');
                        $templateEmail->addRecipient($communityUser);
                        $templateEmail->setTemplateName('panelInvitationAttendPolitician');
                        $templateEmail->setExtensionName($this->request->getControllerExtensionName());
                        $templateEmail->assign('panel', $panelInvitation->getPanel());
                        $templateEmail->enqueue();

                        $this->panelInvitationRepository->update($panelInvitation);
                        $this->persistenceManager->persistAll();
                        return json_encode(array('reloadPanelParticipations' => true));
                    } else {
                        // Error: User tries to attend to a panel invitation of another party
                        $reason = LocalizationUtility::translate('ajax.status.403.panelInvitationController.attendAction.wrongParty',
                            'easyvote_education');
                        return json_encode(array('status' => 403, 'reason' => $reason));
                    }
                } else {
                    // Error: User tries to attend to a panel invitation that already has an attendee
                    $reason = LocalizationUtility::translate('ajax.status.403.panelInvitationController.attendAction.otherPoliticianAttending',
                        'easyvote_education');
                    return json_encode(array('status' => 403, 'reason' => $reason));
                }
            } else {
                // Error: User that is no politician tries to attend a panel invitation
                $reason = LocalizationUtility::translate('ajax.status.403.panelInvitationController.attendAction.notAPolitician',
                    'easyvote_education');
                $reason .= '<br />PanelInvitationController/attendAction';
                return json_encode(array('status' => 403, 'reason' => $reason));
            }
        } else {
            // Error: Not authenticated
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelInvitationController/attendAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Ignore a panelInvitation
     *
     * @param PanelInvitation $panelInvitation
     * @return string
     */
    public function ignoreAction(PanelInvitation $panelInvitation)
    {
        if ($communityUser = $this->getLoggedInUser()) {
            if ($communityUser->isPolitician()) {
                $panelInvitation->addIgnoringCommunityUser($communityUser);
                $this->panelInvitationRepository->update($panelInvitation);
                $this->persistenceManager->persistAll();
                return json_encode(array('reloadPanelParticipations' => true));
            } else {
                // Not a politician
                // Must not be handled since there is no interest for a non-politician to forge ignoring a panel
            }
        } else {
            // Error: Not authenticated
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelInvitationController/ignoreAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * This action initializes the function and calls listForPartyByDemandAction through AJAX
     */
    public function manageInvitationsAction()
    {
        $this->view->assign('language', $this->getFrontendObject()->sys_language_uid);
    }

    /**
     * Return all panel invitations for the party that the currently authentication party administrator is a member of
     * If an empty demand is passed, only active panels are returned
     *
     * @param array $demand
     */
    public function listForPartyByDemandAction($demand = null)
    {
        if ($party = $this->getPartyIfCurrentUserIsAdministrator()) {
            if ($demand) {
                // Save demand to user session
                $this->saveDemandInSession($demand);
            } else {
                // If no demand is passed and a demand is in the session, use it
                $demand = $this->getDemandFromSession();
            }

            $this->view->assign('demand', $demand);

            $this->view->assign('party', $party);

            $allInvitations = $this->panelInvitationRepository->findByAllowedPartyAndDemand($party, null);
            $this->view->assign('allInvitations', $allInvitations);

            $filteredInvitations = $this->panelInvitationRepository->findByAllowedPartyAndDemand($party, $demand);
            $this->view->assign('filteredInvitations', $filteredInvitations);

            $this->view->assign('language', $this->getFrontendObject()->sys_language_uid);

        } else {
            // Not logged in or not a party administrator
            // Does not need to be handled because it's never called if the parent view
        }
    }

    /**
     * Removes a user passed by POST from a PanelInvitation
     *
     * @param PanelInvitation $object
     * @return string
     */
    public function removeUserAction(PanelInvitation $object)
    {
        if ($party = $this->getPartyIfCurrentUserIsAdministrator()) {
            $postData = $this->getPostData();
            if (is_array($postData) && array_key_exists('communityUser', $postData)) {
                /** @var \Visol\Easyvote\Domain\Model\CommunityUser $communityUser */
                $communityUser = $this->communityUserRepository->findByUid((int)$postData['communityUser']);
                if ($communityUser->getParty() === $party) {
                    $object->setAttendingCommunityUser(0);
                    $this->panelInvitationRepository->update($object);
                    $this->persistenceManager->persistAll();

                    // Send information e-mail to politician
                    /** @var \Visol\Easyvote\Service\TemplateEmailService $templateEmail */
                    $templateEmail = $this->objectManager->get('Visol\Easyvote\Service\TemplateEmailService');
                    $templateEmail->addRecipient($communityUser);
                    $templateEmail->setTemplateName('panelInvitationRemovePolitician');
                    $templateEmail->setExtensionName($this->request->getControllerExtensionName());
                    $templateEmail->assign('panel', $object->getPanel());
                    $templateEmail->enqueue();

                    return json_encode(array(
                            'namespace' => 'EasyvoteEducation',
                            'function' => 'getPanelInvitations',
                            'arguments' => $object->getUid()
                        ));
                } else {
                    // Error: Trying to remove user of another party
                    $reason = LocalizationUtility::translate('ajax.status.403.panelInvitationController.removeUserAction.wrongParty',
                        'easyvote_education');
                    $reason .= '<br />PanelInvitationController/removeUserAction';
                    return json_encode(array('status' => 403, 'reason' => $reason));
                }
            } else {
                // Error: No user to remove was submitted
                $reason = LocalizationUtility::translate('ajax.status.400.panelInvitationController.removeUserAction.noUserSubmitted',
                    'easyvote_education');
                $reason .= '<br />PanelInvitationController/removeUserAction';
                return json_encode(array('status' => 400, 'reason' => $reason));
            }
        } else {
            // Error: No user is authenticated or user is not a party administrator
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelInvitationController/removeUserAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Assigns a user passed by POST to a PanelInvitation
     *
     * @param PanelInvitation $object
     * @return string
     */
    public function assignUserAction(PanelInvitation $object)
    {
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

                        // Send information e-mail to politician
                        /** @var \Visol\Easyvote\Service\TemplateEmailService $templateEmail */
                        $templateEmail = $this->objectManager->get('Visol\Easyvote\Service\TemplateEmailService');
                        $templateEmail->addRecipient($communityUser);
                        $templateEmail->setTemplateName('panelInvitationAssignPolitician');
                        $templateEmail->setExtensionName($this->request->getControllerExtensionName());
                        $templateEmail->assign('panel', $object->getPanel());
                        $templateEmail->enqueue();

                        return json_encode(array(
                                'namespace' => 'EasyvoteEducation',
                                'function' => 'getPanelInvitations',
                                'arguments' => $object->getUid()
                            ));
                    } else {
                        // Another user is attending in the meantime, reload panel invitations
                        return json_encode(array(
                                'namespace' => 'EasyvoteEducation',
                                'function' => 'getPanelInvitations',
                                'arguments' => $object->getUid()
                            ));
                    }
                } else {
                    // Error: trying to assign user of another party
                    $reason = LocalizationUtility::translate('ajax.status.403.panelInvitationController.assignUserAction.wrongParty',
                        'easyvote_education');
                    $reason .= '<br />PanelInvitationController/assignUserAction';
                    return json_encode(array('status' => 403, 'reason' => $reason));
                }
            } else {
                // Error: No user to assign was submitted
                $reason = LocalizationUtility::translate('ajax.status.400.panelInvitationController.assignUserAction.noUserSubmitted',
                    'easyvote_education');
                $reason .= '<br />PanelInvitationController/assignUserAction';
                return json_encode(array('status' => 400, 'reason' => $reason));
            }
        } else {
            // Error: No user is authenticated or user is not a party administrator
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelInvitationController/assignUserAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Filter box for panel invitations
     */
    public function filterAction()
    {
        $authenticatedUser = $this->communityUserService->getCommunityUser();
        $this->view->assign('kantons', $authenticatedUser->getPartyAdminAllowedCantons());
        $this->view->assign('demand', $this->getDemandFromSession(true));
        $statusFilters = array(
            'active' => LocalizationUtility::translate('panelInvitations.filter.status.active', 'easyvote_education'),
            'pending' => LocalizationUtility::translate('panelInvitations.filter.status.pending', 'easyvote_education'),
            'archived' => LocalizationUtility::translate('panelInvitations.filter.status.archived',
                'easyvote_education'),
        );
        $this->view->assign('statusFilters', $statusFilters);
    }

    /**
     * @param array $demand
     */
    protected function saveDemandInSession($demand)
    {
        $GLOBALS['TSFE']->fe_user->setKey('ses', 'tx_easyvoteeducation_managePanelInvitationsDemand',
            serialize($demand));
        $GLOBALS['TSFE']->fe_user->sesData_change = true;
        $GLOBALS['TSFE']->fe_user->storeSessionData();
    }

    /**
     * @param bool $sanitized
     * @return mixed
     */
    protected function getDemandFromSession($sanitized = false)
    {
        $demand = unserialize($GLOBALS['TSFE']->fe_user->getKey('ses',
                'tx_easyvoteeducation_managePanelInvitationsDemand'));
        if ($sanitized) {
            return $this->sanitizeDemand($demand);
        } else {
            return $demand;
        }
    }

    /**
     * Sanitize the query of a given demand (strip tags, htmlspecialchars)
     *
     * @param $demand
     * @return array
     */
    protected function sanitizeDemand($demand)
    {
        if (is_array($demand) && array_key_exists('query', $demand)) {
            $demand['query'] = htmlspecialchars(strip_tags($demand['query']));
            return $demand;
        }
    }

}