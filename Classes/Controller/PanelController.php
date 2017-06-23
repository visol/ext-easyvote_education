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

use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Visol\Easyvote\Utility\Algorithms;
use Visol\EasyvoteEducation\Domain\Model\Panel;

class PanelController extends \Visol\EasyvoteEducation\Controller\AbstractController
{

    /**
     * @var \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication frontendUserAuthentication
     */
    protected $frontendUserAuthentication;

    /**
     * PanelController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->frontendUserAuthentication = $GLOBALS['TSFE']->fe_user;
    }

    /**
     * Display new panel form (for authenticated user)
     *
     * @param Panel $newPanel
     * @ignorevalidation $newPanel
     * @return mixed
     */
    public function newAction(Panel $newPanel = null)
    {
        if ($this->getLoggedInUser()) {
            $this->view->assign('newPanel', $newPanel);
            return json_encode(array('content' => $this->view->render()));
        } else {
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelController/newAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Property mapping of date, fromTime, toTime
     */
    protected function initializeCreateAction()
    {
        $propertyMappingConfiguration = $this->arguments['newPanel']->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->forProperty('date')->setTypeConverterOption('TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter',
            \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'd.m.y');
        $propertyMappingConfiguration->forProperty('fromTime')->setTypeConverter($this->objectManager->get('Visol\\Easyvote\\Property\\TypeConverter\\TimestampConverter'))->setTypeConverterOption('Visol\\Easyvote\\Property\\TypeConverter\\TimestampConverter',
            \Visol\Easyvote\Property\TypeConverter\TimestampConverter::CONFIGURATION_DATE_FORMAT, 'H:i');
        $propertyMappingConfiguration->forProperty('toTime')->setTypeConverter($this->objectManager->get('Visol\\Easyvote\\Property\\TypeConverter\\TimestampConverter'))->setTypeConverterOption('Visol\\Easyvote\\Property\\TypeConverter\\TimestampConverter',
            \Visol\Easyvote\Property\TypeConverter\TimestampConverter::CONFIGURATION_DATE_FORMAT, 'H:i');
    }

    /**
     * Create a panel (for authenticated user)
     *
     * @param Panel $newPanel
     * @return string
     */
    public function createAction(Panel $newPanel)
    {
        if ($communityUser = $this->getLoggedInUser()) {
            do {
                $panelId = Algorithms::generateRandomString(4, 'ABCDEFGHJKMNPQRSTUVWXYZ123456789');
            } while ($this->panelRepository->findOneByPanelId($panelId) instanceof Panel);
            $newPanel->setPanelId($panelId);
            $newPanel->setCommunityUser($communityUser);
            $this->panelRepository->add($newPanel);
            $this->persistenceManager->persistAll();
            $message = LocalizationUtility::translate('panel.actions.create.success',
                $this->request->getControllerExtensionName(), array($newPanel->getTitle()));
            $message .= '<script>EasyvoteEducation.openPanel(' . $newPanel->getUid() . ');</script>';
            $this->addFlashMessage($message, '', AbstractMessage::OK);
            return json_encode(array(
                'redirectToAction' => 'managePanels'
            ));
        } else {
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelController/createAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Edit a panel (for panel owner)
     *
     * @param Panel $panel
     * @ignorevalidation $panel
     * @return string
     */
    public function editAction(Panel $panel)
    {
        if ($this->isCurrentUserOwnerOfPanel($panel)) {
            $this->view->assign('panel', $panel);
            return json_encode(array('content' => $this->view->render()));
        } else {
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelController/editAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Property mapping of date, fromTime, toTime
     */
    protected function initializeUpdateAction()
    {
        $propertyMappingConfiguration = $this->arguments['panel']->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->forProperty('date')->setTypeConverterOption('TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter',
            \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'd.m.y');
        $propertyMappingConfiguration->forProperty('fromTime')->setTypeConverter($this->objectManager->get('Visol\\Easyvote\\Property\\TypeConverter\\TimestampConverter'))->setTypeConverterOption('Visol\\Easyvote\\Property\\TypeConverter\\TimestampConverter',
            \Visol\Easyvote\Property\TypeConverter\TimestampConverter::CONFIGURATION_DATE_FORMAT, 'H:i');
        $propertyMappingConfiguration->forProperty('toTime')->setTypeConverter($this->objectManager->get('Visol\\Easyvote\\Property\\TypeConverter\\TimestampConverter'))->setTypeConverterOption('Visol\\Easyvote\\Property\\TypeConverter\\TimestampConverter',
            \Visol\Easyvote\Property\TypeConverter\TimestampConverter::CONFIGURATION_DATE_FORMAT, 'H:i');
    }

    /**
     * Update a panel (for panel owner)
     *
     * @param Panel $panel
     * @return string
     */
    public function updateAction(Panel $panel)
    {
        if ($this->isCurrentUserOwnerOfPanel($panel)) {
            $message = LocalizationUtility::translate('panel.actions.update.success',
                $this->request->getControllerExtensionName(), array($panel->getTitle()));
            $message .= '<script>EasyvoteEducation.openPanel(' . $panel->getUid() . ');</script>';
            $this->addFlashMessage($message, '', AbstractMessage::OK);
            $this->panelRepository->update($panel);
            $this->persistenceManager->persistAll();
            return json_encode(array(
                'redirectToAction' => 'managePanels'
            ));
        } else {
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelController/updateAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Delete a panel (for panel owner)
     *
     * @param Panel $panel
     * @return string
     * @unused Currently not maintained
     */
    public function deleteAction(Panel $panel)
    {
        // Deleting from the frontend is currently not allowed
        return json_encode(array(
            'redirectToAction' => 'managePanels'
        ));

        if ($this->isCurrentUserOwnerOfPanel($panel)) {
            $message = LocalizationUtility::translate('panel.actions.delete.success',
                $this->request->getControllerExtensionName(), array($panel->getTitle()));
            $this->addFlashMessage($message, '', AbstractMessage::OK);
            $this->panelRepository->remove($panel);
            $this->persistenceManager->persistAll();
            return json_encode(array(
                'redirectToAction' => 'managePanels'
            ));
        } else {
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelController/deleteAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Duplicate a panel (for panel owner)
     *
     * @unused Currently not used and maintained
     * @param Panel $panel
     * @return string
     */
    public function duplicateAction(Panel $panel)
    {
        // Duplicating is currently not allowed
        return json_encode(array(
            'redirectToAction' => 'managePanels'
        ));

        if ($this->isCurrentUserOwnerOfPanel($panel)) {
            $message = LocalizationUtility::translate('panel.actions.duplicate.success',
                $this->request->getControllerExtensionName(), array($panel->getTitle()));
            $this->addFlashMessage($message, '', AbstractMessage::OK);
            /** @var Panel $duplicatePanel */
            $duplicatePanel = $this->cloneService->copy($panel);
            // generate a new panelId
            do {
                $panelId = Algorithms::generateRandomString(8, 'ABCDEFGHJKMNPQRSTUVWXYZ123456789');
            } while ($this->panelRepository->findOneByPanelId($panelId) instanceof Panel);
            $duplicatePanel->setPanelId($panelId);
            // Prefix "Copy of" to duplicated panel
            $copyOfText = LocalizationUtility::translate('panel.actions.duplicate.copyOf',
                $this->request->getControllerExtensionName());
            $duplicatePanel->setTitle($copyOfText . ' ' . $panel->getTitle());

            $this->panelRepository->add($duplicatePanel);
            $this->persistenceManager->persistAll();
            return json_encode(array(
                'redirectToAction' => 'managePanels'
            ));
        } else {
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelController/duplicateAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }

    }

    /**
     * Edit Votings (for panel owner)
     *
     * @param Panel $panel
     * @return string
     */
    public function editVotingsAction(Panel $panel)
    {
        if ($this->isCurrentUserOwnerOfPanel($panel)) {
            $this->view->assign('panel', $panel);
            return json_encode(array('content' => $this->view->render()));
        } else {
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelController/editVotingsAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Edit Panel Invitations (for panel owner)
     *
     * @param Panel $panel
     * @return string
     */
    public function editPanelInvitationsAction(Panel $panel)
    {
        if ($this->isCurrentUserOwnerOfPanel($panel)) {
            if ($this->getPanelService()->isPanelInvitationAllowedForPanel($panel) && !$panel->isPanelInvitationsSent()) {
                // allow editing of panel invitations if it is still possible to add panel invitations for a panel
                // in this Kanton or if there are already panel invitations
                $this->view->assign('panel', $panel);
                $this->view->assign('communityHomePid', $this->settings['communityHomePid']);
                return json_encode(array('content' => $this->view->render()));
            } else {
                $reason = LocalizationUtility::translate('ajax.status.403.panelController.editPanelInvitationsAction',
                    'easyvote_education');
                $reason .= '<br />PanelController/editPanelInvitationsAction';
                return json_encode(array('status' => 403, 'reason' => $reason));
            }
        } else {
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelController/editPanelInvitationsAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Send Panel Invitations (for panel owner)
     *
     * @param Panel $panel
     * @return string
     */
    public function sendPanelInvitationsAction(Panel $panel)
    {
        if ($this->isCurrentUserOwnerOfPanel($panel)) {
            if (!$panel->isPanelInvitationsSent()) {
                $panel->setPanelInvitationsSent(true);
                $this->panelRepository->update($panel);
                $this->persistenceManager->persistAll();

                // Send confirmation e-mail to teacher
                /** @var \Visol\Easyvote\Service\TemplateEmailService $templateEmail */
                $templateEmail = $this->objectManager->get('Visol\Easyvote\Service\TemplateEmailService');
                $templateEmail->addRecipient($panel->getCommunityUser());
                $templateEmail->setTemplateName('panelCreateTeacher');
                $templateEmail->setExtensionName($this->request->getControllerExtensionName());
                $templateEmail->assign('panel', $panel);
                $templateEmail->enqueue();

                // Send information e-mails
                $this->getPanelService()->sentMailAboutPanelToAffectedPoliticians($panel);

                return json_encode(array('redirectToAction' => 'managePanels'));
            } else {
                $reason = LocalizationUtility::translate('ajax.status.403.panelController.sendPanelInvitationsAction',
                    'easyvote_education');
                $reason .= '<br />PanelController/sendPanelInvitationsAction';
                return json_encode(array('status' => 403, 'reason' => $reason));
            }
        } else {
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelController/sendPanelInvitationsAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Loads the Panel Host View, a commented view which could be used in addition to the Presentation View
     * This view is currently unused
     *
     * @param Panel $panel
     * @return string
     * @unused Currently unused and unmaintained
     */
    public function executeAction(Panel $panel)
    {
        // Currently unused
        return json_encode(array(
            'redirectToAction' => 'managePanels'
        ));

        if ($this->isCurrentUserOwnerOfPanel($panel)) {
            $this->view->assign('panel', $panel);
            $guestViewUri = $this->uriBuilder->setCreateAbsoluteUri(true)->build();
            $this->view->assign('guestViewUri', urlencode($guestViewUri));
            return json_encode(array('content' => $this->view->render()));
        } else {
            $reason = LocalizationUtility::translate('ajax.status.403.panelController.executeAction',
                'easyvote_education');
            $reason .= '<br />PanelController/executeAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Load/perform a Voting Step
     * This method is used in the Presentation View and the Guest View
     *
     * Protected actions: Actions that can only be executed by the panel owner to perform the panel (e.g. start a voting,
     * stop a voting etc.)
     * Public actions: Actions that can be performed by anonymous users that participate in a panel (e.g. get the current
     * voting, cast a vote)
     *
     * @param string $actionarguments The action arguments
     * @return string
     */
    public function votingStepAction($actionarguments)
    {
        $actionArgumentsArray = GeneralUtility::trimExplode('-', $actionarguments);
        $protectedActions = array('startPanel', 'nextVoting', 'startVoting', 'stopVoting', 'stopPanel');
        $publicActions = array('guestViewContent', 'presentationViewContent', 'castVote');

        if (count($actionArgumentsArray) === 4) {
            // we need four parts in the array for the request to be valid
            list($unusedPanelObjectName, $panelUid, $votingStepAction, $votingUid) = $actionArgumentsArray;
            /* @var Panel $panel */
            $panel = $this->panelRepository->findByUid((int)$panelUid);
            if (in_array($votingStepAction, $protectedActions)) {
                // action can only be performed by the owner of the panel, security check
                if ($this->isCurrentUserOwnerOfPanel($panel)) {
                    // the owner is making the request, so it is valid
                    switch ($votingStepAction) {
                        case 'startPanel':
                            $panel->setCurrentState('');
                            break;

                        case 'nextVoting':
                            /** @var \Visol\EasyvoteEducation\Domain\Model\Voting $voting */
                            $voting = $this->votingRepository->findByUid((int)$votingUid);
                            if ((int)$voting->getType() >= 10) {
                                // A render only content
                                $votingStepAction = 'renderContent';
                                $this->view->assign('originalVotingStepAction', 'PresentationViewContent');
                                $panel->setCurrentState('renderContent-' . $votingUid);
                            } else {
                                // A normal voting
                                $panel->setCurrentState('pendingVoting-' . $votingUid);
                            }
                            break;

                        case 'startVoting':
                            // set voting to enabled
                            $panel->getCurrentVoting()->setIsVotingEnabled(true);
                            $this->votingRepository->update($panel->getCurrentVoting());

                            $panel->setCurrentState('currentVoting-' . $votingUid);
                            break;

                        case 'stopVoting':
                            // set voting to disabled
                            $panel->getCurrentVoting()->setIsVotingEnabled(false);
                            $this->votingRepository->update($panel->getCurrentVoting());
                            $this->votingService->processVotingResult($panel);
                            $panel->setCurrentState('finishedVoting-' . $votingUid);
                            break;

                        case 'stopPanel':
                            $panel->setCurrentState('finishedPanel-0');
                            break;
                    }

                    $this->panelRepository->update($panel);
                    $this->persistenceManager->persistAll();

                    $this->getFileCacheService($panel)->changeState($panel->getCurrentState());

                    $this->view->assign('votingStepAction', $votingStepAction);
                    $this->view->assign('panel', $panel);
                    return json_encode(array('content' => $this->view->render()));
                } else {
                    $reason = LocalizationUtility::translate('ajax.status.403.panelController.executeAction',
                        'easyvote_education');
                    $reason .= '<br />PanelController/votingStepAction/' . $votingStepAction;
                    return json_encode(array('status' => 403, 'reason' => $reason));
                }
            } elseif (in_array($votingStepAction, $publicActions)) {
                $this->view->assign('originalVotingStepAction', ucfirst($votingStepAction));
                $votingStepAction = $this->votingService->getViewNameForCurrentPanelState(
                    $panel,
                    $votingStepAction);
                $this->view->assign('votingStepAction', $votingStepAction);
                $this->view->assign('panel', $panel);
                return json_encode(array('content' => $this->view->render()));
            } else {
                $reason = LocalizationUtility::translate('ajax.status.404.panelController.votingStepAction',
                    'easyvote_education');
                $reason .= '<br />PanelController/votingStepAction/' . $votingStepAction;
                return json_encode(array('status' => 404, 'reason' => $reason));
            }
        } else {
            $reason = LocalizationUtility::translate('ajax.status.400.panelController.votingStepAction',
                'easyvote_education');
            $reason .= '<br />PanelController/votingStepAction';
            return json_encode(array('status' => 400, 'reason' => $reason));
        }
    }

    /**
     * Login Screen for Guest View
     */
    public function guestViewLoginAction()
    {
    }

    /**
     * Check if panel is available
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function initializeGuestViewParticipationAction()
    {
        if ($this->request->hasArgument('panelId')) {
            // check if there is a panel with this ID
            $panelId = $this->request->getArgument('panelId');
            /** @var Panel $panel */
            $panel = $this->panelRepository->findOneByPanelId($panelId);
            if (!$panel instanceof Panel) {
                $message = LocalizationUtility::translate('panel.guestView.panelNotFound',
                    $this->request->getControllerExtensionName());
                $this->flashMessageContainer->add($message, '', AbstractMessage::ERROR);
                $this->redirect('guestViewLogin');
            }
        }
    }

    /**
     * Load Guest View
     *
     * @param string $panelId
     */
    public function guestViewParticipationAction($panelId)
    {
        /** @var Panel $panel */
        $panel = $this->panelRepository->findOneByPanelId($panelId);

        $fileCacheService = $this->getFileCacheService($panel);

        $this->view->assign('panel', $panel);
        $this->view->assign('directoryPath', $fileCacheService->getRelativePath());
        $this->view->assign('language', $this->getFrontendObject()->sys_language_uid);
    }

    /**
     * Login Screen for Presentation View
     */
    public function presentationViewLoginAction()
    {

    }

    /**
     * Check if panel is available
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function initializePresentationViewParticipationAction()
    {
        if ($this->request->hasArgument('panelId')) {
            // check if there is a panel with this ID
            $panelId = $this->request->getArgument('panelId');
            /** @var Panel $panel */
            $panel = $this->panelRepository->findOneByPanelId($panelId);
            if (!$panel instanceof Panel) {
                $message = LocalizationUtility::translate('panel.guestView.panelNotFound',
                    $this->request->getControllerExtensionName());
                $this->flashMessageContainer->add($message, '', AbstractMessage::ERROR);
                $this->redirect('presentationViewLogin');
            }
        }
    }

    /**
     * Load Presentation View
     *
     * @param string $panelId
     * @param boolean $reset
     * @return string
     */
    public function presentationViewParticipationAction($panelId, $reset = false)
    {
        /** @var Panel $panel */
        $panel = $this->panelRepository->findOneByPanelId($panelId);
        // action can only be performed by the owner of the panel, security check
        if ($this->isCurrentUserOwnerOfPanel($panel)) {
            // the owner is making the request, so it is valid
            if ($reset) {
                // panel needs to be reset for a fresh start
                $this->resetPanel($panel);
            }
            $this->view->assign('panel', $panel);
            $this->view->assign('language', $this->getFrontendObject()->sys_language_uid);
        } else {
            // No panel is assigned to Fluid, so a condition in Fluid output an access denied error
            $this->response->setStatus(403);
        }
    }

    /**
     * Sets panel state to beginning (currentState => empty) and removes all votes from all votingOptions from all votings
     *
     * @param Panel $panel
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    protected function resetPanel(Panel $panel)
    {
        $panel->setCurrentState('');
        foreach ($panel->getVotings() as $voting) {
            /** @var $voting \Visol\EasyvoteEducation\Domain\Model\Voting */
            foreach ($voting->getVotingOptions() as $votingOption) {
                /** @var $votingOption \Visol\EasyvoteEducation\Domain\Model\VotingOption */
                $votingOption->getVotes()->removeAll($votingOption->getVotes());
                $this->votingOptionRepository->update($votingOption);
            }
        }
        $this->persistenceManager->persistAll();
    }


    /**
     * Load Manage Panels functionality for teachers
     */
    public function managePanelsStartupAction()
    {
        // Language is used for requests with EXT:routing
        $this->view->assign('language', $this->getFrontendObject()->sys_language_uid);
    }

    /**
     * Load all panels of a teacher
     *
     * @return string
     */
    public function managePanelsAction()
    {
        if ($communityUser = $this->getLoggedInUser()) {
            $panels = $this->panelRepository->findByCommunityUser($communityUser);
            $this->view->assign('panels', $panels);
            return json_encode(array('content' => $this->view->render()));
        } else {
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelController/managePanelsAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Load panel participations functionality for politician
     */
    public function panelParticipationsStartupAction()
    {
    }

    /**
     * Loads all panel invitations in the scope of a politician
     *
     * @return string
     */
    public function panelParticipationsAction()
    {
        if ($communityUser = $this->getLoggedInUser()) {
            $panelInvitations = $this->panelInvitationRepository->findFutureNotIgnoredPanelsByCommunityUser($communityUser);
            $this->view->assign('panelInvitations', $panelInvitations);
            $this->view->assign('communityUser', $communityUser);
            return json_encode(array('content' => $this->view->render()));
        } else {
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />PanelController/panelParticipationsAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * @return \Visol\EasyvoteEducation\Service\PanelService
     */
    protected function getPanelService()
    {
        return $this->objectManager->get('Visol\EasyvoteEducation\Service\PanelService');
    }

    /**
     * @param Panel $panel
     * @return \Visol\EasyvoteEducation\Service\FileCacheService
     */
    protected function getFileCacheService(Panel $panel)
    {
        /** @var \Visol\EasyvoteEducation\Service\FileCacheService $fileCacheService */
        $fileCacheService = $this->objectManager->get('Visol\EasyvoteEducation\Service\FileCacheService', $panel);
        return $fileCacheService->initialize();
    }

}