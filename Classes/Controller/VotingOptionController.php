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

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Visol\EasyvoteEducation\Domain\Model\Panel;
use Visol\Easyvote\Property\TypeConverter\UploadedFileReferenceConverter;

class VotingOptionController extends \Visol\EasyvoteEducation\Controller\AbstractController
{

    /**
     * List all votings options of a voting
     *
     * @param \Visol\EasyvoteEducation\Domain\Model\Voting $voting
     * @return string
     */
    public function listForVotingAction(\Visol\EasyvoteEducation\Domain\Model\Voting $voting)
    {
        if ($this->isCurrentUserOwnerOfPanel($voting->getPanel())) {
            $this->view->assign('voting', $voting);
            return json_encode(array('content' => $this->view->render()));
        } else {
            // Error: Non-owner tries to list voting options
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />VotingOptionController/listForVotingAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Add a new voting option
     *
     * @param \Visol\EasyvoteEducation\Domain\Model\Voting $voting
     * @return string
     */
    public function newAction(\Visol\EasyvoteEducation\Domain\Model\Voting $voting)
    {
        if ($this->isCurrentUserOwnerOfPanel($voting->getPanel())) {
            /** @var \Visol\EasyvoteEducation\Domain\Model\VotingOption $newVotingOption */
            $newVotingOption = $this->objectManager->get('Visol\EasyvoteEducation\Domain\Model\VotingOption');
            $newVotingOptionTitle = LocalizationUtility::translate('votingOption.actions.new.dummyText.newVotingOption',
                $this->request->getControllerExtensionName());
            $newVotingOption->setTitle($newVotingOptionTitle);
            $newVotingOption->setSorting(9999);
            $this->votingOptionRepository->add($newVotingOption);
            $voting->addVotingOption($newVotingOption);
            $this->votingRepository->update($voting);
            $this->persistenceManager->persistAll();
            return json_encode(array('reloadVotingOptions' => $voting->getUid()));
        } else {
            // Error: Non-owner tries to create a new VotingOption
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />VotingOptionController/newAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Edit a voting option
     *
     * @ignorevalidation $voting
     * @param \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption
     * @return string
     */
    public function editAction(\Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption)
    {
        if ($this->isCurrentUserOwnerOfPanel($votingOption->getVoting()->getPanel())) {
            $this->view->assign('votingOption', $votingOption);
            return json_encode(array('content' => $this->view->render()));
        } else {
            // Error: Non-owner tries to edit a voting option
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />VotingOptionController/editAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Allow all properties of communityUser
     * Convert birthdate to DateTime
     */
    protected function initializeUpdateAction()
    {
        $propertyMappingConfiguration = $this->arguments['votingOption']->getPropertyMappingConfiguration();
        $uploadConfiguration = array(
            UploadedFileReferenceConverter::CONFIGURATION_ALLOWED_FILE_EXTENSIONS => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
            UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER => '1:/userimages/',
        );
        $propertyMappingConfiguration->allowAllProperties();
        $propertyMappingConfiguration->forProperty('image')
            ->setTypeConverterOptions(
                'Visol\\Easyvote\\Property\\TypeConverter\\UploadedFileReferenceConverter',
                $uploadConfiguration
            );
    }

    /**
     * Update a voting option
     *
     * @param \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption
     * @return string
     */
    public function updateAction(\Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption)
    {
        if ($this->isCurrentUserOwnerOfPanel($votingOption->getVoting()->getPanel())) {
            $this->votingOptionRepository->update($votingOption);
            $this->persistenceManager->persistAll();
            return json_encode(array('status' => 200));
        } else {
            // Error: Non-owner tries to update a voting option
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />VotingOptionController/updateAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Delete a voting option
     *
     * @param \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption
     * @ignorevalidation $votingOption
     * @return string
     */
    public function deleteAction(\Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption)
    {
        if ($this->isCurrentUserOwnerOfPanel($votingOption->getVoting()->getPanel())) {
            $this->votingOptionRepository->remove($votingOption);
            $this->persistenceManager->persistAll();
            return json_encode(array('removeElement' => true));
        } else {
            // Error: Non-owner tries to delete a voting option
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />VotingOptionController/deleteAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }

    /**
     * Sort a voting option
     *
     * @param \Visol\EasyvoteEducation\Domain\Model\Voting $voting
     * @param array $sorting
     * @return string
     */
    public function sortAction(\Visol\EasyvoteEducation\Domain\Model\Voting $voting, $sorting)
    {
        if ($this->isCurrentUserOwnerOfPanel($voting->getPanel())) {
            $votingOptions = $this->votingOptionRepository->findByVoting($voting);
            foreach ($votingOptions as $votingOption) {
                /** @var $votingOption \Visol\EasyvoteEducation\Domain\Model\VotingOption */
                $votingOption->setSorting((int)$sorting[$votingOption->getUid()]);
                $this->votingOptionRepository->update($votingOption);
            }
            $this->persistenceManager->persistAll();
            return json_encode(array('status' => 200));
        } else {
            // Error: Non-owner tries to sort a voting option
            $reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
            $reason .= '<br />VotingOptionController/sortAction';
            return json_encode(array('status' => 403, 'reason' => $reason));
        }
    }
}